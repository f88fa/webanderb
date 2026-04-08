<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $project->name }} - {{ $settings['site_name'] ?? 'الموقع' }}</title>
    <meta name="description" content="{{ mb_substr(strip_tags($project->description ?? ''), 0, 160) }}">
    
    <!-- Favicon - استخدام نفس أيقونة الهيرو -->
    @if(!empty($settings['site_icon_file']))
        <link rel="icon" type="image/png" href="{{ image_asset_url($settings['site_icon_file']) }}">
        <link rel="shortcut icon" type="image/png" href="{{ image_asset_url($settings['site_icon_file']) }}">
        <link rel="apple-touch-icon" href="{{ image_asset_url($settings['site_icon_file']) }}">
    @endif
    
    <link rel="stylesheet" href="{{ asset('assets/css/frontend.css') }}?v={{ $settings['settings_updated_at'] ?? '1' }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: {{ $settings['site_primary_color'] ?? '#5FB38E' }};
            --primary-dark: {{ $settings['site_primary_dark'] ?? '#1F6B4F' }};
            --secondary-color: {{ $settings['site_secondary_color'] ?? '#A8DCC3' }};
            --accent-color: {{ $settings['site_accent_color'] ?? '#5FB38E' }};
            --gradient-1: linear-gradient(135deg, {{ $settings['site_primary_color'] ?? '#5FB38E' }} 0%, {{ $settings['site_primary_dark'] ?? '#1F6B4F' }} 100%);
            --gradient-2: linear-gradient(135deg, {{ $settings['site_secondary_color'] ?? '#A8DCC3' }} 0%, {{ $settings['site_primary_color'] ?? '#5FB38E' }} 100%);
            --gradient-3: linear-gradient(135deg, {{ $settings['site_secondary_color'] ?? '#A8DCC3' }} 0%, {{ $settings['site_primary_color'] ?? '#5FB38E' }} 100%);
            --shadow-glow: 0 0 30px rgba(95, 179, 142, 0.3);
        }
        
        .project-article-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f3d2e 0%, #1a1a2e 100%);
            padding-top: 80px;
        }
        
        .article-header {
            background: linear-gradient(135deg, rgba(95, 179, 142, 0.1) 0%, rgba(31, 107, 79, 0.1) 100%);
            padding: 4rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .article-breadcrumb {
            margin-bottom: 2rem;
        }
        
        .article-breadcrumb a {
            color: var(--secondary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .article-breadcrumb a:hover {
            color: var(--primary-color);
        }
        
        .article-breadcrumb i {
            margin: 0 0.5rem;
            color: rgba(255, 255, 255, 0.5);
        }
        
        .article-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            line-height: 1.3;
        }
        
        .article-featured-image {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 20px;
            margin: 3rem 0;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.1);
        }
        
        .article-content-wrapper {
            max-width: 900px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }
        
        .article-content {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            padding: 3rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            line-height: 2;
            font-size: 1.15rem;
            color: var(--text-primary);
        }
        
        .article-content p {
            margin-bottom: 1.5rem;
        }
        
        .back-to-projects {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            background: rgba(95, 179, 142, 0.1);
            border-radius: 25px;
            border: 1px solid rgba(95, 179, 142, 0.3);
            transition: all 0.3s ease;
        }
        
        .back-to-projects:hover {
            background: var(--primary-color);
            color: white;
            transform: translateX(-5px);
        }
        
        .article-footer {
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .related-projects-section {
            background: rgba(255, 255, 255, 0.03);
            padding: 4rem 0;
            margin-top: 4rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .related-projects-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 2rem;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .article-title {
                font-size: 1.75rem;
            }
            
            .article-content-wrapper {
                padding: 2rem 1rem;
            }
            
            .article-content {
                padding: 2rem 1.5rem;
                font-size: 1rem;
            }
            
            .article-footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    @include('frontend.partials.header')

    <div class="project-article-page">
        <!-- Article Header -->
        <div class="article-header">
            <div class="container">
                <div class="article-breadcrumb">
                    <a href="{{ url('/') }}"><i class="fas fa-home"></i> الرئيسية</a>
                    <i class="fas fa-chevron-left"></i>
                    <a href="{{ url('/#projects') }}">المشاريع</a>
                    <i class="fas fa-chevron-left"></i>
                    <span style="color: var(--text-secondary);">تفاصيل المشروع</span>
                </div>
                
                <h1 class="article-title">{{ $project->name }}</h1>
            </div>
        </div>

        <!-- Article Content -->
        <div class="container">
            @if($project->image)
                <img src="{{ image_asset_url($project->image) }}" 
                     alt="{{ $project->name }}" 
                     class="article-featured-image">
            @endif
            
            <div class="article-content-wrapper">
                <div class="article-content">
                    @if($project->description)
                        {!! $project->description !!}
                    @endif
                </div>
                
                <div class="article-footer">
                    <a href="{{ url('/#projects') }}" class="back-to-projects">
                        <i class="fas fa-arrow-right"></i>
                        <span>العودة إلى المشاريع</span>
                    </a>
                    
                    @if($project->donate_link)
                        <a href="{{ $project->donate_link }}" target="_blank" rel="noopener noreferrer" class="project-donate-btn" style="font-size: 1.1rem; padding: 1rem 2rem;">
                            <i class="fas fa-heart"></i>
                            <span>{{ $project->donate_button_text ?? 'تبرع الآن' }}</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Related Projects -->
        @if($relatedProjects->count() > 0)
        <div class="related-projects-section">
            <div class="container">
                <h2 class="related-projects-title">مشاريع أخرى</h2>
                <div class="projects-grid">
                    @foreach($relatedProjects as $relatedProject)
                        <div class="project-card">
                            @if($relatedProject->image)
                                <div class="project-image-wrapper">
                                    <img src="{{ image_asset_url($relatedProject->image) }}" alt="{{ $relatedProject->name }}" class="project-image">
                                    <div class="project-image-overlay"></div>
                                </div>
                            @endif
                            <div class="project-content">
                                <h3 class="project-name">{{ $relatedProject->name }}</h3>
                                @if($relatedProject->description)
                                    @php
                                        // Strip HTML tags for length check
                                        $plainText = strip_tags($relatedProject->description);
                                        $descLength = mb_strlen($plainText);
                                        $showFull = $descLength <= 200;
                                        $shortDesc = mb_substr($plainText, 0, 200);
                                    @endphp
                                    <div class="project-description">
                                        @if($showFull)
                                            {!! $relatedProject->description !!}
                                        @else
                                            {!! $shortDesc !!}...
                                        @endif
                                    </div>
                                @endif
                                <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1.5rem;">
                                    @if($relatedProject->description && mb_strlen(strip_tags($relatedProject->description)) > 200)
                                        <a href="{{ route('frontend.projects.article', $relatedProject->id) }}" class="project-details-btn">
                                            <i class="fas fa-info-circle"></i>
                                            <span>التفاصيل</span>
                                        </a>
                                    @endif
                                    @if($relatedProject->donate_link)
                                        <a href="{{ $relatedProject->donate_link }}" target="_blank" rel="noopener noreferrer" class="project-donate-btn">
                                            <i class="fas fa-heart"></i>
                                            <span>{{ $relatedProject->donate_button_text ?? 'تبرع الآن' }}</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    @include('frontend.partials.footer')

    <script src="{{ asset('assets/js/frontend.js') }}?v={{ $settings['settings_updated_at'] ?? '1' }}"></script>
</body>
</html>


