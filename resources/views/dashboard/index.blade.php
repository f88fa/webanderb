<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>لوحة التحكم</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    @php
        $settings = \App\Models\SiteSetting::getAllAsArray();
    @endphp
    <style>
        :root {
            --primary-color: {{ $settings['dashboard_primary_color'] ?? '#5FB38E' }};
            --primary-dark: {{ $settings['dashboard_primary_dark'] ?? '#1F6B4F' }};
            --secondary-color: {{ $settings['dashboard_secondary_color'] ?? '#A8DCC3' }};
            --accent-color: {{ $settings['dashboard_accent_color'] ?? '#5FB38E' }};
            --sidebar-bg: {{ $settings['dashboard_sidebar_bg'] ?? 'rgba(15, 61, 46, 0.95)' }};
            --content-bg: {{ $settings['dashboard_content_bg'] ?? 'rgba(255, 255, 255, 0.05)' }};
            --text-primary: {{ $settings['dashboard_text_primary'] ?? '#FFFFFF' }};
            --text-secondary: {{ $settings['dashboard_text_secondary'] ?? '#FFFFFF' }};
            --border-color: {{ $settings['dashboard_border_color'] ?? 'rgba(255, 255, 255, 0.1)' }};
            --bg-gradient: {{ $settings['dashboard_bg_gradient'] ?? 'linear-gradient(180deg, #0F3D2E 0%, #1F6B4F 30%, #5FB38E 60%, #A8DCC3 85%, #FFFFFF 100%)' }};
        }
        
        body {
            background: var(--bg-gradient) !important;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- القائمة الجانبية اليمنى -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>لوحة التحكم</span>
                </div>
            </div>
            
            <nav class="sidebar-menu">
                <a href="{{ route('dashboard', ['page' => 'home']) }}" class="menu-item {{ $page == 'home' ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>الرئيسية</span>
                </a>
                <a href="{{ route('dashboard', ['page' => 'settings']) }}" class="menu-item {{ $page == 'settings' ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span>إعدادات الموقع</span>
                </a>
                <a href="{{ route('dashboard', ['page' => 'about']) }}" class="menu-item {{ $page == 'about' ? 'active' : '' }}">
                    <i class="fas fa-info-circle"></i>
                    <span>من نحن</span>
                </a>
                <a href="{{ route('dashboard', ['page' => 'vision-mission']) }}" class="menu-item {{ $page == 'vision-mission' ? 'active' : '' }}">
                    <i class="fas fa-eye"></i>
                    <span>الرؤية والرسالة</span>
                </a>
                <a href="{{ route('dashboard', ['page' => 'services']) }}" class="menu-item {{ $page == 'services' ? 'active' : '' }}">
                    <i class="fas fa-concierge-bell"></i>
                    <span>خدماتنا</span>
                </a>
                <a href="{{ route('dashboard', ['page' => 'partners']) }}" class="menu-item {{ $page == 'partners' ? 'active' : '' }}">
                    <i class="fas fa-handshake"></i>
                    <span>شركاؤنا</span>
                </a>
                <a href="{{ route('dashboard', ['page' => 'media']) }}" class="menu-item {{ $page == 'media' ? 'active' : '' }}">
                    <i class="fas fa-photo-video"></i>
                    <span>المركز الإعلامي</span>
                </a>
                <a href="{{ route('dashboard', ['page' => 'banner-sections']) }}" class="menu-item {{ $page == 'banner-sections' ? 'active' : '' }}">
                    <i class="fas fa-image"></i>
                    <span>أقسام البانر</span>
                </a>
                <a href="{{ route('dashboard', ['page' => 'section-order']) }}" class="menu-item {{ $page == 'section-order' ? 'active' : '' }}">
                    <i class="fas fa-sort"></i>
                    <span>ترتيب الأقسام</span>
                </a>
                <a href="{{ route('dashboard', ['page' => 'menu']) }}" class="menu-item {{ $page == 'menu' ? 'active' : '' }}">
                    <i class="fas fa-bars"></i>
                    <span>القائمة العلوية</span>
                </a>
                <a href="{{ route('dashboard', ['page' => 'board-members']) }}" class="menu-item {{ $page == 'board-members' ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>مجلس الإدارة</span>
                </a>
                <a href="{{ route('dashboard', ['page' => 'executive-director']) }}" class="menu-item {{ $page == 'executive-director' ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i>
                    <span>المدير التنفيذي</span>
                </a>
                <a href="{{ route('dashboard', ['page' => 'staff']) }}" class="menu-item {{ $page == 'staff' ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i>
                    <span>الموظفين</span>
                </a>
                <a href="{{ route('dashboard', ['page' => 'files']) }}" class="menu-item {{ $page == 'files' ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>الملفات</span>
                </a>
                <a href="{{ route('dashboard', ['page' => 'reports']) }}" class="menu-item {{ $page == 'reports' ? 'active' : '' }}">
                    <i class="fas fa-file-pdf"></i>
                    <span>التقارير</span>
                </a>
                <a href="{{ route('dashboard', ['page' => 'policies']) }}" class="menu-item {{ $page == 'policies' ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>اللوائح والسياسات</span>
                </a>
                <a href="{{ route('dashboard', ['page' => 'projects']) }}" class="menu-item {{ $page == 'projects' ? 'active' : '' }}">
                    <i class="fas fa-project-diagram"></i>
                    <span>مشاريعنا</span>
                </a>
                <a href="{{ route('dashboard', ['page' => 'testimonials']) }}" class="menu-item {{ $page == 'testimonials' ? 'active' : '' }}">
                    <i class="fas fa-quote-left"></i>
                    <span>ماذا قالوا عنا</span>
                </a>
                <a href="{{ route('dashboard', ['page' => 'news']) }}" class="menu-item {{ $page == 'news' ? 'active' : '' }}">
                    <i class="fas fa-newspaper"></i>
                    <span>الأخبار</span>
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ Auth::user()->name ?? 'المدير' }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin-top: 1rem;">
                    @csrf
                    <button type="submit" class="menu-item" style="width: 100%; border: none; background: transparent; cursor: pointer; color: var(--text-secondary);">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>تسجيل الخروج</span>
                    </button>
                </form>
                
                <!-- حقوق المبرمج -->
                <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.1); text-align: center;">
                    <p style="color: var(--text-secondary); font-size: 0.75rem; margin: 0.5rem 0; line-height: 1.6;">
                        برمجة
                        <a href="https://twitter.com/f88fa" target="_blank" rel="noopener noreferrer" style="color: var(--primary-color); text-decoration: none; font-weight: 600; transition: color 0.3s ease; display: inline-flex; align-items: center;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="width: 1em; height: 1em; vertical-align: -0.125em; margin: 0 0.25rem;">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                            فارس التريباني
                        </a>
                    </p>
                    <p style="color: var(--text-secondary); font-size: 0.7rem; margin: 0; opacity: 0.7;">
                        &copy; {{ date('Y') }} جميع الحقوق محفوظة
                    </p>
                </div>
            </div>
        </aside>

        <!-- المحتوى الرئيسي -->
        <main class="main-content">
            @switch($page)
                @case('home')
                    @include('dashboard.pages.home')
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
                    @include('dashboard.pages.settings')
            @endswitch
        </main>
    </div>

    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>

