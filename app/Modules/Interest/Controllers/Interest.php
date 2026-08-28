<?php

namespace App\Modules\Interest\Controllers;

use App\Http\Controllers\Core\InterestControllers;
use Illuminate\Http\Request;

class Interest extends InterestControllers
{
    /**
     * Display the index page for Interest module.
     * Accessible via: /Interest
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => 'HMVC Interest Module'
        ]);
    }
}