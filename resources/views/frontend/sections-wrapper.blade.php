@php
    $sectionMap = [
        'about' => 'about-section',
        'vision_mission' => 'vision-mission-section',
        'banner_sections' => 'banner-sections',
        'services' => 'services-section',
        'projects' => 'projects-section',
        'media' => 'media-section',
        'testimonials' => 'testimonials-section',
        'partners' => 'partners-section',
        'news' => 'news-section',
    ];
@endphp

@if(isset($sectionOrder) && $sectionOrder->count() > 0)
    @foreach($sectionOrder as $section)
        @if($section->section_key == 'about' && isset($settings['section_about_visible']) && $settings['section_about_visible'] == '1')
            @include('frontend.sections.about')
        @elseif($section->section_key == 'vision_mission' && isset($settings['section_vision_mission_visible']) && $settings['section_vision_mission_visible'] == '1')
            @include('frontend.sections.vision-mission')
        @elseif($section->section_key == 'banner_sections' && isset($settings['section_banner_sections_visible']) && $settings['section_banner_sections_visible'] == '1')
            @include('frontend.sections.banner-sections')
        @elseif($section->section_key == 'services' && isset($settings['section_services_visible']) && $settings['section_services_visible'] == '1')
            @include('frontend.sections.services')
        @elseif($section->section_key == 'projects' && isset($settings['section_projects_visible']) && $settings['section_projects_visible'] == '1')
            @include('frontend.sections.projects')
        @elseif($section->section_key == 'media' && isset($settings['section_media_visible']) && $settings['section_media_visible'] == '1')
            @include('frontend.sections.media')
        @elseif($section->section_key == 'testimonials' && isset($settings['section_testimonials_visible']) && $settings['section_testimonials_visible'] == '1')
            @include('frontend.sections.testimonials')
        @elseif($section->section_key == 'partners' && isset($settings['section_partners_visible']) && $settings['section_partners_visible'] == '1')
            @include('frontend.sections.partners')
        @elseif($section->section_key == 'news' && isset($settings['section_news_visible']) && $settings['section_news_visible'] == '1')
            @include('frontend.sections.news')
        @endif
    @endforeach
@else
    {{-- Fallback to default order if section order not set --}}
    @if(isset($settings['section_about_visible']) && $settings['section_about_visible'] == '1')
        @include('frontend.sections.about')
    @endif
    @if(isset($settings['section_vision_mission_visible']) && $settings['section_vision_mission_visible'] == '1')
        @include('frontend.sections.vision-mission')
    @endif
    @if(isset($settings['section_banner_sections_visible']) && $settings['section_banner_sections_visible'] == '1')
        @include('frontend.sections.banner-sections')
    @endif
    @if(isset($settings['section_services_visible']) && $settings['section_services_visible'] == '1')
        @include('frontend.sections.services')
    @endif
    @if(isset($settings['section_projects_visible']) && $settings['section_projects_visible'] == '1')
        @include('frontend.sections.projects')
    @endif
    @if(isset($settings['section_media_visible']) && $settings['section_media_visible'] == '1')
        @include('frontend.sections.media')
    @endif
    @if(isset($settings['section_testimonials_visible']) && $settings['section_testimonials_visible'] == '1')
        @include('frontend.sections.testimonials')
    @endif
    @if(isset($settings['section_partners_visible']) && $settings['section_partners_visible'] == '1')
        @include('frontend.sections.partners')
    @endif
    @if(isset($settings['section_news_visible']) && $settings['section_news_visible'] == '1')
        @include('frontend.sections.news')
    @endif
@endif
