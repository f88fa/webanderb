<!-- قالب الهيرو بصور متحركة (سلايدر) -->
<section id="home" class="hero hero-template-slider">
    <div class="hero-slider-wrapper">
        <div class="hero-slider" id="heroSlider">
            @if(isset($heroSliderImages) && $heroSliderImages->count() > 0)
                @foreach($heroSliderImages as $index => $sliderImage)
                    <div class="hero-slide {{ $index === 0 ? 'active' : '' }}" 
                         style="background-image: url('{{ image_asset_url($sliderImage->image) }}');">
                        <div class="hero-slide-overlay"></div>
                        @if($sliderImage->title || $sliderImage->description)
                        <div class="hero-slide-content">
                            @if($sliderImage->title)
                            <h2 class="hero-slide-title">{{ $sliderImage->title }}</h2>
                            @endif
                            @if($sliderImage->description)
                            <p class="hero-slide-description">{{ $sliderImage->description }}</p>
                            @endif
                        </div>
                        @endif
                    </div>
                @endforeach
            @else
                <!-- صورة افتراضية إذا لم تكن هناك صور -->
                <div class="hero-slide active" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);">
                    <div class="hero-slide-overlay"></div>
                    <div class="hero-slide-content">
                        <h2 class="hero-slide-title">يرجى إضافة صور للسلايدر</h2>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- أزرار التنقل -->
        @if(isset($heroSliderImages) && $heroSliderImages->count() > 1)
        <button class="hero-slider-nav hero-slider-prev" aria-label="السابق">
            <i class="fas fa-chevron-right"></i>
        </button>
        <button class="hero-slider-nav hero-slider-next" aria-label="التالي">
            <i class="fas fa-chevron-left"></i>
        </button>
        
        <!-- المؤشرات -->
        <div class="hero-slider-indicators">
            @foreach($heroSliderImages as $index => $sliderImage)
                <button class="hero-slider-indicator {{ $index === 0 ? 'active' : '' }}" 
                        data-slide="{{ $index }}" 
                        aria-label="الشريحة {{ $index + 1 }}"></button>
            @endforeach
        </div>
        @endif
    </div>
</section>

