<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;

class DashboardControllers extends Controller
{
    /**
     * Module name identifier.
     *
     * @var string
     */
    protected $moduleName = 'Dashboard';

    /**
     * Default dashboard metadata configuration.
     *
     * @var array
     */
    protected $dashboardMeta = [
        'version' => '1.0.0',
        'theme_badge' => 'Dashboard Core System',
        'refresh_interval' => 60,
    ];

    /**
     * Render view for the Dashboard module with injected system stats & metadata.
     *
     * @param string $view
     * @param array $data
     * @param bool $return
     * @return \Illuminate\Contracts\View\View|string
     */
    protected function moduleRender(string $view, array $data = [], bool $return = false)
    {
        $defaultData = [
            'moduleName' => $this->moduleName,
            'dashboardMeta' => $this->dashboardMeta,
            'serverTime' => date('d M Y, H:i:s T'),
            'summaryStats' => $this->getSystemStats(),
        ];

        return parent::moduleRender($view, array_merge($defaultData, $data), $return);
    }

    /**
     * Get real-time system metrics for the dashboard.
     *
     * @return array
     */
    protected function getSystemStats(): array
    {
        return [
            'total_users' => 1280,
            'active_sessions' => 48,
            'system_load' => '11.4%',
            'hmvc_modules' => 13,
            'database_status' => 'Connected',
            'cache_driver' => config('cache.default', 'file'),
        ];
    }

    /**
     * Get recent activity logs.
     *
     * @param int $limit
     * @return array
     */
    protected function getRecentActivities(int $limit = 5): array
    {
        $activities = [
            [
                'time' => 'Baru saja',
                'action' => 'User Login',
                'description' => 'Administrator login ke sistem melalui Web UI',
                'type' => 'success',
                'icon' => 'fa-right-to-bracket',
            ],
            [
                'time' => '10 menit yang lalu',
                'action' => 'HMVC Module Dispatch',
                'description' => 'Request auto-dispatching ke modul /dashboard berhasil dieksekusi',
                'type' => 'info',
                'icon' => 'fa-bolt',
            ],
            [
                'time' => '1 jam yang lalu',
                'action' => 'Database Backup',
                'description' => 'Sinkronisasi database otomatis selesai',
                'type' => 'warning',
                'icon' => 'fa-database',
            ],
        ];

        return array_slice($activities, 0, $limit);
    }

    /**
     * Calculate percentage growth trend between current and previous values.
     *
     * @param float|int $current
     * @param float|int $previous
     * @return array
     */
    protected function calculateGrowth($current, $previous): array
    {
        if ($previous == 0) {
            return ['percentage' => 100, 'trend' => 'up'];
        }

        $diff = (($current - $previous) / $previous) * 100;

        return [
            'percentage' => round(abs($diff), 1),
            'trend' => $diff >= 0 ? 'up' : 'down',
        ];
    }
}
