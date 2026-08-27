<?php

namespace App\Modules\Customer\Controllers;

use App\Http\Controllers\Core\CustomerControllers;
use Illuminate\Http\Request;

class CustomerController extends CustomerControllers
{
    /**
     * Display the index page for Customer module.
     * Accessible via: /Customer
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => 'HMVC Customer Module'
        ]);
    }
}