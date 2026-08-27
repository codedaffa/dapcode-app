<?php

namespace App\Modules\Order\Controllers;

use App\Http\Controllers\Core\OrderControllers;
use Illuminate\Http\Request;

class OrderController extends OrderControllers
{
    /**
     * Display the index page for Order module.
     * Accessible via: /order or /Order
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => 'HMVC Order Module'
        ]);
    }
}