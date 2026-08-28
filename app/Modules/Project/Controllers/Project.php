<?php

namespace App\Modules\Project\Controllers;

use App\Http\Controllers\Core\ProjectControllers;
use Illuminate\Http\Request;

class Project extends ProjectControllers
{
    /**
     * Display the index page for Project module.
     * Accessible via: /Project
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => 'HMVC Project Module'
        ]);
    }
}