@php
    $sectionColorsList = [
        'about' => 'من نحن',
        'vision_mission' => 'رؤيتنا ورسالتنا',
        'banner_sections' => 'أقسام البانر',
        'services' => 'خدماتنا',
        'projects' => 'المشاريع',
        'media' => 'الإعلام والفيديو',
        'testimonials' => 'آراء العملاء',
        'partners' => 'الشركاء',
        'news' => 'الأخبار',
        'contact' => 'اتصل بنا / تواصل معنا',
    ];
    $sectionColorKeys = [
        'bg_color' => 'لون خلفية القسم',
        'text_color' => 'لون النص',
        'title_color' => 'لون عنوان القسم',
        'icon_color' => 'لون الأيقونات',
        'card_bg_color' => 'لون خلفية البطاقة',
        'card_title_color' => 'لون عنوان البطاقة',
        'hover_text_color' => 'لون النص عند مرور الماوس',
        'button_color' => 'لون أزرار القسم (مثل زر تبرع الآن في المشاريع)',
    ];
    $sectionColorDefaults = [
        'bg_color' => '#FFFFFF',
        'text_color' => '#0F3D2E',
        'title_color' => '#5FB38E',
        'icon_color' => '#5FB38E',
        'card_bg_color' => '#FFFFFF',
        'card_title_color' => '#5FB38E',
        'hover_text_color' => '#5FB38E',
        'button_color' => '#5FB38E',
    ];
