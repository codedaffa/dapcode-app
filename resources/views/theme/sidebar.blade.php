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
        <div class="menu-label">MODUL UTAMA</div>
        <a href="{{ url('/home') }}" class="menu-item {{ request()->is('home*') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
        </a>
        <a href="{{ url('/dashboard') }}" class="menu-item {{ request()->is('dashboard*') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high"></i>
            <span>Dashboard</span>
            <span class="menu-badge">Live</span>
        </a>
        <a href="{{ url('/user') }}" class="menu-item {{ request()->is('user*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i>
            <span>User Management</span>
        </a>
        <a href="{{ url('/product') }}" class="menu-item {{ request()->is('product*') ? 'active' : '' }}">
            <i class="fa-solid fa-box-open"></i>
            <span>Product</span>
        </a>

        <div class="menu-label">FITUR HMVC</div>
        <a href="{{ url('/dashboard/detail/100') }}" class="menu-item">
            <i class="fa-solid fa-bolt"></i>
            <span>Parameter Route</span>
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="system-status">
            <span class="pulse-dot"></span>
            <span>HMVC Engine Active</span>
        </div>
    </div>
</aside>
