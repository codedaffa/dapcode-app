<div class="content-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div style="min-width: 0; flex: 1;">
            <h2 style="font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 4px;">{{ $title }}</h2>
            <p style="color: var(--text-muted); font-size: 13.5px;">{{ $subtitle }}</p>
        </div>
        <button type="button" style="background: var(--primary); color: #fff; border: none; padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-plus"></i> {{ __('common.add_new') }} {{ __('modules.career.name') }}
        </button>
    </div>

    <div class="stats-grid-2" style="margin-bottom: 24px;">
        <div class="stat-item">
            <div style="font-size: 16px; font-weight: 600; color: #fff; margin-bottom: 4px;">Lead Backend Engineer</div>
            <div style="font-size: 13.5px; color: #38bdf8; margin-bottom: 6px;">Tech Global Solution &bull; 2024 - Present</div>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Architected core services, streamlined CI/CD pipelines, and led team of 8 engineers.</p>
        </div>
        <div class="stat-item">
            <div style="font-size: 16px; font-weight: 600; color: #fff; margin-bottom: 4px;">Software Developer</div>
            <div style="font-size: 13.5px; color: #38bdf8; margin-bottom: 6px;">Inovasi Digital Nusantara &bull; 2022 - 2024</div>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Engineered RESTful APIs, modular systems, and real-time dashboard analytics.</p>
        </div>
    </div>

    <div class="stat-item">
        <div style="font-family: monospace; font-size: 13px; color: #38bdf8; word-break: break-all;">
            <i class="fa-solid fa-folder-tree"></i> Controller: app/Modules/Career/Controllers/Career.php<br>
            <i class="fa-solid fa-cube"></i> Base Core: app/Http/Controllers/Core/CareerControllers.php
        </div>
    </div>
</div>