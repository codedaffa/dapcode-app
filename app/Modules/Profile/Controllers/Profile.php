<?php

namespace App\Modules\Profile\Controllers;

use App\Http\Controllers\Core\ProfileControllers;
use Illuminate\Http\Request;

class Profile extends ProfileControllers
{
    /**
     * Display the index page for Profile module.
     * Accessible via: /Profile
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => 'HMVC Profile Module'
        ]);
    }
}