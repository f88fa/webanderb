<!-- قسم شركاؤنا -->
@if(isset($partners) && $partners->count() > 0 && isset($settings['section_partners_visible']) && $settings['section_partners_visible'] == '1')
@php
    $partSecStyle = '--sec-bg: '.($settings['section_partners_bg_color'] ?? '#FFFFFF').'; --sec-text: '.($settings['section_partners_text_color'] ?? '#0F3D2E').'; --sec-title: '.($settings['section_partners_title_color'] ?? '#5FB38E').'; --sec-icon: '.($settings['section_partners_icon_color'] ?? '#5FB38E').'; --sec-card-bg: '.($settings['section_partners_card_bg_color'] ?? '#FFFFFF').'; --sec-card-title: '.($settings['section_partners_card_title_color'] ?? '#5FB38E').'; --sec-hover-text: '.($settings['section_partners_hover_text_color'] ?? '#5FB38E').';';
    if (!empty($settings['section_partners_bg_image'])) $partSecStyle .= ' background-image: url(\''.e(image_asset_url($settings['section_partners_bg_image'])).'\'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;';
@endphp
<section id="partners" class="partners-section" style="{{ $partSecStyle }}">
    @if(!empty($settings['section_partners_bg_image']))
    <div class="section-background-overlay" style="opacity: {{ (100 - ($settings['section_partners_bg_opacity'] ?? 30)) / 100 }};"></div>
    @endif
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="{{ $settings['section_partners_icon'] ?? 'fas fa-handshake' }}"></i>
                {{ $settings['section_partners_title'] ?? 'شركاؤنا الاستراتيجيون' }}
            </h2>
            <p class="section-description">نفتخر بشراكاتنا مع المؤسسات الرائدة</p>
        </div>
        
        <div class="partners-grid">
            @foreach($partners as $partner)
            <div class="partner-card">
                @if($partner->logo)
                    @if($partner->website)
                        <a href="{{ $partner->website }}" target="_blank" rel="noopener noreferrer" class="partner-link">
                            <img src="{{ image_asset_url($partner->logo) }}" alt="{{ $partner->name }}" class="partner-logo">
                        </a>
                    @else
                        <img src="{{ image_asset_url($partner->logo) }}" alt="{{ $partner->name }}" class="partner-logo">
                    @endif
                @else
                    <div class="partner-placeholder">
                        <i class="fas fa-building"></i>
                        <span>{{ $partner->name }}</span>
                    </div>
                @endif
                <div class="partner-overlay"></div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

