<?php

namespace App\Http\Controllers;

use App\Libraries\Template;
use App\Services\Dapcode\LicenseGuard;
use App\Services\HMVC\HMVC;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseLaravelController;

class Controller extends BaseLaravelController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var Template
     */
    protected $template;

    public function __construct()
    {
        $this->template = app(Template::class);

        // Enforce no cross-module instantiation
        if (property_exists($this, 'moduleName') && !empty($this->moduleName)) {
            $canonicalModule = HMVC::resolveCanonicalModuleName($this->moduleName);
            if ($canonicalModule) {
                HMVC::enforceNoCrossModule($this->moduleName);

                // Layer 3 Defense: Enforce module license authorization on controller instantiation
                LicenseGuard::assertModuleAllowed($canonicalModule);
            }
        }
    }

    /**
     * Render a view wrapped in the theme (header, sidebar, footer).
     *
     * @param string $view e.g. "dashboard::index" or "user::profile"
     * @param array $data
     * @param bool $return
     * @return \Illuminate\Contracts\View\View|string
     */
    protected function render(string $view, array $data = [], bool $return = false)
    {
        if (strpos($view, '::') !== false) {
            [$viewNs, $viewFile] = explode('::', $view, 2);
            $canonicalModule = HMVC::resolveCanonicalModuleName($viewNs);
            $availableModules = LicenseGuard::getAllAvailableModules();
            if ($canonicalModule && in_array($canonicalModule, $availableModules, true)) {
                LicenseGuard::assertModuleAllowed($canonicalModule);
            }
        }

        if (!$this->template) {
            $this->template = app(Template::class);
        }

        return $this->template->render($view, $data, $return);
    }

    /**
     * Render a view scoped to the active module's view namespace.
     *
     * @param string $view e.g. "index" (resolved to "modulename::index")
     * @param array $data
     * @param bool $return
     * @return \Illuminate\Contracts\View\View|string
     */
    protected function moduleRender(string $view, array $data = [], bool $return = false)
    {
        $moduleKey = property_exists($this, 'moduleName') && !empty($this->moduleName)
            ? strtolower($this->moduleName)
            : 'app';

        $canonicalModule = HMVC::resolveCanonicalModuleName($moduleKey);

        // Layer 3 Defense: Enforce module authorization before view rendering
        if ($canonicalModule && $canonicalModule !== 'app') {
            LicenseGuard::assertModuleAllowed($canonicalModule);
        }

        $viewPath = strpos($view, '::') === false ? "{$moduleKey}::{$view}" : $view;
        $payload = array_merge(['moduleName' => $this->moduleName ?? ucfirst($moduleKey)], $data);

        return $this->render($viewPath, $payload, $return);
    }

    /**
     * Return a standardized JSON success response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function jsonResponse($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return a standardized JSON error response.
     *
     * @param string $message
     * @param int $code
     * @param array $errors
     * @return JsonResponse
     */
    protected function jsonError(string $message = 'Error', int $code = 400, array $errors = []): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'code' => $code,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Run an HMVC sub-request to another module controller action hierarchically.
     *
     * @param string $target e.g. "Dashboard@widget" or "User/ProfileController@stats"
     * @param array $parameters
     * @return mixed
     */
    protected function hmvc(string $target, array $parameters = [])
    {
        return HMVC::run($target, $parameters);
    }
}
