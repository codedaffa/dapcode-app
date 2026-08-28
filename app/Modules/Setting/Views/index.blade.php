<div class="content-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div style="min-width: 0; flex: 1;">
            <h2 style="font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 4px;">{{ $title }}</h2>
            <p style="color: var(--text-muted); font-size: 13.5px;">{{ $subtitle }}</p>
        </div>
        <button type="button" style="background: var(--primary); color: #fff; border: none; padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-floppy-disk"></i> {{ __('common.save') }} {{ __('modules.setting.name') }}
        </button>
    </div>

    <!-- Language Selector Card inside Settings -->
    <div class="stat-item" style="margin-bottom: 20px;">
        <div style="font-size: 15px; font-weight: 600; color: #fff; margin-bottom: 8px;">
            <i class="fa-solid fa-language" style="color: var(--primary);"></i> {{ __('modules.setting.language_pref') }}
        </div>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">
            Pilih bahasa antarmuka aplikasi. Seluruh modul dan teks sistem akan menyesuaikan secara instan.
        </p>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ url('/lang/id') }}" style="padding: 10px 18px; border-radius: 8px; font-size: 13.5px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; border: 1px solid {{ app()->getLocale() === 'id' ? 'var(--primary)' : 'var(--border-color)' }}; background: {{ app()->getLocale() === 'id' ? 'var(--primary)' : 'rgba(255,255,255,0.03)' }}; color: #fff;">
                <span>🇮🇩</span> <span>{{ __('common.indonesian') }} (ID)</span>
                @if(app()->getLocale() === 'id') <i class="fa-solid fa-check" style="margin-left: 6px;"></i> @endif
            </a>
            <a href="{{ url('/lang/en') }}" style="padding: 10px 18px; border-radius: 8px; font-size: 13.5px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; border: 1px solid {{ app()->getLocale() === 'en' ? 'var(--primary)' : 'var(--border-color)' }}; background: {{ app()->getLocale() === 'en' ? 'var(--primary)' : 'rgba(255,255,255,0.03)' }}; color: #fff;">
                <span>🇬🇧</span> <span>{{ __('common.english') }} (EN)</span>
                @if(app()->getLocale() === 'en') <i class="fa-solid fa-check" style="margin-left: 6px;"></i> @endif
            </a>
        </div>
    </div>

    <div class="stat-item">
        <div style="font-family: monospace; font-size: 13px; color: #38bdf8; word-break: break-all;">
            <i class="fa-solid fa-folder-tree"></i> Controller: app/Modules/Setting/Controllers/Setting.php<br>
            <i class="fa-solid fa-cube"></i> Base Core: app/Http/Controllers/Core/SettingControllers.php
        </div>
    </div>
</div>