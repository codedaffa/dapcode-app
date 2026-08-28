<?php

namespace App\Modules\Education\Controllers;

use App\Http\Controllers\Core\EducationControllers;
use Illuminate\Http\Request;

class Education extends EducationControllers
{
    /**
     * Display the index page for Education module.
     * Accessible via: /Education
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => 'HMVC Education Module'
        ]);
    }
}