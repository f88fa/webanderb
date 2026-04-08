<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>نظام Wesal</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    @if(isset($formType) && $formType == 'finance-reports-index')
    <script src="https://cdn.tailwindcss.com"></script>
    @endif
    @php
        $siteSettings = isset($settings) ? $settings : \App\Models\SiteSetting::getAllAsArray();
        $hex = function($key, $default) use ($siteSettings) {
            $v = $siteSettings[$key] ?? '';
            $n = normalize_css_hex($v);
            return $n !== '' ? $n : $default;
        };
        $primaryHex = $hex('dashboard_primary_color', '#5FB38E');
        $primaryRgb = '95, 179, 142';
        if (preg_match('/^#?([0-9A-Fa-f]{2})([0-9A-Fa-f]{2})([0-9A-Fa-f]{2})$/', $primaryHex, $m)) {
            $primaryRgb = hexdec($m[1]) . ', ' . hexdec($m[2]) . ', ' . hexdec($m[3]);
        }
    @endphp
    <style>
        :root {
            --primary-color: {{ $primaryHex }};
            --primary-rgb: {{ $primaryRgb }};
            --primary-dark: {{ $hex('dashboard_primary_dark', '#1F6B4F') }};
            --secondary-color: {{ $hex('dashboard_secondary_color', '#A8DCC3') }};
            --sidebar-bg: {{ !empty($siteSettings['dashboard_sidebar_bg']) ? $siteSettings['dashboard_sidebar_bg'] : 'rgba(15, 61, 46, 0.95)' }};
            --content-bg: {{ !empty($siteSettings['dashboard_content_bg']) ? $siteSettings['dashboard_content_bg'] : 'rgba(255, 255, 255, 0.05)' }};
            --card-bg: {{ !empty($siteSettings['dashboard_card_bg']) ? $siteSettings['dashboard_card_bg'] : 'rgba(255, 255, 255, 0.08)' }};
            --text-primary: {{ $hex('dashboard_text_primary', '#FFFFFF') }};
            --text-secondary: {{ $hex('dashboard_text_secondary', '#FFFFFF') }};
            --sidebar-text: {{ $hex('dashboard_sidebar_text', $siteSettings['dashboard_text_primary'] ?? '#FFFFFF') }};
            --border-color: {{ !empty($siteSettings['dashboard_border_color']) ? $siteSettings['dashboard_border_color'] : 'rgba(255, 255, 255, 0.1)' }};
            --bg-gradient: {{ !empty($siteSettings['dashboard_bg_gradient']) ? $siteSettings['dashboard_bg_gradient'] : 'linear-gradient(180deg, #0F3D2E 0%, #1F6B4F 30%, #5FB38E 60%, #A8DCC3 85%, #FFFFFF 100%)' }};
        }
        /* تطبيق خلفية القائمة الجانبية من إعدادات النظام (تجاوز style.css) */
        #wesalSidebar.sidebar,
        aside.sidebar#wesalSidebar,
        .dashboard-container .sidebar {
            background: var(--sidebar-bg) !important;
        }
        /* نصوص القائمة الجانبية والأيقونات — استخدام لون منفصل من الإعدادات */
        .sidebar .logo,
        .sidebar .menu-item,
        .sidebar .wesal-dropdown-menu .menu-item,
        .sidebar .user-info {
            color: var(--sidebar-text) !important;
        }
        .sidebar .menu-item:hover,
        .sidebar .wesal-dropdown-toggle:hover {
            color: var(--sidebar-text) !important;
        }
        .sidebar .menu-item.active {
            background: rgba(var(--primary-rgb), 0.2) !important;
        }
        body { 
            background: var(--sidebar-bg) !important; 
        }
        input[type="number"],
        input[type="text"][inputmode="numeric"],
        input[type="text"][inputmode="decimal"],
        input.amount,
        input.line-amount {
            direction: ltr !important;
            text-align: left !important;
        }
        .main-content { 
            background: var(--content-bg) !important; 
        }
        .content-card { 
            background: var(--card-bg) !important; 
            border: 1px solid var(--border-color) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
        }
        .dashboard-container {
            background: var(--sidebar-bg) !important;
        }

        /* كل عناصر القائمة الجانبية لليمين مع الأيقونة على اليمين (RTL: flex-start = يمين) */
        .sidebar-menu {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            text-align: right;
        }
        .sidebar-menu > .menu-item {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-direction: row;
            gap: 0.5rem;
            padding: 0.65rem 1rem;
            text-align: right;
            font-size: 0.875rem;
            border-bottom: 1px solid var(--border-color);
        }
        .sidebar-menu > .wesal-dropdown > .wesal-dropdown-toggle {
            border-bottom: 1px solid var(--border-color);
        }
        .sidebar-menu > .menu-item i {
            order: 0;
        }
        .sidebar-menu > .menu-item span {
            order: 0;
        }
        .sidebar-header .logo {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 0.5rem;
            text-align: right;
            font-size: 1.1rem;
        }
        .sidebar-header .logo i {
            font-size: 1.35rem;
        }
        .wesal-dropdown {
            position: relative;
        }
        .wesal-dropdown-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 0.65rem 1rem;
            padding-left: 1.5rem;
            background: transparent;
            border: none;
            color: var(--sidebar-text);
            cursor: pointer;
            font-family: inherit;
            font-size: 0.875rem;
            font-weight: 500;
            text-align: right;
            transition: all 0.3s ease;
            border-right: 3px solid transparent;
            margin: 0.25rem 0;
            flex-direction: row;
            gap: 0.5rem;
        }
        .wesal-dropdown-toggle > span {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 0.5rem;
            flex-direction: row;
            order: 0;
        }
        .wesal-dropdown-toggle i.fa-chevron-down {
            order: 1;
            flex-shrink: 0;
            font-size: 0.75rem;
            transition: transform 0.22s ease-out;
        }
        .wesal-dropdown-toggle:hover {
            background: rgba(255, 255, 255, 0.08);
            color: var(--sidebar-text);
            border-right-color: var(--primary-color);
        }
        .wesal-dropdown.open .wesal-dropdown-toggle i.fa-chevron-down {
            transform: rotate(180deg);
        }
        /* الزر المفتوح للقائمة المنسدلة — تمييز قوي جداً */
        .sidebar-menu > .wesal-dropdown.open > .wesal-dropdown-toggle {
            background: linear-gradient(90deg, rgba(var(--primary-rgb), 0.6) 0%, rgba(var(--primary-rgb), 0.35) 100%);
            color: var(--sidebar-text) !important;
            font-weight: 700;
            border-right: 5px solid var(--primary-color);
            box-shadow: 0 0 12px rgba(var(--primary-rgb), 0.4);
        }
        /* القائمة المفتوحة — خلفية مميزة واضحة */
        .sidebar-menu > .wesal-dropdown.open > .wesal-dropdown-menu {
            background: rgba(0, 0, 0, 0.25);
            border-right: 4px solid rgba(var(--primary-rgb), 0.7);
            margin-right: 2px;
            border-radius: 0 0 10px 10px;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        .wesal-dropdown-menu {
            max-height: 0;
            overflow: hidden;
            padding-right: 2.5rem;
            padding-left: 1rem;
            padding-bottom: 0;
            opacity: 0;
            transition: max-height 0.22s ease-out, opacity 0.2s ease-out;
            will-change: max-height;
        }
        /* فتح القائمة الرئيسية فقط — ارتفاع ثابت للانتقال السلس دون تعليق */
        .wesal-dropdown.open > .wesal-dropdown-menu {
            max-height: 75vh;
            opacity: 1;
            padding-bottom: 0.5rem;
            overflow-y: auto;
            will-change: auto;
        }
        /* القائمة الفرعية: انتقال سريع وسلس */
        .wesal-dropdown-sub .wesal-dropdown-menu {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.22s ease-out, opacity 0.2s ease-out;
            will-change: max-height;
        }
        .wesal-dropdown-sub.open .wesal-dropdown-menu {
            max-height: 280px;
            opacity: 1;
            padding-bottom: 0.5rem;
            overflow-y: auto;
            will-change: auto;
        }
        .wesal-dropdown-sub .wesal-dropdown-toggle i.fa-chevron-down {
            transition: transform 0.22s ease-out;
        }
        .wesal-dropdown-sub.open .wesal-dropdown-toggle i.fa-chevron-down {
            transform: rotate(180deg);
        }
        /* عناصر فرعية موحّدة: أزرار فرعية مباشرة + قوائم منسدلة فرعية + عناصر تحت القائمة الفرعية — لليمين مع تصغير خط */
        .wesal-dropdown-sub {
            position: relative;
        }
        .wesal-dropdown-menu {
            padding-right: 1.25rem;
            padding-left: 0.5rem;
        }
        .wesal-dropdown-sub .wesal-dropdown-menu {
            padding-right: 2rem;
            padding-left: 0.25rem;
        }
        /* زر فرعي مباشر (نظرة عامة، دليل الحسابات، القيود اليومية، إلخ) — محاذاة لليمين وأيقونة يمين */
        .wesal-dropdown-menu > .menu-item {
            padding: 0.5rem 0.65rem;
            font-size: 0.8125rem;
            text-align: right;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 0.5rem;
            flex-direction: row;
            margin: 0.2rem 0;
            border-radius: 6px;
            transition: background 0.2s;
            border-bottom: 1px solid var(--border-color);
        }
        .wesal-dropdown-menu > .menu-item:hover {
            background: rgba(255, 255, 255, 0.08);
        }
        .wesal-dropdown-menu > .menu-item i {
            font-size: 0.75rem;
            opacity: 0.9;
        }
        /* قائمة منسدلة فرعية (زر السندات، طلبات الصرف) */
        .wesal-dropdown-sub .wesal-dropdown-toggle {
            margin: 0.15rem 0;
            font-size: 0.8125rem;
            text-align: right;
            justify-content: space-between;
            padding: 0.5rem 0.65rem;
            padding-left: 1.5rem;
            border-radius: 6px;
            border-bottom: 1px solid var(--border-color);
        }
        /* الزر الفرعي المفتوح — تمييز قوي */
        .wesal-dropdown-sub.open > .wesal-dropdown-toggle {
            background: rgba(var(--primary-rgb), 0.5);
            color: var(--sidebar-text) !important;
            font-weight: 700;
            border-right: 4px solid var(--primary-color);
        }
        .wesal-dropdown-sub .wesal-dropdown-toggle span {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 0.5rem;
            flex-direction: row;
            order: 0;
        }
        .wesal-dropdown-sub .wesal-dropdown-toggle i.fa-chevron-down {
            order: 1;
            flex-shrink: 0;
            font-size: 0.75rem;
        }
        /* زر فرعي تحت قائمة منسدلة فرعية (سند قبض، سند صرف، سجل الطلبات، إلخ) — محاذاة لليمين وأيقونة يمين */
        .wesal-dropdown-sub .wesal-dropdown-menu .menu-item {
            padding: 0.4rem 0.5rem 0.4rem 0.65rem;
            font-size: 0.78rem;
            text-align: right;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 0.5rem;
            flex-direction: row;
            margin: 0.15rem 0;
            border-radius: 6px;
            transition: background 0.2s;
            border-bottom: 1px solid var(--border-color);
        }
        .wesal-dropdown-sub .wesal-dropdown-menu .menu-item:hover {
            background: rgba(255, 255, 255, 0.08);
        }
        .wesal-dropdown-sub .wesal-dropdown-menu .menu-item i {
            font-size: 0.75rem;
            opacity: 0.9;
        }
        /* القائمة الجانبية القابلة للطي */
        .sidebar {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
            overflow: visible;
        }
        .sidebar.collapsed {
            width: 64px !important;
            min-width: 64px;
        }
        .sidebar.collapsed .sidebar-header .logo span,
        .sidebar.collapsed .menu-item span,
        .sidebar.collapsed .wesal-dropdown-toggle span,
        .sidebar.collapsed .wesal-dropdown-toggle .fa-chevron-down,
        .sidebar.collapsed .user-info span {
            opacity: 0;
            width: 0;
            overflow: hidden;
            white-space: nowrap;
            margin: 0;
            padding: 0;
            display: none;
            transition: opacity 0.2s ease;
        }
        .sidebar.collapsed .wesal-sidebar-footer {
            display: none;
        }
        .sidebar.collapsed .logo,
        .sidebar.collapsed .menu-item,
        .sidebar.collapsed .wesal-dropdown-toggle {
            justify-content: center;
            padding-right: 0;
            padding-left: 0;
        }
        .sidebar.collapsed .sidebar-header {
            padding: 1rem 0.5rem;
        }
        .sidebar.collapsed .wesal-dropdown-menu {
            position: fixed;
            right: 64px;
            width: 220px;
            max-height: 85vh;
            overflow-y: auto;
            background: var(--sidebar-bg);
            border-radius: 0 12px 12px 0;
            box-shadow: 4px 0 24px rgba(0,0,0,0.25);
            border: 1px solid var(--border-color);
            border-right: none;
            padding: 0.5rem 0;
            z-index: 1001;
        }
        .sidebar.collapsed .wesal-dropdown-menu .menu-item span {
            opacity: 1;
            width: auto;
            display: inline;
        }
        .sidebar-toggle-btn {
            position: absolute;
            left: -14px;
            top: 50%;
            transform: translateY(-50%);
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--primary-color);
            border: 2px solid var(--sidebar-bg);
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: transform 0.2s ease, background 0.2s ease;
            z-index: 1002;
        }
        .sidebar-toggle-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-50%) scale(1.08);
        }
        .sidebar.collapsed .sidebar-toggle-btn i {
            transform: rotate(180deg);
        }
        /* غلاف المحتوى + الشريط العلوي الممتد من القائمة الجانبية */
        .wesal-content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-right: 240px;
            min-width: 0;
            min-height: 100vh;
            transition: margin-right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar.collapsed ~ .wesal-content-wrapper {
            margin-right: 64px;
        }
        .wesal-content-wrapper .main-content {
            margin-right: 0;
            flex: 1;
            min-height: 0;
            overflow: auto;
        }
        .wesal-page-footer {
            flex-shrink: 0;
            margin-top: 0;
        }
        .wesal-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem 1rem 2rem;
            background: var(--sidebar-bg);
            border-bottom: 1px solid var(--border-color);
            border-left: 1px solid var(--border-color);
            min-height: 72px;
            flex-shrink: 0;
        }
        .wesal-topbar-user {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            color: var(--text-primary);
            font-size: 0.95rem;
            font-weight: 600;
        }
        .wesal-topbar-user i {
            font-size: 1.4rem;
            color: var(--primary-color);
        }
        .wesal-topbar-logout-form {
            margin: 0;
        }
        .wesal-topbar-logout-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .wesal-topbar-logout-btn:hover {
            background: rgba(255, 255, 255, 0.12);
            color: var(--text-primary);
        }
        .wesal-notifications-btn:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        .wesal-notifications-dropdown[aria-hidden="false"] {
            display: block !important;
        }
        .wesal-notifications-dropdown a:hover {
            background: rgba(255, 255, 255, 0.06);
        }
        /* زر فتح القائمة على الموبايل */
        .wesal-mobile-menu-btn {
            display: none;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            padding: 0;
            margin-left: 0.75rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-primary);
            cursor: pointer;
            font-size: 1.25rem;
            transition: background 0.2s;
        }
        .wesal-mobile-menu-btn:hover {
            background: rgba(255, 255, 255, 0.18);
        }
        .wesal-sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        .wesal-sidebar-overlay.is-open {
            display: block;
            opacity: 1;
        }
        /* توافق كامل مع الشاشات الصغيرة والموبايل */
        @media (max-width: 991px) {
            .sidebar {
                width: min(240px, calc(100vw - 2rem));
                max-width: 100%;
                transform: translateX(100%);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                padding-right: env(safe-area-inset-right);
            }
            .sidebar.wesal-sidebar-open {
                transform: translateX(0);
                box-shadow: -8px 0 24px rgba(0, 0, 0, 0.3);
            }
            .wesal-content-wrapper {
                margin-right: 0 !important;
            }
            .sidebar.collapsed ~ .wesal-content-wrapper {
                margin-right: 0 !important;
            }
            .wesal-mobile-menu-btn {
                display: flex;
            }
            .sidebar-toggle-btn {
                display: none;
            }
        }
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                width: 240px;
            }
            .wesal-topbar {
                padding: 0.75rem 1rem;
                padding-top: max(0.75rem, env(safe-area-inset-top));
                padding-left: max(1rem, env(safe-area-inset-left));
                flex-wrap: nowrap;
                gap: 0.5rem;
                min-height: auto;
                overflow: visible;
            }
            .wesal-topbar > div:first-child {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                min-width: 0;
                flex: 1;
            }
            .wesal-topbar-user {
                min-width: 0;
                overflow: visible;
            }
            .wesal-topbar-user span {
                font-size: 0.72rem;
                display: block;
                white-space: normal;
                line-height: 1.25;
                max-width: 95px;
            }
            .wesal-notifications-wrap {
                flex-shrink: 0;
            }
            .wesal-notifications-dropdown {
                position: fixed !important;
                top: calc(env(safe-area-inset-top) + 56px) !important;
                left: 1rem !important;
                right: 1rem !important;
                min-width: auto !important;
                max-width: calc(100vw - 2rem);
                max-height: 70vh;
                overflow-y: auto;
            }
            .wesal-topbar-logout-btn span {
                display: none;
            }
            .wesal-topbar-logout-btn {
                padding: 0.5rem 0.75rem;
            }
            .main-content {
                padding: 1rem !important;
            }
            .content-card {
                padding: 1.25rem !important;
                margin-bottom: 1.25rem;
                border-radius: 12px;
            }
            .page-title {
                font-size: 1.5rem !important;
            }
            .page-subtitle {
                font-size: 0.95rem;
            }
            table {
                font-size: 0.875rem;
            }
            th, td {
                padding: 0.6rem 0.5rem !important;
            }
        }
        @media (max-width: 480px) {
            .main-content {
                padding: 0.75rem !important;
                padding-left: max(0.75rem, env(safe-area-inset-left));
                padding-right: max(0.75rem, env(safe-area-inset-right));
            }
            .content-card {
                padding: 1rem !important;
            }
            .page-title {
                font-size: 1.35rem !important;
            }
        }

        /* الطباعة: إظهار المحتوى فقط ١٠٠٪ — بدون القائمة الجانبية واسم المستخدم وكامل الصفحة */
        @media print {
            body, html { margin: 0 !important; padding: 0 !important; background: #fff !important; }
            .wesal-sidebar-overlay,
            .sidebar,
            .sidebar-toggle-btn,
            .wesal-topbar,
            .wesal-mobile-menu-btn,
            .no-print {
                display: none !important;
            }
            .dashboard-container { display: block !important; }
            .wesal-content-wrapper {
                margin: 0 !important;
                width: 100% !important;
                max-width: none !important;
            }
            .main-content {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: none !important;
                overflow: visible !important;
                background: #fff !important;
            }
            .content-card {
                background: #fff !important;
                border: 1px solid #ddd !important;
                box-shadow: none !important;
                padding: 1rem !important;
            }
            .alert-success, .alert-error { display: none !important; }
        }
    </style>
</head>
<body>
    <div id="wesalSidebarOverlay" class="wesal-sidebar-overlay" aria-hidden="true"></div>
    <div class="dashboard-container" id="dashboardContainer">
        <aside class="sidebar" id="wesalSidebar" aria-label="القائمة الرئيسية">
            <button type="button" class="sidebar-toggle-btn" id="sidebarToggle" title="طي / توسيع القائمة" aria-label="طي القائمة">
                <i class="fas fa-chevron-right"></i>
            </button>
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-layer-group"></i>
                    <span>نظام Wesal</span>
                </div>
            </div>

            <nav class="sidebar-menu">
                @can('wesal.home')
                <a href="{{ route('wesal') }}" class="menu-item {{ ($page ?? 'home') == 'home' ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>الرئيسية</span>
                </a>
                @endcan
                {{-- المكتب الإلكتروني - قائمة منسدلة --}}
                {{-- المكتب الإلكتروني — ثابت لكل مستخدم (بياناته فقط) --}}
                @php $isEOfficePage = ($page ?? '') == 'e-office' || request()->routeIs('wesal.e-office.*'); @endphp
                <div class="wesal-dropdown {{ $isEOfficePage ? 'open' : '' }}" id="eOfficeDropdown">
                    <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="main" data-wesal-target="eOfficeDropdown">
                        <span style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-desktop"></i>
                            <span>المكتب الإلكتروني</span>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="wesal-dropdown-menu">
                        <a href="{{ route('wesal.page', 'e-office') }}" class="menu-item {{ ($page ?? '') == 'e-office' && !request()->routeIs('wesal.e-office.*') ? 'active' : '' }}">
                            <i class="fas fa-th-large"></i>
                            <span>نظرة عامة</span>
                        </a>
                        <a href="{{ route('wesal.e-office.mail.inbox') }}" class="menu-item {{ request()->routeIs('wesal.e-office.mail.*') ? 'active' : '' }}">
                            <i class="fas fa-envelope"></i>
                            <span>البريد</span>
                        </a>
                        <a href="{{ route('wesal.e-office.tasks.index') }}" class="menu-item {{ request()->routeIs('wesal.e-office.tasks.*') ? 'active' : '' }}">
                            <i class="fas fa-tasks"></i>
                            <span>المهام</span>
                        </a>
                    </div>
                </div>

                {{-- الاجتماعات — ثابت لكل مستخدم (خاص به) --}}
                @php $isMeetingsPage = ($page ?? '') == 'meetings' || request()->routeIs('wesal.meetings.*'); @endphp
                <div class="wesal-dropdown {{ $isMeetingsPage ? 'open' : '' }}" id="meetingsDropdown">
                    <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="main" data-wesal-target="meetingsDropdown">
                        <span style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-users"></i>
                            <span>الاجتماعات</span>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="wesal-dropdown-menu">
                        <a href="{{ route('wesal.meetings.show') }}" class="menu-item {{ request()->routeIs('wesal.meetings.show') && !request()->route('section') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>نظرة عامة</span>
                        </a>
                        <a href="{{ route('wesal.meetings.show', ['section' => 'board-meetings']) }}" class="menu-item {{ request()->route('section') === 'board-meetings' ? 'active' : '' }}">
                            <i class="fas fa-gavel"></i>
                            <span>اجتماعات المجلس</span>
                        </a>
                        <a href="{{ route('wesal.meetings.show', ['section' => 'staff-meetings']) }}" class="menu-item {{ request()->route('section') === 'staff-meetings' ? 'active' : '' }}">
                            <i class="fas fa-user-friends"></i>
                            <span>اجتماعات الموظفين</span>
                        </a>
                        <a href="{{ route('wesal.meetings.show', ['section' => 'board-decisions']) }}" class="menu-item {{ request()->route('section') === 'board-decisions' ? 'active' : '' }}">
                            <i class="fas fa-file-signature"></i>
                            <span>قرارات المجلس</span>
                        </a>
                        <a href="{{ route('wesal.meetings.show', ['section' => 'board-members']) }}" class="menu-item {{ request()->route('section') === 'board-members' ? 'active' : '' }}">
                            <i class="fas fa-user-tie"></i>
                            <span>أعضاء المجلس</span>
                        </a>
                        <a href="{{ route('wesal.meetings.show', ['section' => 'meeting-types']) }}" class="menu-item {{ request()->route('section') === 'meeting-types' ? 'active' : '' }}">
                            <i class="fas fa-list-alt"></i>
                            <span>أنواع الاجتماعات</span>
                        </a>
                    </div>
                </div>

                {{-- الطلبات الإدارية — ثابت لكل مستخدم (إجازة، طلب عام، طلب مالي، حضور وانصراف) --}}
                @php $isRequestsPage = ($page ?? '') == 'requests' || request()->routeIs('wesal.requests.*'); @endphp
                <div class="wesal-dropdown {{ $isRequestsPage ? 'open' : '' }}" id="requestsDropdown">
                    <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="main" data-wesal-target="requestsDropdown">
                        <span style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-clipboard-list"></i>
                            <span>الطلبات الإدارية</span>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="wesal-dropdown-menu">
                        <a href="{{ route('wesal.requests.show', ['section' => 'leave']) }}" class="menu-item {{ ($requestSection ?? '') === 'leave' ? 'active' : '' }}">
                            <i class="fas fa-umbrella-beach"></i>
                            <span>طلب إجازة</span>
                        </a>
                        <a href="{{ route('wesal.requests.show', ['section' => 'general']) }}" class="menu-item {{ ($requestSection ?? '') === 'general' ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i>
                            <span>طلب عام</span>
                        </a>
                        <a href="{{ route('wesal.requests.show', ['section' => 'financial']) }}" class="menu-item {{ ($requestSection ?? '') === 'financial' ? 'active' : '' }}">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>طلب مالي</span>
                        </a>
                        <a href="{{ route('wesal.requests.show', ['section' => 'attendance']) }}" class="menu-item {{ ($requestSection ?? '') === 'attendance' ? 'active' : '' }}">
                            <i class="fas fa-fingerprint"></i>
                            <span>تسجيل حضور وانصراف</span>
                        </a>
                    </div>
                </div>

                {{-- الاتصالات الإدارية - الصادر والوارد --}}
                @can('wesal.communications')
                @php $isCommunicationsPage = ($page ?? '') == 'communications' || request()->routeIs('wesal.communications.*'); @endphp
                <div class="wesal-dropdown {{ $isCommunicationsPage ? 'open' : '' }}" id="communicationsDropdown">
                    <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="main" data-wesal-target="communicationsDropdown">
                        <span style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-envelope-open-text"></i>
                            <span>الاتصالات الإدارية</span>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="wesal-dropdown-menu">
                        <a href="{{ route('wesal.communications.outgoing') }}" class="menu-item {{ request()->routeIs('wesal.communications.outgoing') ? 'active' : '' }}">
                            <i class="fas fa-paper-plane"></i>
                            <span>الصادر</span>
                        </a>
                        <a href="{{ route('wesal.communications.incoming') }}" class="menu-item {{ request()->routeIs('wesal.communications.incoming') ? 'active' : '' }}">
                            <i class="fas fa-inbox"></i>
                            <span>الوارد</span>
                        </a>
                    </div>
                </div>
                @endcan

                {{-- المالية - قائمة منسدلة --}}
                @can('wesal.finance')
                @php $isFinancePage = ($page ?? '') == 'finance' || request()->routeIs('wesal.finance.*'); @endphp
                <div class="wesal-dropdown {{ $isFinancePage ? 'open' : '' }}" id="financeDropdown">
                    <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="main" data-wesal-target="financeDropdown">
                        <span style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-coins"></i>
                            <span>المالية</span>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="wesal-dropdown-menu">
                        <a href="{{ route('wesal.page', 'finance') }}" class="menu-item @if(request()->routeIs('wesal.page') && request()->route('page') == 'finance') active @endif">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>نظرة عامة</span>
                        </a>
                        <a href="{{ route('wesal.finance.reports.financial-movement') }}" class="menu-item {{ request()->routeIs('wesal.finance.reports.financial-movement') ? 'active' : '' }}">
                            <i class="fas fa-exchange-alt"></i>
                            <span>الحركة المالية</span>
                        </a>
                        <a href="{{ route('wesal.finance.reports.index') }}" class="menu-item {{ request()->routeIs('wesal.finance.reports.index') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span>التقارير المالية</span>
                        </a>
                        <a href="{{ route('wesal.finance.chart-accounts.index') }}" class="menu-item {{ request()->routeIs('wesal.finance.chart-accounts.*') && !request()->routeIs('wesal.finance.chart-accounts.trial-balance') ? 'active' : '' }}">
                            <i class="fas fa-sitemap"></i>
                            <span>دليل الحسابات</span>
                        </a>
                        {{-- الفترات المالية: قائمة فرعية (السنوات + الفترات المحاسبية) --}}
                        <div class="wesal-dropdown-sub" id="fiscalPeriodsSubDropdown" onclick="event.stopPropagation()">
                            <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="sub" data-wesal-target="fiscalPeriodsSubDropdown">
                                <span style="display: flex; align-items: center; gap: 1rem;">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>الفترات المالية</span>
                                </span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="wesal-dropdown-menu">
                                <a href="{{ route('wesal.finance.fiscal-years.index') }}" class="menu-item {{ request()->routeIs('wesal.finance.fiscal-years.*') ? 'active' : '' }}">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>السنوات المالية</span>
                                </a>
                                <a href="{{ route('wesal.finance.periods.index') }}" class="menu-item {{ request()->routeIs('wesal.finance.periods.*') ? 'active' : '' }}">
                                    <i class="fas fa-calendar-check"></i>
                                    <span>الفترات المحاسبية</span>
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('wesal.finance.journal-entries.index') }}" class="menu-item {{ request()->routeIs('wesal.finance.journal-entries.index') || request()->routeIs('wesal.finance.journal-entries.show') ? 'active' : '' }}">
                            <i class="fas fa-file-invoice"></i>
                            <span>القيود اليومية</span>
                        </a>
                        {{-- السندات: قائمة فرعية (مغلقة تلقائياً) --}}
                        <div class="wesal-dropdown-sub" id="vouchersSubDropdown" onclick="event.stopPropagation()">
                            <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="sub" data-wesal-target="vouchersSubDropdown">
                                <span style="display: flex; align-items: center; gap: 1rem;">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <span>السندات</span>
                                </span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="wesal-dropdown-menu">
                                <a href="{{ route('wesal.finance.journal-entries.index', ['entry_type' => 'receipt']) }}" class="menu-item {{ request('entry_type') === 'receipt' ? 'active' : '' }}">
                                    <i class="fas fa-hand-holding-usd"></i>
                                    <span>سند قبض</span>
                                </a>
                                <a href="{{ route('wesal.finance.journal-entries.index', ['entry_type' => 'payment']) }}" class="menu-item {{ request('entry_type') === 'payment' ? 'active' : '' }}">
                                    <i class="fas fa-money-check-alt"></i>
                                    <span>سند صرف</span>
                                </a>
                            </div>
                        </div>
                        {{-- طلبات الصرف: قائمة فرعية (مغلقة تلقائياً) --}}
                        <div class="wesal-dropdown-sub" id="paymentRequestsSubDropdown" onclick="event.stopPropagation()">
                            <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="sub" data-wesal-target="paymentRequestsSubDropdown">
                                <span style="display: flex; align-items: center; gap: 1rem;">
                                    <i class="fas fa-clipboard-list"></i>
                                    <span>طلبات الصرف</span>
                                </span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="wesal-dropdown-menu">
                                <a href="{{ route('wesal.finance.payment-requests.index') }}" class="menu-item {{ request()->routeIs('wesal.finance.payment-requests.index') ? 'active' : '' }}">
                                    <i class="fas fa-list"></i>
                                    <span>سجل الطلبات</span>
                                </a>
                                <a href="{{ route('wesal.finance.payment-requests.create') }}" class="menu-item {{ request()->routeIs('wesal.finance.payment-requests.create') ? 'active' : '' }}">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>طلب صرف جديد</span>
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('wesal.finance.cost-centers.index') }}" class="menu-item {{ request()->routeIs('wesal.finance.cost-centers.*') ? 'active' : '' }}">
                            <i class="fas fa-building"></i>
                            <span>مراكز التكلفة</span>
                        </a>
                        <a href="{{ route('wesal.finance.funds.index') }}" class="menu-item {{ request()->routeIs('wesal.finance.funds.*') ? 'active' : '' }}">
                            <i class="fas fa-mosque"></i>
                            <span>الأوقاف (أصناف الأموال)</span>
                        </a>
                    </div>
                </div>
                @endcan

                {{-- الموارد البشرية - قائمة منسدلة --}}
                @can('wesal.hr')
                @php $isHrPage = ($page ?? '') == 'hr' || request()->routeIs('wesal.hr.*'); @endphp
                <div class="wesal-dropdown {{ $isHrPage ? 'open' : '' }}" id="hrDropdown">
                    <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="main" data-wesal-target="hrDropdown">
                        <span style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-users"></i>
                            <span>الموارد البشرية</span>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="wesal-dropdown-menu">
                        <a href="{{ route('wesal.hr.show') }}" class="menu-item @if(request()->routeIs('wesal.hr.show') && request()->route('section') === null) active @endif">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>نظرة عامة</span>
                        </a>
                        {{-- الموظفون --}}
                        <div class="wesal-dropdown-sub" id="hrEmployeesSub" onclick="event.stopPropagation()">
                            <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="sub" data-wesal-target="hrEmployeesSub">
                                <span style="display: flex; align-items: center; gap: 1rem;">
                                    <i class="fas fa-user-friends"></i>
                                    <span>الموظفون</span>
                                </span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="wesal-dropdown-menu">
                                <a href="{{ route('wesal.hr.show', ['section' => 'employees']) }}" class="menu-item {{ request()->route('section') === 'employees' && !request()->route('sub') ? 'active' : '' }}">
                                    <i class="fas fa-list"></i>
                                    <span>قائمة الموظفين</span>
                                </a>
                                @can('hr.employees.create')
                                <a href="{{ route('wesal.hr.show', ['section' => 'employees', 'sub' => 'create']) }}" class="menu-item {{ request()->route('section') === 'employees' && request()->route('sub') === 'create' ? 'active' : '' }}">
                                    <i class="fas fa-user-plus"></i>
                                    <span>إضافة موظف</span>
                                </a>
                                @endcan
                                <a href="{{ route('wesal.hr.show', ['section' => 'departments']) }}" class="menu-item {{ request()->route('section') === 'departments' ? 'active' : '' }}">
                                    <i class="fas fa-sitemap"></i>
                                    <span>الأقسام</span>
                                </a>
                                <a href="{{ route('wesal.hr.show', ['section' => 'organization']) }}" class="menu-item {{ request()->route('section') === 'organization' ? 'active' : '' }}">
                                    <i class="fas fa-project-diagram"></i>
                                    <span>الهيكل التنظيمي</span>
                                </a>
                            </div>
                        </div>
                        {{-- الحضور والانصراف --}}
                        <div class="wesal-dropdown-sub" id="hrAttendanceSub" onclick="event.stopPropagation()">
                            <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="sub" data-wesal-target="hrAttendanceSub">
                                <span style="display: flex; align-items: center; gap: 1rem;">
                                    <i class="fas fa-clock"></i>
                                    <span>الحضور والانصراف</span>
                                </span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="wesal-dropdown-menu">
                                <a href="{{ route('wesal.hr.show', ['section' => 'attendance']) }}" class="menu-item {{ request()->route('section') === 'attendance' && !request()->route('sub') ? 'active' : '' }}">
                                    <i class="fas fa-fingerprint"></i>
                                    <span>تسجيل الحضور</span>
                                </a>
                                <a href="{{ route('wesal.hr.show', ['section' => 'attendance', 'sub' => 'log']) }}" class="menu-item {{ request()->route('section') === 'attendance' && request()->route('sub') === 'log' ? 'active' : '' }}">
                                    <i class="fas fa-calendar-day"></i>
                                    <span>السجل اليومي</span>
                                </a>
                                <a href="{{ route('wesal.hr.show', ['section' => 'shifts']) }}" class="menu-item {{ request()->route('section') === 'shifts' ? 'active' : '' }}">
                                    <i class="fas fa-business-time"></i>
                                    <span>الورديات</span>
                                </a>
                                <a href="{{ route('wesal.hr.show', ['section' => 'attendance', 'sub' => 'reports']) }}" class="menu-item {{ request()->route('section') === 'attendance' && request()->route('sub') === 'reports' ? 'active' : '' }}">
                                    <i class="fas fa-chart-bar"></i>
                                    <span>التقارير</span>
                                </a>
                            </div>
                        </div>
                        {{-- الإجازات --}}
                        <div class="wesal-dropdown-sub" id="hrLeaveSub" onclick="event.stopPropagation()">
                            <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="sub" data-wesal-target="hrLeaveSub">
                                <span style="display: flex; align-items: center; gap: 1rem;">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>الإجازات</span>
                                </span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="wesal-dropdown-menu">
                                <a href="{{ route('wesal.hr.show', ['section' => 'leave']) }}" class="menu-item {{ request()->route('section') === 'leave' && !request()->route('sub') ? 'active' : '' }}">
                                    <i class="fas fa-paper-plane"></i>
                                    <span>طلب إجازة</span>
                                </a>
                                <a href="{{ route('wesal.hr.show', ['section' => 'leave', 'sub' => 'balance']) }}" class="menu-item {{ request()->route('section') === 'leave' && request()->route('sub') === 'balance' ? 'active' : '' }}">
                                    <i class="fas fa-wallet"></i>
                                    <span>الرصيد</span>
                                </a>
                                <a href="{{ route('wesal.hr.show', ['section' => 'leave', 'sub' => 'approvals']) }}" class="menu-item {{ request()->route('section') === 'leave' && request()->route('sub') === 'approvals' ? 'active' : '' }}">
                                    <i class="fas fa-check-double"></i>
                                    <span>الموافقات</span>
                                </a>
                                <a href="{{ route('wesal.hr.show', ['section' => 'leave-types']) }}" class="menu-item {{ request()->route('section') === 'leave-types' ? 'active' : '' }}">
                                    <i class="fas fa-list"></i>
                                    <span>أنواع الإجازات</span>
                                </a>
                                <a href="{{ route('wesal.hr.show', ['section' => 'leave', 'sub' => 'record']) }}" class="menu-item {{ request()->route('section') === 'leave' && request()->route('sub') === 'record' ? 'active' : '' }}">
                                    <i class="fas fa-history"></i>
                                    <span>سجل الإجازات</span>
                                </a>
                            </div>
                        </div>
                        {{-- الرواتب --}}
                        <div class="wesal-dropdown-sub" id="hrPayrollSub" onclick="event.stopPropagation()">
                            <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="sub" data-wesal-target="hrPayrollSub">
                                <span style="display: flex; align-items: center; gap: 1rem;">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span>الرواتب</span>
                                </span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="wesal-dropdown-menu">
                                <a href="{{ route('wesal.hr.show', ['section' => 'payroll']) }}" class="menu-item {{ request()->route('section') === 'payroll' && !request()->route('sub') ? 'active' : '' }}">
                                    <i class="fas fa-calculator"></i>
                                    <span>مسير الرواتب</span>
                                </a>
                                <a href="{{ route('wesal.hr.show', ['section' => 'payroll', 'sub' => 'allowances']) }}" class="menu-item {{ request()->route('section') === 'payroll' && request()->route('sub') === 'allowances' ? 'active' : '' }}">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>البدلات والخصومات</span>
                                </a>
                                <a href="{{ route('wesal.hr.show', ['section' => 'payroll', 'sub' => 'advances']) }}" class="menu-item {{ request()->route('section') === 'payroll' && request()->route('sub') === 'advances' ? 'active' : '' }}">
                                    <i class="fas fa-hand-holding-usd"></i>
                                    <span>السلف</span>
                                </a>
                                <a href="{{ route('wesal.hr.show', ['section' => 'payroll', 'sub' => 'export']) }}" class="menu-item {{ request()->route('section') === 'payroll' && request()->route('sub') === 'export' ? 'active' : '' }}">
                                    <i class="fas fa-file-export"></i>
                                    <span>التصدير للمالية</span>
                                </a>
                            </div>
                        </div>
                        {{-- العقود --}}
                        <div class="wesal-dropdown-sub" id="hrContractsSub" onclick="event.stopPropagation()">
                            <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="sub" data-wesal-target="hrContractsSub">
                                <span style="display: flex; align-items: center; gap: 1rem;">
                                    <i class="fas fa-file-contract"></i>
                                    <span>العقود</span>
                                </span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="wesal-dropdown-menu">
                                <a href="{{ route('wesal.hr.show', ['section' => 'contracts']) }}" class="menu-item {{ request()->route('section') === 'contracts' ? 'active' : '' }}">
                                    <i class="fas fa-file-signature"></i>
                                    <span>العقود</span>
                                </a>
                                <a href="{{ route('wesal.hr.show', ['section' => 'decisions']) }}" class="menu-item {{ request()->route('section') === 'decisions' ? 'active' : '' }}">
                                    <i class="fas fa-gavel"></i>
                                    <span>القرارات</span>
                                </a>
                                <a href="{{ route('wesal.hr.show', ['section' => 'letters']) }}" class="menu-item {{ request()->route('section') === 'letters' ? 'active' : '' }}">
                                    <i class="fas fa-envelope-open-text"></i>
                                    <span>الخطابات</span>
                                </a>
                            </div>
                        </div>
                        {{-- الأداء --}}
                        <div class="wesal-dropdown-sub" id="hrPerformanceSub" onclick="event.stopPropagation()">
                            <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="sub" data-wesal-target="hrPerformanceSub">
                                <span style="display: flex; align-items: center; gap: 1rem;">
                                    <i class="fas fa-star"></i>
                                    <span>الأداء</span>
                                </span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="wesal-dropdown-menu">
                                <a href="{{ route('wesal.hr.show', ['section' => 'performance']) }}" class="menu-item {{ request()->route('section') === 'performance' && !request()->route('sub') ? 'active' : '' }}">
                                    <i class="fas fa-clipboard-check"></i>
                                    <span>التقييم</span>
                                </a>
                                <a href="{{ route('wesal.hr.show', ['section' => 'performance', 'sub' => 'goals']) }}" class="menu-item {{ request()->route('section') === 'performance' && request()->route('sub') === 'goals' ? 'active' : '' }}">
                                    <i class="fas fa-bullseye"></i>
                                    <span>الأهداف</span>
                                </a>
                                <a href="{{ route('wesal.hr.show', ['section' => 'performance', 'sub' => 'training']) }}" class="menu-item {{ request()->route('section') === 'performance' && request()->route('sub') === 'training' ? 'active' : '' }}">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span>التدريب</span>
                                </a>
                            </div>
                        </div>
                        {{-- الخدمات الذاتية --}}
                        <div class="wesal-dropdown-sub" id="hrSelfServiceSub" onclick="event.stopPropagation()">
                            <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="sub" data-wesal-target="hrSelfServiceSub">
                                <span style="display: flex; align-items: center; gap: 1rem;">
                                    <i class="fas fa-user-cog"></i>
                                    <span>الخدمات الذاتية</span>
                                </span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="wesal-dropdown-menu">
                                <a href="{{ route('wesal.hr.show', ['section' => 'self-service']) }}" class="menu-item {{ request()->route('section') === 'self-service' && !request()->route('sub') ? 'active' : '' }}">
                                    <i class="fas fa-tasks"></i>
                                    <span>طلباتي</span>
                                </a>
                                <a href="{{ route('wesal.hr.show', ['section' => 'self-service', 'sub' => 'documents']) }}" class="menu-item {{ request()->route('section') === 'self-service' && request()->route('sub') === 'documents' ? 'active' : '' }}">
                                    <i class="fas fa-folder-open"></i>
                                    <span>مستنداتي</span>
                                </a>
                                <a href="{{ route('wesal.hr.show', ['section' => 'self-service', 'sub' => 'notifications']) }}" class="menu-item {{ request()->route('section') === 'self-service' && request()->route('sub') === 'notifications' ? 'active' : '' }}">
                                    <i class="fas fa-bell"></i>
                                    <span>إشعاراتي</span>
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('wesal.hr.show', ['section' => 'reports']) }}" class="menu-item {{ request()->route('section') === 'reports' ? 'active' : '' }}">
                            <i class="fas fa-file-pdf"></i>
                            <span>التقارير</span>
                        </a>
                        <a href="{{ route('wesal.hr.show', ['section' => 'request-settings']) }}" class="menu-item {{ request()->route('section') === 'request-settings' ? 'active' : '' }}">
                            <i class="fas fa-list-ol"></i>
                            <span>إعدادات الطلبات</span>
                        </a>
                        <a href="{{ route('wesal.hr.show', ['section' => 'settings']) }}" class="menu-item {{ request()->route('section') === 'settings' ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>
                            <span>الإعدادات</span>
                        </a>
                    </div>
                </div>
                @endcan

                {{-- المستفيدين - قائمة منسدلة --}}
                @can('wesal.beneficiaries')
                @php $isBenPage = ($page ?? '') == 'beneficiaries' || request()->routeIs('wesal.beneficiaries.*'); @endphp
                <div class="wesal-dropdown {{ $isBenPage ? 'open' : '' }}" id="beneficiariesDropdown">
                    <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="main" data-wesal-target="beneficiariesDropdown">
                        <span style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-hands-helping"></i>
                            <span>المستفيدين</span>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="wesal-dropdown-menu">
                        <a href="{{ route('wesal.beneficiaries.show') }}" class="menu-item @if(request()->routeIs('wesal.beneficiaries.show') && request()->route('section') === null) active @endif">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>نظرة عامة</span>
                        </a>
                        {{-- إدارة المستفيدين --}}
                        <div class="wesal-dropdown-sub" id="benManagementSub" onclick="event.stopPropagation()">
                            <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="sub" data-wesal-target="benManagementSub">
                                <span style="display: flex; align-items: center; gap: 1rem;">
                                    <i class="fas fa-user-friends"></i>
                                    <span>إدارة المستفيدين</span>
                                </span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="wesal-dropdown-menu">
                                <a href="{{ route('wesal.beneficiaries.show', ['section' => 'list']) }}" class="menu-item {{ request()->route('section') === 'list' ? 'active' : '' }}">
                                    <i class="fas fa-list"></i>
                                    <span>قائمة المستفيدين</span>
                                </a>
                                <a href="{{ route('wesal.beneficiaries.show', ['section' => 'create']) }}" class="menu-item {{ request()->route('section') === 'create' ? 'active' : '' }}">
                                    <i class="fas fa-user-plus"></i>
                                    <span>إضافة مستفيد</span>
                                </a>
                                <a href="{{ route('wesal.beneficiaries.show', ['section' => 'archive']) }}" class="menu-item {{ request()->route('section') === 'archive' ? 'active' : '' }}">
                                    <i class="fas fa-archive"></i>
                                    <span>الأرشيف</span>
                                </a>
                                <a href="{{ route('wesal.beneficiaries.show', ['section' => 'registration-requests']) }}" class="menu-item {{ request()->route('section') === 'registration-requests' ? 'active' : '' }}">
                                    <i class="fas fa-user-plus"></i>
                                    <span>طلبات التسجيل من البوابة</span>
                                </a>
                                <a href="{{ route('wesal.beneficiaries.forms.index') }}" class="menu-item {{ request()->routeIs('wesal.beneficiaries.forms.*') ? 'active' : '' }}">
                                    <i class="fas fa-wpforms"></i>
                                    <span>نماذج المستفيدين</span>
                                </a>
                            </div>
                        </div>
                        {{-- الطلبات --}}
                        <div class="wesal-dropdown-sub" id="benRequestsSub" onclick="event.stopPropagation()">
                            <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="sub" data-wesal-target="benRequestsSub">
                                <span style="display: flex; align-items: center; gap: 1rem;">
                                    <i class="fas fa-inbox"></i>
                                    <span>الطلبات</span>
                                </span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="wesal-dropdown-menu">
                                <a href="{{ route('wesal.beneficiaries.show', ['section' => 'requests', 'sub' => 'new']) }}" class="menu-item {{ request()->route('section') === 'requests' && request()->route('sub') === 'new' ? 'active' : '' }}">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>جديدة</span>
                                </a>
                                <a href="{{ route('wesal.beneficiaries.show', ['section' => 'requests', 'sub' => 'under-study']) }}" class="menu-item {{ request()->route('section') === 'requests' && request()->route('sub') === 'under-study' ? 'active' : '' }}">
                                    <i class="fas fa-search"></i>
                                    <span>تحت الدراسة</span>
                                </a>
                                <a href="{{ route('wesal.beneficiaries.show', ['section' => 'requests', 'sub' => 'approved']) }}" class="menu-item {{ request()->route('section') === 'requests' && request()->route('sub') === 'approved' ? 'active' : '' }}">
                                    <i class="fas fa-check-circle"></i>
                                    <span>معتمدة</span>
                                </a>
                                <a href="{{ route('wesal.beneficiaries.show', ['section' => 'requests', 'sub' => 'rejected']) }}" class="menu-item {{ request()->route('section') === 'requests' && request()->route('sub') === 'rejected' ? 'active' : '' }}">
                                    <i class="fas fa-times-circle"></i>
                                    <span>مرفوضة</span>
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('wesal.beneficiaries.show', ['section' => 'services']) }}" class="menu-item {{ request()->route('section') === 'services' ? 'active' : '' }}">
                            <i class="fas fa-gift"></i>
                            <span>الخدمات والمساعدات</span>
                        </a>
                        <a href="{{ route('wesal.beneficiaries.show', ['section' => 'medical']) }}" class="menu-item {{ request()->route('section') === 'medical' ? 'active' : '' }}">
                            <i class="fas fa-heartbeat"></i>
                            <span>المتابعة الطبية</span>
                        </a>
                        <a href="{{ route('wesal.beneficiaries.show', ['section' => 'assessment']) }}" class="menu-item {{ request()->route('section') === 'assessment' ? 'active' : '' }}">
                            <i class="fas fa-star"></i>
                            <span>التقييم والأهلية</span>
                        </a>
                        <a href="{{ route('wesal.beneficiaries.show', ['section' => 'documents']) }}" class="menu-item {{ request()->route('section') === 'documents' ? 'active' : '' }}">
                            <i class="fas fa-folder-open"></i>
                            <span>المستندات</span>
                        </a>
                        <a href="{{ route('wesal.beneficiaries.show', ['section' => 'reports']) }}" class="menu-item {{ request()->route('section') === 'reports' ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span>التقارير</span>
                        </a>
                        <a href="{{ route('wesal.beneficiaries.show', ['section' => 'programs']) }}" class="menu-item {{ request()->route('section') === 'programs' ? 'active' : '' }}">
                            <i class="fas fa-bullseye"></i>
                            <span>البرامج والحملات</span>
                        </a>
                        <a href="{{ route('wesal.beneficiaries.show', ['section' => 'settings']) }}" class="menu-item {{ request()->route('section') === 'settings' ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>
                            <span>الإعدادات</span>
                        </a>
                    </div>
                </div>
                @endcan

                {{-- البرامج والمشاريع — متاح للمستخدمين العاديين والجهات المانحة --}}
                @if(auth()->user()->can('wesal.programs-projects') || (auth()->user()->can('donor.view_projects') && auth()->user()->donor))
                @php $isPPPage = ($page ?? '') == 'programs-projects' || request()->routeIs('wesal.programs-projects.*'); @endphp
                <div class="wesal-dropdown {{ $isPPPage ? 'open' : '' }}" id="programsProjectsDropdown">
                    <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="main" data-wesal-target="programsProjectsDropdown">
                        <span style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-project-diagram"></i>
                            <span>البرامج والمشاريع</span>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="wesal-dropdown-menu">
                        <a href="{{ route('wesal.programs-projects.show') }}" class="menu-item @if(request()->routeIs('wesal.programs-projects.show') && !request()->route('section')) active @endif">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>نظرة عامة</span>
                        </a>
                        <div class="wesal-dropdown-sub" id="ppProjectsSub" onclick="event.stopPropagation()">
                            <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="sub" data-wesal-target="ppProjectsSub">
                                <span style="display: flex; align-items: center; gap: 1rem;">
                                    <i class="fas fa-folder"></i>
                                    <span>المشاريع</span>
                                </span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="wesal-dropdown-menu">
                                <a href="{{ route('wesal.programs-projects.show', ['section' => 'projects', 'sub' => 'list']) }}" class="menu-item {{ request()->route('section') === 'projects' && request()->route('sub') === 'list' ? 'active' : '' }}">
                                    <i class="fas fa-list"></i>
                                    <span>قائمة المشاريع</span>
                                </a>
                                @unless(auth()->user()->donor ?? false)
                                <a href="{{ route('wesal.programs-projects.show', ['section' => 'projects', 'sub' => 'add']) }}" class="menu-item {{ request()->route('section') === 'projects' && request()->route('sub') === 'add' ? 'active' : '' }}">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>إضافة مشروع</span>
                                </a>
                                @endunless
                                <a href="{{ route('wesal.programs-projects.show', ['section' => 'projects', 'sub' => 'archive']) }}" class="menu-item {{ request()->route('section') === 'projects' && request()->route('sub') === 'archive' ? 'active' : '' }}">
                                    <i class="fas fa-archive"></i>
                                    <span>الأرشيف</span>
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('wesal.programs-projects.show', ['section' => 'stages']) }}" class="menu-item {{ request()->route('section') === 'stages' ? 'active' : '' }}">
                            <i class="fas fa-puzzle-piece"></i>
                            <span>المراحل</span>
                        </a>
                        <a href="{{ route('wesal.programs-projects.show', ['section' => 'tasks']) }}" class="menu-item {{ request()->route('section') === 'tasks' ? 'active' : '' }}">
                            <i class="fas fa-tasks"></i>
                            <span>المهام</span>
                        </a>
                        @unless(auth()->user()->donor ?? false)
                        <div class="wesal-dropdown-sub" id="ppDonorsSub" onclick="event.stopPropagation()">
                            <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="sub" data-wesal-target="ppDonorsSub">
                                <span style="display: flex; align-items: center; gap: 1rem;">
                                    <i class="fas fa-handshake"></i>
                                    <span>الجهات المانحة</span>
                                </span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="wesal-dropdown-menu">
                                <a href="{{ route('wesal.programs-projects.show', ['section' => 'donors', 'sub' => 'list']) }}" class="menu-item {{ request()->route('section') === 'donors' && request()->route('sub') === 'list' ? 'active' : '' }}">
                                    <i class="fas fa-list"></i>
                                    <span>قائمة الجهات</span>
                                </a>
                                <a href="{{ route('wesal.programs-projects.show', ['section' => 'donors', 'sub' => 'agreements']) }}" class="menu-item {{ request()->route('section') === 'donors' && request()->route('sub') === 'agreements' ? 'active' : '' }}">
                                    <i class="fas fa-file-contract"></i>
                                    <span>الاتفاقيات</span>
                                </a>
                                <a href="{{ route('wesal.programs-projects.show', ['section' => 'donors', 'sub' => 'grants']) }}" class="menu-item {{ request()->route('section') === 'donors' && request()->route('sub') === 'grants' ? 'active' : '' }}">
                                    <i class="fas fa-coins"></i>
                                    <span>المنح</span>
                                </a>
                            </div>
                        </div>
                        @endunless
                        <a href="{{ route('wesal.programs-projects.show', ['section' => 'budgets']) }}" class="menu-item {{ request()->route('section') === 'budgets' ? 'active' : '' }}">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>الميزانيات والمصروفات</span>
                        </a>
                        <a href="{{ route('wesal.programs-projects.show', ['section' => 'documents']) }}" class="menu-item {{ request()->route('section') === 'documents' ? 'active' : '' }}">
                            <i class="fas fa-folder-open"></i>
                            <span>المستندات</span>
                        </a>
                        <a href="{{ route('wesal.programs-projects.show', ['section' => 'reports']) }}" class="menu-item {{ request()->route('section') === 'reports' ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span>التقارير</span>
                        </a>
                        @unless(auth()->user()->donor ?? false)
                        <a href="{{ route('wesal.programs-projects.show', ['section' => 'settings']) }}" class="menu-item {{ request()->route('section') === 'settings' ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>
                            <span>الإعدادات</span>
                        </a>
                        @endunless
                    </div>
                </div>
                @endif

                {{-- تقنية المعلومات - قائمة منسدلة (مستخدمين النظام + إعدادات النظام) --}}
                @if(auth()->user()->can('wesal.users') || auth()->user()->can('wesal.system-settings'))
                @php $isITPage = in_array($page ?? '', ['users', 'roles-permissions', 'system-settings']); @endphp
                <div class="wesal-dropdown {{ $isITPage ? 'open' : '' }}" id="itDropdown">
                    <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="main" data-wesal-target="itDropdown">
                        <span style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-laptop-code"></i>
                            <span>تقنية المعلومات</span>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="wesal-dropdown-menu">
                        @can('wesal.users')
                        <a href="{{ route('wesal.page', 'users') }}" class="menu-item {{ in_array($page ?? '', ['users', 'roles-permissions']) ? 'active' : '' }}">
                            <i class="fas fa-users-cog"></i>
                            <span>مستخدمين النظام والصلاحيات</span>
                        </a>
                        @endcan
                        @can('wesal.system-settings')
                        <a href="{{ route('wesal.page', 'system-settings') }}" class="menu-item {{ ($page ?? '') == 'system-settings' ? 'active' : '' }}">
                            <i class="fas fa-sliders-h"></i>
                            <span>إعدادات النظام</span>
                        </a>
                        @endcan
                    </div>
                </div>
                @endif

                {{-- زر الموقع الالكتروني مع قائمة منسدلة --}}
                @can('wesal.website')
                @php $websitePages = ['settings','about','vision-mission','services','partners','media','banner-sections','section-order','menu','board-members','executive-director','staff','files','reports','policies','projects','testimonials','news']; $isWebsitePage = in_array($page ?? '', $websitePages); @endphp
                <div class="wesal-dropdown {{ $isWebsitePage ? 'open' : '' }}" id="websiteDropdown">
                    <button type="button" class="wesal-dropdown-toggle" data-wesal-dropdown="main" data-wesal-target="websiteDropdown">
                        <span style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-globe"></i>
                            <span>الموقع الإلكتروني</span>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="wesal-dropdown-menu">
                        <a href="{{ route('wesal.page', 'settings') }}" class="menu-item {{ ($page ?? '') == 'settings' ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>
                            <span>إعدادات الموقع</span>
                        </a>
                        @can('wesal.system-settings')
                        <a href="{{ route('wesal.page', 'system-settings') }}" class="menu-item {{ ($page ?? '') == 'system-settings' ? 'active' : '' }}">
                            <i class="fas fa-sliders-h"></i>
                            <span>إعدادات النظام</span>
                        </a>
                        @endcan
                        <a href="{{ route('wesal.page', 'about') }}" class="menu-item {{ ($page ?? '') == 'about' ? 'active' : '' }}">
                            <i class="fas fa-info-circle"></i>
                            <span>من نحن</span>
                        </a>
                        <a href="{{ route('wesal.page', 'vision-mission') }}" class="menu-item {{ ($page ?? '') == 'vision-mission' ? 'active' : '' }}">
                            <i class="fas fa-eye"></i>
                            <span>الرؤية والرسالة</span>
                        </a>
                        <a href="{{ route('wesal.page', 'services') }}" class="menu-item {{ ($page ?? '') == 'services' ? 'active' : '' }}">
                            <i class="fas fa-concierge-bell"></i>
                            <span>خدماتنا</span>
                        </a>
                        <a href="{{ route('wesal.page', 'partners') }}" class="menu-item {{ ($page ?? '') == 'partners' ? 'active' : '' }}">
                            <i class="fas fa-handshake"></i>
                            <span>شركاؤنا</span>
                        </a>
                        <a href="{{ route('wesal.page', 'media') }}" class="menu-item {{ ($page ?? '') == 'media' ? 'active' : '' }}">
                            <i class="fas fa-photo-video"></i>
                            <span>المركز الإعلامي</span>
                        </a>
                        <a href="{{ route('wesal.page', 'banner-sections') }}" class="menu-item {{ ($page ?? '') == 'banner-sections' ? 'active' : '' }}">
                            <i class="fas fa-image"></i>
                            <span>أقسام البانر</span>
                        </a>
                        <a href="{{ route('wesal.page', 'section-order') }}" class="menu-item {{ in_array($page ?? '', ['section-order','section_order']) ? 'active' : '' }}">
                            <i class="fas fa-sort"></i>
                            <span>ترتيب الأقسام</span>
                        </a>
                        <a href="{{ route('wesal.page', 'menu') }}" class="menu-item {{ ($page ?? '') == 'menu' ? 'active' : '' }}">
                            <i class="fas fa-bars"></i>
                            <span>القائمة العلوية</span>
                        </a>
                        <a href="{{ route('wesal.page', 'board-members') }}" class="menu-item {{ ($page ?? '') == 'board-members' ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>مجلس الإدارة</span>
                        </a>
                        <a href="{{ route('wesal.page', 'executive-director') }}" class="menu-item {{ ($page ?? '') == 'executive-director' ? 'active' : '' }}">
                            <i class="fas fa-user-tie"></i>
                            <span>المدير التنفيذي</span>
                        </a>
                        <a href="{{ route('wesal.page', 'staff') }}" class="menu-item {{ ($page ?? '') == 'staff' ? 'active' : '' }}">
                            <i class="fas fa-user-tie"></i>
                            <span>الموظفين</span>
                        </a>
                        <a href="{{ route('wesal.page', 'files') }}" class="menu-item {{ ($page ?? '') == 'files' ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i>
                            <span>الملفات</span>
                        </a>
                        <a href="{{ route('wesal.page', 'reports') }}" class="menu-item {{ ($page ?? '') == 'reports' ? 'active' : '' }}">
                            <i class="fas fa-file-pdf"></i>
                            <span>التقارير</span>
                        </a>
                        <a href="{{ route('wesal.page', 'policies') }}" class="menu-item {{ ($page ?? '') == 'policies' ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i>
                            <span>اللوائح والسياسات</span>
                        </a>
                        <a href="{{ route('wesal.page', 'projects') }}" class="menu-item {{ ($page ?? '') == 'projects' ? 'active' : '' }}">
                            <i class="fas fa-project-diagram"></i>
                            <span>مشاريعنا</span>
                        </a>
                        <a href="{{ route('wesal.page', 'testimonials') }}" class="menu-item {{ ($page ?? '') == 'testimonials' ? 'active' : '' }}">
                            <i class="fas fa-quote-left"></i>
                            <span>ماذا قالوا عنا</span>
                        </a>
                        <a href="{{ route('wesal.page', 'news') }}" class="menu-item {{ ($page ?? '') == 'news' ? 'active' : '' }}">
                            <i class="fas fa-newspaper"></i>
                            <span>الأخبار</span>
                        </a>
                    </div>
                </div>
                @endcan
            </nav>
        </aside>

        <div class="wesal-content-wrapper">
            <header class="wesal-topbar">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <button type="button" class="wesal-mobile-menu-btn" id="wesalMobileMenuBtn" title="فتح القائمة" aria-label="فتح القائمة">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="wesal-topbar-user">
                        <i class="fas fa-user-circle"></i>
                        <span>{{ Auth::user()->name ?? 'المستخدم' }}</span>
                    </div>
                    <div class="wesal-notifications-wrap" style="position: relative;">
                        <button type="button" class="wesal-notifications-btn" id="wesalNotificationsBtn" title="الإشعارات" aria-label="الإشعارات" aria-expanded="false" aria-haspopup="true" style="display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; padding: 0; margin-right: 0.25rem; background: rgba(255,255,255,0.08); border: 1px solid var(--border-color); border-radius: 10px; color: var(--text-primary); cursor: pointer; font-size: 1.1rem; position: relative;">
                            <i class="fas fa-bell"></i>
                            @if(($notificationsTotal ?? 0) > 0)
                                <span class="wesal-notifications-badge" style="position: absolute; top: 4px; right: 4px; min-width: 18px; height: 18px; padding: 0 4px; background: #e57373; color: white; font-size: 0.7rem; font-weight: 700; border-radius: 9px; display: flex; align-items: center; justify-content: center;">{{ $notificationsTotal > 99 ? '99+' : $notificationsTotal }}</span>
                            @endif
                        </button>
                        <div id="wesalNotificationsDropdown" class="wesal-notifications-dropdown" aria-hidden="true" style="position: absolute; display: none; top: 100%; right: 0; margin-top: 6px; min-width: 260px; background: var(--sidebar-bg); border: 1px solid var(--border-color); border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.3); z-index: 100; overflow: hidden;">
                            <div style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); font-weight: 600; color: var(--text-primary); font-size: 0.9rem;">
                                <i class="fas fa-bell" style="margin-left: 0.35rem; color: var(--primary-color);"></i> الإشعارات
                            </div>
                            <a href="{{ route('wesal.e-office.mail.inbox') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; color: var(--text-primary); text-decoration: none; border-bottom: 1px solid var(--border-color); transition: background 0.2s;">
                                <span><i class="fas fa-envelope" style="margin-left: 0.5rem; color: var(--primary-color);"></i> رسائل جديدة</span>
                                @if(($unreadMessagesCount ?? 0) > 0)
                                    <span style="background: rgba(229,115,115,0.3); color: #ffabab; padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">{{ $unreadMessagesCount }}</span>
                                @else
                                    <span style="color: var(--text-secondary); font-size: 0.85rem;">—</span>
                                @endif
                            </a>
                            <a href="{{ route('wesal.e-office.tasks.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; color: var(--text-primary); text-decoration: none; border-bottom: 1px solid var(--border-color); transition: background 0.2s;">
                                <span><i class="fas fa-tasks" style="margin-left: 0.5rem; color: var(--primary-color);"></i> مهام جديدة</span>
                                @if(($newTasksCount ?? 0) > 0)
                                    <span style="background: rgba(229,115,115,0.3); color: #ffabab; padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">{{ $newTasksCount }}</span>
                                @else
                                    <span style="color: var(--text-secondary); font-size: 0.85rem;">—</span>
                                @endif
                            </a>
                            <a href="{{ route('wesal.beneficiaries.show', ['section' => 'registration-requests']) }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; color: var(--text-primary); text-decoration: none; transition: background 0.2s;">
                                <span><i class="fas fa-user-plus" style="margin-left: 0.5rem; color: var(--primary-color);"></i> طلب مستفيد جديد</span>
                                @if(($newBeneficiaryRequestsCount ?? 0) > 0)
                                    <span style="background: rgba(229,115,115,0.3); color: #ffabab; padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">{{ $newBeneficiaryRequestsCount }}</span>
                                @else
                                    <span style="color: var(--text-secondary); font-size: 0.85rem;">—</span>
                                @endif
                            </a>
                            @if(($notificationsTotal ?? 0) === 0)
                                <div style="padding: 1rem; text-align: center; color: var(--text-secondary); font-size: 0.9rem;">لا توجد إشعارات جديدة</div>
                            @endif
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="wesal-topbar-logout-form">
                    @csrf
                    <button type="submit" class="wesal-topbar-logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>تسجيل الخروج</span>
                    </button>
                </form>
            </header>

        <main class="main-content">
            @if(session('success'))
                <div class="alert alert-success" style="margin-bottom: 1rem;"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error" style="margin-bottom: 1rem;"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif
            @switch($page ?? 'home')
                @case('home')
                    @include('wesal.pages.home')
                    @break
                @case('e-office')
                    @if(isset($formType) && $formType == 'mail-inbox')
                        @include('wesal.pages.e-office.mail.inbox')
                    @elseif(isset($formType) && $formType == 'mail-sent')
                        @include('wesal.pages.e-office.mail.sent')
                    @elseif(isset($formType) && $formType == 'mail-compose')
                        @include('wesal.pages.e-office.mail.compose')
                    @elseif(isset($formType) && $formType == 'mail-show')
                        @include('wesal.pages.e-office.mail.show')
                    @elseif(isset($formType) && $formType == 'tasks-index')
                        @include('wesal.pages.e-office.tasks.index')
                    @elseif(isset($formType) && $formType == 'tasks-create')
                        @include('wesal.pages.e-office.tasks.create')
                    @elseif(isset($formType) && $formType == 'tasks-show')
                        @include('wesal.pages.e-office.tasks.show')
                    @else
                        @include('wesal.pages.e-office')
                    @endif
                    @break
                @case('communications')
                    @if(isset($formType) && $formType == 'communications-outgoing')
                        @include('wesal.pages.communications.outgoing')
                    @elseif(isset($formType) && $formType == 'communications-incoming')
                        @include('wesal.pages.communications.incoming')
                    @elseif(isset($formType) && $formType == 'communications-letter-form')
                        @include('wesal.pages.communications.letter-form')
                    @elseif(isset($formType) && $formType == 'communications-letter-show')
                        @include('wesal.pages.communications.letter-show')
                    @else
                        @include('wesal.pages.communications.outgoing')
                    @endif
                    @break
                @case('finance')
                    @if(isset($formType) && $formType == 'ledger')
                        @include('wesal.pages.finance.ledger')
                    @elseif(request()->routeIs('wesal.finance.chart-accounts.create') || request()->routeIs('wesal.finance.chart-accounts.edit'))
                        @include('wesal.pages.finance.chart-account-form')
                    @elseif(isset($formType) && $formType == 'chart-account-show')
                        @include('wesal.pages.finance.chart-account-show')
                    @elseif(isset($formType) && $formType == 'trial-balance')
                        @include('wesal.pages.finance.trial-balance')
                    @elseif(request()->routeIs('wesal.finance.chart-accounts.*'))
                        @include('wesal.pages.finance.chart-accounts')
                    @elseif(request()->routeIs('wesal.finance.receipt-voucher.create'))
                        @include('wesal.pages.finance.receipt-voucher-form')
                    @elseif(request()->routeIs('wesal.finance.payment-voucher.create'))
                        @include('wesal.pages.finance.payment-voucher-form')
                    @elseif(request()->routeIs('wesal.finance.journal-entries.select-period') || (isset($formType) && $formType == 'journal-entry-select-period'))
                        @include('wesal.pages.finance.journal-entry-select-period')
                    @elseif(request()->routeIs('wesal.finance.journal-entries.create'))
                        @include('wesal.pages.finance.journal-entry-form')
                    @elseif(request()->routeIs('wesal.finance.journal-entries.print'))
                        @include('wesal.pages.finance.voucher-print')
                    @elseif(isset($formType) && $formType == 'journal-entries')
                        @include('wesal.pages.finance.journal-entries')
                    @elseif(isset($formType) && $formType == 'journal-entry-show')
                        @include('wesal.pages.finance.journal-entry-show')
                    @elseif(isset($formType) && $formType == 'finance-reports-index')
                        @include('wesal.pages.finance.reports.index')
                    @elseif(isset($formType) && $formType == 'finance-report-income-statement')
                        @include('wesal.pages.finance.reports.income-statement')
                    @elseif(isset($formType) && $formType == 'finance-report-balance-sheet')
                        @include('wesal.pages.finance.reports.balance-sheet')
                    @elseif(isset($formType) && $formType == 'finance-report-cash-flow')
                        @include('wesal.pages.finance.reports.cash-flow')
                    @elseif(isset($formType) && $formType == 'finance-report-general-ledger')
                        @include('wesal.pages.finance.reports.general-ledger')
                    @elseif(isset($formType) && $formType == 'finance-report-net-assets-changes')
                        @include('wesal.pages.finance.reports.net-assets-changes')
                    @elseif(isset($formType) && $formType == 'finance-report-activities-by-function')
                        @include('wesal.pages.finance.reports.statement-activities-by-function')
                    @elseif(isset($formType) && $formType == 'fiscal-years')
                        @include('wesal.pages.finance.fiscal-years')
                    @elseif(isset($formType) && $formType == 'periods')
                        @include('wesal.pages.finance.periods')
                    @elseif(isset($formType) && $formType == 'cost-centers')
                        @include('wesal.pages.finance.cost-centers')
                    @elseif(isset($formType) && $formType == 'funds')
                        @include('wesal.pages.finance.funds')
                    @elseif(isset($formType) && $formType == 'payment-requests-index')
                        @include('wesal.pages.finance.payment-requests-index')
                    @elseif(isset($formType) && $formType == 'payment-request-beneficiaries-report')
                        @include('wesal.pages.finance.payment-request-beneficiaries-report')
                    @elseif(isset($formType) && $formType == 'payment-request-form')
                        @include('wesal.pages.finance.payment-request-form')
                    @elseif(isset($formType) && $formType == 'financial-movement')
                        @include('wesal.pages.finance.financial-movement')
                    @else
                        @include('wesal.pages.finance')
                    @endif
                    @break
                @case('hr')
                    @include('wesal.pages.hr')
                    @break
                @case('beneficiaries')
                    @include('wesal.pages.beneficiaries')
                    @break
                @case('programs-projects')
                    @include('wesal.pages.programs-projects')
                    @break
                @case('meetings')
                    @include('wesal.meetings')
                    @break
                @case('requests')
                    @include('wesal.pages.requests.wrap')
                    @break
                @case('users')
                    @include('wesal.pages.users')
                    @break
                @case('roles-permissions')
                    @include('wesal.pages.roles-permissions')
                    @break
                @case('system-settings')
                    @include('dashboard.pages.system-settings')
                    @break
                @case('settings')
                    @include('dashboard.pages.settings')
                    @break
                @case('about')
                    @include('dashboard.pages.about')
                    @break
                @case('vision-mission')
                    @include('dashboard.pages.vision-mission')
                    @break
                @case('services')
                    @include('dashboard.pages.services')
                    @break
                @case('partners')
                    @include('dashboard.pages.partners')
                    @break
                @case('media')
                    @include('dashboard.pages.media')
                    @break
                @case('banner-sections')
                    @include('dashboard.pages.banner-sections')
                    @break
                @case('section-order')
                @case('section_order')
                    @include('dashboard.pages.section-order')
                    @break
                @case('menu')
                    @include('dashboard.pages.menu')
                    @break
                @case('board-members')
                    @include('dashboard.pages.board-members')
                    @break
                @case('executive-director')
                    @include('dashboard.pages.executive-director')
                    @break
                @case('staff')
                    @include('dashboard.pages.staff')
                    @break
                @case('files')
                    @include('dashboard.pages.files')
                    @break
                @case('reports')
                    @include('dashboard.pages.reports')
                    @break
                @case('policies')
                    @include('dashboard.pages.policies')
                    @break
                @case('projects')
                    @include('dashboard.pages.projects')
                    @break
                @case('testimonials')
                    @include('dashboard.pages.testimonials')
                    @break
                @case('news')
                    @include('dashboard.pages.news')
                    @break
                @default
                    @include('wesal.pages.home')
            @endswitch
        </main>

            {{-- تذييل ثابت أسفل المحتوى (لا يتحرك مع التمرير) --}}
            <footer class="wesal-page-footer" style="padding: 0.35rem 1rem; border-top: 1px solid var(--border-color); font-size: 0.65rem; color: var(--text-secondary); line-height: 1.4; background: var(--sidebar-bg); opacity: 0.85; text-align: center;">
                <p style="margin: 0; display: flex; align-items: center; justify-content: center; gap: 0.5rem; flex-wrap: wrap;">
                    <span>جميع الحقوق محفوظة لـ {{ $settings['site_title'] ?? 'الموقع' }}</span>
                    <span style="opacity: 0.7;">·</span>
                    <span>برمجة</span>
                    <a href="https://twitter.com/f88fa" target="_blank" rel="noopener noreferrer" style="color: var(--primary-color); text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;" title="تويتر - فارس التريباني">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" style="flex-shrink: 0;"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        فارس التريباني
                    </a>
                </p>
            </footer>
        </div>
    </div>

    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script>
    (function() {
        var sidebar = document.getElementById('wesalSidebar');
        var toggle = document.getElementById('sidebarToggle');
        var overlay = document.getElementById('wesalSidebarOverlay');
        var mobileBtn = document.getElementById('wesalMobileMenuBtn');

        function closeMobileSidebar() {
            if (sidebar) sidebar.classList.remove('wesal-sidebar-open');
            if (overlay) {
                overlay.classList.remove('is-open');
                overlay.setAttribute('aria-hidden', 'true');
            }
            document.body.style.overflow = '';
        }
        function openMobileSidebar() {
            if (sidebar) sidebar.classList.add('wesal-sidebar-open');
            if (overlay) {
                overlay.classList.add('is-open');
                overlay.setAttribute('aria-hidden', 'false');
            }
            document.body.style.overflow = 'hidden';
        }

        if (overlay) {
            overlay.addEventListener('click', closeMobileSidebar);
        }
        if (mobileBtn && sidebar) {
            mobileBtn.addEventListener('click', function() {
                if (sidebar.classList.contains('wesal-sidebar-open')) {
                    closeMobileSidebar();
                } else {
                    openMobileSidebar();
                }
            });
        }
        document.querySelectorAll('#wesalSidebar .menu-item, #wesalSidebar .wesal-dropdown-menu .menu-item').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 991) closeMobileSidebar();
            });
        });

        var collapsed = localStorage.getItem('wesalSidebarCollapsed') === '1';
        if (collapsed && sidebar) {
            sidebar.classList.add('collapsed');
            if (toggle) toggle.setAttribute('aria-label', 'توسيع القائمة');
        }
        if (toggle && sidebar) {
            toggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                var isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('wesalSidebarCollapsed', isCollapsed ? '1' : '0');
                toggle.setAttribute('aria-label', isCollapsed ? 'توسيع القائمة' : 'طي القائمة');
            });
        }

        document.querySelectorAll('.wesal-dropdown-toggle[data-wesal-dropdown][data-wesal-target]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var targetId = btn.getAttribute('data-wesal-target');
                var targetEl = document.getElementById(targetId);
                if (!targetEl) return;
                var isMain = btn.getAttribute('data-wesal-dropdown') === 'main';
                if (isMain) {
                    document.querySelectorAll('.wesal-dropdown').forEach(function(d) {
                        if (d.id !== targetId) d.classList.remove('open');
                    });
                } else {
                    var parent = targetEl.parentElement;
                    if (parent) {
                        parent.querySelectorAll('.wesal-dropdown-sub').forEach(function(s) {
                            if (s.id !== targetId) s.classList.remove('open');
                        });
                    }
                }
                targetEl.classList.toggle('open');
            });
        });

        var notifBtn = document.getElementById('wesalNotificationsBtn');
        var notifDrop = document.getElementById('wesalNotificationsDropdown');
        if (notifBtn && notifDrop) {
            notifBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                var open = notifDrop.getAttribute('aria-hidden') !== 'false';
                notifDrop.style.display = open ? 'block' : 'none';
                notifDrop.setAttribute('aria-hidden', open ? 'false' : 'true');
                notifBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
            document.addEventListener('click', function() {
                if (notifDrop.getAttribute('aria-hidden') === 'false') {
                    notifDrop.style.display = 'none';
                    notifDrop.setAttribute('aria-hidden', 'true');
                    notifBtn.setAttribute('aria-expanded', 'false');
                }
            });
            notifDrop.addEventListener('click', function(e) { e.stopPropagation(); });
        }
    })();

    // اعتراض الضغطات: منع إدخال الأرقام العربية/الهندية (إجبار المستخدم على الكيبورد الإنجليزي للأرقام)
    (function() {
        var ar = '\u0660\u0661\u0662\u0663\u0664\u0665\u0666\u0667\u0668\u0669';
        var fa = '\u06F0\u06F1\u06F2\u06F3\u06F4\u06F5\u06F6\u06F7\u06F8\u06F9';
        var en = '0123456789';
        function getEnglishDigit(char) {
            var ai = ar.indexOf(char);
            var fi = fa.indexOf(char);
            if (ai !== -1) return en[ai];
            if (fi !== -1) return en[fi];
            return null;
        }
        document.addEventListener('keypress', function(e) {
            var el = e.target;
            if (el.isContentEditable) return;
            var tag = (el.tagName || '').toLowerCase();
            if (tag !== 'input' && tag !== 'textarea') return;
            var eng = getEnglishDigit(e.key);
            if (eng !== null) {
                e.preventDefault();
                // لا ندخل أي رقم عند استخدام لوحة عربية؛ يجب التحويل للوحة إنجليزية
            }
        }, true);
    })();
    </script>
</body>
</html>
