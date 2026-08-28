<?php

namespace App\Modules\Research\Controllers;

use App\Http\Controllers\Core\ResearchControllers;
use Illuminate\Http\Request;

class Research extends ResearchControllers
{
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => __('modules.research.title'),
            'subtitle' => __('modules.research.subtitle'),
        ]);
    }
}