<?php

namespace App\Modules\Setting\Controllers;

use App\Http\Controllers\Core\SettingControllers;
use Illuminate\Http\Request;

class Setting extends SettingControllers
{
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => __('modules.setting.title'),
            'subtitle' => __('modules.setting.subtitle'),
        ]);
    }
}