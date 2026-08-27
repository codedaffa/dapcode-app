<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;

class CustomerControllers extends Controller
{
    /**
     * Nama modul identitas.
     *
     * @var string
     */
    protected $moduleName = 'Customer';

    /**
     * Helper to render view within Customer module namespace.
     *
     * @param string $view
     * @param array $data
     * @param bool $return
     * @return \Illuminate\Contracts\View\View|string
     */
    protected function moduleRender(string $view, array $data = [], bool $return = false)
    {
        $viewPath = strpos($view, '::') === false ? "customer::{$view}" : $view;
        return $this->render($viewPath, array_merge(['moduleName' => $this->moduleName], $data), $return);
    }
}
