<?php

namespace App\Modules\Research\Controllers;

use App\Http\Controllers\Core\ResearchControllers;
use Illuminate\Http\Request;

class Research extends ResearchControllers
{
    /**
     * Display the index page for Research module.
     * Accessible via: /Research
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => 'HMVC Research Module'
        ]);
    }
}