@endphp
<div id="section-colors" style="scroll-margin-top: 120px;">
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-top: 2rem; padding: 2rem;">
        <h2 style="color: var(--text-primary); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-palette" style="color: var(--primary-color);"></i>
            ألوان الأقسام
        </h2>
        <p style="color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 0.95rem;">
            كل قسم له تحكم مستقل: لون الخلفية، النص، العنوان، الأيقونات، البطاقات، ولون النص عند مرور الماوس. يمكنك استخدام الذكاء الاصطناعي لتوليد ألوان منسقة لكل قسم.
        </p>

        {{-- توليد ألوان الموقع بالكامل من 3 ألوان --}}
        <div class="section-color-accordion" style="margin-bottom: 1.25rem; border: 2px solid rgba(95,179,142,0.4); border-radius: 12px; overflow: hidden;">
            <button type="button" style="width: 100%; padding: 1rem 1.25rem; background: linear-gradient(135deg, rgba(95,179,142,0.25) 0%, rgba(31,107,79,0.2) 100%); border: none; color: #fff; font-size: 1.05rem; font-weight: 600; text-align: right; cursor: pointer; display: flex; align-items: center; justify-content: space-between;" onclick="var d=this.nextElementSibling; d.style.display=d.style.display==='none'?'block':'none';">
                <span><i class="fas fa-magic" style="margin-left: 0.5rem;"></i> توليد ألوان الموقع بالذكاء الاصطناعي (من 3 ألوان)</span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="section-color-fields" style="display: none; padding: 1.25rem; background: rgba(0,0,0,0.2);">
                <p style="color: var(--text-secondary); margin-bottom: 1rem; font-size: 0.95rem;">
                    أدخل 3 ألوان أساسية وسيتم توليد ألوان منسقة للشريط العلوي والهيرو والفوتر وجميع الأقسام. يمكنك تعديل أي لون لاحقاً من داخل كل قسم ثم حفظ الإعدادات.
                </p>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                    @php
                        $ai1 = old('site_ai_color_1', $settings['site_primary_color'] ?? '#5FB38E');
                        $ai2 = old('site_ai_color_2', $settings['site_primary_dark'] ?? '#1F6B4F');
                        $ai3 = old('site_ai_color_3', '#0F3D2E');
                    @endphp
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-palette" style="color: {{ $ai1 }};"></i> اللون الأساسي 1</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="site_ai_color_1_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $ai1 }}" onchange="document.getElementById('site_ai_color_1').value=this.value">
                            <input type="text" id="site_ai_color_1" class="form-control" value="{{ $ai1 }}" placeholder="#5FB38E" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('site_ai_color_1_picker').value=v;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-palette" style="color: {{ $ai2 }};"></i> اللون الأساسي 2</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="site_ai_color_2_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $ai2 }}" onchange="document.getElementById('site_ai_color_2').value=this.value">
                            <input type="text" id="site_ai_color_2" class="form-control" value="{{ $ai2 }}" placeholder="#1F6B4F" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('site_ai_color_2_picker').value=v;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-palette" style="color: {{ $ai3 }};"></i> اللون الأساسي 3</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="site_ai_color_3_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $ai3 }}" onchange="document.getElementById('site_ai_color_3').value=this.value">
                            <input type="text" id="site_ai_color_3" class="form-control" value="{{ $ai3 }}" placeholder="#0F3D2E" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('site_ai_color_3_picker').value=v;">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-primary" id="btn-generate-site-colors" onclick="generateSiteColors()" style="padding: 0.6rem 1.25rem;">
                    <i class="fas fa-magic"></i> توليد ألوان الموقع بالكامل
                </button>
                <span id="site-colors-ai-status" style="margin-right: 0.75rem; font-size: 0.9rem; color: rgba(255,255,255,0.8);"></span>
            </div>
        </div>

        {{-- الشريط العلوي (النافبار) --}}
        <div class="section-color-accordion" style="margin-bottom: 1rem; border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; overflow: hidden;">
            <button type="button" style="width: 100%; padding: 1rem 1.25rem; background: rgba(95,179,142,0.15); border: none; color: #fff; font-size: 1.05rem; font-weight: 600; text-align: right; cursor: pointer; display: flex; align-items: center; justify-content: space-between;" onclick="var d=this.nextElementSibling; d.style.display=d.style.display==='none'?'block':'none';">
                <span><i class="fas fa-bars" style="margin-left: 0.5rem;"></i> الشريط العلوي (القائمة)</span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="section-color-fields" style="display: none; padding: 1.25rem; background: rgba(0,0,0,0.15);">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                    @php
                        $navBg = old('navbar_bg_color', $settings['navbar_bg_color'] ?? '#FFFFFF');
                        $navText = old('navbar_text_color', $settings['navbar_text_color'] ?? '#0F3D2E');
                    @endphp
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-fill-drip" style="color: {{ $navBg }};"></i> لون خلفية الشريط العلوي</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="navbar_bg_color_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $navBg }}" onchange="document.getElementById('navbar_bg_color').value=this.value">
                            <input type="text" name="navbar_bg_color" id="navbar_bg_color" class="form-control" value="{{ $navBg }}" placeholder="#FFFFFF" pattern="^(#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{6})?$" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('navbar_bg_color_picker').value=v;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-font" style="color: {{ $navText }};"></i> لون نص الشريط العلوي</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="navbar_text_color_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $navText }}" onchange="document.getElementById('navbar_text_color').value=this.value">
                            <input type="text" name="navbar_text_color" id="navbar_text_color" class="form-control" value="{{ $navText }}" placeholder="#0F3D2E" pattern="^(#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{6})?$" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('navbar_text_color_picker').value=v;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- الهيرو والفوتر (لون واحد للاثنين) --}}
        <div class="section-color-accordion" style="margin-bottom: 1rem; border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; overflow: hidden;">
            <button type="button" style="width: 100%; padding: 1rem 1.25rem; background: rgba(95,179,142,0.15); border: none; color: #fff; font-size: 1.05rem; font-weight: 600; text-align: right; cursor: pointer; display: flex; align-items: center; justify-content: space-between;" onclick="var d=this.nextElementSibling; d.style.display=d.style.display==='none'?'block':'none';">
                <span><i class="fas fa-layer-group" style="margin-left: 0.5rem;"></i> الهيرو والفوتر (لون واحد)</span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="section-color-fields" style="display: none; padding: 1.25rem; background: rgba(0,0,0,0.15);">
                <p style="color: var(--text-secondary); margin-bottom: 0.5rem; font-size: 0.9rem;">نفس الألوان تُطبَّق على قسم الهيرو وقسم التذييل (الفوتر). <strong>لون العنوان</strong> = النص الأساسي في الهيرو، <strong>لون النص</strong> = النص الفرعي.</p>
                <p style="color: var(--text-secondary); margin-bottom: 1rem; font-size: 0.85rem;">ألوان إضافية للهيرو فقط: الدائرة، أيقونة الدائرة، وأيقونات التواصل.</p>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                    @php
                        $hfBg = old('hero_footer_bg_color', $settings['hero_footer_bg_color'] ?? $settings['footer_bg_color'] ?? '#0F3D2E');
                        $hfText = old('hero_footer_text_color', $settings['hero_footer_text_color'] ?? $settings['footer_text_color'] ?? '#FFFFFF');
                        $hfTitle = old('hero_footer_title_color', $settings['hero_footer_title_color'] ?? $settings['site_hero_title_color'] ?? '#FFFFFF');
                        $heroCircleBg = old('hero_circle_bg_color', $settings['hero_circle_bg_color'] ?? '#5FB38E');
                        $heroCircleIcon = old('hero_circle_icon_color', $settings['hero_circle_icon_color'] ?? '#FFFFFF');
                        $heroSocial = old('hero_social_icons_color', $settings['hero_social_icons_color'] ?? '#FFFFFF');
                    @endphp
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-fill-drip" style="color: {{ $hfBg }};"></i> لون خلفية الهيرو والفوتر</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="hero_footer_bg_color_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $hfBg }}" onchange="document.getElementById('hero_footer_bg_color').value=this.value">
                            <input type="text" name="hero_footer_bg_color" id="hero_footer_bg_color" class="form-control" value="{{ $hfBg }}" placeholder="#0F3D2E" pattern="^(#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{6})?$" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('hero_footer_bg_color_picker').value=v;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-font" style="color: {{ $hfText }};"></i> لون النص (الفرعي)</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="hero_footer_text_color_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $hfText }}" onchange="document.getElementById('hero_footer_text_color').value=this.value">
                            <input type="text" name="hero_footer_text_color" id="hero_footer_text_color" class="form-control" value="{{ $hfText }}" placeholder="#FFFFFF" pattern="^(#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{6})?$" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('hero_footer_text_color_picker').value=v;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-heading" style="color: {{ $hfTitle }};"></i> لون العنوان (النص الأساسي)</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="hero_footer_title_color_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $hfTitle }}" onchange="document.getElementById('hero_footer_title_color').value=this.value">
                            <input type="text" name="hero_footer_title_color" id="hero_footer_title_color" class="form-control" value="{{ $hfTitle }}" placeholder="#FFFFFF" pattern="^(#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{6})?$" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('hero_footer_title_color_picker').value=v;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-circle" style="color: {{ $heroCircleBg }};"></i> لون خلفية الدائرة (الهيرو)</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="hero_circle_bg_color_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $heroCircleBg }}" onchange="document.getElementById('hero_circle_bg_color').value=this.value">
                            <input type="text" name="hero_circle_bg_color" id="hero_circle_bg_color" class="form-control" value="{{ $heroCircleBg }}" placeholder="#5FB38E" pattern="^(#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{6})?$" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('hero_circle_bg_color_picker').value=v;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-star" style="color: {{ $heroCircleIcon }};"></i> لون أيقونة الدائرة (الهيرو)</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="hero_circle_icon_color_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $heroCircleIcon }}" onchange="document.getElementById('hero_circle_icon_color').value=this.value">
                            <input type="text" name="hero_circle_icon_color" id="hero_circle_icon_color" class="form-control" value="{{ $heroCircleIcon }}" placeholder="#FFFFFF" pattern="^(#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{6})?$" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('hero_circle_icon_color_picker').value=v;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-share-alt" style="color: {{ $heroSocial }};"></i> لون أيقونات التواصل (الهيرو)</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="hero_social_icons_color_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $heroSocial }}" onchange="document.getElementById('hero_social_icons_color').value=this.value">
                            <input type="text" name="hero_social_icons_color" id="hero_social_icons_color" class="form-control" value="{{ $heroSocial }}" placeholder="#FFFFFF" pattern="^(#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{6})?$" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('hero_social_icons_color_picker').value=v;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach($sectionColorsList as $sk => $slabel)
        <div class="section-color-accordion" style="margin-bottom: 1rem; border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; overflow: hidden;">
            <button type="button" style="width: 100%; padding: 1rem 1.25rem; background: rgba(95,179,142,0.15); border: none; color: #fff; font-size: 1.05rem; font-weight: 600; text-align: right; cursor: pointer; display: flex; align-items: center; justify-content: space-between;" onclick="var d=this.nextElementSibling; d.style.display=d.style.display==='none'?'block':'none';">
                <span><i class="fas fa-layer-group" style="margin-left: 0.5rem;"></i>{{ $slabel }}</span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="section-color-fields" style="display: none; padding: 1.25rem; background: rgba(0,0,0,0.15);">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                    @foreach($sectionColorKeys as $ck => $clab)
                    @php $n = 'section_'.$sk.'_'.$ck; $v = old($n, $settings[$n] ?? $sectionColorDefaults[$ck]); @endphp
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-circle" style="color: {{ $v }};"></i> {{ $clab }}</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="{{ $n }}_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $v }}" onchange="document.getElementById('{{ $n }}').value=this.value">
                            <input type="text" name="{{ $n }}" id="{{ $n }}" class="form-control" value="{{ $v }}" placeholder="{{ $sectionColorDefaults[$ck] }}" pattern="^(#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{6})?$" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('{{ $n }}_picker').value=v;">
                        </div>
                    </div>
                    @endforeach
                </div>
                <div style="margin-top: 1rem;">
                    <button type="button" class="btn btn-primary btn-sm" onclick="generateSectionColors('{{ $sk }}')" id="ai-btn-{{ $sk }}" style="padding: 0.5rem 1rem;">
                        <i class="fas fa-magic"></i> توليد ألوان هذا القسم بالذكاء الاصطناعي
                    </button>
                    <span id="ai-status-{{ $sk }}" style="margin-right: 0.75rem; font-size: 0.85rem; color: rgba(255,255,255,0.7);"></span>
                </div>
            </div>
        </div>
        @endforeach

        {{-- ألوان صفحة الخبر الداخلية (عند النقر على خبر من قسم الأخبار) --}}
        <div class="section-color-accordion" style="margin-bottom: 1rem; border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; overflow: hidden;">
            <button type="button" style="width: 100%; padding: 1rem 1.25rem; background: rgba(95,179,142,0.15); border: none; color: #fff; font-size: 1.05rem; font-weight: 600; text-align: right; cursor: pointer; display: flex; align-items: center; justify-content: space-between;" onclick="var d=this.nextElementSibling; d.style.display=d.style.display==='none'?'block':'none';">
                <span><i class="fas fa-newspaper" style="margin-left: 0.5rem;"></i> ألوان صفحة الخبر الداخلية</span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="section-color-fields" style="display: none; padding: 1.25rem; background: rgba(0,0,0,0.15);">
                <p style="color: var(--text-secondary); margin-bottom: 1rem; font-size: 0.9rem;">تطبق على صفحة تفاصيل الخبر (عند النقر على «اقرأ المزيد» من قسم الأخبار).</p>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                    @php
                        $artBg = old('article_bg_color', $settings['article_bg_color'] ?? '#FFFFFF');
                        $artText = old('article_text_color', $settings['article_text_color'] ?? '#000000');
                        $artTitle = old('article_title_color', $settings['article_title_color'] ?? '#000000');
                        $artMeta = old('article_meta_color', $settings['article_meta_color'] ?? '#000000');
                        $artBtn = old('article_button_color', $settings['article_button_color'] ?? '#000000');
                        $artBtnHover = old('article_button_hover_color', $settings['article_button_hover_color'] ?? '#333333');
                    @endphp
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-fill-drip" style="color: {{ $artBg }};"></i> لون خلفية الصفحة</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="article_bg_color_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $artBg }}" onchange="document.getElementById('article_bg_color').value=this.value">
                            <input type="text" name="article_bg_color" id="article_bg_color" class="form-control" value="{{ $artBg }}" placeholder="#FFFFFF" pattern="^(#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{6})?$" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('article_bg_color_picker').value=v;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-font" style="color: {{ $artText }};"></i> لون النص</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="article_text_color_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $artText }}" onchange="document.getElementById('article_text_color').value=this.value">
                            <input type="text" name="article_text_color" id="article_text_color" class="form-control" value="{{ $artText }}" placeholder="#0F3D2E" pattern="^(#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{6})?$" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('article_text_color_picker').value=v;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-heading" style="color: {{ $artTitle }};"></i> لون العناوين</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="article_title_color_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $artTitle }}" onchange="document.getElementById('article_title_color').value=this.value">
                            <input type="text" name="article_title_color" id="article_title_color" class="form-control" value="{{ $artTitle }}" placeholder="#0F3D2E" pattern="^(#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{6})?$" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('article_title_color_picker').value=v;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-calendar-alt" style="color: {{ $artMeta }};"></i> لون التاريخ والمعلومات</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="article_meta_color_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $artMeta }}" onchange="document.getElementById('article_meta_color').value=this.value">
                            <input type="text" name="article_meta_color" id="article_meta_color" class="form-control" value="{{ $artMeta }}" placeholder="#6B7280" pattern="^(#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{6})?$" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('article_meta_color_picker').value=v;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-mouse-pointer" style="color: {{ $artBtn }};"></i> لون الأزرار</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="article_button_color_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $artBtn }}" onchange="document.getElementById('article_button_color').value=this.value">
                            <input type="text" name="article_button_color" id="article_button_color" class="form-control" value="{{ $artBtn }}" placeholder="#5FB38E" pattern="^(#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{6})?$" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('article_button_color_picker').value=v;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-mouse-pointer" style="color: {{ $artBtnHover }};"></i> لون الأزرار عند التمرير</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="article_button_hover_color_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $artBtnHover }}" onchange="document.getElementById('article_button_hover_color').value=this.value">
                            <input type="text" name="article_button_hover_color" id="article_button_hover_color" class="form-control" value="{{ $artBtnHover }}" placeholder="#1F6B4F" pattern="^(#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{6})?$" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('article_button_hover_color_picker').value=v;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ألوان محتوى الصفحات المستقلة (صفحة محتوى، الهيئة، السياسات، الموظفين، إلخ) --}}
        <div class="section-color-accordion" style="margin-bottom: 1rem; border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; overflow: hidden;">
            <button type="button" style="width: 100%; padding: 1rem 1.25rem; background: rgba(95,179,142,0.15); border: none; color: #fff; font-size: 1.05rem; font-weight: 600; text-align: right; cursor: pointer; display: flex; align-items: center; justify-content: space-between;" onclick="var d=this.nextElementSibling; d.style.display=d.style.display==='none'?'block':'none';">
                <span><i class="fas fa-file-alt" style="margin-left: 0.5rem;"></i> ألوان محتوى الصفحات المستقلة</span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="section-color-fields" style="display: none; padding: 1.25rem; background: rgba(0,0,0,0.15);">
                <p style="color: var(--text-secondary); margin-bottom: 1rem; font-size: 0.9rem;">تطبق على صفحة المحتوى، الهيئة، السياسات، الموظفين وأي صفحة مستقلة.</p>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                    @php
                        $pageBg = old('page_content_bg_color', $settings['page_content_bg_color'] ?? '#FFFFFF');
                        $pageText = old('page_content_text_color', $settings['page_content_text_color'] ?? '#0F3D2E');
                        $pageTitle = old('page_content_title_color', $settings['page_content_title_color'] ?? '#5FB38E');
                    @endphp
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-fill-drip" style="color: {{ $pageBg }};"></i> لون خلفية المحتوى</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="page_content_bg_color_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $pageBg }}" onchange="document.getElementById('page_content_bg_color').value=this.value">
                            <input type="text" name="page_content_bg_color" id="page_content_bg_color" class="form-control" value="{{ $pageBg }}" placeholder="#FFFFFF" pattern="^(#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{6})?$" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('page_content_bg_color_picker').value=v;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-font" style="color: {{ $pageText }};"></i> لون النص</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="page_content_text_color_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $pageText }}" onchange="document.getElementById('page_content_text_color').value=this.value">
                            <input type="text" name="page_content_text_color" id="page_content_text_color" class="form-control" value="{{ $pageText }}" placeholder="#0F3D2E" pattern="^(#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{6})?$" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('page_content_text_color_picker').value=v;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.9rem;"><i class="fas fa-heading" style="color: {{ $pageTitle }};"></i> لون العنوان</label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" id="page_content_title_color_picker" style="width: 50px; height: 40px; padding: 0; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; cursor: pointer;" value="{{ $pageTitle }}" onchange="document.getElementById('page_content_title_color').value=this.value">
                            <input type="text" name="page_content_title_color" id="page_content_title_color" class="form-control" value="{{ $pageTitle }}" placeholder="#5FB38E" pattern="^(#[0-9A-Fa-f]{6}|[0-9A-Fa-f]{6})?$" style="flex:1;" oninput="var v=this.value; if(v.length===6&&/^[0-9A-Fa-f]+$/.test(v)) v='#'+v; if(/^#[0-9A-Fa-f]{6}$/i.test(v)) document.getElementById('page_content_title_color_picker').value=v;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1);">
            <button type="button" class="btn btn-secondary" style="background: rgba(239, 68, 68, 0.2); border-color: rgba(239, 68, 68, 0.3); color: #ef4444;" onclick="if(confirm('هل تريد إعادة جميع ألوان الأقسام إلى الافتراضي؟')) { var f=document.createElement('form'); f.method='POST'; f.action='{{ route('dashboard.settings.reset-colors') }}'; var t=document.createElement('input'); t.name='_token'; t.value=document.querySelector('input[name=_token]').value; f.appendChild(t); document.body.appendChild(f); f.submit(); }">
                <i class="fas fa-undo"></i> إعادة ضبط ألوان الأقسام إلى الافتراضي
            </button>
        </div>
    </div>
</div>
