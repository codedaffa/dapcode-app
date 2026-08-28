<?php

namespace App\Http\Controllers;

use App\Libraries\Template;
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
            HMVC::enforceNoCrossModule($this->moduleName);
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
        if (!$this->template) {
            $this->template = app(Template::class);
        }

        return $this->template->render($view, $data, $return);
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
