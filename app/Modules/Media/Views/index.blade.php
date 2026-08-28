<div class="content-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div style="min-width: 0; flex: 1;">
            <h2 style="font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 4px;">{{ $title }}</h2>
            <p style="color: var(--text-muted); font-size: 13.5px;">{{ $subtitle }}</p>
        </div>
        <button type="button" style="background: var(--primary); color: #fff; border: none; padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-cloud-arrow-up"></i> {{ __('modules.media.upload_new') }}
        </button>
    </div>

    <div class="stats-grid-3" style="margin-bottom: 24px;">
        <div class="stat-item">
            <div style="font-size: 24px; color: #38bdf8; margin-bottom: 8px;"><i class="fa-solid fa-file-image"></i></div>
            <div style="font-size: 15px; font-weight: 600; color: #fff; margin-bottom: 4px;">profile-avatar.webp</div>
            <div style="font-size: 12px; color: var(--text-muted);">Image &bull; 148 KB &bull; Uploaded 2026</div>
        </div>
        <div class="stat-item">
            <div style="font-size: 24px; color: #f59e0b; margin-bottom: 8px;"><i class="fa-solid fa-file-pdf"></i></div>
            <div style="font-size: 15px; font-weight: 600; color: #fff; margin-bottom: 4px;">curriculum-vitae.pdf</div>
            <div style="font-size: 12px; color: var(--text-muted);">PDF Document &bull; 2.4 MB &bull; Uploaded 2026</div>
        </div>
        <div class="stat-item">
            <div style="font-size: 24px; color: #a855f7; margin-bottom: 8px;"><i class="fa-solid fa-file-lines"></i></div>
            <div style="font-size: 15px; font-weight: 600; color: #fff; margin-bottom: 4px;">aws-certificate.pdf</div>
            <div style="font-size: 12px; color: var(--text-muted);">PDF Document &bull; 1.1 MB &bull; Uploaded 2026</div>
        </div>
    </div>

    <div class="stat-item">
        <div style="font-family: monospace; font-size: 13px; color: #38bdf8; word-break: break-all;">
            <i class="fa-solid fa-folder-tree"></i> Controller: app/Modules/Media/Controllers/Media.php<br>
            <i class="fa-solid fa-cube"></i> Base Core: app/Http/Controllers/Core/MediaControllers.php
        </div>
    </div>
</div>