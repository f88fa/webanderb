<!-- قسم من نحن -->
@if(isset($settings['section_about_visible']) && $settings['section_about_visible'] == '1' && isset($about) && $about)
@php
    $aboutSecStyle = '--sec-bg: '.($settings['section_about_bg_color'] ?? '#FFFFFF').'; --sec-text: '.($settings['section_about_text_color'] ?? '#0F3D2E').'; --sec-title: '.($settings['section_about_title_color'] ?? '#5FB38E').'; --sec-icon: '.($settings['section_about_icon_color'] ?? '#5FB38E').'; --sec-card-bg: '.($settings['section_about_card_bg_color'] ?? '#FFFFFF').'; --sec-card-title: '.($settings['section_about_card_title_color'] ?? '#5FB38E').'; --sec-hover-text: '.($settings['section_about_hover_text_color'] ?? '#5FB38E').';';
    if (!empty($settings['section_about_bg_image'])) $aboutSecStyle .= ' background-image: url(\''.e(image_asset_url($settings['section_about_bg_image'])).'\'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;';
@endphp
<section id="about" class="about-section" style="{{ $aboutSecStyle }}">
    @if(!empty($settings['section_about_bg_image']))
    <div class="section-background-overlay" style="opacity: {{ (100 - ($settings['section_about_bg_opacity'] ?? 30)) / 100 }};"></div>
    @endif
    <div class="about-background-pattern"></div>
    <div class="container">
        <div class="section-header" style="margin-bottom: 3rem;">
            <h2 class="section-title about-section-title">
                <i class="{{ $settings['section_about_icon'] ?? 'fas fa-users' }}"></i>
                {{ $about->section_title ?? 'من نحن' }}
            </h2>
            <p class="section-description">تعرف على من نحن وما نقدمه</p>
        </div>
        <div class="about-content">
            <div class="about-main-content">
                <!-- المحتوى النصي -->
                <div class="about-text-content">
                    <!-- الوصف -->
                    <div class="about-description">
                        {!! nl2br(e($about->content)) !!}
                    </div>
                    
                    <!-- المدير التنفيذي -->
                    @php
                        // Debug: Log values for troubleshooting
                        $executiveVisibleValue = $settings['executive_director_visible'] ?? 'NOT SET';
                        $executiveNameValue = $settings['executive_director_name'] ?? 'NOT SET';
                        $executiveImageValue = $settings['executive_director_image'] ?? 'NOT SET';
                        
                        $executiveVisible = isset($settings['executive_director_visible']) && ($settings['executive_director_visible'] == '1' || $settings['executive_director_visible'] == 1);
                        $executiveName = isset($settings['executive_director_name']) ? trim($settings['executive_director_name']) : '';
                        $executivePosition = isset($settings['executive_director_position']) ? trim($settings['executive_director_position']) : '';
                        $executiveImage = isset($settings['executive_director_image']) ? trim($settings['executive_director_image']) : '';
                    @endphp
                    {{-- Debug output (remove after testing) --}}
                    {{-- 
                    <div style="background: yellow; padding: 10px; margin: 10px 0;">
                        Visible: {{ $executiveVisibleValue }} ({{ $executiveVisible ? 'TRUE' : 'FALSE' }})<br>
                        Name: {{ $executiveNameValue }}<br>
                        Image: {{ $executiveImageValue }}
                    </div>
                    --}}
                    @if($executiveVisible && !empty($executiveName))
                    <div class="executive-director-signature" style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid rgba(95, 179, 142, 0.2); display: flex; align-items: center; gap: 1rem; flex-direction: row-reverse;">
                        @if(!empty($executiveImage))
                        <div class="executive-director-image-wrapper" style="flex-shrink: 0;">
                            <img src="{{ image_asset_url($executiveImage) }}" 
                                 alt="{{ $executiveName }}" 
                                 class="executive-director-image"
                                 style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(95, 179, 142, 0.3); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                        </div>
                        @endif
                        <div class="executive-director-info" style="flex: 1;">
                            @if(!empty($executivePosition))
                            <div class="executive-director-label" style="color: rgba(15, 61, 46, 0.7); font-size: 0.9rem; margin-bottom: 0.5rem; font-weight: 600;">
                                {{ $executivePosition }}
                            </div>
                            @else
                            <div class="executive-director-label" style="color: rgba(15, 61, 46, 0.7); font-size: 0.9rem; margin-bottom: 0.5rem; font-weight: 600;">
                                المدير التنفيذي
                            </div>
                            @endif
                            <div class="executive-director-name" style="color: var(--primary-dark); font-size: 1.2rem; font-weight: 700;">
                                {{ $executiveName }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- الإحصائيات -->
                    @if(isset($aboutStats) && $aboutStats->count() > 0)
                    <div class="about-stats">
                        @foreach($aboutStats as $stat)
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="{{ $stat->icon ?? 'fas fa-star' }}"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $stat->number }}</div>
                                <div class="stat-label">{{ $stat->label }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- أهدافنا -->
                    @if(isset($aboutFeatures) && $aboutFeatures->count() > 0)
                    <div class="about-features-section">
                        <div class="about-features-header">
                            <h3 class="about-features-title">
                                <i class="{{ $settings['section_about_features_icon'] ?? 'fas fa-bullseye' }}"></i>
                                {{ $settings['section_about_features_title'] ?? 'أهدافنا' }}
                            </h3>
                            @if(!empty($settings['section_about_features_description']))
                            <p class="about-features-description">{{ $settings['section_about_features_description'] }}</p>
                            @endif
                        </div>
                        <div class="about-features-grid">
                            @foreach($aboutFeatures as $feature)
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="{{ $feature->icon ?? 'fas fa-check-circle' }}"></i>
                                </div>
                                <div class="feature-content">
                                    <h3 class="feature-title">{{ $feature->title }}</h3>
                                    <p class="feature-text">{{ $feature->text }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- زر الدعوة للإجراء -->
                    @if($about->cta_text)
                    <div class="about-cta-wrapper">
                        <a href="{{ $about->cta_link ?? '#contact' }}" class="about-cta-btn">
                            <span class="cta-text">{{ $about->cta_text }}</span>
                            <span class="cta-icon">
                                <i class="fas fa-arrow-left"></i>
                            </span>
                            <div class="cta-shine"></div>
                        </a>
                    </div>
                    @endif
                </div>

                <!-- الصورة -->
                <div class="about-image-wrapper">
                    @if($about->image)
                        <div class="about-image-container">
                            <div class="image-frame">
                                <img src="{{ image_asset_url($about->image) }}" 
                                     alt="{{ $about->title }}" 
                                     class="about-image">
                                <div class="image-overlay-gradient"></div>
                            </div>
                            <div class="image-decoration image-decoration-1"></div>
                            <div class="image-decoration image-decoration-2"></div>
                            <div class="image-decoration image-decoration-3"></div>
                            
                            <!-- بطاقة الإنجاز -->
                            @if(isset($aboutStats) && $aboutStats->count() > 0)
                            <div class="achievement-card">
                                <div class="achievement-icon">
                                    <i class="{{ $aboutStats->first()->icon ?? 'fas fa-trophy' }}"></i>
                                </div>
                                <div class="achievement-content">
                                    <div class="achievement-number">{{ $aboutStats->first()->number }}</div>
                                    <div class="achievement-label">{{ $aboutStats->first()->label }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="about-image-placeholder">
                            <div class="placeholder-icon">
                                <i class="fas fa-image"></i>
                            </div>
                            <p>لا توجد صورة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endif
