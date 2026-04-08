<!-- قسم الأخبار -->
@if(isset($settings['section_news_visible']) && $settings['section_news_visible'] == '1')
@php
    $newsBg = $settings['section_news_bg_color'] ?? '#FFFFFF';
    $newsText = $settings['section_news_text_color'] ?? '#0F3D2E';
    $newsTitle = $settings['section_news_title_color'] ?? '#5FB38E';
    $newsIcon = $settings['section_news_icon_color'] ?? '#5FB38E';
    $newsCardBg = $settings['section_news_card_bg_color'] ?? '#FFFFFF';
    $newsCardTitle = $settings['section_news_card_title_color'] ?? '#5FB38E';
    $newsHover = $settings['section_news_hover_text_color'] ?? '#5FB38E';
    $newsSecStyle = '--sec-bg: '.$newsBg.'; --sec-text: '.$newsText.'; --sec-title: '.$newsTitle.'; --sec-icon: '.$newsIcon.'; --sec-card-bg: '.$newsCardBg.'; --sec-card-title: '.$newsCardTitle.'; --sec-hover-text: '.$newsHover.'; background-color: '.$newsBg.' !important;';
    if (!empty($settings['section_news_bg_image'])) $newsSecStyle .= ' background-image: url(\''.e(image_asset_url($settings['section_news_bg_image'])).'\'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;';
@endphp
<section id="news" class="news-section" style="{{ $newsSecStyle }}">
    @if(!empty($settings['section_news_bg_image']))
    <div class="section-background-overlay" style="opacity: {{ (100 - ($settings['section_news_bg_opacity'] ?? 30)) / 100 }};"></div>
    @endif
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="{{ $settings['section_news_icon'] ?? 'fas fa-newspaper' }}"></i>
                {{ $settings['section_news_title'] ?? 'آخر الأخبار' }}
            </h2>
            <p class="section-description">تابع آخر التحديثات والأخبار</p>
        </div>
        
        @if($news->count() > 0)
            <div class="news-grid">
                @foreach($news as $item)
                    <article class="news-card">
                        <a href="{{ route('frontend.news.article', $item->id) }}" style="text-decoration: none; color: inherit; display: block;">
                            @if($item->image)
                                <div class="news-card-image">
                                    <img src="{{ image_asset_url($item->image) }}" 
                                         alt="{{ $item->title }}">
                                    <div class="news-card-overlay"></div>
                                </div>
                            @endif
                            <div class="news-card-content">
                                <div class="news-card-date">
                                    <i class="fas fa-calendar"></i>
                                    <span>{{ $item->created_at->format('Y-m-d') }}</span>
                                </div>
                                <h3 class="news-card-title">{{ $item->title }}</h3>
                                <p class="news-card-excerpt">{{ news_excerpt($item->content ?? '', 150) }}</p>
                                <div class="news-card-link" style="margin-top: 1rem; color: var(--sec-hover-text, var(--primary-color)); font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                                    <span>اقرأ المزيد</span>
                                    <i class="fas fa-arrow-left"></i>
                                </div>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        @else
            <div class="news-placeholder">
                <i class="fas fa-newspaper"></i>
                <p>لا توجد أخبار متاحة حالياً</p>
            </div>
        @endif
    </div>
</section>
@endif

