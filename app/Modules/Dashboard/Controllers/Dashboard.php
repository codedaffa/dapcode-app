<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Core\DashboardControllers;
use Illuminate\Http\Request;

class Dashboard extends DashboardControllers
{
    /**
     * Halaman Utama Dashboard.
     * Accessible via: /dashboard atau /Dashboard
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        $stats = $this->getSystemStats();
        $activities = $this->getRecentActivities();
        $userGrowth = $this->calculateGrowth(1280, 1150);

        return $this->moduleRender('index', [
            'title' => 'Dashboard Overview',
            'stats' => $stats,
            'activities' => $activities,
            'userGrowth' => $userGrowth,
        ]);
    }

    /**
     * Halaman Analitik Dashboard.
     * Accessible via: /dashboard/analytics
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analytics(Request $request)
    {
        return $this->jsonResponse([
            'module' => $this->moduleName,
            'system_stats' => $this->getSystemStats(),
            'growth_rate' => $this->calculateGrowth(1280, 1150),
            'timestamp' => now()->toIso8601String(),
        ], "Data analitik dari Base DashboardControllers");
    }

    /**
     * Endpoint detail dengan parameter.
     * Accessible via: /dashboard/detail/123
     *
     * @param Request $request
     * @param string|int|null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(Request $request, $id = null)
    {
        return $this->jsonResponse([
            'module' => $this->moduleName,
            'controller' => 'Dashboard',
            'base_class' => 'App\Http\Controllers\Core\DashboardControllers',
            'received_id' => $id,
            'stats_from_base' => $this->getSystemStats(),
        ], "HMVC Parameter Dispatching dengan Core DashboardControllers Berhasil!");
    }

    /**
     * Method Hierarchical Sub-Request HMVC (dapat dipanggil via hmvc('Dashboard@widget')).
     *
     * @param string $widgetTitle
     * @return \Illuminate\Contracts\View\View
     */
    public function widget(string $widgetTitle = 'Statistik Sistem')
    {
        return view('dashboard::widget', [
            'widgetTitle' => $widgetTitle,
            'stats' => [
                'Total Modul' => 6,
                'HMVC Mode' => 'Active (Auto Dispatched)',
                'Template Library' => 'Integrated (Header, Sidebar, Footer)',
                'Core Base Class' => 'App\Http\Controllers\Core\DashboardControllers'
            ]
        ]);
    }
}
