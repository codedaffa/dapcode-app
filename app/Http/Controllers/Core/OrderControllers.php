<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;

class OrderControllers extends Controller
{
    /**
     * Nama modul identitas.
     *
     * @var string
     */
    protected $moduleName = 'Order';

    /**
     * Helper to render view within Order module namespace.
     *
     * @param string $view
     * @param array $data
     * @param bool $return
     * @return \Illuminate\Contracts\View\View|string
     */
    protected function moduleRender(string $view, array $data = [], bool $return = false)
    {
        $viewPath = strpos($view, '::') === false ? "order::{$view}" : $view;
        return $this->render($viewPath, array_merge(['moduleName' => $this->moduleName], $data), $return);
    }
}
