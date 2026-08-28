<?php

namespace App\Modules\Activity\Controllers;

use App\Http\Controllers\Core\ActivityControllers;
use Illuminate\Http\Request;

class Activity extends ActivityControllers
{
    /**
     * Display the index page for Activity module.
     * Accessible via: /Activity
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => 'HMVC Activity Module'
        ]);
    }
}