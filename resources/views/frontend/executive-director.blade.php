<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المدير التنفيذي - {{ $settings['site_title'] ?? 'الموقع' }}</title>
    
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

    <!-- صفحة المدير التنفيذي -->
    <section class="executive-director-section" style="padding: 6rem 0; min-height: 80vh; position: relative;">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 4rem;">
                <span class="section-badge" style="display: inline-block; padding: 0.5rem 1.5rem; background: var(--primary-color); color: white; border-radius: 25px; font-size: 0.9rem; font-weight: 600; margin-bottom: 1rem;">المدير التنفيذي</span>
                <h1 class="section-title" style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;">
                    المدير التنفيذي
                </h1>
            </div>

            @php
                $executiveName = $settings['executive_director_name'] ?? '';
                $executivePosition = $settings['executive_director_position'] ?? 'المدير التنفيذي';
                $executiveImage = $settings['executive_director_image'] ?? '';
                $executiveEmail = $settings['executive_director_email'] ?? '';
                $executivePhone = $settings['executive_director_phone'] ?? '';
                $executiveBio = $settings['executive_director_bio'] ?? '';
                $executiveFacebook = $settings['executive_director_facebook'] ?? '';
                $executiveTwitter = $settings['executive_director_twitter'] ?? '';
                $executiveInstagram = $settings['executive_director_instagram'] ?? '';
                $executiveLinkedin = $settings['executive_director_linkedin'] ?? '';
                $executiveWhatsapp = $settings['executive_director_whatsapp'] ?? '';
                $executiveTelegram = $settings['executive_director_telegram'] ?? '';
                
                // Debug logging (commented out - uncomment if needed)
                // \Log::info('Executive Director Frontend Data:', [
                //     'name' => $executiveName,
                //     'email' => $executiveEmail,
                //     'phone' => $executivePhone,
                //     'image' => $executiveImage,
                //     'bio' => $executiveBio ? 'Has bio' : 'No bio',
                // ]);
            @endphp

            @if(!empty($executiveName) || !empty($executiveImage) || !empty($executiveEmail) || !empty($executivePhone) || !empty($executiveBio) || !empty($executiveFacebook) || !empty($executiveTwitter) || !empty($executiveInstagram) || !empty($executiveLinkedin) || !empty($executiveWhatsapp) || !empty($executiveTelegram))
            <div class="executive-director-card" style="max-width: 900px; margin: 0 auto; background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(20px); border-radius: 30px; padding: 3rem; border: 2px solid rgba(95, 179, 142, 0.2); box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);">
                <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 2rem;">
                    <!-- الصورة -->
                    @if(!empty($executiveImage))
                    <div class="executive-image-wrapper" style="position: relative; width: 250px; height: 250px; border-radius: 50%; overflow: hidden; border: 5px solid var(--primary-color); box-shadow: 0 15px 40px rgba(95, 179, 142, 0.3);">
                        <img src="{{ image_asset_url($executiveImage) }}" alt="{{ $executiveName }}" 
                             style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    @endif

                    <!-- الاسم والمنصب -->
                    <div>
                        <h2 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                            {{ $executiveName }}
                        </h2>
                        @if(!empty($executivePosition))
                        <p style="font-size: 1.3rem; color: var(--primary-color); font-weight: 600; margin-bottom: 0;">
                            {{ $executivePosition }}
                        </p>
                        @endif
                    </div>

                    <!-- معلومات التواصل -->
                    <div style="display: flex; flex-direction: column; gap: 1rem; width: 100%; max-width: 500px;">
                        @if(!empty($executiveEmail))
                        <div class="contact-item" style="display: flex; align-items: center; justify-content: center; gap: 1rem; padding: 1rem; background: rgba(255, 255, 255, 0.05); border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);">
                            <i class="fas fa-envelope" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                            <a href="mailto:{{ $executiveEmail }}" style="color: var(--text-primary); text-decoration: none; font-size: 1.1rem; font-weight: 500;">
                                {{ $executiveEmail }}
                            </a>
                        </div>
                        @endif

                        @if(!empty($executivePhone))
                        <div class="contact-item" style="display: flex; align-items: center; justify-content: center; gap: 1rem; padding: 1rem; background: rgba(255, 255, 255, 0.05); border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1);">
                            <i class="fas fa-phone" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                            <a href="tel:{{ $executivePhone }}" style="color: var(--text-primary); text-decoration: none; font-size: 1.1rem; font-weight: 500;">
                                {{ $executivePhone }}
                            </a>
                        </div>
                        @endif
                    </div>

                    <!-- السيرة الذاتية -->
                    @if(!empty($executiveBio))
                    <div style="width: 100%; margin-top: 1rem; padding: 2rem; background: rgba(255, 255, 255, 0.05); border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.1);">
                        <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.5rem; text-align: center;">
                            <i class="fas fa-file-alt" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                            السيرة الذاتية
                        </h3>
                        <div style="color: var(--text-secondary); font-size: 1.1rem; line-height: 1.8; text-align: justify;">
                            {!! nl2br(e($executiveBio)) !!}
                        </div>
                    </div>
                    @endif

                    <!-- حسابات التواصل الاجتماعي -->
                    @if(!empty($executiveFacebook) || !empty($executiveTwitter) || !empty($executiveInstagram) || !empty($executiveLinkedin) || !empty($executiveWhatsapp) || !empty($executiveTelegram))
                    <div style="width: 100%; margin-top: 2rem; padding-top: 2rem; border-top: 2px solid rgba(95, 179, 142, 0.2);">
                        <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.5rem; text-align: center;">
                            <i class="fas fa-share-alt" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                            حسابات التواصل الاجتماعي
                        </h3>
                        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 1rem;">
                            @if(!empty($executiveFacebook))
                            <a href="{{ $executiveFacebook }}" target="_blank" rel="noopener noreferrer" 
                               class="social-link" 
                               style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: rgba(59, 89, 152, 0.2); border-radius: 50%; color: #3b5998; text-decoration: none; font-size: 1.5rem; transition: all 0.3s ease; border: 2px solid rgba(59, 89, 152, 0.3);">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            @endif

                            @if(!empty($executiveTwitter))
                            <a href="{{ $executiveTwitter }}" target="_blank" rel="noopener noreferrer" 
                               class="social-link" 
                               style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: rgba(0, 0, 0, 0.2); border-radius: 50%; color: #000000; text-decoration: none; font-size: 1.5rem; transition: all 0.3s ease; border: 2px solid rgba(0, 0, 0, 0.3);">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" style="width: 1.5em; height: 1.5em;">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>
                            @endif

                            @if(!empty($executiveInstagram))
                            <a href="{{ $executiveInstagram }}" target="_blank" rel="noopener noreferrer" 
                               class="social-link" 
                               style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: rgba(225, 48, 108, 0.2); border-radius: 50%; color: #e1306c; text-decoration: none; font-size: 1.5rem; transition: all 0.3s ease; border: 2px solid rgba(225, 48, 108, 0.3);">
                                <i class="fab fa-instagram"></i>
                            </a>
                            @endif

                            @if(!empty($executiveLinkedin))
                            <a href="{{ $executiveLinkedin }}" target="_blank" rel="noopener noreferrer" 
                               class="social-link" 
                               style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: rgba(0, 119, 181, 0.2); border-radius: 50%; color: #0077b5; text-decoration: none; font-size: 1.5rem; transition: all 0.3s ease; border: 2px solid rgba(0, 119, 181, 0.3);">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            @endif

                            @if(!empty($executiveWhatsapp))
                            <a href="https://wa.me/{{ $executiveWhatsapp }}" target="_blank" rel="noopener noreferrer" 
                               class="social-link" 
                               style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: rgba(37, 211, 102, 0.2); border-radius: 50%; color: #25d366; text-decoration: none; font-size: 1.5rem; transition: all 0.3s ease; border: 2px solid rgba(37, 211, 102, 0.3);">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            @endif

                            @if(!empty($executiveTelegram))
                            <a href="{{ $executiveTelegram }}" target="_blank" rel="noopener noreferrer" 
                               class="social-link" 
                               style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: rgba(0, 136, 204, 0.2); border-radius: 50%; color: #0088cc; text-decoration: none; font-size: 1.5rem; transition: all 0.3s ease; border: 2px solid rgba(0, 136, 204, 0.3);">
                                <i class="fab fa-telegram-plane"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="empty-state" style="text-align: center; padding: 4rem 2rem;">
                <i class="fas fa-user-tie" style="font-size: 4rem; color: rgba(255, 255, 255, 0.3); margin-bottom: 1.5rem;"></i>
                <p style="font-size: 1.2rem; color: var(--text-secondary);">لا توجد معلومات متاحة للمدير التنفيذي حالياً</p>
            </div>
            @endif
        </div>
    </section>

    <style>
        .executive-director-card {
            animation: fadeInUp 0.8s ease-out;
        }

        .executive-image-wrapper {
            transition: transform 0.3s ease;
        }

        .executive-director-card:hover .executive-image-wrapper {
            transform: scale(1.05);
        }

        .contact-item {
            transition: all 0.3s ease;
        }

        .contact-item:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            transform: translateY(-3px);
        }

        .contact-item a:hover {
            color: var(--primary-color) !important;
        }

        .social-link:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 10px 25px rgba(95, 179, 142, 0.3);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .executive-director-card {
                padding: 2rem 1.5rem !important;
            }

            .executive-image-wrapper {
                width: 200px !important;
                height: 200px !important;
            }

            h2 {
                font-size: 2rem !important;
            }
        }
    </style>
    
    @include('frontend.partials.footer')
    
    <script src="{{ asset('assets/js/frontend.js') }}?v={{ $settings['settings_updated_at'] ?? '1' }}"></script>
</body>
</html>

