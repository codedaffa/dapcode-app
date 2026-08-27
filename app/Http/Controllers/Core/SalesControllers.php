<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;

class SalesControllers extends Controller
{
    /**
     * Module name identifier.
     *
     * @var string
     */
    protected $moduleName = 'Sales';

    /**
     * Helper to render view within Sales module namespace.
     *
     * @param string $view
     * @param array $data
     * @param bool $return
     * @return \Illuminate\Contracts\View\View|string
     */
    protected function moduleRender(string $view, array $data = [], bool $return = false)
    {
        $viewPath = strpos($view, '::') === false ? "sales::{$view}" : $view;
        return $this->render($viewPath, array_merge(['moduleName' => $this->moduleName], $data), $return);
    }
}