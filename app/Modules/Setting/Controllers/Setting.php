<?php

namespace App\Modules\Setting\Controllers;

use App\Http\Controllers\Core\SettingControllers;
use Illuminate\Http\Request;

class Setting extends SettingControllers
{
    /**
     * Display the index page for Setting module.
     * Accessible via: /Setting
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => 'HMVC Setting Module'
        ]);
    }
}