<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UiShowcaseController extends Controller
{
    /**
     * Display UI Component Library Showcase / Playground.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        return $this->render('showcase.index', [
            'title' => 'UI Component Showcase',
            'pageTitle' => 'UI Component Showcase | Dapcode Framework',
            'subtitle' => 'Katalog Lengkap UI Component Library & Design System Internal',
        ]);
    }
}
