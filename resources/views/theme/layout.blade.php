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
            <span class="page-badge"><i class="fa-solid fa-cube"></i> HMVC Module</span>
            <h1 class="header-title">{{ $title ?? $pageTitle ?? 'DapCode System' }}</h1>
        </div>
        <div class="header-right">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Cari modul / data...">
            </div>
            <div class="user-profile">
                <div class="avatar">
                    <span>AD</span>
                </div>
                <div class="user-info">
                    <div class="user-name">Admin User</div>
                    <div class="user-role">Super Admin</div>
                </div>
            </div>
        </div>
    </header>

    <main class="app-content">
        {!! $content !!}
    </main>

@include('theme.footer')
