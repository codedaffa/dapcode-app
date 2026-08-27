<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;

class UserControllers extends Controller
{
    /**
     * Nama modul identitas.
     *
     * @var string
     */
    protected $moduleName = 'User';

    /**
     * Helper to render view within User module namespace.
     *
     * @param string $view
     * @param array $data
     * @param bool $return
     * @return \Illuminate\Contracts\View\View|string
     */
    protected function moduleRender(string $view, array $data = [], bool $return = false)
    {
        $viewPath = strpos($view, '::') === false ? "user::{$view}" : $view;
        return $this->render($viewPath, array_merge(['moduleName' => $this->moduleName], $data), $return);
    }
}
