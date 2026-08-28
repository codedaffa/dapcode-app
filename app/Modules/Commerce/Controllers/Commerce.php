<?php

namespace App\Modules\Commerce\Controllers;

use App\Http\Controllers\Core\CommerceControllers;
use Illuminate\Http\Request;

class Commerce extends CommerceControllers
{
    /**
     * Display the index page for Commerce module.
     * Accessible via: /Commerce
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        $moduleKey = 'commerce';
        return $this->moduleRender('index', [
            'title' => __("modules.{$moduleKey}.title") !== "modules.{$moduleKey}.title" ? __("modules.{$moduleKey}.title") : "Commerce Module",
            'subtitle' => __("modules.{$moduleKey}.subtitle") !== "modules.{$moduleKey}.subtitle" ? __("modules.{$moduleKey}.subtitle") : "HMVC Commerce Module",
        ]);
    }
}