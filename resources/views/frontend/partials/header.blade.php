<!-- بطاقة اللوقو المنفصلة -->
<div class="logo-card-wrapper">
    @php
        $logoBgType = $settings['logo_background_type'] ?? 'white';
        $logoBgColor = $settings['logo_background_color'] ?? '#FFFFFF';
    @endphp
    <a href="{{ url('/') }}" class="logo-card logo-bg-{{ $logoBgType }}" id="logo-card"
       style="@if($logoBgType == 'custom')background: {{ $logoBgColor }} !important;@elseif($logoBgType == 'gradient')background: var(--gradient-1) !important;@elseif($logoBgType == 'transparent')background: transparent !important;@endif">
        @if(!empty($settings['site_logo']))
            <!-- استخدام شعار الموقع إذا كان موجوداً -->
            <img src="{{ image_asset_url($settings['site_logo']) }}" alt="شعار الموقع" class="logo-card-image">
        @elseif(!empty($settings['site_icon_file']))
            <!-- استخدام أيقونة الموقع إذا كان موجوداً -->
            <div class="logo-card-icon">
                <img src="{{ image_asset_url($settings['site_icon_file']) }}" alt="أيقونة الموقع">
            </div>
        @else
            <!-- استخدام أيقونة افتراضية -->
            <div class="logo-card-icon">
                <i class="{{ $settings['site_icon'] ?? 'fas fa-rocket' }}"></i>
            </div>
        @endif
    </a>
</div>

<!-- زر القائمة للجوال (منفصل) -->
<div class="mobile-nav-toggle">
    <span></span>
    <span></span>
    <span></span>
</div>

<!-- القائمة المنسدلة للجوال (منفصلة) -->
<div class="nav-menu-wrapper mobile-menu-wrapper">
    <ul class="nav-menu nav-menu-single-row">
        @if(isset($menuItems) && $menuItems->count() > 0)
            @foreach($menuItems as $menuItem)
                @include('frontend.menu-item', ['item' => $menuItem])
            @endforeach
        @else
            <li><a href="#about" class="nav-link">من نحن</a></li>
            <li><a href="#news" class="nav-link">الأخبار</a></li>
            <li><a href="#contact" class="nav-link">اتصل بنا</a></li>
        @endif
    </ul>
</div>

<!-- شريط علوي -->
<nav class="navbar">
    <div class="container">
        <!-- القائمة في المنتصف - سطر واحد -->
        <div class="nav-menu-wrapper desktop-menu-wrapper">
            <ul class="nav-menu nav-menu-single-row">
                @if(isset($menuItems) && $menuItems->count() > 0)
                    @foreach($menuItems as $menuItem)
                        @include('frontend.menu-item', ['item' => $menuItem])
                    @endforeach
                @else
                    <li><a href="#about" class="nav-link">من نحن</a></li>
                    <li><a href="#news" class="nav-link">الأخبار</a></li>
                    <li><a href="#contact" class="nav-link">اتصل بنا</a></li>
                @endif
            </ul>
        </div>

        <div class="nav-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</nav>

