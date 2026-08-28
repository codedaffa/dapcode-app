<?php

namespace App\Modules\Certification\Controllers;

use App\Http\Controllers\Core\CertificationControllers;
use Illuminate\Http\Request;

class Certification extends CertificationControllers
{
    /**
     * Display the index page for Certification module.
     * Accessible via: /Certification
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => 'HMVC Certification Module'
        ]);
    }
}