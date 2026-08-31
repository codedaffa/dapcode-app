<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;

class CommerceControllers extends Controller
{
    /**
     * Module name identifier.
     *
     * @var string
     */
    protected $moduleName = 'Commerce';

    /**
     * Helper to render view within Commerce module namespace.
     *
     * @param string $view
     * @param array $data
     * @param bool $return
     * @return \Illuminate\Contracts\View\View|string
     */
    protected function moduleRender(string $view, array $data = [], bool $return = false)
    {
        return parent::moduleRender($view, $data, $return);
    }
}