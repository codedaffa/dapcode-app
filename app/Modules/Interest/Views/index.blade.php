<div class="content-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div style="min-width: 0; flex: 1;">
            <h2 style="font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 4px;">{{ $title }}</h2>
            <p style="color: var(--text-muted); font-size: 13.5px;">{{ $subtitle }}</p>
        </div>
        <button type="button" style="background: var(--primary); color: #fff; border: none; padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-plus"></i> {{ __('common.add_new') }} {{ __('modules.interest.name') }}
        </button>
    </div>

    <div class="stats-grid-3" style="margin-bottom: 24px;">
        <div class="stat-item">
            <div style="font-size: 20px; color: #818cf8; margin-bottom: 8px;"><i class="fa-solid fa-microchip"></i></div>
            <div style="font-size: 15px; font-weight: 600; color: #fff; margin-bottom: 4px;">System Architecture</div>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">HMVC, Microservices, Domain-Driven Design (DDD).</p>
        </div>
        <div class="stat-item">
            <div style="font-size: 20px; color: #38bdf8; margin-bottom: 8px;"><i class="fa-solid fa-cloud"></i></div>
            <div style="font-size: 15px; font-weight: 600; color: #fff; margin-bottom: 4px;">Cloud Infrastructure</div>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Docker, Kubernetes, AWS, Serverless computing.</p>
        </div>
        <div class="stat-item">
            <div style="font-size: 20px; color: #34d399; margin-bottom: 8px;"><i class="fa-solid fa-brain"></i></div>
            <div style="font-size: 15px; font-weight: 600; color: #fff; margin-bottom: 4px;">Artificial Intelligence</div>
            <p style="font-size: 13px; color: var(--text-muted); margin: 0;">LLM Integration, Machine Learning, Automation.</p>
        </div>
    </div>

    <div class="stat-item">
        <div style="font-family: monospace; font-size: 13px; color: #38bdf8; word-break: break-all;">
            <i class="fa-solid fa-folder-tree"></i> Controller: app/Modules/Interest/Controllers/Interest.php<br>
            <i class="fa-solid fa-cube"></i> Base Core: app/Http/Controllers/Core/InterestControllers.php
        </div>
    </div>
</div>