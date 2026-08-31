<?php

namespace App\Http\Controllers;

use App\Services\Dapcode\LicenseGuard;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    /**
     * Display the main portfolio homepage.
     * Protected by central license assertion execution boundary.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        // Central Execution Boundary: Enforce application license access
        LicenseGuard::assertModuleAllowed('portfolio');

        return view('portfolio', [
            'title'     => __('common.portfolio_home'),
            'pageTitle' => __('common.app_name') . ' - ' . __('common.portfolio_home'),
        ]);
    }
}
