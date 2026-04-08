<!-- قسم خدماتنا -->
@if(isset($services) && $services->count() > 0 && isset($settings['section_services_visible']) && $settings['section_services_visible'] == '1')
@php
    $svcSecStyle = '--sec-bg: '.($settings['section_services_bg_color'] ?? '#FFFFFF').'; --sec-text: '.($settings['section_services_text_color'] ?? '#0F3D2E').'; --sec-title: '.($settings['section_services_title_color'] ?? '#5FB38E').'; --sec-icon: '.($settings['section_services_icon_color'] ?? '#5FB38E').'; --sec-card-bg: '.($settings['section_services_card_bg_color'] ?? '#FFFFFF').'; --sec-card-title: '.($settings['section_services_card_title_color'] ?? '#5FB38E').'; --sec-hover-text: '.($settings['section_services_hover_text_color'] ?? '#5FB38E').';';
    if (!empty($settings['section_services_bg_image'])) $svcSecStyle .= ' background-image: url(\''.e(image_asset_url($settings['section_services_bg_image'])).'\'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;';
@endphp
<section id="services" class="services-section" style="{{ $svcSecStyle }}">
    @if(!empty($settings['section_services_bg_image']))
    <div class="section-background-overlay" style="opacity: {{ (100 - ($settings['section_services_bg_opacity'] ?? 30)) / 100 }};"></div>
    @endif
    <div class="container">
        <div class="section-header" style="margin-bottom: 3rem;">
            <h2 class="section-title services-section-title">
                <i class="{{ $settings['section_services_icon'] ?? 'fas fa-concierge-bell' }}"></i>
                {{ $settings['section_services_title'] ?? 'خدماتنا المميزة' }}
            </h2>
            <p class="section-description">{{ $settings['section_services_description'] ?? 'نقدم لكم أفضل الخدمات بجودة عالية' }}</p>
        </div>
        
        <div class="services-grid">
            @foreach($services as $service)
            <div class="service-card">
                <div class="service-icon-wrapper">
                    <i class="{{ $service->icon }}"></i>
                    <div class="icon-background"></div>
                    <div class="icon-glow-effect"></div>
                </div>
                <h3 class="service-title">{{ $service->title }}</h3>
                @if($service->description)
                <p class="service-description">{{ $service->description }}</p>
                @endif
                <div class="service-decoration"></div>
                <div class="service-hover-effect"></div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
