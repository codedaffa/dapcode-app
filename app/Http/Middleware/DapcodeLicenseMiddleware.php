<?php

namespace App\Http\Middleware;

use App\Services\Dapcode\LicenseGuard;
use Closure;
use Illuminate\Http\Request;

class DapcodeLicenseMiddleware
{
    /**
     * Handle global and route-specific license access control (Layer 1 Defense).
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $module
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ?string $module = null)
    {
        // 1. Check if the current route is excluded from license enforcement
        if (LicenseGuard::isExcludedRequest($request)) {
            return $next($request);
        }

        // 2. Resolve target module
        $targetModule = $module ?? $request->route('module') ?? $request->segment(1);

        // 3. Layer 1 Defense: Execute central license assertion
        LicenseGuard::assertModuleAllowed($targetModule);

        return $next($request);
    }
}
