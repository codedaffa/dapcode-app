<?php

namespace App\Modules\Project\Controllers;

use App\Http\Controllers\Core\ProjectControllers;
use Illuminate\Http\Request;

class Project extends ProjectControllers
{
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => __('modules.project.title'),
            'subtitle' => __('modules.project.subtitle'),
        ]);
    }
}