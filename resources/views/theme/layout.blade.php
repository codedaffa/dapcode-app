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
            <span class="page-badge"><i class="fa-solid fa-cube"></i> {{ __('common.hmvc_module') }}</span>
            <h1 class="header-title">{{ $title ?? $pageTitle ?? __('common.framework_title') }}</h1>
        </div>
        <div class="header-right">
            <!-- Language Switcher -->
            <div class="lang-switcher" style="display: flex; align-items: center; background: rgba(255,255,255,0.06); border: 1px solid var(--border-color); border-radius: 8px; padding: 2px; gap: 2px;">
                <a href="{{ url('/lang/id') }}" class="lang-btn {{ app()->getLocale() === 'id' ? 'active' : '' }}" style="padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 5px; color: {{ app()->getLocale() === 'id' ? '#fff' : 'var(--text-muted)' }}; background: {{ app()->getLocale() === 'id' ? 'var(--primary)' : 'transparent' }}; transition: var(--transition-smooth);" title="{{ __('common.indonesian') }}">
                    <span>🇮🇩</span> <span>ID</span>
                </a>
                <a href="{{ url('/lang/en') }}" class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}" style="padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 5px; color: {{ app()->getLocale() === 'en' ? '#fff' : 'var(--text-muted)' }}; background: {{ app()->getLocale() === 'en' ? 'var(--primary)' : 'transparent' }}; transition: var(--transition-smooth);" title="{{ __('common.english') }}">
                    <span>🇬🇧</span> <span>EN</span>
                </a>
            </div>

            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="{{ __('common.search_placeholder') }}">
            </div>
            <div class="user-profile">
                <div class="avatar">
                    <span>AD</span>
                </div>
                <div class="user-info">
                    <div class="user-name">{{ __('common.admin_user') }}</div>
                    <div class="user-role">{{ __('common.super_admin') }}</div>
                </div>
            </div>
        </div>
    </header>

    <main class="app-content">
        {!! $content !!}
    </main>

@include('theme.footer')
