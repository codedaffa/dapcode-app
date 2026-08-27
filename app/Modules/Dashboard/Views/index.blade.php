<div class="content-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div style="min-width: 0; flex: 1;">
            <h2 style="font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 4px;">{{ $title }}</h2>
            <p style="color: var(--text-muted); font-size: 13.5px;">
                Modul <strong>{{ $moduleName }}</strong> menggunakan Base Controller <code>App\Http\Controllers\Core\Dashboard</code>. Server Time: <span style="color: #38bdf8;">{{ $serverTime }}</span>
            </p>
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ url('/dashboard/analytics') }}" style="background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border-color); color: #fff; text-decoration: none; padding: 9px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-chart-pie"></i> Analytics JSON
            </a>
            <a href="{{ url('/dashboard/detail/2026') }}" style="background: var(--primary); color: #fff; text-decoration: none; padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; white-space: nowrap;">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Test JSON Detail
            </a>
        </div>
    </div>

    <!-- Responsive Metric Grid dari Base Controller -->
    <div class="stats-grid-4">
        <div class="stat-item">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: var(--text-muted); font-size: 12px; font-weight: 500;">TOTAL USERS</span>
                <span style="background: rgba(16, 185, 129, 0.15); color: #34d399; font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 4px;">
                    +{{ $userGrowth['percentage'] }}% <i class="fa-solid fa-arrow-trend-up"></i>
                </span>
            </div>
            <div style="font-size: 24px; font-weight: 700; color: #38bdf8; margin-top: 6px;">{{ number_format($stats['total_users']) }}</div>
        </div>
        <div class="stat-item">
            <div style="color: var(--text-muted); font-size: 12px; font-weight: 500;">ACTIVE SESSIONS</div>
            <div style="font-size: 24px; font-weight: 700; color: #10b981; margin-top: 6px;">{{ $stats['active_sessions'] }}</div>
        </div>
        <div class="stat-item">
            <div style="color: var(--text-muted); font-size: 12px; font-weight: 500;">SYSTEM LOAD</div>
            <div style="font-size: 24px; font-weight: 700; color: #f59e0b; margin-top: 6px;">{{ $stats['system_load'] }}</div>
        </div>
        <div class="stat-item">
            <div style="color: var(--text-muted); font-size: 12px; font-weight: 500;">HMVC MODULES</div>
            <div style="font-size: 24px; font-weight: 700; color: #a855f7; margin-top: 6px;">{{ $stats['hmvc_modules'] }} Modul</div>
        </div>
    </div>

    <!-- Recent Activities Feed dari Base Controller -->
    @if(isset($activities) && count($activities) > 0)
    <div style="margin-top: 24px; margin-bottom: 24px;">
        <h3 style="font-size: 15px; font-weight: 600; color: #fff; margin-bottom: 12px;">
            <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary);"></i> Aktivitas Sistem Terbaru (Disediakan oleh Base Core)
        </h3>
        <div style="display: flex; flex-direction: column; gap: 10px;">
            @foreach($activities as $item)
            <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color); padding: 12px 16px; border-radius: 8px; flex-wrap: wrap; gap: 8px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(99, 102, 241, 0.15); color: #818cf8; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                        <i class="fa-solid {{ $item['icon'] }}"></i>
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 600; color: #fff;">{{ $item['action'] }}</div>
                        <div style="font-size: 12px; color: var(--text-muted);">{{ $item['description'] }}</div>
                    </div>
                </div>
                <div style="font-size: 12px; color: #64748b; font-family: monospace;">
                    {{ $item['time'] }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Information box -->
    <div style="background: rgba(99, 102, 241, 0.08); border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 10px; padding: 18px; min-width: 0; word-break: break-word;">
        <h4 style="font-size: 14px; color: #818cf8; margin-bottom: 6px;"><i class="fa-solid fa-circle-nodes"></i> Struktur Pewarisan Kelas Core</h4>
        <p style="font-size: 13px; color: #cbd5e1; line-height: 1.5; margin: 0;">
            <code>DashboardController</code> meng-extend <code>App\Http\Controllers\Core\Dashboard</code> yang meng-extend <code>App\Http\Controllers\Core\BaseController</code>.
            Data metrik, kalkulasi tren, serta log aktivitas diwarisi langsung dari Base Controller.
        </p>
    </div>
</div>