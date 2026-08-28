<div class="content-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div style="min-width: 0; flex: 1;">
            <h2 style="font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 4px;">{{ $title }}</h2>
            <p style="color: var(--text-muted); font-size: 13.5px;">{{ $subtitle }}</p>
        </div>
        <button type="button" style="background: var(--primary); color: #fff; border: none; padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-plus"></i> {{ __('common.add_new') }} {{ __('modules.project.name') }}
        </button>
    </div>

    <div class="stats-grid-2" style="margin-bottom: 24px;">
        <div class="stat-item">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <h3 style="font-size: 16px; font-weight: 600; color: #fff; margin: 0;">DapCode HMVC Core</h3>
                <span style="background: rgba(16, 185, 129, 0.15); color: #34d399; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">Active</span>
            </div>
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">Next-generation modular Laravel framework with isolated modules and multi-language engine.</p>
            <div style="font-size: 12px; color: #38bdf8; font-family: monospace;">PHP 8 &bull; Laravel 8 &bull; HMVC Architecture</div>
        </div>
        <div class="stat-item">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <h3 style="font-size: 16px; font-weight: 600; color: #fff; margin: 0;">Enterprise API Gateway</h3>
                <span style="background: rgba(99, 102, 241, 0.15); color: #818cf8; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">Completed</span>
            </div>
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">High-throughput microservices gateway handling authentication, rate-limiting and telemetry.</p>
            <div style="font-size: 12px; color: #38bdf8; font-family: monospace;">Go &bull; Redis &bull; PostgreSQL</div>
        </div>
    </div>

    <div class="stat-item">
        <div style="font-family: monospace; font-size: 13px; color: #38bdf8; word-break: break-all;">
            <i class="fa-solid fa-folder-tree"></i> Controller: app/Modules/Project/Controllers/Project.php<br>
            <i class="fa-solid fa-cube"></i> Base Core: app/Http/Controllers/Core/ProjectControllers.php
        </div>
    </div>
</div>