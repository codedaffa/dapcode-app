<?php

namespace App\Modules\Education\Controllers;

use App\Http\Controllers\Core\EducationControllers;
use Illuminate\Http\Request;

class Education extends EducationControllers
{
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => __('modules.education.title'),
            'subtitle' => __('modules.education.subtitle'),
        ]);
    }
}