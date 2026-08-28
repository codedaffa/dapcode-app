<?php

namespace App\Modules\Career\Controllers;

use App\Http\Controllers\Core\CareerControllers;
use Illuminate\Http\Request;

class Career extends CareerControllers
{
    /**
     * Display the index page for Career module.
     * Accessible via: /Career
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => 'HMVC Career Module'
        ]);
    }
}