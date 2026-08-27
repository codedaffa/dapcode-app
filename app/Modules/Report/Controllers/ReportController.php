<?php

namespace App\Modules\Report\Controllers;

use App\Http\Controllers\Core\ReportControllers;
use Illuminate\Http\Request;

class ReportController extends ReportControllers
{
    /**
     * Display the index page for Report module.
     * Accessible via: /report or /Report
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        return $this->moduleRender('index', [
            'title' => 'HMVC Report Module'
        ]);
    }
}