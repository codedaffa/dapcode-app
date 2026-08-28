<?php

namespace App\Modules\Career\Controllers;

use App\Http\Controllers\Core\CareerControllers;
use Illuminate\Http\Request;

class Career extends CareerControllers
{
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => __('modules.career.title'),
            'subtitle' => __('modules.career.subtitle'),
        ]);
    }
}