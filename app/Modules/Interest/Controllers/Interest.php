<?php

namespace App\Modules\Interest\Controllers;

use App\Http\Controllers\Core\InterestControllers;
use Illuminate\Http\Request;

class Interest extends InterestControllers
{
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => __('modules.interest.title'),
            'subtitle' => __('modules.interest.subtitle'),
        ]);
    }
}