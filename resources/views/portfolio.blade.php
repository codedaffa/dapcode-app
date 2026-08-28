<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $pageTitle ?? 'DapCode - Modern Portfolio' }}</title>
    
    <!-- Google Fonts for Dynamic Holiday Themes & Modern UI -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cinzel:wght@500;700;900&family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,800;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Automatic Indonesian Holiday Themes -->
    <link rel="stylesheet" href="{{ asset('assets/css/holiday-themes.css') }}">
    
    <style>
        /* ==========================================================================
           PORTFOLIO STANDALONE STYLESHEET - MODERN, CLEAN & PROFESSIONAL
           ========================================================================== */
        :root {
            --bg-dark: #090d16;
            --bg-card: rgba(18, 26, 43, 0.7);
            --bg-card-hover: rgba(28, 39, 64, 0.85);
            --border-glass: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(99, 102, 241, 0.5);
            --primary: #6366f1;
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #38bdf8 100%);
            --accent-cyan: #38bdf8;
            --accent-emerald: #10b981;
            --accent-amber: #f59e0b;
            --accent-purple: #a855f7;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-sub: #64748b;
            --transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            --container-max: 1240px;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 16px;
        }

        body {
            min-height: 100vh;
            overflow-x: hidden;
            background-color: var(--bg-dark);
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.18) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(56, 189, 248, 0.15) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(168, 85, 247, 0.12) 0px, transparent 50%);
            background-attachment: fixed;
            line-height: 1.6;
        }

        .container {
            width: 100%;
            max-width: var(--container-max);
            margin: 0 auto;
            padding: 0 24px;
        }

        /* --------------------------------------------------------------------------
           1. Modern Floating Glass Header
           -------------------------------------------------------------------------- */
        .portfolio-nav {
            position: fixed;
            top: 16px;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 0 24px;
        }

        .nav-inner {
            max-width: var(--container-max);
            margin: 0 auto;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-glass);
            border-radius: 9999px;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .portfolio-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #fff;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #fff;
            box-shadow: 0 0 16px rgba(99, 102, 241, 0.5);
        }

        .brand-name {
            font-size: 17px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #fff;
        }

        .brand-name span {
            color: var(--accent-cyan);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
            list-style: none;
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-links a:hover {
            color: #fff;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Language Switcher */
        .lang-toggle {
            display: flex;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-glass);
            border-radius: 9999px;
            padding: 3px;
            gap: 2px;
        }

        .lang-btn {
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            color: var(--text-muted);
            transition: var(--transition);
        }

        .lang-btn.active {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.4);
        }

        .btn-launch {
            background: var(--primary-gradient);
            color: #fff;
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 9999px;
            font-size: 13.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        }

        .btn-launch:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
        }

        .mobile-menu-btn {
            display: none;
            background: transparent;
            border: none;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
        }

        /* --------------------------------------------------------------------------
           2. Hero Section
           -------------------------------------------------------------------------- */
        .hero-section {
            padding-top: 150px;
            padding-bottom: 70px;
            position: relative;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 48px;
            align-items: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(99, 102, 241, 0.12);
            border: 1px solid rgba(99, 102, 241, 0.3);
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
            color: var(--accent-cyan);
            margin-bottom: 20px;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: var(--accent-emerald);
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 rgba(16, 185, 129, 0.4);
            animation: pulse-ring 1.8s infinite;
        }

        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .hero-title {
            font-size: clamp(32px, 4.5vw, 52px);
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            letter-spacing: -0.03em;
            margin-bottom: 20px;
        }

        .hero-title .highlight {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: clamp(15px, 1.6vw, 17px);
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 32px;
            max-width: 600px;
        }

        .hero-buttons {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: #fff;
            text-decoration: none;
            padding: 13px 26px;
            border-radius: 12px;
            font-size: 14.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(99, 102, 241, 0.5);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-glass);
            color: #fff;
            text-decoration: none;
            padding: 13px 24px;
            border-radius: 12px;
            font-size: 14.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        /* Hero Right Profile Card */
        .hero-card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-glass);
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            position: relative;
        }

        .hero-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: var(--primary-gradient);
            border-radius: 26px;
            z-index: -1;
            opacity: 0.3;
            filter: blur(12px);
        }

        .avatar-box {
            width: 90px;
            height: 90px;
            border-radius: 20px;
            background: linear-gradient(135deg, #1e1b4b, #312e81);
            border: 2px solid rgba(99, 102, 241, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            color: var(--accent-cyan);
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.3);
        }

        .profile-name {
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 4px;
        }

        .profile-role {
            font-size: 14px;
            color: var(--accent-cyan);
            font-weight: 600;
            margin-bottom: 16px;
        }

        .profile-tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .tag-pill {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-glass);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .social-links {
            display: flex;
            gap: 12px;
            padding-top: 18px;
            border-top: 1px solid var(--border-glass);
        }

        .social-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-glass);
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 16px;
            transition: var(--transition);
        }

        .social-btn:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        /* --------------------------------------------------------------------------
           3. Core Metrics Section
           -------------------------------------------------------------------------- */
        .metrics-section {
            padding: 30px 0 60px;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .metric-card {
            background: var(--bg-card);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-glass);
            border-radius: 16px;
            padding: 24px;
            transition: var(--transition);
        }

        .metric-card:hover {
            transform: translateY(-4px);
            border-color: var(--border-hover);
            background: var(--bg-card-hover);
        }

        .metric-num {
            font-size: 32px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 4px;
        }

        .metric-label {
            font-size: 13.5px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* --------------------------------------------------------------------------
           4. HMVC Modules Showcase Section
           -------------------------------------------------------------------------- */
        .section-header {
            text-align: center;
            max-width: 680px;
            margin: 0 auto 48px;
        }

        .section-subtitle {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--accent-cyan);
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 10px;
        }

        .section-title {
            font-size: clamp(26px, 3.5vw, 36px);
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.02em;
            margin-bottom: 12px;
        }

        .section-desc {
            color: var(--text-muted);
            font-size: 15px;
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .module-card {
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-decoration: none;
            color: inherit;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .module-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--primary-gradient);
            opacity: 0;
            transition: var(--transition);
        }

        .module-card:hover {
            transform: translateY(-6px);
            border-color: rgba(99, 102, 241, 0.4);
            background: var(--bg-card-hover);
            box-shadow: 0 16px 36px -10px rgba(0, 0, 0, 0.5), 0 0 24px rgba(99, 102, 241, 0.15);
        }

        .module-card:hover::after {
            opacity: 1;
        }

        .module-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .module-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .module-name {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 8px;
            transition: var(--transition);
        }

        .module-card:hover .module-name {
            color: var(--accent-cyan);
        }

        .module-desc {
            font-size: 13.5px;
            color: var(--text-muted);
            line-height: 1.55;
            margin-bottom: 20px;
        }

        .module-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 14px;
            border-top: 1px solid var(--border-glass);
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-sub);
        }

        .module-card:hover .module-footer {
            color: var(--accent-cyan);
        }

        .badge-live {
            background: rgba(16, 185, 129, 0.15);
            color: var(--accent-emerald);
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 9999px;
        }

        /* --------------------------------------------------------------------------
           5. Highlights & Tech Stack Section
           -------------------------------------------------------------------------- */
        .tech-section {
            padding: 80px 0 60px;
        }

        .tech-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 16px;
        }

        .tech-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 14px;
            padding: 16px;
            text-align: center;
            transition: var(--transition);
        }

        .tech-card:hover {
            transform: translateY(-3px);
            border-color: rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.05);
        }

        .tech-card i {
            font-size: 28px;
            margin-bottom: 8px;
            display: block;
        }

        .tech-card span {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
        }

        /* --------------------------------------------------------------------------
           6. CTA & Contact Card
           -------------------------------------------------------------------------- */
        .cta-section {
            padding: 40px 0 90px;
        }

        .cta-card {
            background: radial-gradient(circle at center, rgba(99, 102, 241, 0.25), transparent 70%), var(--bg-card);
            border: 1px solid rgba(99, 102, 241, 0.35);
            border-radius: 28px;
            padding: 48px 32px;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .cta-title {
            font-size: clamp(24px, 3.5vw, 36px);
            font-weight: 800;
            color: #fff;
            margin-bottom: 12px;
        }

        .cta-desc {
            color: var(--text-muted);
            font-size: 15px;
            max-width: 580px;
            margin: 0 auto 28px;
        }

        /* --------------------------------------------------------------------------
           7. Standalone Portfolio Footer
           -------------------------------------------------------------------------- */
        .portfolio-footer {
            border-top: 1px solid var(--border-glass);
            padding: 32px 0;
            background: rgba(9, 13, 22, 0.95);
        }

        .footer-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .footer-copy {
            font-size: 13.5px;
            color: var(--text-muted);
        }

        .footer-copy strong {
            color: #fff;
        }

        /* --------------------------------------------------------------------------
           8. Responsive Media Queries
           -------------------------------------------------------------------------- */
        @media (max-width: 992px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .nav-links {
                display: none;
            }
            .mobile-menu-btn {
                display: block;
            }
        }

        @media (max-width: 640px) {
            .metrics-grid {
                grid-template-columns: 1fr;
            }
            .modules-grid {
                grid-template-columns: 1fr;
            }
            .nav-inner {
                padding: 8px 16px;
            }
            .hero-section {
                padding-top: 120px;
            }
            .cta-card {
                padding: 32px 20px;
            }
            .footer-inner {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body class="{{ holiday_theme()['css_class'] }}">

    <!-- 1. Floating Glass Navigation -->
    <header class="portfolio-nav">
        <div class="nav-inner">
            <a href="{{ url('/') }}" class="portfolio-brand">
                <div class="brand-icon">
                    <i class="fa-solid fa-cube"></i>
                </div>
                <div class="brand-name">Dap<span>Code</span></div>
            </a>

            <ul class="nav-links">
                <li><a href="#about"><i class="fa-solid fa-user"></i> {{ __('modules.profile.name') }}</a></li>
                <li><a href="#modules"><i class="fa-solid fa-cubes"></i> {{ __('common.main_modules') }}</a></li>
                <li><a href="{{ url('/project') }}"><i class="fa-solid fa-diagram-project"></i> {{ __('modules.project.name') }}</a></li>
                <li><a href="{{ url('/career') }}"><i class="fa-solid fa-briefcase"></i> {{ __('modules.career.name') }}</a></li>
                <li><a href="#contact"><i class="fa-solid fa-paper-plane"></i> Contact</a></li>
            </ul>

            <div class="nav-actions">
                <!-- Theme Picker Dropdown -->
                @php
                    $currentTheme = holiday_theme();
                    $allThemes = \App\Services\Theme\HolidayThemeService::getAllThemePresets();
                    $isManual = session()->has('holiday_theme') && session('holiday_theme') !== 'auto';
                @endphp
                <div class="theme-dropdown-container" style="position: relative;">
                    <button type="button" id="portfolioThemeBtn" style="width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.06); border: 1px solid var(--border-glass); color: var(--accent-cyan); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; transition: var(--transition);" title="Tema: {{ $currentTheme['name'] }}">
                        <i class="fa-solid fa-palette"></i>
                    </button>

                    <div id="portfolioThemeMenu" style="display: none; position: absolute; top: calc(100% + 10px); right: 0; width: 280px; background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid var(--border-glass); border-radius: 16px; padding: 14px; box-shadow: 0 16px 36px rgba(0,0,0,0.6); z-index: 1050;">
                        <div style="font-size: 11px; font-weight: 700; color: var(--accent-cyan); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px solid var(--border-glass); display: flex; justify-content: space-between; align-items: center;">
                            <span>PILIH TEMA PERAYAAN</span>
                            <a href="{{ url('/theme/auto') }}" style="color: var(--text-muted); font-size: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;"><i class="fa-solid fa-rotate-right"></i> Reset Auto</a>
                        </div>
                        
                        <div style="max-height: 260px; overflow-y: auto; display: flex; flex-direction: column; gap: 4px;">
                            <a href="{{ url('/theme/auto') }}" style="padding: 8px 10px; border-radius: 8px; font-size: 12px; text-decoration: none; color: #fff; display: flex; align-items: center; justify-content: space-between; background: {{ !$isManual ? 'rgba(99, 102, 241, 0.2)' : 'transparent' }}; border: 1px solid {{ !$isManual ? 'var(--primary)' : 'transparent' }};">
                                <span style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fa-solid fa-wand-magic-sparkles" style="color: #38bdf8;"></i>
                                    <span>Otomatis (Kalender)</span>
                                </span>
                                @if(!$isManual) <i class="fa-solid fa-check" style="color: #34d399;"></i> @endif
                            </a>

                            @foreach($allThemes as $key => $preset)
                                @if($key !== 'default')
                                    @php $isActiveThis = $isManual && session('holiday_theme') === $key; @endphp
                                    <a href="{{ url('/theme/' . $key) }}" style="padding: 8px 10px; border-radius: 8px; font-size: 12px; text-decoration: none; color: #fff; display: flex; align-items: center; justify-content: space-between; background: {{ $isActiveThis ? 'rgba(255,255,255,0.08)' : 'transparent' }}; border: 1px solid {{ $isActiveThis ? $preset['primary_color'] : 'transparent' }};">
                                        <span style="display: flex; align-items: center; gap: 8px;">
                                            <i class="{{ $preset['icon'] }}" style="color: {{ $preset['primary_color'] }}; width: 14px;"></i>
                                            <span>{{ $preset['badge'] }}</span>
                                        </span>
                                        @if($isActiveThis) <i class="fa-solid fa-check" style="color: #34d399;"></i> @endif
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Language Switcher Toggle -->
                <div class="lang-toggle">
                    <a href="{{ url('/lang/id') }}" class="lang-btn {{ app()->getLocale() === 'id' ? 'active' : '' }}" title="Bahasa Indonesia">ID</a>
                    <a href="{{ url('/lang/en') }}" class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}" title="English">EN</a>
                </div>

                <a href="{{ url('/dashboard') }}" class="btn-launch">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
            </div>
        </div>
    </header>

    <!-- 2. Hero Section -->
    <section id="about" class="hero-section">
        <div class="container">
            @php $activeHoliday = holiday_theme(); @endphp
            <div class="hero-grid">
                <div>
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 20px;">
                        <div class="hero-badge" style="margin-bottom: 0;">
                            <span class="pulse-dot"></span>
                            {{ __('common.hero_badge') }}
                        </div>
                        @if($activeHoliday['id'] !== 'default')
                            <div class="holiday-celebration-pill" style="font-size: 13px; padding: 6px 14px;">
                                <i class="{{ $activeHoliday['icon'] }}"></i>
                                <span>{{ app()->getLocale() === 'id' ? $activeHoliday['greeting'] : $activeHoliday['greeting_en'] }}</span>
                            </div>
                        @endif
                    </div>

                    <h1 class="hero-title">
                        {{ __('common.hero_title') }}
                    </h1>

                    <p class="hero-desc">
                        {{ __('common.hero_subtitle') }}
                    </p>

                    <div class="hero-buttons">
                        <a href="#modules" class="btn-primary">
                            <i class="fa-solid fa-layer-group"></i> {{ __('common.explore_modules') }}
                        </a>
                        <a href="{{ url('/profile') }}" class="btn-secondary">
                            <i class="fa-solid fa-id-badge"></i> {{ __('common.view_cv') }}
                        </a>
                    </div>
                </div>

                <div>
                    <div class="hero-card">
                        <div class="avatar-box">
                            <i class="fa-solid fa-code"></i>
                        </div>
                        <div class="profile-name">DapCode Studio</div>
                        <div class="profile-role">Software Architect &bull; Full-Stack Specialist</div>
                        
                        <div class="profile-tags">
                            <span class="tag-pill"><i class="fa-solid fa-shield-halved" style="color: #10b981;"></i> HMVC Core</span>
                            <span class="tag-pill"><i class="fa-solid fa-language" style="color: #38bdf8;"></i> Bilingual (ID/EN)</span>
                            <span class="tag-pill"><i class="fa-solid fa-bolt" style="color: #f59e0b;"></i> High Performance</span>
                        </div>

                        <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.6; margin-bottom: 20px;">
                            Specializing in Laravel, HMVC architecture, high-throughput microservices, and clean responsive user interfaces.
                        </p>

                        <div class="social-links">
                            <a href="https://github.com" target="_blank" class="social-btn" aria-label="GitHub"><i class="fa-brands fa-github"></i></a>
                            <a href="https://linkedin.com" target="_blank" class="social-btn" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                            <a href="mailto:admin@dapcode.test" class="social-btn" aria-label="Email"><i class="fa-solid fa-envelope"></i></a>
                            <a href="{{ url('/media') }}" class="social-btn" aria-label="Media"><i class="fa-solid fa-photo-film"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Key Metrics Bar -->
    <section class="metrics-section">
        <div class="container">
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-num" style="color: #38bdf8;">13</div>
                    <div class="metric-label">{{ __('common.stat_modules_count') }}</div>
                </div>
                <div class="metric-card">
                    <div class="metric-num" style="color: #10b981;">100%</div>
                    <div class="metric-label">{{ __('common.stat_system_mode') }}</div>
                </div>
                <div class="metric-card">
                    <div class="metric-num" style="color: #818cf8;">2 Lang</div>
                    <div class="metric-label">Bahasa Indonesia &amp; English</div>
                </div>
                <div class="metric-card">
                    <div class="metric-num" style="color: #f59e0b;">&lt; 15ms</div>
                    <div class="metric-label">Ultra-Fast Route Dispatching</div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Interactive 13 HMVC Modules Showcase -->
    <section id="modules" style="padding: 40px 0 80px;">
        <div class="container">
            <div class="section-header">
                <div class="section-subtitle"><i class="fa-solid fa-cubes"></i> Architecture Directory</div>
                <h2 class="section-title">{{ __('common.all_modules_title') }}</h2>
                <p class="section-desc">{{ __('common.all_modules_desc') }}</p>
            </div>

            <div class="modules-grid">
                <!-- 1. Dashboard -->
                <a href="{{ url('/dashboard') }}" class="module-card">
                    <div>
                        <div class="module-top">
                            <div class="module-icon" style="background: rgba(99, 102, 241, 0.15); color: #818cf8;">
                                <i class="fa-solid fa-gauge-high"></i>
                            </div>
                            <span class="badge-live">{{ __('common.live_badge') }}</span>
                        </div>
                        <h3 class="module-name">{{ __('modules.dashboard.name') }}</h3>
                        <p class="module-desc">{{ __('modules.dashboard.subtitle') }}</p>
                    </div>
                    <div class="module-footer">
                        <span>/dashboard</span>
                        <span><i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>

                <!-- 2. Profile -->
                <a href="{{ url('/profile') }}" class="module-card">
                    <div>
                        <div class="module-top">
                            <div class="module-icon" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                                <i class="fa-solid fa-id-card"></i>
                            </div>
                            <span class="tag-pill">Personal</span>
                        </div>
                        <h3 class="module-name">{{ __('modules.profile.name') }}</h3>
                        <p class="module-desc">{{ __('modules.profile.subtitle') }}</p>
                    </div>
                    <div class="module-footer">
                        <span>/profile</span>
                        <span><i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>

                <!-- 3. Education -->
                <a href="{{ url('/education') }}" class="module-card">
                    <div>
                        <div class="module-top">
                            <div class="module-icon" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                            <span class="tag-pill">Academic</span>
                        </div>
                        <h3 class="module-name">{{ __('modules.education.name') }}</h3>
                        <p class="module-desc">{{ __('modules.education.subtitle') }}</p>
                    </div>
                    <div class="module-footer">
                        <span>/education</span>
                        <span><i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>

                <!-- 4. Certification -->
                <a href="{{ url('/certification') }}" class="module-card">
                    <div>
                        <div class="module-top">
                            <div class="module-icon" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">
                                <i class="fa-solid fa-certificate"></i>
                            </div>
                            <span class="tag-pill">Credentials</span>
                        </div>
                        <h3 class="module-name">{{ __('modules.certification.name') }}</h3>
                        <p class="module-desc">{{ __('modules.certification.subtitle') }}</p>
                    </div>
                    <div class="module-footer">
                        <span>/certification</span>
                        <span><i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>

                <!-- 5. Achievement -->
                <a href="{{ url('/achievement') }}" class="module-card">
                    <div>
                        <div class="module-top">
                            <div class="module-icon" style="background: rgba(239, 68, 68, 0.15); color: #f87171;">
                                <i class="fa-solid fa-trophy"></i>
                            </div>
                            <span class="tag-pill">Awards</span>
                        </div>
                        <h3 class="module-name">{{ __('modules.achievement.name') }}</h3>
                        <p class="module-desc">{{ __('modules.achievement.subtitle') }}</p>
                    </div>
                    <div class="module-footer">
                        <span>/achievement</span>
                        <span><i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>

                <!-- 6. Interest -->
                <a href="{{ url('/interest') }}" class="module-card">
                    <div>
                        <div class="module-top">
                            <div class="module-icon" style="background: rgba(236, 72, 153, 0.15); color: #f472b6;">
                                <i class="fa-solid fa-heart"></i>
                            </div>
                            <span class="tag-pill">Skills</span>
                        </div>
                        <h3 class="module-name">{{ __('modules.interest.name') }}</h3>
                        <p class="module-desc">{{ __('modules.interest.subtitle') }}</p>
                    </div>
                    <div class="module-footer">
                        <span>/interest</span>
                        <span><i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>

                <!-- 7. Project -->
                <a href="{{ url('/project') }}" class="module-card">
                    <div>
                        <div class="module-top">
                            <div class="module-icon" style="background: rgba(168, 85, 247, 0.15); color: #c084fc;">
                                <i class="fa-solid fa-diagram-project"></i>
                            </div>
                            <span class="tag-pill">Portfolio</span>
                        </div>
                        <h3 class="module-name">{{ __('modules.project.name') }}</h3>
                        <p class="module-desc">{{ __('modules.project.subtitle') }}</p>
                    </div>
                    <div class="module-footer">
                        <span>/project</span>
                        <span><i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>

                <!-- 8. Research -->
                <a href="{{ url('/research') }}" class="module-card">
                    <div>
                        <div class="module-top">
                            <div class="module-icon" style="background: rgba(6, 182, 212, 0.15); color: #22d3ee;">
                                <i class="fa-solid fa-flask-vial"></i>
                            </div>
                            <span class="tag-pill">Publications</span>
                        </div>
                        <h3 class="module-name">{{ __('modules.research.name') }}</h3>
                        <p class="module-desc">{{ __('modules.research.subtitle') }}</p>
                    </div>
                    <div class="module-footer">
                        <span>/research</span>
                        <span><i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>

                <!-- 9. Career -->
                <a href="{{ url('/career') }}" class="module-card">
                    <div>
                        <div class="module-top">
                            <div class="module-icon" style="background: rgba(234, 88, 12, 0.15); color: #fb923c;">
                                <i class="fa-solid fa-briefcase"></i>
                            </div>
                            <span class="tag-pill">Experience</span>
                        </div>
                        <h3 class="module-name">{{ __('modules.career.name') }}</h3>
                        <p class="module-desc">{{ __('modules.career.subtitle') }}</p>
                    </div>
                    <div class="module-footer">
                        <span>/career</span>
                        <span><i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>

                <!-- 10. Activity -->
                <a href="{{ url('/activity') }}" class="module-card">
                    <div>
                        <div class="module-top">
                            <div class="module-icon" style="background: rgba(20, 184, 166, 0.15); color: #2dd4bf;">
                                <i class="fa-solid fa-person-running"></i>
                            </div>
                            <span class="tag-pill">Leadership</span>
                        </div>
                        <h3 class="module-name">{{ __('modules.activity.name') }}</h3>
                        <p class="module-desc">{{ __('modules.activity.subtitle') }}</p>
                    </div>
                    <div class="module-footer">
                        <span>/activity</span>
                        <span><i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>

                <!-- 11. Media -->
                <a href="{{ url('/media') }}" class="module-card">
                    <div>
                        <div class="module-top">
                            <div class="module-icon" style="background: rgba(147, 51, 234, 0.15); color: #c084fc;">
                                <i class="fa-solid fa-photo-film"></i>
                            </div>
                            <span class="tag-pill">Assets</span>
                        </div>
                        <h3 class="module-name">{{ __('modules.media.name') }}</h3>
                        <p class="module-desc">{{ __('modules.media.subtitle') }}</p>
                    </div>
                    <div class="module-footer">
                        <span>/media</span>
                        <span><i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>

                <!-- 12. Commerce -->
                <a href="{{ url('/commerce') }}" class="module-card">
                    <div>
                        <div class="module-top">
                            <div class="module-icon" style="background: rgba(14, 165, 233, 0.15); color: #38bdf8;">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </div>
                            <span class="tag-pill">Products</span>
                        </div>
                        <h3 class="module-name">{{ __('modules.commerce.name') }}</h3>
                        <p class="module-desc">{{ __('modules.commerce.subtitle') }}</p>
                    </div>
                    <div class="module-footer">
                        <span>/commerce</span>
                        <span><i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>

                <!-- 13. Setting -->
                <a href="{{ url('/setting') }}" class="module-card">
                    <div>
                        <div class="module-top">
                            <div class="module-icon" style="background: rgba(100, 116, 139, 0.2); color: #94a3b8;">
                                <i class="fa-solid fa-gear"></i>
                            </div>
                            <span class="tag-pill">System</span>
                        </div>
                        <h3 class="module-name">{{ __('modules.setting.name') }}</h3>
                        <p class="module-desc">{{ __('modules.setting.subtitle') }}</p>
                    </div>
                    <div class="module-footer">
                        <span>/setting</span>
                        <span><i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- 5. Tech Stack Section -->
    <section class="tech-section">
        <div class="container">
            <div class="section-header" style="margin-bottom: 36px;">
                <div class="section-subtitle"><i class="fa-solid fa-code"></i> Competencies</div>
                <h2 class="section-title">{{ __('common.tech_stack') }}</h2>
            </div>

            <div class="tech-grid">
                <div class="tech-card">
                    <i class="fa-brands fa-php" style="color: #818cf8;"></i>
                    <span>PHP 8.x</span>
                </div>
                <div class="tech-card">
                    <i class="fa-brands fa-laravel" style="color: #ef4444;"></i>
                    <span>Laravel</span>
                </div>
                <div class="tech-card">
                    <i class="fa-solid fa-layer-group" style="color: #38bdf8;"></i>
                    <span>HMVC</span>
                </div>
                <div class="tech-card">
                    <i class="fa-brands fa-js" style="color: #fbbf24;"></i>
                    <span>JavaScript</span>
                </div>
                <div class="tech-card">
                    <i class="fa-brands fa-vuejs" style="color: #34d399;"></i>
                    <span>Vue.js</span>
                </div>
                <div class="tech-card">
                    <i class="fa-solid fa-database" style="color: #60a5fa;"></i>
                    <span>MySQL/Postgres</span>
                </div>
                <div class="tech-card">
                    <i class="fa-brands fa-docker" style="color: #38bdf8;"></i>
                    <span>Docker</span>
                </div>
                <div class="tech-card">
                    <i class="fa-brands fa-aws" style="color: #f59e0b;"></i>
                    <span>AWS Cloud</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. Call to Action & Contact -->
    <section id="contact" class="cta-section">
        <div class="container">
            <div class="cta-card">
                <h2 class="cta-title">Ready to Collaborate on High-Impact Systems?</h2>
                <p class="cta-desc">
                    Let's discuss architecture, software engineering, or explore the modular capabilities of this HMVC ecosystem.
                </p>
                <div style="display: flex; justify-content: center; gap: 14px; flex-wrap: wrap;">
                    <a href="mailto:admin@dapcode.test" class="btn-primary">
                        <i class="fa-solid fa-envelope"></i> Send Email
                    </a>
                    <a href="{{ url('/profile') }}" class="btn-secondary">
                        <i class="fa-solid fa-id-card"></i> View Full Bio
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. Portfolio Footer -->
    <footer class="portfolio-footer">
        <div class="container">
            <div class="footer-inner">
                <div class="footer-copy">
                    &copy; {{ date('Y') }} <strong>DapCode</strong>. {{ __('common.footer_text') }}
                </div>
                <div style="display: flex; gap: 16px; font-size: 13.5px;">
                    <a href="#about" style="color: var(--text-muted); text-decoration: none;"><i class="fa-solid fa-arrow-up"></i> Top</a>
                    <a href="{{ url('/dashboard') }}" style="color: var(--accent-cyan); text-decoration: none;"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                </div>
            </div>
        </div>
    <!-- Portfolio Script for Theme Dropdown -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const portfolioThemeBtn = document.getElementById('portfolioThemeBtn');
            const portfolioThemeMenu = document.getElementById('portfolioThemeMenu');

            if (portfolioThemeBtn && portfolioThemeMenu) {
                portfolioThemeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isOpen = portfolioThemeMenu.style.display === 'block';
                    portfolioThemeMenu.style.display = isOpen ? 'none' : 'block';
                });

                document.addEventListener('click', (e) => {
                    if (!portfolioThemeBtn.contains(e.target) && !portfolioThemeMenu.contains(e.target)) {
                        portfolioThemeMenu.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>
