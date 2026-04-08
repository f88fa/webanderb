<!-- قسم التقارير -->
@if(isset($reports) && $reports->count() > 0 && isset($settings['section_reports_visible']) && $settings['section_reports_visible'] == '1')
<section id="reports" class="reports-section"
         @if(!empty($settings['section_reports_bg_image']))
         style="background-image: url('{{ image_asset_url($settings['section_reports_bg_image']) }}'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;"
         @endif>
    @if(!empty($settings['section_reports_bg_image']))
    <div class="section-background-overlay" style="opacity: {{ (100 - ($settings['section_reports_bg_opacity'] ?? 30)) / 100 }};"></div>
    @endif
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="{{ $settings['section_reports_icon'] ?? 'fas fa-file-pdf' }}"></i>
                {{ $settings['section_reports_title'] ?? 'التقارير' }}
            </h2>
            <p class="section-description">{{ $settings['section_reports_description'] ?? 'تصفح تقاريرنا السنوية والتقارير المهمة' }}</p>
        </div>
        
        <div class="reports-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem; margin-top: 3rem;">
            @foreach($reports as $report)
            <div class="report-card" style="background: rgba(255, 255, 255, 0.05); border-radius: 20px; overflow: hidden; transition: all 0.4s ease; border: 1px solid rgba(255, 255, 255, 0.1); position: relative; display: flex; flex-direction: column; height: 100%;">
                <div class="report-image-wrapper" style="position: relative; width: 100%; padding-bottom: 100%; overflow: hidden; flex-shrink: 0;">
                    @if($report->image)
                        <img src="{{ image_asset_url($report->image) }}" alt="{{ $report->title }}" 
                             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;">
                    @else
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: var(--gradient-1); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-file-pdf" style="font-size: 3rem; color: white; opacity: 0.5;"></i>
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
                       style="display: inline-flex; align-items: center; justify-content: center; gap: 0.75rem; padding: 0.75rem 1.5rem; background: var(--gradient-1); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; transition: all 0.4s ease; box-shadow: 0 4px 15px rgba(95, 179, 142, 0.3); width: 100%;">
                        <i class="fas fa-external-link-alt"></i>
                        <span>عرض التقرير</span>
                    </a>
                    @endif
                </div>
                
                <div class="report-decoration" style="position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: var(--primary-color); transform: scaleX(0); transition: transform 0.4s ease;"></div>
            </div>
            @endforeach
        </div>
        
        <div style="text-align: center; margin-top: 3rem;">
            <a href="{{ url('/reports') }}" class="btn btn-primary" style="text-decoration: none;">
                <span>عرض جميع التقارير</span>
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
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
@endif

