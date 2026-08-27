<?php

namespace App\Modules\Sales\Controllers;

use App\Http\Controllers\Core\SalesControllers;
use Illuminate\Http\Request;

class SalesController extends SalesControllers
{
    /**
     * Display the index page for Sales module.
     * Accessible via: /Sales
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => 'HMVC Sales Module'
        ]);
    }
}