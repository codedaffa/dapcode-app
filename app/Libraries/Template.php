<?php

namespace App\Libraries;

use Illuminate\Support\Facades\View;

class Template
{
    /**
     * Data repository for template views.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Set a key-value or array of data into template.
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Get data from repository.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function get(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->data;
        }

        return $this->data[$key] ?? $default;
    }

    /**
     * Render the module view wrapped in the theme (header, sidebar, footer).
     *
     * @param string $view e.g. "dashboard::index" or "home::index"
     * @param array $data
     * @param bool $return
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render(string $view, array $data = [], bool $return = false)
    {
        $mergedData = array_merge($this->data, $data);

        // Security Execution Boundary: Enforce module license assertion if rendering a module view
        if (strpos($view, '::') !== false) {
            [$viewNs, $viewFile] = explode('::', $view, 2);
            $canonicalModule = \App\Services\HMVC\HMVC::resolveCanonicalModuleName($viewNs);
            $availableModules = \App\Services\Dapcode\LicenseGuard::getAllAvailableModules();
            if ($canonicalModule && in_array($canonicalModule, $availableModules, true)) {
                \App\Services\Dapcode\LicenseGuard::assertModuleAllowed($canonicalModule);
            }
        }

        // 1. Render the module's inner content view
        $moduleContent = View::make($view, $mergedData)->render();

        // 2. Prepare payload for the master theme layout
        $themePayload = array_merge($mergedData, [
            'content' => $moduleContent,
            'viewName' => $view,
            'pageTitle' => $mergedData['title'] ?? 'Dapcode Application',
        ]);

        // 3. Render the theme layout which includes header, sidebar, and footer
        $renderedLayout = View::make('theme.layout', $themePayload);

        if ($return) {
            return $renderedLayout->render();
        }

        return $renderedLayout;
    }

    /**
     * Static shortcut to render view using Template instance.
     * Usage: Template::render('dashboard::index', $data)
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return app(static::class)->$name(...$arguments);
    }
}
