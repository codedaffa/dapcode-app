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

    <!-- 1. Language Selector Card inside Settings -->
    <div id="section-language" class="stat-item" style="margin-bottom: 20px; scroll-margin-top: 80px; {{ request()->query('tab') === 'language' ? 'border-color: var(--primary) !important; box-shadow: 0 0 20px rgba(99, 102, 241, 0.3);' : '' }}">
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

    <!-- 2. Indonesian Celebration & Holiday Theme Engine -->
    @php
        $activeHoliday = holiday_theme();
        $allPresets = \App\Services\Theme\HolidayThemeService::getAllThemePresets();
        $isManual = session()->has('holiday_theme') && session('holiday_theme') !== 'auto';
    @endphp
    <div id="section-themes" class="stat-item" style="margin-bottom: 20px; scroll-margin-top: 80px; {{ request()->query('tab') === 'themes' || request()->has('theme') ? 'border-color: var(--primary) !important; box-shadow: 0 0 20px rgba(99, 102, 241, 0.3);' : '' }}">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 8px;">
            <div style="font-size: 15px; font-weight: 600; color: #fff; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-calendar-days" style="color: var(--primary);"></i> Tema Hari Perayaan Indonesia (Otomatis &amp; Real-Time)
            </div>
            <span class="holiday-celebration-pill" style="font-size: 11px;">
                <i class="{{ $activeHoliday['icon'] }}"></i> {{ $activeHoliday['name'] }}
            </span>
        </div>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 14px;">
            Sistem secara otomatis mendeteksi kalender nasional Indonesia (HUT Kemerdekaan RI 17 Agustus, Idul Fitri, Imlek, Natal, Hari Pahlawan, dll.) dan mengubah aksen warna, logo, dan ucapan tema secara real-time. Anda juga dapat melakukan pratinjau tema di bawah ini:
        </p>

        <!-- Presets Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 8px; margin-bottom: 12px;">
            <!-- Auto Mode -->
            <a href="{{ request()->fullUrlWithQuery(['theme' => 'auto']) }}" style="padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; display: flex; align-items: center; justify-content: space-between; border: 1px solid {{ !$isManual ? 'var(--primary)' : 'var(--border-color)' }}; background: {{ !$isManual ? 'var(--primary-light)' : 'rgba(255,255,255,0.02)' }}; color: #fff;">
                <span><i class="fa-solid fa-wand-magic-sparkles" style="color: #38bdf8; margin-right: 6px;"></i> Otomatis (Kalender)</span>
                @if(!$isManual) <i class="fa-solid fa-check" style="color: #34d399;"></i> @endif
            </a>

            @foreach($allPresets as $key => $preset)
                @if($key !== 'default')
                    @php $isActiveThis = $isManual && session('holiday_theme') === $key; @endphp
                    <a href="{{ request()->fullUrlWithQuery(['theme' => $key]) }}" style="padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; display: flex; align-items: center; justify-content: space-between; border: 1px solid {{ $isActiveThis ? $preset['primary_color'] : 'var(--border-color)' }}; background: {{ $isActiveThis ? 'rgba(255,255,255,0.08)' : 'rgba(255,255,255,0.02)' }}; color: #fff;">
                        <span><i class="{{ $preset['icon'] }}" style="color: {{ $preset['primary_color'] }}; margin-right: 6px;"></i> {{ $preset['badge'] }}</span>
                        @if($isActiveThis) <i class="fa-solid fa-check" style="color: #34d399;"></i> @endif
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    <!-- 3. System HMVC Info -->
    <div class="stat-item">
        <div style="font-family: monospace; font-size: 13px; color: #38bdf8; word-break: break-all;">
            <i class="fa-solid fa-folder-tree"></i> Controller: app/Modules/Setting/Controllers/Setting.php<br>
            <i class="fa-solid fa-cube"></i> Base Core: app/Http/Controllers/Core/SettingControllers.php
        </div>
    </div>
</div>