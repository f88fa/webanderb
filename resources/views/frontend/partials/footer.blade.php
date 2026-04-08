@include('frontend.partials.popup-video')
<footer class="footer">
    <div class="container">
        <div class="footer-main">
            <!-- معلومات الجمعية -->
            <div class="footer-column">
                <div class="footer-brand">
                    @if(!empty($settings['site_logo']))
                        <img src="{{ image_asset_url($settings['site_logo']) }}" alt="شعار الموقع" class="footer-logo">
                    @elseif(!empty($settings['site_icon_file']))
                        <img src="{{ image_asset_url($settings['site_icon_file']) }}" alt="أيقونة الموقع" class="footer-logo">
                    @else
                        <div class="footer-logo-icon">
                            <i class="{{ $settings['site_icon'] ?? 'fas fa-rocket' }}"></i>
                        </div>
                    @endif
                    <div class="footer-brand-text">
                        <h3 class="footer-brand-title">{{ $settings['site_title'] ?? 'الموقع' }}</h3>
                        <p class="footer-brand-slogan">{{ $settings['site_description'] ?? 'لمستقبل بيئي مستدام' }}</p>
                    </div>
                </div>
                @if(!empty($settings['site_description_footer']))
                    <p class="footer-description">{{ $settings['site_description_footer'] }}</p>
                @endif
                
                <!-- صورة الترخيص -->
                @if(!empty($settings['license_image']))
                <div class="footer-license" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                    <div class="license-image-wrapper" style="display: inline-block; cursor: pointer; transition: transform 0.3s ease;">
                        <img src="{{ image_asset_url($settings['license_image']) }}" 
                             alt="رخصة الجمعية" 
                             class="license-thumbnail"
                             style="max-width: 120px; max-height: 120px; border-radius: 8px; border: 2px solid rgba(255, 255, 255, 0.2); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); object-fit: contain; background: rgba(255, 255, 255, 0.05); padding: 0.5rem;">
                    </div>
                </div>
                @endif
            </div>

            <!-- روابط سريعة -->
            <div class="footer-column">
                <h4 class="footer-column-title">
                    <span class="footer-title-line"></span>
                    روابط سريعة
                </h4>
                <ul class="footer-links">
                    <li><a href="{{ url('/') }}#about"><i class="fas fa-circle"></i> من نحن</a></li>
                    <li><a href="{{ url('/') }}#news"><i class="fas fa-circle"></i> الأخبار</a></li>
                    <li><a href="{{ url('/') }}#contact"><i class="fas fa-circle"></i> اتصل بنا</a></li>
                </ul>
            </div>

            <!-- تواصل معنا -->
            <div class="footer-column">
                <h4 class="footer-column-title">
                    <span class="footer-title-line"></span>
                    تواصل معنا
                </h4>
                <ul class="footer-contact">
                    @if(!empty($settings['contact_address']))
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $settings['contact_address'] }}</span>
                        </li>
                    @endif
                    @if(!empty($settings['contact_phone']))
                        <li>
                            <i class="fas fa-phone"></i>
                            <a href="tel:{{ $settings['contact_phone'] }}">{{ $settings['contact_phone'] }}</a>
                        </li>
                    @endif
                    @if(!empty($settings['contact_email']))
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:{{ $settings['contact_email'] }}">{{ $settings['contact_email'] }}</a>
                        </li>
                    @endif
                </ul>
                
                <!-- روابط التواصل الاجتماعي -->
                @if(isset($settings['social_facebook']) || isset($settings['social_twitter']) || isset($settings['social_instagram']) || 
                    isset($settings['social_linkedin']) || isset($settings['social_youtube']) || isset($settings['social_whatsapp']) || 
                    isset($settings['social_telegram']))
                <div class="footer-social">
                    @if(!empty($settings['social_facebook']))
                    <a href="{{ $settings['social_facebook'] }}" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    @endif
                    @if(!empty($settings['social_twitter']))
                    <a href="{{ $settings['social_twitter'] }}" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="width: 1em; height: 1em; vertical-align: -0.125em;">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                    @endif
                    @if(!empty($settings['social_instagram']))
                    <a href="{{ $settings['social_instagram'] }}" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                        <i class="fab fa-instagram"></i>
                    </a>
                    @endif
                    @if(!empty($settings['social_linkedin']))
                    <a href="{{ $settings['social_linkedin'] }}" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    @endif
                    @if(!empty($settings['social_youtube']))
                    <a href="{{ $settings['social_youtube'] }}" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                        <i class="fab fa-youtube"></i>
                    </a>
                    @endif
                    @if(!empty($settings['social_whatsapp']))
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['social_whatsapp']) }}" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    @endif
                    @if(!empty($settings['social_telegram']))
                    <a href="{{ $settings['social_telegram'] }}" target="_blank" rel="noopener noreferrer" class="footer-social-link">
                        <i class="fab fa-telegram-plane"></i>
                    </a>
                    @endif
                </div>
                @endif
            </div>

            <!-- ساعات العمل -->
            <div class="footer-column">
                <h4 class="footer-column-title">
                    <span class="footer-title-line"></span>
                    ساعات العمل
                </h4>
                <div class="footer-working-hours">
                    @php
                        $daysMap = [
                            'sunday' => 'الأحد',
                            'monday' => 'الاثنين',
                            'tuesday' => 'الثلاثاء',
                            'wednesday' => 'الأربعاء',
                            'thursday' => 'الخميس',
                            'friday' => 'الجمعة',
                            'saturday' => 'السبت'
                        ];
                        $selectedDays = json_decode($settings['working_days'] ?? '[]', true) ?: [];
                        $hoursFrom = $settings['working_hours_from'] ?? '';
                        $hoursTo = $settings['working_hours_to'] ?? '';
                    @endphp
                    
                    @if(!empty($selectedDays) && is_array($selectedDays) && count($selectedDays) > 0 && !empty($hoursFrom) && !empty($hoursTo))
                        @php
                            // Group consecutive days
                            $dayOrder = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                            $sortedDays = array_values(array_intersect($dayOrder, $selectedDays));
                            
                            $dayRanges = [];
                            if (count($sortedDays) > 0) {
                                $start = $sortedDays[0];
                                $end = $sortedDays[0];
                                
                                for ($i = 1; $i < count($sortedDays); $i++) {
                                    $currentIndex = array_search($sortedDays[$i], $dayOrder);
                                    $prevIndex = array_search($sortedDays[$i-1], $dayOrder);
                                    
                                    if ($currentIndex == $prevIndex + 1) {
                                        $end = $sortedDays[$i];
                                    } else {
                                        if ($start == $end) {
                                            $dayRanges[] = $daysMap[$start];
                                        } else {
                                            $dayRanges[] = $daysMap[$start] . ' - ' . $daysMap[$end];
                                        }
                                        $start = $sortedDays[$i];
                                        $end = $sortedDays[$i];
                                    }
                                }
                                
                                if ($start == $end) {
                                    $dayRanges[] = $daysMap[$start];
                                } else {
                                    $dayRanges[] = $daysMap[$start] . ' - ' . $daysMap[$end];
                                }
                            }
                            
                            $daysDisplay = implode('، ', $dayRanges);
                            
                            // Convert 24-hour format to 12-hour format with AM/PM
                            $fromTime = date('g:i', strtotime($hoursFrom));
                            $fromPeriod = date('A', strtotime($hoursFrom)) == 'AM' ? 'ص' : 'م';
                            $toTime = date('g:i', strtotime($hoursTo));
                            $toPeriod = date('A', strtotime($hoursTo)) == 'AM' ? 'ص' : 'م';
                            
                            $timeDisplay = $fromTime . ' ' . $fromPeriod . ' - ' . $toTime . ' ' . $toPeriod;
                        @endphp
                        <div class="working-hours-item">
                            <span class="working-days">{{ $daysDisplay }}</span>
                            <span class="working-time">{{ $timeDisplay }}</span>
                        </div>
                    @elseif(!empty($settings['working_hours_weekdays']))
                        <div class="working-hours-item">
                            <span class="working-time">{{ $settings['working_hours_weekdays'] }}</span>
                        </div>
                        @if(!empty($settings['working_hours_weekend']) && strtolower(trim($settings['working_hours_weekend'])) !== 'مغلق' && strtolower(trim($settings['working_hours_weekend'])) !== 'closed' && !empty(trim($settings['working_hours_weekend'])))
                        <div class="working-hours-item">
                            <span class="working-time">{{ $settings['working_hours_weekend'] }}</span>
                        </div>
                        @endif
                    @else
                        <div class="working-hours-item">
                            <span class="working-time">لم يتم تحديد ساعات العمل</span>
                        </div>
                    @endif
                </div>
                
                @if(!empty($settings['google_maps_link']))
                    <a href="{{ $settings['google_maps_link'] }}" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="footer-location-btn">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>موقع الجمعية</span>
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                @endif
            </div>
        </div>

        <!-- الشريط السفلي -->
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <div class="footer-scroll-top">
                    <button class="scroll-to-top-btn" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
                        <i class="fas fa-arrow-up"></i>
                    </button>
                </div>
                <div class="footer-copyright">
                    <p class="copyright-text">
                        &copy; {{ date('Y') }} جميع الحقوق محفوظة لـ 
                        <span class="site-name">{{ $settings['site_title'] ?? 'الموقع' }}</span>
                    </p>
                    <p class="developer-credit">
                        برمجة 
                        <a href="https://twitter.com/f88fa" target="_blank" rel="noopener noreferrer" class="developer-link">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="width: 1em; height: 1em; vertical-align: -0.125em; display: inline-block;">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                            فارس التريباني
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Modal لعرض صورة الرخصة -->
@if(!empty($settings['license_image']))
<div id="licenseModal" class="license-modal" style="display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.9); backdrop-filter: blur(10px);">
    <div class="license-modal-content" style="position: relative; margin: 2% auto; max-width: 90%; max-height: 90%; display: flex; flex-direction: column; align-items: center; justify-content: center;">
        <span class="license-modal-close" style="position: absolute; top: -40px; left: 0; color: #fff; font-size: 2.5rem; font-weight: bold; cursor: pointer; z-index: 10001; transition: transform 0.3s ease;">
            &times;
        </span>
        <h3 style="color: #fff; margin-bottom: 1.5rem; font-size: 1.5rem; text-align: center;">رخصة الجمعية</h3>
        <img src="{{ image_asset_url($settings['license_image']) }}"
             alt="رخصة الجمعية"
             style="max-width: 100%; max-height: 80vh; border-radius: 12px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5); object-fit: contain;">
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const licenseThumbnail = document.querySelector('.license-thumbnail');
    const licenseModal = document.getElementById('licenseModal');
    const licenseModalClose = document.querySelector('.license-modal-close');
    
    if (licenseThumbnail && licenseModal) {
        licenseThumbnail.addEventListener('click', function() {
            licenseModal.style.display = 'flex';
        });
    }
    
    if (licenseModalClose) {
        licenseModalClose.addEventListener('click', function() {
            licenseModal.style.display = 'none';
        });
    }
    
    if (licenseModal) {
        licenseModal.addEventListener('click', function(e) {
            if (e.target === licenseModal) {
                licenseModal.style.display = 'none';
            }
        });
    }
});
</script>
@endif

