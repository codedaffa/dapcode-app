<?php

namespace App\Modules\Certification\Controllers;

use App\Http\Controllers\Core\CertificationControllers;
use Illuminate\Http\Request;

class Certification extends CertificationControllers
{
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => __('modules.certification.title'),
            'subtitle' => __('modules.certification.subtitle'),
        ]);
    }
}