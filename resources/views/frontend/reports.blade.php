<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التقارير - {{ $settings['site_name'] ?? 'الموقع' }}</title>
    
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
    </style>
</head>
<body>
    @include('frontend.partials.header')

    <!-- Reports Section -->
    <section class="reports-section" style="padding: 6rem 0; min-height: 80vh; position: relative;">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 4rem;">
                <span class="section-badge" style="display: inline-block; padding: 0.5rem 1.5rem; background: var(--primary-color); color: white; border-radius: 25px; font-size: 0.9rem; font-weight: 600; margin-bottom: 1rem;">التقارير</span>
                <h1 class="section-title" style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;">
                    التقارير
                </h1>
                <p class="section-description" style="font-size: 1.1rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto;">
                    تصفح تقاريرنا السنوية والتقارير المهمة
                </p>
            </div>

            @if($reports->count() > 0)
                <div class="reports-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
                    @foreach($reports as $report)
                        <div class="report-card" style="background: rgba(255, 255, 255, 0.05); border-radius: 20px; overflow: hidden; transition: all 0.4s ease; border: 1px solid rgba(255, 255, 255, 0.1); position: relative; display: flex; flex-direction: column; height: 100%;">
                            <div class="report-image-wrapper" style="position: relative; width: 100%; padding-bottom: 100%; overflow: hidden; flex-shrink: 0;">
                                @if($report->image)
                                    <img src="{{ image_asset_url($report->image) }}" alt="{{ $report->title }}" 
                                         style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;">
                                @else
                                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: var(--gradient-1); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-file-pdf" style="font-size: 4rem; color: white; opacity: 0.5;"></i>
                                    </div>
                                @endif
                                <div class="report-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.6) 100%); opacity: 0; transition: opacity 0.4s ease;"></div>
                            </div>
                            
                            <div class="report-content" style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1; justify-content: space-between;">
                                <h3 class="report-title" style="font-size: 1.2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; line-height: 1.4;">
                                    {{ $report->title }}
                                </h3>
                                
                                @if($report->link)
                                <a href="{{ $report->link }}" target="_blank" class="report-btn" 
                                   style="display: inline-flex; align-items: center; justify-content: center; gap: 0.75rem; padding: 0.875rem 1.5rem; background: var(--gradient-1); color: white; text-decoration: none; border-radius: 12px; font-weight: 600; transition: all 0.4s ease; box-shadow: 0 4px 15px rgba(95, 179, 142, 0.3); width: 100%;">
                                    <i class="fas fa-external-link-alt"></i>
                                    <span>عرض التقرير</span>
                                </a>
                                @endif
                            </div>
                            
                            <div class="report-decoration" style="position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: var(--primary-color); transform: scaleX(0); transition: transform 0.4s ease;"></div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state" style="text-align: center; padding: 4rem 2rem;">
                    <i class="fas fa-file-pdf" style="font-size: 4rem; color: rgba(255, 255, 255, 0.3); margin-bottom: 1.5rem;"></i>
                    <p style="font-size: 1.2rem; color: var(--text-secondary);">لا يوجد تقارير متاحة حالياً</p>
                </div>
            @endif
        </div>
    </section>

    <style>
        .report-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(95, 179, 142, 0.2);
            border-color: var(--primary-color);
        }
        
        .report-card:hover .report-overlay {
            opacity: 1;
        }
        
        .report-card:hover .report-image-wrapper img {
            transform: scale(1.1);
        }
        
        .report-card:hover .report-decoration {
            transform: scaleX(1);
        }
        
        .report-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(95, 179, 142, 0.4);
        }

        @media (max-width: 768px) {
            .reports-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)) !important;
                gap: 1.5rem !important;
            }
        }
    </style>
    
    @include('frontend.partials.footer')
    
    <script src="{{ asset('assets/js/frontend.js') }}?v={{ $settings['settings_updated_at'] ?? '1' }}"></script>
</body>
</html>

