<aside class="app-sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo">
            <i class="fa-solid fa-layer-group"></i>
        </div>
        <div class="brand-text">
            <h2>DapCode</h2>
            <span>HMVC Framework</span>
        </div>
        <button type="button" id="sidebarClose" class="sidebar-close-btn" aria-label="Close Sidebar">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <div class="sidebar-menu">
        <div class="menu-label">{{ __('common.main_modules') }}</div>
        <a href="{{ url('/dashboard') }}" class="menu-item {{ request()->is('dashboard*') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high"></i>
            <span>{{ __('modules.dashboard.name') }}</span>
            <span class="menu-badge">{{ __('common.live_badge') }}</span>
        </a>
        <a href="{{ url('/profile') }}" class="menu-item {{ request()->is('profile*') ? 'active' : '' }}">
            <i class="fa-solid fa-id-card"></i>
            <span>{{ __('modules.profile.name') }}</span>
        </a>
        <a href="{{ url('/education') }}" class="menu-item {{ request()->is('education*') ? 'active' : '' }}">
            <i class="fa-solid fa-graduation-cap"></i>
            <span>{{ __('modules.education.name') }}</span>
        </a>
        <a href="{{ url('/certification') }}" class="menu-item {{ request()->is('certification*') ? 'active' : '' }}">
            <i class="fa-solid fa-certificate"></i>
            <span>{{ __('modules.certification.name') }}</span>
        </a>
        <a href="{{ url('/achievement') }}" class="menu-item {{ request()->is('achievement*') ? 'active' : '' }}">
            <i class="fa-solid fa-trophy"></i>
            <span>{{ __('modules.achievement.name') }}</span>
        </a>
        <a href="{{ url('/interest') }}" class="menu-item {{ request()->is('interest*') ? 'active' : '' }}">
            <i class="fa-solid fa-heart"></i>
            <span>{{ __('modules.interest.name') }}</span>
        </a>
        <a href="{{ url('/project') }}" class="menu-item {{ request()->is('project*') ? 'active' : '' }}">
            <i class="fa-solid fa-diagram-project"></i>
            <span>{{ __('modules.project.name') }}</span>
        </a>
        <a href="{{ url('/research') }}" class="menu-item {{ request()->is('research*') ? 'active' : '' }}">
            <i class="fa-solid fa-flask-vial"></i>
            <span>{{ __('modules.research.name') }}</span>
        </a>
        <a href="{{ url('/career') }}" class="menu-item {{ request()->is('career*') ? 'active' : '' }}">
            <i class="fa-solid fa-briefcase"></i>
            <span>{{ __('modules.career.name') }}</span>
        </a>
        <a href="{{ url('/activity') }}" class="menu-item {{ request()->is('activity*') ? 'active' : '' }}">
            <i class="fa-solid fa-person-running"></i>
            <span>{{ __('modules.activity.name') }}</span>
        </a>
        <a href="{{ url('/media') }}" class="menu-item {{ request()->is('media*') ? 'active' : '' }}">
            <i class="fa-solid fa-photo-film"></i>
            <span>{{ __('modules.media.name') }}</span>
        </a>
        <a href="{{ url('/setting') }}" class="menu-item {{ request()->is('setting*') ? 'active' : '' }}">
            <i class="fa-solid fa-gear"></i>
            <span>{{ __('modules.setting.name') }}</span>
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="system-status">
            <span class="pulse-dot"></span>
            <span>{{ __('common.system_status') }}</span>
        </div>
    </div>
</aside>
