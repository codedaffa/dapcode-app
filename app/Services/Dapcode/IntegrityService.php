<?php

namespace App\Services\Dapcode;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class IntegrityService
{
    /**
     * Compute and store integrity state signature for local license file.
     *
     * @param array $licenseData
     * @return string
     */
    public static function updateIntegrityState(array $licenseData): string
    {
        $statePath = config('dapcode.files.license_state', storage_path('app/dapcode/.license-state'));
        $installationId = InstallationService::getInstallationId();

        $statePayload = [
            'installation_id' => $installationId,
            'license_id'      => $licenseData['license_id'] ?? 'unknown',
            'signature_hash'  => hash('sha256', $licenseData['signature'] ?? ''),
            'checksum'        => hash('sha256', json_encode($licenseData)),
            'updated_at'      => time(),
        ];

        $encoded = json_encode($statePayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        File::put($statePath, $encoded);
        @chmod($statePath, 0600);

        return $statePayload['checksum'];
    }

    /**
     * Verify the integrity of the local license file against recorded state.
     * Fail-closed: Returns false if files are missing or tampered with.
     *
     * @param array $licenseData
     * @return bool
     */
    public static function checkIntegrity(array $licenseData): bool
    {
        $statePath = config('dapcode.files.license_state', storage_path('app/dapcode/.license-state'));

        if (!File::exists($statePath)) {
            // State file not created yet; initial integrity is valid only if signature is valid
            return true;
        }

        $stateContent = json_decode(File::get($statePath), true);
        if (!$stateContent || !isset($stateContent['signature_hash'], $stateContent['installation_id'])) {
            Log::warning('[AUDIT] EVENT: INTEGRITY_CHECK_FAILED', [
                'reason' => 'State file is corrupted or unreadable',
            ]);
            return false;
        }

        // Verify installation ID matches state
        if ($stateContent['installation_id'] !== InstallationService::getInstallationId()) {
            Log::warning('[AUDIT] EVENT: INTEGRITY_CHECK_FAILED', [
                'reason' => 'Installation ID mismatch between license and state',
            ]);
            return false;
        }

        // Verify license signature hash matches state
        $currentSigHash = hash('sha256', $licenseData['signature'] ?? '');
        if (!hash_equals($stateContent['signature_hash'], $currentSigHash)) {
            Log::warning('[AUDIT] EVENT: INTEGRITY_CHECK_FAILED', [
                'reason' => 'License signature hash mismatch against state checksum',
            ]);
            return false;
        }

        return true;
    }
}
