<?php

namespace App\Http\Middleware;

use App\Libraries\Template;
use App\Services\Dapcode\LicenseGuard;
use Closure;
use Illuminate\Http\Request;

class DapcodeLicenseMiddleware
{
    /**
     * Handle global and route-specific license access control.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $module
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ?string $module = null)
    {
        // 1. Check if the current route is excluded from license enforcement
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
                return $next($request);
            }
        }

        $installationId = LicenseGuard::getInstallationId();
        $status = LicenseGuard::getStatus();

        // 2. Check general application access (requires active and valid license)
        if (!LicenseGuard::canAccessApplication()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status'          => 'forbidden',
                    'code'            => 403,
                    'message'         => 'Aktivasi lisensi DapCode diperlukan untuk mengakses aplikasi.',
                    'installation_id' => $installationId,
                    'license_status'  => $status,
                    'activation_url'  => url('/dapcode/activate'),
                ], 403);
            }

            $template = app(Template::class);
            $rendered = $template->render('dapcode.license-required', [
                'title'          => 'Aktivasi Lisensi Diperlukan',
                'pageTitle'      => 'Aktivasi Diperlukan',
                'moduleName'     => 'Aplikasi',
                'installationId' => $installationId,
                'licenseStatus'  => $status,
                'activationUrl'  => url('/dapcode/activate'),
            ], true);

            return response($rendered, 403);
        }

        // 3. Application access is valid. Check specific module authorization if targeting a module.
        $targetModule = $module ?? $request->route('module') ?? $request->segment(1);
        $configuredModules = config('dapcode.modules', []);
        $normalizedConfigured = array_map('strtolower', $configuredModules);

        if ($targetModule && in_array(strtolower($targetModule), $normalizedConfigured, true)) {
            if (!LicenseGuard::isModuleAllowed($targetModule)) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'status'          => 'forbidden',
                        'code'            => 403,
                        'message'         => "Aktivasi Diperlukan: Modul [{$targetModule}] membutuhkan lisensi DapCode yang aktif.",
                        'installation_id' => $installationId,
                        'license_status'  => $status,
                        'activation_url'  => url('/dapcode/activate'),
                    ], 403);
                }

                $template = app(Template::class);
                $rendered = $template->render('dapcode.license-required', [
                    'title'          => 'Aktivasi Lisensi Diperlukan',
                    'pageTitle'      => 'Aktivasi Diperlukan - ' . ucfirst($targetModule),
                    'moduleName'     => ucfirst($targetModule),
                    'installationId' => $installationId,
                    'licenseStatus'  => $status,
                    'activationUrl'  => url('/dapcode/activate'),
                ], true);

                return response($rendered, 403);
            }
        }

        return $next($request);
    }
}
