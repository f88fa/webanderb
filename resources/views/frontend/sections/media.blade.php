<!-- المركز الإعلامي -->
@if(((isset($mediaVideos) && $mediaVideos->count() > 0) || (isset($mediaSlides) && $mediaSlides->count() > 0)) && isset($settings['section_media_visible']) && $settings['section_media_visible'] == '1')
@php
    $mediaSecStyle = '--sec-bg: '.($settings['section_media_bg_color'] ?? '#FFFFFF').'; --sec-text: '.($settings['section_media_text_color'] ?? '#0F3D2E').'; --sec-title: '.($settings['section_media_title_color'] ?? '#5FB38E').'; --sec-icon: '.($settings['section_media_icon_color'] ?? '#5FB38E').'; --sec-card-bg: '.($settings['section_media_card_bg_color'] ?? '#FFFFFF').'; --sec-card-title: '.($settings['section_media_card_title_color'] ?? '#5FB38E').'; --sec-hover-text: '.($settings['section_media_hover_text_color'] ?? '#5FB38E').';';
    if (!empty($settings['section_media_bg_image'])) $mediaSecStyle .= ' background-image: url(\''.e(image_asset_url($settings['section_media_bg_image'])).'\'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;';
@endphp
<section id="media" class="media-section" style="{{ $mediaSecStyle }}">
    @if(!empty($settings['section_media_bg_image']))
    <div class="section-background-overlay" style="opacity: {{ (100 - ($settings['section_media_bg_opacity'] ?? 30)) / 100 }};"></div>
    @endif
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="{{ $settings['section_media_icon'] ?? 'fas fa-video' }}"></i>
                {{ $settings['section_media_title'] ?? 'محتوى إعلامي مميز' }}
            </h2>
            <p class="section-description">استكشف مقاطع الفيديو والصور</p>
        </div>

        <!-- قسم مقاطع اليوتيوب -->
        @if(isset($mediaVideos) && $mediaVideos->count() > 0)
        <div class="media-videos-section">
            <h3 class="media-subtitle">
                <i class="fab fa-youtube"></i> مقاطع الفيديو
            </h3>
            <div class="videos-grid">
                @foreach($mediaVideos as $video)
                <div class="video-card">
                    <div class="video-thumbnail-wrapper">
                        @if($video->thumbnail)
                            <img src="{{ image_asset_url($video->thumbnail) }}" alt="{{ $video->title }}" class="video-thumbnail">
                        @else
                            <img src="https://img.youtube.com/vi/{{ $video->youtube_id }}/maxresdefault.jpg" alt="{{ $video->title }}" class="video-thumbnail">
                        @endif
                        <div class="video-play-overlay">
                            <i class="fas fa-play"></i>
                        </div>
                        <a href="{{ $video->youtube_url }}" target="_blank" rel="noopener noreferrer" class="video-link"></a>
                    </div>
                    <h4 class="video-title">{{ $video->title }}</h4>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- قسم السلايدر -->
        @if(isset($mediaSlides) && $mediaSlides->count() > 0)
        <div class="media-slider-section">
            <h3 class="media-subtitle">
                <i class="fas fa-images"></i> معرض الصور
            </h3>
            <div class="slides-container">
                <div class="slides-wrapper" id="mediaSlides">
                    @foreach($mediaSlides as $index => $slide)
                    <div class="slide-item" data-type="{{ $slide->type }}" data-index="{{ $index }}">
                        @if($slide->type === 'video')
                            @if($slide->isYouTube())
                                <div class="slide-video-wrapper">
                                    <iframe 
                                        id="slideVideo{{ $index }}"
                                        class="slide-video"
                                        src="https://www.youtube.com/embed/{{ $slide->youtube_id }}?autoplay=1&loop=1&playlist={{ $slide->youtube_id }}&mute=1&controls=0&showinfo=0&rel=0&modestbranding=1"
                                        frameborder="0"
                                        allow="autoplay; encrypted-media"
                                        allowfullscreen>
                                    </iframe>
                                    <div class="slide-video-overlay"></div>
                                </div>
                            @else
                                <div class="slide-video-wrapper">
                                    <video 
                                        id="slideVideo{{ $index }}"
                                        class="slide-video"
                                        autoplay
                                        loop
                                        muted
                                        playsinline>
                                        <source src="{{ $slide->video_url }}" type="video/mp4">
                                        متصفحك لا يدعم تشغيل الفيديو.
                                    </video>
                                    <div class="slide-video-overlay"></div>
                                </div>
                            @endif
                        @else
                            @if($slide->link)
                                <a href="{{ $slide->link }}" target="_blank" rel="noopener noreferrer" class="slide-link">
                            @endif
                            <img src="{{ image_asset_url($slide->image) }}" alt="{{ $slide->title ?? 'صورة' }}" class="slide-image">
                            @if($slide->link)
                                </a>
                            @endif
                        @endif
                        @if($slide->title || $slide->description)
                        <div class="slide-content">
                            @if($slide->title)
                                <h4 class="slide-title">{{ $slide->title }}</h4>
                            @endif
                            @if($slide->description)
                                <p class="slide-description">{{ $slide->description }}</p>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                <button class="slide-nav slide-prev" onclick="moveSlide(-1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <button class="slide-nav slide-next" onclick="moveSlide(1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="slide-indicators">
                    @foreach($mediaSlides as $index => $slide)
                    <span class="slide-indicator {{ $index === 0 ? 'active' : '' }}" onclick="goToSlide({{ $index }})"></span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
@endif

