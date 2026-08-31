<?php

namespace App\Services\Dapcode;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ActivationService
{
    /**
     * Activate or reactivate the installation with an asymmetric signed license payload.
     *
     * @param array|string $licenseInput JSON string or Array of license data
     * @return array{success: bool, message: string, data?: array}
     */
    public static function activate($licenseInput): array
    {
        Log::info('[AUDIT] EVENT: ACTIVATION_REQUESTED', [
            'installation_id' => InstallationService::getInstallationId(),
            'timestamp'       => date('c'),
        ]);

        $licenseData = is_array($licenseInput) ? $licenseInput : json_decode($licenseInput, true);

        if (!$licenseData || !is_array($licenseData)) {
            Log::warning('[AUDIT] EVENT: ACTIVATION_FAILED', [
                'installation_id' => InstallationService::getInstallationId(),
                'reason'          => 'Invalid payload format',
            ]);
            return ['success' => false, 'message' => 'Format payload aktivasi tidak valid.'];
        }

        // Check if this is a reactivation from previously revoked state
        $licensePath = config('dapcode.files.license', storage_path('app/dapcode/.license'));
        $isReactivation = false;
        if (File::exists($licensePath)) {
            $existing = json_decode(File::get($licensePath), true);
            if ($existing && isset($existing['status']) && strtoupper($existing['status']) === 'REVOKED') {
                $isReactivation = true;
            }
        }

        // Cryptographic verification against Public Verification Key
        $verification = LicenseVerifier::verify($licenseData);
        if (!$verification['valid']) {
            Log::warning('[AUDIT] EVENT: ACTIVATION_FAILED', [
                'installation_id' => InstallationService::getInstallationId(),
                'license_id'      => $licenseData['license_id'] ?? 'unknown',
                'reason'          => $verification['reason'],
            ]);
            return ['success' => false, 'message' => 'Aktivasi ditolak: ' . $verification['reason']];
        }

        // Store active license to private storage
        $directory = dirname($licensePath);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }

        $licenseData['status'] = 'ACTIVE';
        $licenseData['activated_at'] = date('c');
        $encoded = json_encode($licenseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        File::put($licensePath, $encoded);
        @chmod($licensePath, 0600);

        // Update local integrity checksum & core files manifest
        IntegrityService::updateIntegrityState($licenseData);
        IntegrityService::recordCoreFilesManifest();
        LicenseGuard::clearCache();

        // Unlock and decrypt authorized modules
        ModuleEncryptionService::unlockAuthorizedModules($licenseData);

        if ($isReactivation) {
            Log::info('[AUDIT] EVENT: LICENSE_REACTIVATED', [
                'license_id'      => $licenseData['license_id'] ?? 'unknown',
                'installation_id' => InstallationService::getInstallationId(),
                'modules'         => $licenseData['modules'] ?? [],
            ]);
        } else {
            Log::info('[AUDIT] EVENT: ACTIVATION_SUCCESS', [
                'license_id'      => $licenseData['license_id'] ?? 'unknown',
                'installation_id' => InstallationService::getInstallationId(),
                'modules'         => $licenseData['modules'] ?? [],
            ]);
        }

        return [
            'success' => true,
            'message' => 'Instalasi DapCode berhasil diaktivasi.',
            'data'    => $licenseData,
        ];
    }

