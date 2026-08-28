<div class="content-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div style="min-width: 0; flex: 1;">
            <h2 style="font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 4px;">{{ $title }}</h2>
            <p style="color: var(--text-muted); font-size: 13.5px;">{{ $subtitle }}</p>
        </div>
        <button type="button" style="background: var(--primary); color: #fff; border: none; padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-plus"></i> {{ __('common.add_new') }} {{ __('modules.achievement.name') }}
        </button>
    </div>

    <div class="stats-grid-2" style="margin-bottom: 24px;">
        <div class="stat-item">
            <div style="font-size: 16px; font-weight: 600; color: #fff; margin-bottom: 4px;">1st Place &ndash; National Hackathon 2024</div>
            <div style="font-size: 13.5px; color: #f59e0b; margin-bottom: 6px;"><i class="fa-solid fa-medal"></i> Winner &bull; Ministry of Communication & IT</div>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Developed AI-powered agricultural logistics optimization system.</p>
        </div>
        <div class="stat-item">
            <div style="font-size: 16px; font-weight: 600; color: #fff; margin-bottom: 4px;">Best Research Paper Award</div>
            <div style="font-size: 13.5px; color: #f59e0b; margin-bottom: 6px;"><i class="fa-solid fa-award"></i> IEEE Conference 2023</div>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Awarded for excellence in microservices and modular distributed architectures.</p>
        </div>
    </div>

    <div class="stat-item">
        <div style="font-family: monospace; font-size: 13px; color: #38bdf8; word-break: break-all;">
            <i class="fa-solid fa-folder-tree"></i> Controller: app/Modules/Achievement/Controllers/Achievement.php<br>
            <i class="fa-solid fa-cube"></i> Base Core: app/Http/Controllers/Core/AchievementControllers.php
        </div>
    </div>
</div>