<!-- قسم مشاريعنا -->
@if(isset($projects) && $projects->count() > 0 && isset($settings['section_projects_visible']) && $settings['section_projects_visible'] == '1')
@php
    $projSecStyle = '--sec-bg: '.($settings['section_projects_bg_color'] ?? '#FFFFFF').'; --sec-text: '.($settings['section_projects_text_color'] ?? '#0F3D2E').'; --sec-title: '.($settings['section_projects_title_color'] ?? '#5FB38E').'; --sec-icon: '.($settings['section_projects_icon_color'] ?? '#5FB38E').'; --sec-card-bg: '.($settings['section_projects_card_bg_color'] ?? '#FFFFFF').'; --sec-card-title: '.($settings['section_projects_card_title_color'] ?? '#5FB38E').'; --sec-hover-text: '.($settings['section_projects_hover_text_color'] ?? '#5FB38E').'; --sec-button: '.($settings['section_projects_button_color'] ?? '#5FB38E').';';
    if (!empty($settings['section_projects_bg_image'])) $projSecStyle .= ' background-image: url(\''.e(image_asset_url($settings['section_projects_bg_image'])).'\'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;';
@endphp
<section id="projects" class="projects-section" style="{{ $projSecStyle }}">
    @if(!empty($settings['section_projects_bg_image']))
    <div class="section-background-overlay" style="opacity: {{ (100 - ($settings['section_projects_bg_opacity'] ?? 30)) / 100 }};"></div>
    @endif
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="{{ $settings['section_projects_icon'] ?? 'fas fa-project-diagram' }}"></i>
                {{ $settings['section_projects_title'] ?? 'مشاريعنا المميزة' }}
            </h2>
            <p class="section-description">اكتشف مشاريعنا وشارك في دعمها</p>
        </div>
        
        <div class="projects-grid">
            @foreach($projects as $project)
            <div class="project-card">
                @if($project->image)
                    <div class="project-image-wrapper">
                        <img src="{{ image_asset_url($project->image) }}" alt="{{ $project->name }}" class="project-image">
                        <div class="project-image-overlay"></div>
                    </div>
                @endif
                <div class="project-content">
                    <h3 class="project-name">{{ $project->name }}</h3>
                    @if($project->description)
                        @php
                            // Strip HTML tags for length check
                            $plainText = strip_tags($project->description);
                            $descriptionLength = mb_strlen($plainText);
                            $showFullDescription = $descriptionLength <= 200;
                            // For short description, strip HTML and show plain text
                            $shortDescription = mb_substr($plainText, 0, 200);
                        @endphp
                        <div class="project-description">
                            @if($showFullDescription)
                                {!! $project->description !!}
                            @else
                                {!! $shortDescription !!}...
                            @endif
                        </div>
                    @endif
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1.5rem;">
                        @if($project->description && mb_strlen(strip_tags($project->description)) > 200)
                            <a href="{{ route('frontend.projects.article', $project->id) }}" class="project-details-btn">
                                <i class="fas fa-info-circle"></i>
                                <span>التفاصيل</span>
                            </a>
                        @endif
                        @if($project->donate_link)
                            <a href="{{ $project->donate_link }}" target="_blank" rel="noopener noreferrer" class="project-donate-btn">
                                <i class="fas fa-heart"></i>
                                <span>{{ $project->donate_button_text ?? 'تبرع الآن' }}</span>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="project-decoration"></div>
                <div class="project-hover-effect"></div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

