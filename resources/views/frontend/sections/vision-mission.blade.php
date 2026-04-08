<!-- قسم الرؤية والرسالة -->
@if($visionMission && ($visionMission->vision || $visionMission->mission) && isset($settings['section_vision_mission_visible']) && $settings['section_vision_mission_visible'] == '1')
@php
    $vmSecStyle = '--sec-bg: '.($settings['section_vision_mission_bg_color'] ?? '#FFFFFF').'; --sec-text: '.($settings['section_vision_mission_text_color'] ?? '#0F3D2E').'; --sec-title: '.($settings['section_vision_mission_title_color'] ?? '#5FB38E').'; --sec-icon: '.($settings['section_vision_mission_icon_color'] ?? '#5FB38E').'; --sec-card-bg: '.($settings['section_vision_mission_card_bg_color'] ?? '#FFFFFF').'; --sec-card-title: '.($settings['section_vision_mission_card_title_color'] ?? '#5FB38E').'; --sec-hover-text: '.($settings['section_vision_mission_hover_text_color'] ?? '#5FB38E').';';
    if (!empty($settings['section_vision_mission_bg_image'])) $vmSecStyle .= ' background-image: url(\''.e(image_asset_url($settings['section_vision_mission_bg_image'])).'\'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;';
@endphp
<section id="vision-mission" class="vision-mission-section" style="{{ $vmSecStyle }}">
    @if(!empty($settings['section_vision_mission_bg_image']))
    <div class="section-background-overlay" style="opacity: {{ (100 - ($settings['section_vision_mission_bg_opacity'] ?? 30)) / 100 }};"></div>
    @endif
    <div class="container">
        <div class="section-header" style="margin-bottom: 3rem;">
            <h2 class="section-title vision-mission-title">
                <i class="{{ $settings['section_vision_mission_icon'] ?? 'fas fa-eye' }}"></i>
                {{ $visionMission->section_title ?? 'رؤيتنا ورسالتنا' }}
            </h2>
            <p class="section-description">نحو مستقبل أفضل</p>
        </div>
        <div class="vision-mission-wrapper">
            @if($visionMission->vision)
            <div class="vision-card">
                <div class="particles">
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                </div>
                <div class="corner-glow"></div>
                <div class="corner-glow"></div>
                <div class="vision-icon-wrapper">
                    <i class="{{ $visionMission->vision_icon ?? 'fas fa-eye' }}"></i>
                    <div class="icon-glow"></div>
                </div>
                <h3 class="vision-title">رؤيتنا</h3>
                <div class="vision-content">
                    <p>{{ $visionMission->vision }}</p>
                </div>
                <div class="card-decoration"></div>
            </div>
            @endif

            @if($visionMission->mission)
            <div class="mission-card">
                <div class="particles">
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                    <div class="particle"></div>
                </div>
                <div class="corner-glow"></div>
                <div class="corner-glow"></div>
                <div class="mission-icon-wrapper">
                    <i class="{{ $visionMission->mission_icon ?? 'fas fa-bullseye' }}"></i>
                    <div class="icon-glow"></div>
                </div>
                <h3 class="mission-title">رسالتنا</h3>
                <div class="mission-content">
                    <p>{{ $visionMission->mission }}</p>
                </div>
                <div class="card-decoration"></div>
            </div>
            @endif
        </div>
    </div>
</section>
@endif
