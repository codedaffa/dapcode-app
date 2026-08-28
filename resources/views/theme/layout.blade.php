@include('theme.header')

<!-- Responsive Sidebar Overlay -->
<div id="sidebarOverlay" class="sidebar-overlay"></div>

@include('theme.sidebar')

<div class="main-wrapper">
    <header class="app-header">
        <div class="header-left">
            <button type="button" id="sidebarToggle" class="sidebar-toggle-btn" aria-label="Toggle Sidebar">
                <i class="fa-solid fa-bars"></i>
            </button>
            
            @php
                $currentModSegment = strtolower(request()->segment(1) ?: 'dashboard');
                $allModulesData = [
                    'dashboard' => ['name' => __('modules.dashboard.name'), 'icon' => 'fa-solid fa-gauge-high', 'url' => url('/dashboard')],
                    'profile' => ['name' => __('modules.profile.name'), 'icon' => 'fa-solid fa-id-card', 'url' => url('/profile')],
                    'education' => ['name' => __('modules.education.name'), 'icon' => 'fa-solid fa-graduation-cap', 'url' => url('/education')],
                    'certification' => ['name' => __('modules.certification.name'), 'icon' => 'fa-solid fa-certificate', 'url' => url('/certification')],
                    'achievement' => ['name' => __('modules.achievement.name'), 'icon' => 'fa-solid fa-trophy', 'url' => url('/achievement')],
                    'interest' => ['name' => __('modules.interest.name'), 'icon' => 'fa-solid fa-heart', 'url' => url('/interest')],
                    'project' => ['name' => __('modules.project.name'), 'icon' => 'fa-solid fa-diagram-project', 'url' => url('/project')],
                    'research' => ['name' => __('modules.research.name'), 'icon' => 'fa-solid fa-flask-vial', 'url' => url('/research')],
                    'career' => ['name' => __('modules.career.name'), 'icon' => 'fa-solid fa-briefcase', 'url' => url('/career')],
                    'activity' => ['name' => __('modules.activity.name'), 'icon' => 'fa-solid fa-person-running', 'url' => url('/activity')],
                    'media' => ['name' => __('modules.media.name'), 'icon' => 'fa-solid fa-photo-film', 'url' => url('/media')],
                    'commerce' => ['name' => __('modules.commerce.name'), 'icon' => 'fa-solid fa-cart-shopping', 'url' => url('/commerce')],
                    'setting' => ['name' => __('modules.setting.name'), 'icon' => 'fa-solid fa-gear', 'url' => url('/setting')],
                ];
            @endphp
            <div class="header-title-dropdown-container" style="position: relative;">
                <button type="button" id="headerTitleDropdownBtn" class="header-title-btn" style="background: rgba(255,255,255,0.03); border: 1px solid transparent; padding: 6px 12px; border-radius: 10px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; color: #fff; transition: var(--transition-smooth);" title="Klik untuk berganti modul">
                    <h1 class="header-title" style="margin: 0; font-size: 17px; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 8px; line-height: 1;">
                        <span>{{ $title ?? $pageTitle ?? __('common.framework_title') }}</span>
                        <i class="fa-solid fa-chevron-down title-dropdown-icon" style="font-size: 11px; color: var(--primary); transition: transform 0.2s ease;"></i>
                    </h1>
                </button>

                <div id="headerTitleDropdownMenu" class="header-module-menu" style="display: none; position: absolute; top: calc(100% + 8px); left: 0; width: 340px; background: rgba(15, 23, 42, 0.98); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid var(--border-color); border-radius: 12px; padding: 12px; box-shadow: 0 16px 36px rgba(0,0,0,0.6); z-index: 1100;">
                    <div style="font-size: 11px; font-weight: 700; color: #38bdf8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                        <span>PILIH MODUL HMVC</span>
                        <span style="color: var(--text-muted); font-size: 10px;">13 Modul</span>
                    </div>

                    <div style="max-height: 320px; overflow-y: auto; display: grid; grid-template-columns: repeat(2, 1fr); gap: 6px;">
                        @foreach($allModulesData as $mKey => $mVal)
                            @php $isCurrent = ($currentModSegment === $mKey); @endphp
                            <a href="{{ $mVal['url'] }}" style="padding: 8px 10px; border-radius: 8px; font-size: 12px; font-weight: 500; text-decoration: none; color: #fff; display: flex; align-items: center; gap: 8px; background: {{ $isCurrent ? 'var(--primary-light)' : 'rgba(255,255,255,0.03)' }}; border: 1px solid {{ $isCurrent ? 'var(--primary)' : 'var(--border-color)' }}; transition: var(--transition-smooth);">
                                <i class="{{ $mVal['icon'] }}" style="color: {{ $isCurrent ? 'var(--primary)' : '#38bdf8' }}; width: 14px; text-align: center;"></i>
                                <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $mVal['name'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="header-right">

            <!-- Compact Theme Selector Button -->
            @php
                $currentTheme = holiday_theme();
                $allThemes = \App\Services\Theme\HolidayThemeService::getAllThemePresets();
                $isManual = session()->has('holiday_theme') && session('holiday_theme') !== 'auto';
            @endphp
            <div class="theme-dropdown-container" style="position: relative;">
                <button type="button" id="themeDropdownBtn" class="sidebar-toggle-btn" style="border-radius: 8px; cursor: pointer; color: var(--primary);" title="Tema: {{ $currentTheme['name'] }}">
                    <i class="fa-solid fa-palette"></i>
                </button>

                <div id="themeDropdownMenu" class="theme-dropdown-menu" style="display: none; position: absolute; top: calc(100% + 8px); right: 0; width: 250px; background: rgba(15, 23, 42, 0.96); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid var(--border-color); border-radius: 12px; padding: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); z-index: 1050;">
                    <div style="font-size: 11px; font-weight: 700; color: #38bdf8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; padding-bottom: 6px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                        <span>PILIH TEMA</span>
                        <a href="{{ url('/theme/auto') }}" style="color: var(--text-muted); font-size: 10px; text-decoration: none;"><i class="fa-solid fa-rotate-right"></i> Auto</a>
                    </div>
                    
                    <div style="max-height: 240px; overflow-y: auto; display: flex; flex-direction: column; gap: 3px;">
                        <a href="{{ url('/theme/auto') }}" style="padding: 7px 10px; border-radius: 6px; font-size: 12px; text-decoration: none; color: #fff; display: flex; align-items: center; justify-content: space-between; background: {{ !$isManual ? 'var(--primary-light)' : 'transparent' }};">
                            <span><i class="fa-solid fa-wand-magic-sparkles" style="color: #38bdf8; margin-right: 6px;"></i> Otomatis (Kalender)</span>
                            @if(!$isManual) <i class="fa-solid fa-check" style="color: #34d399; font-size: 11px;"></i> @endif
                        </a>

                        @foreach($allThemes as $key => $preset)
                            @if($key !== 'default')
                                @php $isActiveThis = $isManual && session('holiday_theme') === $key; @endphp
                                <a href="{{ url('/theme/' . $key) }}" style="padding: 7px 10px; border-radius: 6px; font-size: 12px; text-decoration: none; color: #fff; display: flex; align-items: center; justify-content: space-between; background: {{ $isActiveThis ? 'rgba(255,255,255,0.08)' : 'transparent' }};">
                                    <span><i class="{{ $preset['icon'] }}" style="color: {{ $preset['primary_color'] }}; margin-right: 6px; width: 14px;"></i> {{ $preset['badge'] }}</span>
                                    @if($isActiveThis) <i class="fa-solid fa-check" style="color: #34d399; font-size: 11px;"></i> @endif
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Compact Language Switcher -->
            <div class="lang-switcher" style="display: flex; align-items: center; background: rgba(255,255,255,0.04); border: 1px solid var(--border-color); border-radius: 8px; padding: 2px; gap: 2px;">
                <a href="{{ url('/lang/id') }}" class="lang-btn {{ app()->getLocale() === 'id' ? 'active' : '' }}" style="padding: 4px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 700; text-decoration: none; color: {{ app()->getLocale() === 'id' ? '#fff' : 'var(--text-muted)' }}; background: {{ app()->getLocale() === 'id' ? 'var(--primary)' : 'transparent' }};" title="{{ __('common.indonesian') }}">ID</a>
                <a href="{{ url('/lang/en') }}" class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}" style="padding: 4px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 700; text-decoration: none; color: {{ app()->getLocale() === 'en' ? '#fff' : 'var(--text-muted)' }}; background: {{ app()->getLocale() === 'en' ? 'var(--primary)' : 'transparent' }};" title="{{ __('common.english') }}">EN</a>
            </div>
        </div>
    </header>

    <main class="app-content">
        {!! $content !!}
    </main>

@include('theme.footer')
