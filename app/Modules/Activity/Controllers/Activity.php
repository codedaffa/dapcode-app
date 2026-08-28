<?php

namespace App\Modules\Activity\Controllers;

use App\Http\Controllers\Core\ActivityControllers;
use Illuminate\Http\Request;

class Activity extends ActivityControllers
{
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => __('modules.activity.title'),
            'subtitle' => __('modules.activity.subtitle'),
        ]);
    }
}