<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;

class ProfileControllers extends Controller
{
    /**
     * Module name identifier.
     *
     * @var string
     */
    protected $moduleName = 'Profile';

    /**
     * Helper to render view within Profile module namespace.
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