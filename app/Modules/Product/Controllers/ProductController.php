<?php

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Core\ProductControllers;
use Illuminate\Http\Request;

class ProductController extends ProductControllers
{
    /**
     * Display the index page for Product module.
     * Accessible via: /product
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => 'HMVC Product Module'
        ]);
    }
}