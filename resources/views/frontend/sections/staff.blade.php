<!-- قسم الموظفين -->
@if(isset($settings['section_staff_visible']) && $settings['section_staff_visible'] == '1')
<section id="staff" class="staff-section"
         @if(!empty($settings['section_staff_bg_image']))
         style="background-image: url('{{ image_asset_url($settings['section_staff_bg_image']) }}'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;"
         @endif>
    @if(!empty($settings['section_staff_bg_image']))
    <div class="section-background-overlay" style="opacity: {{ (100 - ($settings['section_staff_bg_opacity'] ?? 30)) / 100 }};"></div>
    @endif
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="{{ $settings['section_staff_icon'] ?? 'fas fa-user-tie' }}"></i>
                {{ $settings['section_staff_title'] ?? 'فريقنا المتميز' }}
            </h2>
            @if(!empty($settings['section_staff_description']))
            <p class="section-description">{{ $settings['section_staff_description'] }}</p>
            @else
            <p class="section-description">نفتخر بفريقنا المتميز من المحترفين</p>
            @endif
        </div>
        
        @if(isset($staff) && $staff->count() > 0)
        <div class="staff-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem; margin-top: 3rem;">
            @foreach($staff as $member)
            <div class="staff-card" style="background: rgba(255, 255, 255, 0.05); border-radius: 20px; padding: 2rem; text-align: center; transition: all 0.4s ease; border: 1px solid rgba(255, 255, 255, 0.1); position: relative; overflow: hidden;">
                <div class="staff-card-bg" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(95, 179, 142, 0.1) 0%, rgba(31, 107, 79, 0.1) 100%); opacity: 0; transition: opacity 0.4s ease; pointer-events: none;"></div>
                
                <div class="staff-image-wrapper" style="position: relative; width: 180px; height: 180px; margin: 0 auto 1.5rem; border-radius: 50%; overflow: hidden; border: 4px solid var(--primary-color); box-shadow: 0 10px 30px rgba(95, 179, 142, 0.3); transition: all 0.4s ease;">
                    @if($member->image)
                        <img src="{{ image_asset_url($member->image) }}" alt="{{ $member->name }}" 
                             style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;">
                    @else
                        <div style="width: 100%; height: 100%; background: var(--gradient-1); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user" style="font-size: 4rem; color: white;"></i>
                        </div>
                    @endif
                </div>
                
                <h3 class="staff-name" style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem; position: relative; z-index: 1;">{{ $member->name }}</h3>
                <p class="staff-position" style="font-size: 1.1rem; color: var(--primary-color); font-weight: 600; margin-bottom: 1rem; position: relative; z-index: 1;">{{ $member->position }}</p>
                
                <div class="staff-decoration" style="position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: var(--primary-color); transform: scaleX(0); transition: transform 0.4s ease;"></div>
            </div>
            @endforeach
        </div>
        @else
        <div style="text-align: center; padding: 3rem 0; color: rgba(255, 255, 255, 0.7);">
            <i class="fas fa-user-tie" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
            <p style="font-size: 1.2rem;">لا يوجد موظفين حالياً</p>
        </div>
        @endif
    </div>
</section>
@endif

<style>
    .staff-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 60px rgba(95, 179, 142, 0.2);
        border-color: var(--primary-color);
    }
    
    .staff-card:hover .staff-card-bg {
        opacity: 1;
    }
    
    .staff-card:hover .staff-image-wrapper {
        transform: scale(1.05);
        box-shadow: 0 15px 40px rgba(95, 179, 142, 0.4);
    }
    
    .staff-card:hover .staff-image-wrapper img {
        transform: scale(1.1);
    }
    
    .staff-card:hover .staff-decoration {
        transform: scaleX(1);
    }

    @media (max-width: 768px) {
        .staff-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)) !important;
            gap: 1.5rem !important;
        }
        
        .staff-image-wrapper {
            width: 150px !important;
            height: 150px !important;
        }
    }
</style>

