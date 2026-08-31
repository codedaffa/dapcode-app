<?php

namespace App\Services\Dapcode;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class IntegrityService
{
    /**
     * In-memory cache for core files integrity check.
     *
     * @var bool|null
     */
    protected static $cachedCoreIntegrity = null;

    /**
     * Timestamp when core files integrity was last cached.
     *
     * @var int
     */
    protected static $cachedCoreIntegrityTime = 0;

    /**
     * Cache TTL in seconds for core integrity checks.
     *
     * @var int
     */
    protected static $cacheTtlSeconds = 60;

    /**
     * Get the list of critical core system files to monitor for integrity.
     * Uses relative Laravel base paths for portability.
     *
     * @return array<string, string>
     */
    public static function getCoreFiles(): array
    {
        return [
            'middleware'       => app_path('Http/Middleware/DapcodeLicenseMiddleware.php'),
            'license_guard'    => app_path('Services/Dapcode/LicenseGuard.php'),
            'license_verifier' => app_path('Services/Dapcode/LicenseVerifier.php'),
            'integrity'        => app_path('Services/Dapcode/IntegrityService.php'),
            'activation'       => app_path('Services/Dapcode/ActivationService.php'),
            'installation'      => app_path('Services/Dapcode/InstallationService.php'),
            'module_encryption' => app_path('Services/Dapcode/ModuleEncryptionService.php'),
            'hmvc'              => app_path('Services/HMVC/HMVC.php'),
            'controller'        => app_path('Http/Controllers/Controller.php'),
            'hmvc_controller'      => app_path('Http/Controllers/HMVCController.php'),
            'portfolio_controller' => app_path('Http/Controllers/PortfolioController.php'),
            'template'             => app_path('Libraries/Template.php'),
            'hmvc_provider'        => app_path('Providers/HMVCServiceProvider.php'),
            'app_provider'         => app_path('Providers/AppServiceProvider.php'),
        ];
    }

    /**
     * Generate and record the core files integrity manifest.
     *
     * @return array
     */
    public static function recordCoreFilesManifest(): array
    {
        $manifest = [];
        foreach (self::getCoreFiles() as $key => $filePath) {
            if (File::exists($filePath)) {
                $manifest[$key] = [
                    'file' => str_replace('\\', '/', str_replace(base_path() . DIRECTORY_SEPARATOR, '', $filePath)),
                    'hash' => hash_file('sha256', $filePath),
                    'size' => filesize($filePath),
                ];
            }
        }

        $manifestPath = config('dapcode.files.integrity_manifest', storage_path('app/dapcode/.integrity-manifest'));
        $manifestPayload = [
            'installation_id' => InstallationService::getInstallationId(),
            'generated_at'    => time(),
            'files'           => $manifest,
        ];

        File::put($manifestPath, json_encode($manifestPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        @chmod($manifestPath, 0600);

        static::$cachedCoreIntegrity = true;
        static::$cachedCoreIntegrityTime = time();

        return $manifest;
    }

    /**
     * Verify the integrity of critical core application files (Layer 5 Defense).
     * Implements in-memory caching with TTL to eliminate performance overhead.
     * Fail-closed: Returns false on mismatch or tampering.
     *
     * @return bool
     */
    public static function verifyCoreFilesIntegrity(): bool
    {
        $now = time();
        if (static::$cachedCoreIntegrity !== null && ($now - static::$cachedCoreIntegrityTime) < static::$cacheTtlSeconds) {
            return static::$cachedCoreIntegrity;
        }

        $manifestPath = config('dapcode.files.integrity_manifest', storage_path('app/dapcode/.integrity-manifest'));

        // If manifest does not exist yet (e.g. initial setup), generate it automatically
        if (!File::exists($manifestPath)) {
            self::recordCoreFilesManifest();
            static::$cachedCoreIntegrity = true;
            static::$cachedCoreIntegrityTime = $now;
            return true;
        }

        $manifestData = json_decode(File::get($manifestPath), true);
        if (!$manifestData || !isset($manifestData['files']) || !is_array($manifestData['files'])) {
            Log::warning('[AUDIT] EVENT: CORE_INTEGRITY_CHECK_FAILED', [
                'reason' => 'Manifest file is unreadable or has invalid structure',
            ]);
            static::$cachedCoreIntegrity = false;
            static::$cachedCoreIntegrityTime = $now;
            return false;
        }

        foreach (self::getCoreFiles() as $key => $filePath) {
            if (!File::exists($filePath)) {
                Log::warning('[AUDIT] EVENT: CORE_INTEGRITY_CHECK_FAILED', [
                    'reason' => "Critical core file missing: {$key} ({$filePath})",
                ]);
                static::$cachedCoreIntegrity = false;
                static::$cachedCoreIntegrityTime = $now;
                return false;
            }

            if (!isset($manifestData['files'][$key]['hash'])) {
                Log::warning('[AUDIT] EVENT: CORE_INTEGRITY_CHECK_FAILED', [
                    'reason' => "Core file key missing in manifest: {$key}",
                ]);
                static::$cachedCoreIntegrity = false;
                static::$cachedCoreIntegrityTime = $now;
                return false;
            }

            $expectedHash = $manifestData['files'][$key]['hash'];
            $actualHash = hash_file('sha256', $filePath);

            if (!hash_equals($expectedHash, $actualHash)) {
                Log::warning('[AUDIT] EVENT: CORE_INTEGRITY_CHECK_FAILED', [
                    'file_key' => $key,
                    'file'     => $filePath,
                    'reason'   => 'Core file SHA-256 hash mismatch against integrity manifest',
                ]);
                static::$cachedCoreIntegrity = false;
                static::$cachedCoreIntegrityTime = $now;
                return false;
            }
        }

        static::$cachedCoreIntegrity = true;
        static::$cachedCoreIntegrityTime = $now;
        return true;
    }

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

    /**
     * Clear the in-memory integrity cache.
     *
     * @return void
     */
    public static function clearCache(): void
    {
        static::$cachedCoreIntegrity = null;
        static::$cachedCoreIntegrityTime = 0;
    }
}
