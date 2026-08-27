<?php

namespace App\Modules\Home\Controllers;

use App\Http\Controllers\Core\HomeControllers;
use Illuminate\Http\Request;

class HomeController extends HomeControllers
{
    /**
     * Display the index page for Home module.
     * Accessible via: /home or /Home
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => 'Home Dashboard'
        ]);
    }
}