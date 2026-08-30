<div class="content-card" style="max-width: 680px; margin: 40px auto; text-align: center; padding: 40px 30px;">
    <div style="width: 68px; height: 68px; margin: 0 auto 20px; background: rgba(239, 68, 68, 0.15); border: 2px solid #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 26px;">
        <i class="fa-solid fa-lock"></i>
    </div>

    <h2 style="font-size: 22px; font-weight: 700; color: #fff; margin-bottom: 8px;">Aktivasi Modul Diperlukan</h2>
    <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 24px; line-height: 1.6;">
        Modul <strong style="color: #38bdf8;">{{ $moduleName }}</strong> merupakan <em>Protected Module</em> dan memerlukan lisensi <strong>DapCode</strong> yang aktif pada instalasi ini.
    </p>

    <div class="stat-item" style="margin-bottom: 24px; text-align: left;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <span style="font-size: 11px; color: var(--text-sub); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Installation ID</span>
            <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b; font-size: 11px; padding: 3px 8px; border-radius: 4px; font-weight: 600;">
                STATUS: {{ $licenseStatus }}
            </span>
        </div>
        <div style="font-family: monospace; font-size: 13px; color: #e2e8f0; word-break: break-all; background: rgba(0,0,0,0.3); padding: 10px 12px; border-radius: 6px; border: 1px solid var(--border-color);">
            {{ $installationId }}
        </div>
    </div>

    <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
        <a href="{{ $activationUrl }}" class="btn" style="background: var(--primary); color: #fff; text-decoration: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);">
            <i class="fa-solid fa-key"></i> Aktivasi Sekarang
        </a>
        <a href="{{ url('/') }}" class="btn" style="background: rgba(255,255,255,0.08); border: 1px solid var(--border-color); color: var(--text-main); text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 500; font-size: 14px; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-left"></i> Halaman Utama
        </a>
    </div>
</div>
