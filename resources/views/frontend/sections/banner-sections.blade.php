<!-- أقسام البانر -->
@if(isset($bannerSections) && $bannerSections->count() > 0 && isset($settings['section_banner_sections_visible']) && $settings['section_banner_sections_visible'] == '1')
@foreach($bannerSections as $banner)
@php
    $bgType = $banner->background_type ?? 'white';
@endphp
<section class="banner-section {{ $bgType == 'white' ? 'banner-section-white' : 'banner-section-site' }}">
    @if($bgType == 'white')
    <div class="banner-background-pattern"></div>
    @endif
    <div class="container">
        <div class="banner-wrapper">
            @if($banner->link)
                <a href="{{ $banner->link }}" target="_blank" rel="noopener noreferrer" class="banner-link">
            @endif
            <div class="banner-content">
                @if(!empty($banner->title))
                <h2 class="banner-title">{{ $banner->title }}</h2>
                @endif
                @if($banner->youtube_video_id)
                    <div class="banner-video-container banner-youtube-container">
                        <iframe class="banner-youtube-iframe" src="https://www.youtube.com/embed/{{ $banner->youtube_video_id }}?autoplay=1&mute=1&loop=1&playlist={{ $banner->youtube_video_id }}&controls=0&showinfo=0&rel=0&modestbranding=1" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        <div class="banner-overlay"></div>
                    </div>
                @elseif(!empty($banner->video))
                    <div class="banner-video-container">
                        <video class="banner-video" autoplay muted loop playsinline preload="auto"
                               style="width: 100%; height: 100%; object-fit: cover;">
                            <source src="{{ image_asset_url($banner->video) }}" type="video/mp4">
                            <source src="{{ image_asset_url($banner->video) }}" type="video/webm">
                            <source src="{{ image_asset_url($banner->video) }}" type="video/ogg">
                        </video>
                        <div class="banner-overlay"></div>
                    </div>
                @elseif($banner->image)
                    <div class="banner-image-container">
                        <img src="{{ image_asset_url($banner->image) }}" alt="{{ $banner->title }}" class="banner-image">
                        <div class="banner-overlay"></div>
                    </div>
                @endif
            </div>
            @if($banner->link)
                </a>
            @endif
        </div>
    </div>
</section>
@endforeach
@endif
