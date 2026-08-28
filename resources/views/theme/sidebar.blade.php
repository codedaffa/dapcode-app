@php
    $currentSegment = strtolower(request()->segment(1) ?: 'dashboard');
    if (!in_array($currentSegment, ['dashboard', 'profile', 'education', 'certification', 'achievement', 'interest', 'project', 'research', 'career', 'activity', 'media', 'commerce', 'setting'])) {
        $currentSegment = 'dashboard';
    }

    $currentFullUrl = url()->current() . (request()->getQueryString() ? '?' . request()->getQueryString() : '');

    // Purely sub-menus belonging strictly to the active module
    $moduleSubMenus = [
        'dashboard' => [
            'icon' => 'fa-solid fa-gauge-high',
            'title' => __('modules.dashboard.name'),
            'label' => 'SUB MENU DASHBOARD',
            'items' => [
                ['url' => url('/dashboard'), 'icon' => 'fa-solid fa-chart-pie', 'label' => 'Ringkasan / Overview', 'active' => request()->is('dashboard') && !request()->getQueryString()],
                ['url' => url('/dashboard?tab=analytics'), 'icon' => 'fa-solid fa-chart-line', 'label' => 'Statistik & Analitik', 'active' => request()->fullUrlIs(url('/dashboard?tab=analytics'))],
                ['url' => url('/dashboard?tab=activity'), 'icon' => 'fa-solid fa-clock-rotate-left', 'label' => 'Aktivitas Terbaru', 'active' => request()->fullUrlIs(url('/dashboard?tab=activity'))],
            ]
        ],
        'profile' => [
            'icon' => 'fa-solid fa-id-card',
            'title' => __('modules.profile.name'),
            'label' => 'SUB MENU PROFIL',
            'items' => [
                ['url' => url('/profile'), 'icon' => 'fa-solid fa-user-pen', 'label' => 'Biodata & Info Pribadi', 'active' => request()->is('profile') && !request()->getQueryString()],
                ['url' => url('/profile?tab=skills'), 'icon' => 'fa-solid fa-code', 'label' => 'Keahlian & Kemampuan', 'active' => request()->fullUrlIs(url('/profile?tab=skills'))],
                ['url' => url('/profile?tab=contact'), 'icon' => 'fa-solid fa-address-book', 'label' => 'Kontak & Media Sosial', 'active' => request()->fullUrlIs(url('/profile?tab=contact'))],
            ]
        ],
        'education' => [
            'icon' => 'fa-solid fa-graduation-cap',
            'title' => __('modules.education.name'),
            'label' => 'SUB MENU PENDIDIKAN',
            'items' => [
                ['url' => url('/education'), 'icon' => 'fa-solid fa-school', 'label' => 'Pendidikan Formal', 'active' => request()->is('education') && !request()->getQueryString()],
                ['url' => url('/education?tab=courses'), 'icon' => 'fa-solid fa-laptop-file', 'label' => 'Pelatihan & Kursus', 'active' => request()->fullUrlIs(url('/education?tab=courses'))],
                ['url' => url('/education?tab=honors'), 'icon' => 'fa-solid fa-award', 'label' => 'Penghargaan Akademik', 'active' => request()->fullUrlIs(url('/education?tab=honors'))],
            ]
        ],
        'certification' => [
            'icon' => 'fa-solid fa-certificate',
            'title' => __('modules.certification.name'),
            'label' => 'SUB MENU SERTIFIKASI',
            'items' => [
                ['url' => url('/certification'), 'icon' => 'fa-solid fa-stamp', 'label' => 'Daftar Sertifikat', 'active' => request()->is('certification') && !request()->getQueryString()],
                ['url' => url('/certification?tab=licenses'), 'icon' => 'fa-solid fa-id-badge', 'label' => 'Lisensi Profesional', 'active' => request()->fullUrlIs(url('/certification?tab=licenses'))],
                ['url' => url('/certification?tab=verify'), 'icon' => 'fa-solid fa-circle-check', 'label' => 'Verifikasi Sertifikat', 'active' => request()->fullUrlIs(url('/certification?tab=verify'))],
            ]
        ],
        'achievement' => [
            'icon' => 'fa-solid fa-trophy',
            'title' => __('modules.achievement.name'),
            'label' => 'SUB MENU PRESTASI',
            'items' => [
                ['url' => url('/achievement'), 'icon' => 'fa-solid fa-trophy', 'label' => 'Semua Prestasi', 'active' => request()->is('achievement') && !request()->getQueryString()],
                ['url' => url('/achievement?tab=competitions'), 'icon' => 'fa-solid fa-medal', 'label' => 'Juara Kompetisi', 'active' => request()->fullUrlIs(url('/achievement?tab=competitions'))],
                ['url' => url('/achievement?tab=awards'), 'icon' => 'fa-solid fa-crown', 'label' => 'Penghargaan Kehormatan', 'active' => request()->fullUrlIs(url('/achievement?tab=awards'))],
            ]
        ],
        'interest' => [
            'icon' => 'fa-solid fa-heart',
            'title' => __('modules.interest.name'),
            'label' => 'SUB MENU MINAT',
            'items' => [
                ['url' => url('/interest'), 'icon' => 'fa-solid fa-lightbulb', 'label' => 'Bidang Minat & Riset', 'active' => request()->is('interest') && !request()->getQueryString()],
                ['url' => url('/interest?tab=hobbies'), 'icon' => 'fa-solid fa-gamepad', 'label' => 'Hobi & Passion', 'active' => request()->fullUrlIs(url('/interest?tab=hobbies'))],
                ['url' => url('/interest?tab=tech'), 'icon' => 'fa-solid fa-microchip', 'label' => 'Teknologi Favorit', 'active' => request()->fullUrlIs(url('/interest?tab=tech'))],
            ]
        ],
        'project' => [
            'icon' => 'fa-solid fa-diagram-project',
            'title' => __('modules.project.name'),
            'label' => 'SUB MENU PROYEK',
            'items' => [
                ['url' => url('/project'), 'icon' => 'fa-solid fa-folder-open', 'label' => 'Katalog Semua Proyek', 'active' => request()->is('project') && !request()->getQueryString()],
                ['url' => url('/project?filter=web'), 'icon' => 'fa-solid fa-globe', 'label' => 'Aplikasi Web & Fullstack', 'active' => request()->fullUrlIs(url('/project?filter=web'))],
                ['url' => url('/project?filter=mobile'), 'icon' => 'fa-solid fa-mobile-screen', 'label' => 'Aplikasi Mobile', 'active' => request()->fullUrlIs(url('/project?filter=mobile'))],
                ['url' => url('/project?filter=oss'), 'icon' => 'fa-brands fa-github', 'label' => 'Open Source & Package', 'active' => request()->fullUrlIs(url('/project?filter=oss'))],
            ]
        ],
        'research' => [
            'icon' => 'fa-solid fa-flask-vial',
            'title' => __('modules.research.name'),
            'label' => 'SUB MENU RISET',
            'items' => [
                ['url' => url('/research'), 'icon' => 'fa-solid fa-book-bookmark', 'label' => 'Publikasi & Jurnal', 'active' => request()->is('research') && !request()->getQueryString()],
                ['url' => url('/research?tab=papers'), 'icon' => 'fa-solid fa-file-lines', 'label' => 'Karya Tulis Ilmiah', 'active' => request()->fullUrlIs(url('/research?tab=papers'))],
                ['url' => url('/research?tab=experiments'), 'icon' => 'fa-solid fa-vials', 'label' => 'Eksperimen & Lab', 'active' => request()->fullUrlIs(url('/research?tab=experiments'))],
            ]
        ],
        'career' => [
            'icon' => 'fa-solid fa-briefcase',
            'title' => __('modules.career.name'),
            'label' => 'SUB MENU KARIER',
            'items' => [
                ['url' => url('/career'), 'icon' => 'fa-solid fa-building', 'label' => 'Pengalaman Kerja', 'active' => request()->is('career') && !request()->getQueryString()],
                ['url' => url('/career?type=freelance'), 'icon' => 'fa-solid fa-laptop-code', 'label' => 'Proyek Freelance & Kontrak', 'active' => request()->fullUrlIs(url('/career?type=freelance'))],
                ['url' => url('/career?tab=milestones'), 'icon' => 'fa-solid fa-flag-checkered', 'label' => 'Pencapaian Karier', 'active' => request()->fullUrlIs(url('/career?tab=milestones'))],
            ]
        ],
        'activity' => [
            'icon' => 'fa-solid fa-person-running',
            'title' => __('modules.activity.name'),
            'label' => 'SUB MENU AKTIVITAS',
            'items' => [
                ['url' => url('/activity'), 'icon' => 'fa-solid fa-users', 'label' => 'Kegiatan Organisasi', 'active' => request()->is('activity') && !request()->getQueryString()],
                ['url' => url('/activity?tab=community'), 'icon' => 'fa-solid fa-hand-holding-heart', 'label' => 'Komunitas & Volunteering', 'active' => request()->fullUrlIs(url('/activity?tab=community'))],
                ['url' => url('/activity?tab=events'), 'icon' => 'fa-solid fa-calendar-check', 'label' => 'Seminar & Workshop', 'active' => request()->fullUrlIs(url('/activity?tab=events'))],
            ]
        ],
        'media' => [
            'icon' => 'fa-solid fa-photo-film',
            'title' => __('modules.media.name'),
            'label' => 'SUB MENU MEDIA',
            'items' => [
                ['url' => url('/media'), 'icon' => 'fa-solid fa-images', 'label' => 'Galeri Foto & Gambar', 'active' => request()->is('media') && !request()->getQueryString()],
                ['url' => url('/media?tab=videos'), 'icon' => 'fa-solid fa-video', 'label' => 'Video & Animasi', 'active' => request()->fullUrlIs(url('/media?tab=videos'))],
                ['url' => url('/media?tab=documents'), 'icon' => 'fa-solid fa-file-pdf', 'label' => 'Dokumen & Berkas', 'active' => request()->fullUrlIs(url('/media?tab=documents'))],
            ]
        ],
        'commerce' => [
            'icon' => 'fa-solid fa-cart-shopping',
            'title' => __('modules.commerce.name'),
            'label' => 'SUB MENU COMMERCE',
            'items' => [
                ['url' => url('/commerce'), 'icon' => 'fa-solid fa-tags', 'label' => 'Katalog Produk & Aplikasi', 'active' => request()->is('commerce') && !request()->getQueryString()],
                ['url' => url('/commerce?tab=services'), 'icon' => 'fa-solid fa-headset', 'label' => 'Layanan & Jasa Konsultasi', 'active' => request()->fullUrlIs(url('/commerce?tab=services'))],
                ['url' => url('/commerce?tab=orders'), 'icon' => 'fa-solid fa-receipt', 'label' => 'Daftar Pesanan & Order', 'active' => request()->fullUrlIs(url('/commerce?tab=orders'))],
            ]
        ],
        'setting' => [
            'icon' => 'fa-solid fa-gear',
            'title' => __('modules.setting.name'),
            'label' => 'SUB MENU PENGATURAN',
            'items' => [
                ['url' => url('/setting'), 'icon' => 'fa-solid fa-sliders', 'label' => 'Konfigurasi Sistem', 'active' => request()->is('setting') && !request()->has('tab')],
                ['url' => url('/setting?tab=language'), 'icon' => 'fa-solid fa-language', 'label' => 'Preferensi Bahasa', 'active' => request()->query('tab') === 'language'],
                ['url' => url('/setting?tab=themes'), 'icon' => 'fa-solid fa-palette', 'label' => 'Tema Perayaan', 'active' => request()->query('tab') === 'themes' || request()->has('theme')],
            ]
        ],
    ];

    $activeMenuConfig = $moduleSubMenus[$currentSegment] ?? $moduleSubMenus['dashboard'];
