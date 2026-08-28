<div class="content-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div style="min-width: 0; flex: 1;">
            <h2 style="font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 4px;">{{ $title }}</h2>
            <p style="color: var(--text-muted); font-size: 13.5px;">{{ $subtitle }}</p>
        </div>
        <button type="button" style="background: var(--primary); color: #fff; border: none; padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-cart-plus"></i> {{ __('modules.commerce.add_product') }}
        </button>
    </div>

    <!-- Product Showcase Cards Grid -->
    <div class="stats-grid-3" style="margin-bottom: 24px;">
        <div class="stat-item">
            <div style="font-size: 24px; color: #38bdf8; margin-bottom: 8px;"><i class="fa-solid fa-box-open"></i></div>
            <div style="font-size: 16px; font-weight: 600; color: #fff; margin-bottom: 4px;">Enterprise HMVC Starter Kit</div>
            <div style="font-size: 14px; font-weight: 700; color: #34d399; margin-bottom: 6px;">Rp 750.000 / license</div>
            <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">Production-ready boilerplate for multi-modular Laravel projects with built-in HMVC engine.</p>
        </div>
        <div class="stat-item">
            <div style="font-size: 24px; color: #818cf8; margin-bottom: 8px;"><i class="fa-solid fa-server"></i></div>
            <div style="font-size: 16px; font-weight: 600; color: #fff; margin-bottom: 4px;">Cloud Deployment Suite</div>
            <div style="font-size: 14px; font-weight: 700; color: #34d399; margin-bottom: 6px;">Rp 1.200.000 / year</div>
            <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">Automated CI/CD workflows, Docker containerization, and Kubernetes cluster configurations.</p>
        </div>
        <div class="stat-item">
            <div style="font-size: 24px; color: #f59e0b; margin-bottom: 8px;"><i class="fa-solid fa-code"></i></div>
            <div style="font-size: 16px; font-weight: 600; color: #fff; margin-bottom: 4px;">Custom Module Development</div>
            <div style="font-size: 14px; font-weight: 700; color: #34d399; margin-bottom: 6px;">Custom Quote</div>
            <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">Professional tailored software modules, API integrations, and database migrations.</p>
        </div>
    </div>

    <div class="stat-item">
        <div style="font-family: monospace; font-size: 13px; color: #38bdf8; word-break: break-all;">
            <i class="fa-solid fa-folder-tree"></i> Controller: app/Modules/Commerce/Controllers/Commerce.php<br>
            <i class="fa-solid fa-cube"></i> Base Core: app/Http/Controllers/Core/CommerceControllers.php
        </div>
    </div>
</div>