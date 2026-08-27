<?php

use App\Services\HMVC\HMVC;

if (!function_exists('hmvc')) {
    /**
     * Run a module action hierarchically.
     *
     * @param string $target e.g. "Dashboard@widget" or "User/ProfileController@stats"
     * @param array $parameters
     * @return mixed
     */
    function hmvc(string $target, array $parameters = [])
    {
        return HMVC::run($target, $parameters);
    }
}

if (!function_exists('module_view')) {
    /**
     * Render a module view.
     *
     * @param string $view e.g. "dashboard::index" or "user::profile"
     * @param array $data
     * @param array $mergeData
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    function module_view(string $view, array $data = [], array $mergeData = [])
    {
        return view($view, $data, $mergeData);
    }
}

if (!function_exists('template_render')) {
    /**
     * Render a view wrapped in the theme using Template library.
     *
     * @param string $view e.g. "dashboard::index" or "home::index"
     * @param array $data
     * @param bool $return
     * @return \Illuminate\Contracts\View\View|string
     */
    function template_render(string $view, array $data = [], bool $return = false)
    {
        return app(\App\Libraries\Template::class)->render($view, $data, $return);
    }
}
