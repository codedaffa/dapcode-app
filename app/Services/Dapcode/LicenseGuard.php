<?php

namespace App\Services\Dapcode;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class LicenseGuard
{
    /**
     * Cached license state for current request cycle.
     *
     * @var array|null
     */
    protected static $cachedLicense = null;

    /**
     * Check if the application as a whole can be accessed.
     * Requires ACTIVE status, valid signature, matching installation ID, and non-expired license.
     *
     * @return bool
     */
    public static function canAccessApplication(): bool
    {
        $license = self::getLicense();
        if (!$license) {
            return false;
        }

        // Integrity check
        if (!IntegrityService::checkIntegrity($license)) {
            return false;
        }

        // Status must be ACTIVE
        if (!isset($license['status']) || strtoupper($license['status']) !== 'ACTIVE') {
            return false;
        }

        // Expiration check
        if (!empty($license['expires_at'])) {
            $expiresAt = Carbon::parse($license['expires_at']);
            if (Carbon::now()->isAfter($expiresAt)) {
                return false;
            }
        }

        // Cryptographic verification
        $verification = LicenseVerifier::verify($license);
        return $verification['valid'];
    }

    /**
     * Check if the given module is allowed to execute under the active license.
     *
     * @param string $moduleName
     * @return bool
     */
    public static function isModuleAllowed(string $moduleName): bool
    {
        // 1. Must satisfy overall application access
        if (!self::canAccessApplication()) {
            return false;
        }

        $license = self::getLicense();
        if (!$license) {
            return false;
        }

        $normalizedModule = strtolower($moduleName);

        // 2. Verify module authorization against license and revocation state
        $verification = LicenseVerifier::verify($license, $normalizedModule);
        return $verification['valid'];
    }

    /**
     * Check if installation is currently active with a valid license.
     *
     * @return bool
     */
    public static function isActivated(): bool
    {
        return self::canAccessApplication();
    }

    /**
     * Get the current license status (ACTIVE, PENDING, EXPIRED, REVOKED, CORRUPTED, INVALID).
     *
     * @return string
     */
    public static function getStatus(): string
    {
        $license = self::getLicense();
        if (!$license) {
            return 'PENDING';
        }

        if (!IntegrityService::checkIntegrity($license)) {
            return 'CORRUPTED';
        }

        if (isset($license['status']) && strtoupper($license['status']) === 'REVOKED') {
            return 'REVOKED';
        }

        if (!empty($license['expires_at'])) {
            $expiresAt = Carbon::parse($license['expires_at']);
            if (Carbon::now()->isAfter($expiresAt)) {
                return 'EXPIRED';
            }
        }

        $verification = LicenseVerifier::verify($license);
        if (!$verification['valid']) {
            return 'INVALID';
        }

        return 'ACTIVE';
    }

    /**
     * Get the active license data from local private storage.
     *
     * @return array|null
     */
    public static function getLicense(): ?array
    {
        if (static::$cachedLicense !== null) {
            return static::$cachedLicense;
        }

        $licensePath = config('dapcode.files.license', storage_path('app/dapcode/.license'));
        if (!File::exists($licensePath)) {
            return null;
        }

        $content = json_decode(File::get($licensePath), true);
        static::$cachedLicense = is_array($content) ? $content : null;

        return static::$cachedLicense;
    }

    /**
     * Clear cached license in memory.
     *
     * @return void
     */
    public static function clearCache(): void
    {
        static::$cachedLicense = null;
    }

    /**
     * Get installation ID.
     *
     * @return string
     */
    public static function getInstallationId(): string
    {
        return InstallationService::getInstallationId();
    }
}
