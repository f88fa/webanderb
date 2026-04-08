<!-- قسم ماذا قالوا عنا -->
@if(isset($testimonials) && $testimonials->count() > 0 && isset($settings['section_testimonials_visible']) && $settings['section_testimonials_visible'] == '1')
@php
    $testSecStyle = '--sec-bg: '.($settings['section_testimonials_bg_color'] ?? '#FFFFFF').'; --sec-text: '.($settings['section_testimonials_text_color'] ?? '#0F3D2E').'; --sec-title: '.($settings['section_testimonials_title_color'] ?? '#5FB38E').'; --sec-icon: '.($settings['section_testimonials_icon_color'] ?? '#5FB38E').'; --sec-card-bg: '.($settings['section_testimonials_card_bg_color'] ?? '#FFFFFF').'; --sec-card-title: '.($settings['section_testimonials_card_title_color'] ?? '#5FB38E').'; --sec-hover-text: '.($settings['section_testimonials_hover_text_color'] ?? '#5FB38E').';';
    if (!empty($settings['section_testimonials_bg_image'])) $testSecStyle .= ' background-image: url(\''.e(image_asset_url($settings['section_testimonials_bg_image'])).'\'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;';
@endphp
<section id="testimonials" class="testimonials-section" style="{{ $testSecStyle }}">
    @if(!empty($settings['section_testimonials_bg_image']))
    <div class="section-background-overlay" style="opacity: {{ (100 - ($settings['section_testimonials_bg_opacity'] ?? 30)) / 100 }};"></div>
    @endif
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="{{ $settings['section_testimonials_icon'] ?? 'fas fa-quote-right' }}"></i>
                {{ $settings['section_testimonials_title'] ?? 'آراء عملائنا' }}
            </h2>
            <p class="section-description">نفتخر بثقة عملائنا وشركائنا</p>
        </div>
        
        <div class="testimonials-grid">
            @foreach($testimonials as $testimonial)
            <div class="testimonial-card">
                <div class="testimonial-content">
                    <div class="testimonial-quote-icon">
                        <i class="fas fa-quote-right"></i>
                    </div>
                    <p class="testimonial-text">{{ $testimonial->text }}</p>
                </div>
                <div class="testimonial-author">
                    <div class="testimonial-image-wrapper">
                        @if($testimonial->image)
                            <img src="{{ image_asset_url($testimonial->image) }}" alt="{{ $testimonial->name }}" class="testimonial-image">
                        @else
                            <div class="testimonial-default-icon">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                    </div>
                    <div class="testimonial-author-info">
                        <h4 class="testimonial-name">{{ $testimonial->name }}</h4>
                    </div>
                </div>
                <div class="testimonial-decoration"></div>
                <div class="testimonial-hover-effect"></div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