@endphp

<aside class="app-sidebar">
    <!-- Brand Header -->
    <div class="sidebar-brand">
        <a href="{{ url('/') }}" class="sidebar-brand-link" title="DapCode Portfolio">
            <div class="brand-logo">
                <i class="fa-solid fa-cube"></i>
            </div>
            <div class="brand-text">
                <div class="brand-name">Dap<span>Code</span></div>
                <span class="brand-sub">HMVC Ecosystem</span>
            </div>
        </a>
        <button type="button" id="sidebarClose" class="sidebar-close-btn" aria-label="Close Sidebar">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- Active Module Context Header Card -->
    <div class="sidebar-active-module" style="padding: 12px 16px; margin: 8px 12px 14px; background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.25); border-radius: 10px; display: flex; align-items: center; gap: 10px;">
        <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
            <i class="{{ $activeMenuConfig['icon'] }}"></i>
        </div>
        <div class="active-module-text" style="min-width: 0; flex: 1;">
            <div style="font-size: 10px; font-weight: 700; color: #38bdf8; text-transform: uppercase; letter-spacing: 0.05em;">MODUL AKTIF</div>
            <div style="font-size: 13.5px; font-weight: 700; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $activeMenuConfig['title'] }}</div>
        </div>
    </div>

    <!-- Sidebar Sub Menus (Strictly Active Module Only) -->
    <div class="sidebar-menu">
        <!-- Quick Link Back to Portfolio Landing Page -->
        <a href="{{ url('/') }}" class="menu-item" style="margin-bottom: 12px; border: 1px dashed var(--border-color); background: rgba(255, 255, 255, 0.02);" title="{{ __('common.portfolio_home') }}">
            <i class="fa-solid fa-house" style="color: #38bdf8;"></i>
            <span>{{ __('common.portfolio_home') }}</span>
        </a>

        <div class="menu-label">{{ $activeMenuConfig['label'] }}</div>
        
        @foreach($activeMenuConfig['items'] as $item)
            <a href="{{ $item['url'] }}" class="menu-item {{ $item['active'] ? 'active' : '' }}" title="{{ $item['label'] }}">
                <i class="{{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
                @if($loop->first && $currentSegment === 'dashboard')
                    <span class="menu-badge">{{ __('common.live_badge') }}</span>
                @endif
            </a>
        @endforeach
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="system-status">
            <span class="pulse-dot"></span>
            <span>{{ __('common.system_status') }}</span>
        </div>
    </div>
</aside>
