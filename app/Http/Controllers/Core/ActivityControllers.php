<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;

class ActivityControllers extends Controller
{
    /**
     * Module name identifier.
     *
     * @var string
     */
    protected $moduleName = 'Activity';

    /**
     * Helper to render view within Activity module namespace.
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