    /**
     * Deactivate and revoke the local license (supports full or granular module revocation).
     * Requires an asymmetric Signed Revocation Token issued and signed by DapCode License Authority.
     *
     * @param array|string $revocationInput
     * @param string $fallbackReason
     * @return array{success: bool, message: string}
     */
    public static function deactivate($revocationInput, string $fallbackReason = 'Manual Revocation'): array
    {
        $licensePath = config('dapcode.files.license', storage_path('app/dapcode/.license'));

        if (!File::exists($licensePath)) {
            return ['success' => false, 'message' => 'Tidak ada lisensi aktif yang terpasang.'];
        }

        $currentLicense = json_decode(File::get($licensePath), true);
        if (!$currentLicense) {
            return ['success' => false, 'message' => 'File lisensi lokal tidak valid.'];
        }

        $revocationData = is_array($revocationInput) ? $revocationInput : json_decode($revocationInput, true);

        if (!$revocationData || !isset($revocationData['action'], $revocationData['signature'], $revocationData['license_id'])) {
            Log::warning('[AUDIT] EVENT: DEACTIVATION_INVALID_PAYLOAD', [
                'installation_id' => InstallationService::getInstallationId(),
            ]);
            return ['success' => false, 'message' => 'Format Signed Revocation Token tidak valid.'];
        }

        $action = strtoupper($revocationData['action']);
        if ($action !== 'REVOKE' && $action !== 'REVOKE_MODULES') {
            return ['success' => false, 'message' => 'Action payload bukan REVOKE atau REVOKE_MODULES.'];
        }

        if ($revocationData['license_id'] !== $currentLicense['license_id']) {
            return ['success' => false, 'message' => 'Revocation token tidak cocok dengan License ID aktif.'];
        }

        $currentInstallationId = InstallationService::getInstallationId();
        if (isset($revocationData['installation_id']) && $revocationData['installation_id'] !== '*' && $revocationData['installation_id'] !== $currentInstallationId) {
            return ['success' => false, 'message' => 'Revocation token ditujukan untuk Installation ID berbeda.'];
        }

        // Verify encrypted authority token on revocation payload
        $expectedRevokeAuthToken = LicenseVerifier::generateAuthToken((string) $revocationData['license_id'], (string) ($revocationData['installation_id'] ?? $currentInstallationId), 'REVOKE');
        if (!isset($revocationData['auth_token']) || !hash_equals($expectedRevokeAuthToken, (string) $revocationData['auth_token'])) {
            Log::warning('[AUDIT] EVENT: DEACTIVATION_INVALID_AUTH_TOKEN', [
                'installation_id' => $currentInstallationId,
                'license_id'      => $currentLicense['license_id'],
            ]);
            return ['success' => false, 'message' => 'Encrypted Authority Token (auth_token) pada Revocation Token tidak valid.'];
        }

        // Verify cryptographic signature against Authority Public Key
        $signature = $revocationData['signature'];
        $clean = $revocationData;
        unset($clean['signature']);
        ksort($clean);
        if (isset($clean['revoked_modules']) && is_array($clean['revoked_modules'])) {
            sort($clean['revoked_modules']);
        }
        if (isset($clean['modules']) && is_array($clean['modules'])) {
            sort($clean['modules']);
        }
        $canonical = json_encode($clean, JSON_UNESCAPED_SLASHES);

        if (!LicenseVerifier::verifyAsymmetricSignature($canonical, $signature)) {
            Log::warning('[AUDIT] EVENT: DEACTIVATION_UNAUTHORIZED', [
                'installation_id' => $currentInstallationId,
                'license_id'      => $currentLicense['license_id'],
            ]);
            return ['success' => false, 'message' => 'Tanda tangan digital Revocation Token tidak sah (Ditolak oleh Public Key).'];
        }

        // Check if this is a Granular Module Revocation
        $revokedModules = $revocationData['revoked_modules'] ?? $revocationData['modules'] ?? null;

        if (!empty($revokedModules) && is_array($revokedModules) && !in_array('*', $revokedModules, true)) {
            $currentModules = (array) ($currentLicense['modules'] ?? []);
            if (in_array('*', $currentModules, true)) {
                $currentModules = LicenseGuard::getAllAvailableModules();
            }

            $normalizedRevoked = array_map('strtolower', $revokedModules);
            $currentLicense['revoked_modules'] = array_values(array_unique(array_merge(
                $currentLicense['revoked_modules'] ?? [],
                $normalizedRevoked
            )));

            $remainingModules = array_values(array_filter($currentModules, function ($mod) use ($currentLicense) {
                return !in_array(strtolower($mod), $currentLicense['revoked_modules'], true);
            }));

            if (empty($remainingModules)) {
                $currentLicense['status'] = 'REVOKED';
            }

            $currentLicense['revoked_at'] = $revocationData['revoked_at'] ?? date('c');
            $currentLicense['revocation_reason'] = $revocationData['reason'] ?? $fallbackReason;

            File::put($licensePath, json_encode($currentLicense, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            @chmod($licensePath, 0600);

            IntegrityService::updateIntegrityState($currentLicense);
            LicenseGuard::clearCache();

            // Lock and remove plaintext for revoked modules
            ModuleEncryptionService::lockRevokedModules($normalizedRevoked);

            $modList = implode(', ', $normalizedRevoked);
            Log::info('[AUDIT] EVENT: MODULES_REVOKED', [
                'license_id'      => $currentLicense['license_id'],
                'installation_id' => $currentInstallationId,
                'revoked_modules' => $normalizedRevoked,
                'remaining'       => $remainingModules,
            ]);

            return [
                'success' => true,
                'message' => "Modul [{$modList}] berhasil dicabut dari lisensi aktif.",
            ];
        }

        // Full License Revocation
        $currentLicense['status'] = 'REVOKED';
        $currentLicense['revoked_at'] = $revocationData['revoked_at'] ?? date('c');
        $currentLicense['revocation_reason'] = $revocationData['reason'] ?? $fallbackReason;

        File::put($licensePath, json_encode($currentLicense, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        @chmod($licensePath, 0600);

        // Update integrity state to reflect REVOKED status
        IntegrityService::updateIntegrityState($currentLicense);
        LicenseGuard::clearCache();

        // Invalidate application cache on license revocation
        if (function_exists('cache')) {
            try {
                cache()->flush();
            } catch (\Throwable $e) {
                // Ignore cache driver errors
            }
        }

        // Full Revocation: Lock and remove plaintext for all encrypted modules
        ModuleEncryptionService::lockRevokedModules();

        Log::info('[AUDIT] EVENT: LICENSE_REVOKED', [
            'license_id'      => $currentLicense['license_id'],
            'installation_id' => $currentInstallationId,
            'reason'          => $currentLicense['revocation_reason'],
        ]);

        return [
            'success' => true,
            'message' => 'Lisensi berhasil dicabut secara resmi oleh DapCode License Authority (Status: REVOKED).',
        ];
    }
}
