<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;

class DashboardControllers extends Controller
{
    /**
     * Nama modul identitas.
     *
     * @var string
     */
    protected $moduleName = 'Dashboard';

    /**
     * Konfigurasi / metadata default untuk modul Dashboard.
     *
     * @var array
     */
    protected $dashboardMeta = [
        'version' => '1.0.0',
        'theme_badge' => 'Dashboard Core System',
        'refresh_interval' => 60,
    ];

    /**
     * Helper render view khusus modul Dashboard.
     * Otomatis menginjeksi metadata modul dan view namespace dashboard::
     *
     * @param string $view Nama file view (misal: 'index', 'analytics', dsb.)
     * @param array $data Data variabel yang dikirim ke view
     * @param bool $return True jika ingin return raw string
     * @return \Illuminate\Contracts\View\View|string
     */
    protected function moduleRender(string $view, array $data = [], bool $return = false)
    {
        $viewPath = strpos($view, '::') === false ? "dashboard::{$view}" : $view;

        $defaultData = [
            'moduleName' => $this->moduleName,
            'dashboardMeta' => $this->dashboardMeta,
            'serverTime' => date('d M Y, H:i:s T'),
            'summaryStats' => $this->getSystemStats(),
        ];

        return $this->render($viewPath, array_merge($defaultData, $data), $return);
    }

    /**
     * Mengambil metrik data umum dashboard.
     *
     * @return array
     */
    protected function getSystemStats(): array
    {
        return [
            'total_users' => 1280,
            'active_sessions' => 48,
            'system_load' => '11.4%',
            'hmvc_modules' => 6,
            'database_status' => 'Connected',
            'cache_driver' => config('cache.default', 'file'),
        ];
    }

    /**
     * Mengambil log aktivitas terbaru untuk dashboard.
     *
     * @param int $limit
     * @return array
     */
    protected function getRecentActivities(int $limit = 5): array
    {
        return [
            [
                'time' => 'Baru saja',
                'action' => 'User Login',
                'description' => 'Administrator login ke sistem melalui Web UI',
                'type' => 'success',
                'icon' => 'fa-right-to-bracket'
            ],
            [
                'time' => '10 menit yang lalu',
                'action' => 'HMVC Module Dispatch',
                'description' => 'Request auto-dispatching ke modul /dashboard berhasil dieksekusi',
                'type' => 'info',
                'icon' => 'fa-bolt'
            ],
            [
                'time' => '1 jam yang lalu',
                'action' => 'Database Backup',
                'description' => 'Sinkronisasi database otomatis selesai',
                'type' => 'warning',
                'icon' => 'fa-database'
            ]
        ];
    }

    /**
     * Helper kalkulasi tren persentase kenaikan/penurunan.
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
            'trend' => $diff >= 0 ? 'up' : 'down'
        ];
    }
}
