<?php

namespace App\Services\Dapcode;

use App\Libraries\Template;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
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
     * Requires valid core files integrity, ACTIVE status, valid signature, matching installation ID, and non-expired license.
     *
     * @return bool
     */
    public static function canAccessApplication(): bool
    {
        // 1. Layer 5 Check: Core system files integrity
        if (!IntegrityService::verifyCoreFilesIntegrity()) {
            return false;
        }

        $license = self::getLicense();
        if (!$license) {
            return false;
        }

        // 2. License file integrity check
        if (!IntegrityService::checkIntegrity($license)) {
            return false;
        }

        // 3. Status must be ACTIVE
        if (!isset($license['status']) || strtoupper($license['status']) !== 'ACTIVE') {
            return false;
        }

        // 4. Expiration check
        if (!empty($license['expires_at'])) {
            $expiresAt = Carbon::parse($license['expires_at']);
            if (Carbon::now()->isAfter($expiresAt)) {
                return false;
            }
        }

        // 5. Cryptographic verification
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
        // 1. Must satisfy overall application access and integrity
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
     * Flag to prevent recursion when rendering error views.
     *
     * @var bool
     */
    protected static $isRenderingErrorView = false;

    /**
     * Single Central Authorization Decision Point.
     * Asserts that a module and the entire application are fully authorized and untampered.
     * Throws an immediate fail-closed HttpResponseException (403 HTML/JSON) if unauthorized.
     *
     * @param string|null $module
     * @throws HttpResponseException
     * @return void
     */
    public static function assertModuleAllowed(?string $module = null): void
    {
        if (static::$isRenderingErrorView) {
            return;
        }

        $request = request();

        // Excluded routes (e.g. /dapcode/activate, assets) are always permitted
        if ($request && self::isExcludedRequest($request)) {
            return;
        }

        $installationId = self::getInstallationId();

        // 1. Layer 5: System integrity verification
        if (!IntegrityService::verifyCoreFilesIntegrity()) {
            if ($request && ($request->expectsJson() || $request->is('api/*'))) {
                throw new HttpResponseException(response()->json([
                    'status'  => 'forbidden',
                    'code'    => 403,
                    'message' => 'System integrity verification failed.',
                ], 403));
            }

            static::$isRenderingErrorView = true;
            try {
                $template = app(Template::class);
                $rendered = $template->render('dapcode.license-required', [
                    'title'          => 'System Integrity Verification Failed',
                    'pageTitle'      => 'Integritas Sistem Gagal',
                    'moduleName'     => $module ? ucfirst($module) : 'Aplikasi',
                    'installationId' => $installationId,
                    'licenseStatus'  => 'INTEGRITY_FAILED',
                    'activationUrl'  => url('/dapcode/activate'),
                ], true);
            } finally {
                static::$isRenderingErrorView = false;
            }

            throw new HttpResponseException(response($rendered, 403));
        }

        // 2. Application-wide license access validation
        if (!self::canAccessApplication()) {
            $status = self::getStatus();

            if ($request && ($request->expectsJson() || $request->is('api/*'))) {
                throw new HttpResponseException(response()->json([
                    'status'          => 'forbidden',
                    'code'            => 403,
                    'message'         => 'Aktivasi lisensi DapCode diperlukan untuk mengakses aplikasi.',
                    'installation_id' => $installationId,
                    'license_status'  => $status,
                    'activation_url'  => url('/dapcode/activate'),
                ], 403));
            }

            static::$isRenderingErrorView = true;
            try {
                $template = app(Template::class);
                $rendered = $template->render('dapcode.license-required', [
                    'title'          => 'Aktivasi Lisensi Diperlukan',
                    'pageTitle'      => 'Aktivasi Diperlukan',
                    'moduleName'     => 'Aplikasi',
                    'installationId' => $installationId,
                    'licenseStatus'  => $status,
                    'activationUrl'  => url('/dapcode/activate'),
                ], true);
            } finally {
                static::$isRenderingErrorView = false;
            }

            throw new HttpResponseException(response($rendered, 403));
        }

        // 3. Protected Module authorization check
        $targetModule = $module ?? ($request ? ($request->route('module') ?? $request->segment(1)) : null);

        if ($targetModule !== null && $targetModule !== '') {
            $normalizedModule = strtolower($targetModule);
            $availableModules = self::getAllAvailableModules();

            if (in_array($normalizedModule, $availableModules, true)) {
                if (!self::isModuleAllowed($normalizedModule)) {
                    $status = self::getStatus();

                    if ($request && ($request->expectsJson() || $request->is('api/*'))) {
                        throw new HttpResponseException(response()->json([
                            'status'          => 'forbidden',
                            'code'            => 403,
                            'message'         => "Aktivasi Diperlukan: Modul [{$normalizedModule}] membutuhkan lisensi DapCode yang aktif.",
                            'installation_id' => $installationId,
                            'license_status'  => $status,
                            'activation_url'  => url('/dapcode/activate'),
                        ], 403));
                    }

                    $template = app(Template::class);
                    $rendered = $template->render('dapcode.license-required', [
                        'title'          => 'Aktivasi Lisensi Diperlukan',
                        'pageTitle'      => 'Aktivasi Diperlukan - ' . ucfirst($normalizedModule),
                        'moduleName'     => ucfirst($normalizedModule),
                        'installationId' => $installationId,
                        'licenseStatus'  => $status,
                        'activationUrl'  => url('/dapcode/activate'),
                    ], true);

                    throw new HttpResponseException(response($rendered, 403));
                }

                // 4. Layer 6 Defense: Encrypted Module Availability & Checksum Verification
                if (ModuleEncryptionService::isModuleEncrypted($normalizedModule)) {
                    $moduleStatus = ModuleEncryptionService::getModuleStatus($normalizedModule);

                    if ($moduleStatus === 'TAMPERED') {
                        if ($request && ($request->expectsJson() || $request->is('api/*'))) {
                            throw new HttpResponseException(response()->json([
                                'status'          => 'forbidden',
                                'code'            => 403,
                                'message'         => "Verifikasi integritas modul [{$normalizedModule}] gagal (File dimodifikasi atau rusak).",
                                'installation_id' => $installationId,
                                'license_status'  => $status,
                                'activation_url'  => url('/dapcode/activate'),
                            ], 403));
                        }

                        $template = app(Template::class);
                        $rendered = $template->render('dapcode.license-required', [
                            'title'          => 'Integritas Modul Gagal',
                            'pageTitle'      => 'Integritas Modul Gagal - ' . ucfirst($normalizedModule),
                            'moduleName'     => ucfirst($normalizedModule),
                            'installationId' => $installationId,
                            'licenseStatus'  => 'MODULE_INTEGRITY_FAILED',
                            'activationUrl'  => url('/dapcode/activate'),
                        ], true);

                        throw new HttpResponseException(response($rendered, 403));
                    }

                    if (!ModuleEncryptionService::isModuleAvailable($normalizedModule)) {
                        if ($request && ($request->expectsJson() || $request->is('api/*'))) {
                            throw new HttpResponseException(response()->json([
                                'status'          => 'forbidden',
                                'code'            => 403,
                                'message'         => "Modul [{$normalizedModule}] terkunci atau file controller belum di-unlock (LOCKED).",
                                'installation_id' => $installationId,
                                'license_status'  => $status,
                                'activation_url'  => url('/dapcode/activate'),
                            ], 403));
                        }

                        $template = app(Template::class);
                        $rendered = $template->render('dapcode.license-required', [
                            'title'          => 'Modul Terkunci',
                            'pageTitle'      => 'Modul Terkunci - ' . ucfirst($normalizedModule),
                            'moduleName'     => ucfirst($normalizedModule),
                            'installationId' => $installationId,
                            'licenseStatus'  => 'MODULE_LOCKED',
                            'activationUrl'  => url('/dapcode/activate'),
                        ], true);

                        throw new HttpResponseException(response($rendered, 403));
                    }
                }
            }
        }
    }

    /**
     * Check if a given HTTP Request matches excluded route patterns.
     *
     * @param Request|null $request
     * @return bool
     */
    public static function isExcludedRequest(?Request $request): bool
    {
        if (!$request) {
            return false;
        }

        $excludedRoutes = config('dapcode.excluded_routes', [
            'dapcode/*',
            'dapcode',
            'build/*',
            'assets/*',
            'favicon.ico',
            '_debugbar/*',
        ]);

        foreach ($excludedRoutes as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
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
     * Get the current license status (ACTIVE, PENDING, EXPIRED, REVOKED, CORRUPTED, INVALID, INTEGRITY_FAILED).
     *
     * @return string
     */
    public static function getStatus(): string
    {
        if (!IntegrityService::verifyCoreFilesIntegrity()) {
            return 'INTEGRITY_FAILED';
        }

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
     * Clear cached license and integrity state in memory.
     *
     * @return void
     */
    public static function clearCache(): void
    {
        static::$cachedLicense = null;
        IntegrityService::clearCache();
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

    /**
     * Get all available HMVC modules automatically discovered from app/Modules and config.
     *
     * @return array
     */
    public static function getAllAvailableModules(): array
    {
        $configured = (array) config('dapcode.modules', []);
        $discovered = [];

        $modulesPath = app_path('Modules');
        if (File::isDirectory($modulesPath)) {
            $dirs = File::directories($modulesPath);
            foreach ($dirs as $dir) {
                $discovered[] = strtolower(basename($dir));
            }
        }

        return array_values(array_unique(array_merge(
            array_map('strtolower', $configured),
            $discovered
        )));
    }

    /**
     * Get all allowed modules for active license.
     *
     * @return array
     */
    public static function getAllowedModules(): array
    {
        $license = self::getLicense();
        if (!$license || !self::canAccessApplication()) {
            return [];
        }

        $modules = (array) ($license['modules'] ?? []);
        $revoked = (array) ($license['revoked_modules'] ?? []);

        if (in_array('*', $modules, true)) {
            $all = self::getAllAvailableModules();
            return array_values(array_filter($all, function ($m) use ($revoked) {
                return !in_array(strtolower($m), array_map('strtolower', $revoked), true);
            }));
        }

        return array_values(array_filter($modules, function ($m) use ($revoked) {
            return !in_array(strtolower($m), array_map('strtolower', $revoked), true);
        }));
    }
}
