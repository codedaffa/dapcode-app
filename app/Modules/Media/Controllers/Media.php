<?php

namespace App\Modules\Media\Controllers;

use App\Http\Controllers\Core\MediaControllers;
use Illuminate\Http\Request;

class Media extends MediaControllers
{
    /**
     * Display the index page for Media module.
     * Accessible via: /Media
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => 'HMVC Media Module'
        ]);
    }
}