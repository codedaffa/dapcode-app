<div class="content-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div style="min-width: 0; flex: 1;">
            <h2 style="font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 4px;">{{ $title }}</h2>
            <p style="color: var(--text-muted); font-size: 13.5px;">{{ $subtitle }}</p>
        </div>
        <button type="button" style="background: var(--primary); color: #fff; border: none; padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-pen-to-square"></i> {{ __('common.edit') }} {{ __('modules.profile.name') }}
        </button>
    </div>

    <div class="stats-grid-3" style="margin-bottom: 24px;">
        <div class="stat-item">
            <div style="color: var(--text-muted); font-size: 12px; font-weight: 500;">{{ __('modules.profile.full_name') }}</div>
            <div style="font-size: 16px; font-weight: 600; color: #fff; margin-top: 4px;">DapCode Administrator</div>
        </div>
        <div class="stat-item">
            <div style="color: var(--text-muted); font-size: 12px; font-weight: 500;">{{ __('modules.profile.headline') }}</div>
            <div style="font-size: 16px; font-weight: 600; color: #38bdf8; margin-top: 4px;">Senior Software Engineer</div>
        </div>
        <div class="stat-item">
            <div style="color: var(--text-muted); font-size: 12px; font-weight: 500;">{{ __('modules.profile.email') }}</div>
            <div style="font-size: 16px; font-weight: 600; color: #34d399; margin-top: 4px;">admin@dapcode.test</div>
        </div>
    </div>

    <div class="stat-item">
        <div style="font-family: monospace; font-size: 13px; color: #38bdf8; word-break: break-all;">
            <i class="fa-solid fa-folder-tree"></i> Controller: app/Modules/Profile/Controllers/Profile.php<br>
            <i class="fa-solid fa-cube"></i> Base Core: app/Http/Controllers/Core/ProfileControllers.php
        </div>
    </div>
</div>