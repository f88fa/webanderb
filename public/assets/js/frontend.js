// JavaScript للواجهة الأمامية
// تعليم المتصفح بأن JavaScript يعمل (لاستخدامه في CSS كـ fallback)
document.documentElement.classList.add('js-enabled');

// التحكم في حجم اللوقو داخل البطاقة من قاعدة البيانات
(function initLogoIconSizeControl() {
    const DEFAULT_ICON_SIZE = 70; // حجم افتراضي للأيقونة بالبكسل
    
    // تطبيق الحجم من قاعدة البيانات عند تحميل الصفحة
    function applyLogoIconSize() {
        const logoCard = document.getElementById('logo-card');
        if (!logoCard) {
            setTimeout(applyLogoIconSize, 100);
            return;
        }
        
        // الحصول على الحجم من window.siteSettings (يتم تمريره من PHP)
        const iconSize = parseInt(window.siteSettings?.logoIconSize) || DEFAULT_ICON_SIZE;
        
        console.log('Logo Icon Size Debug:', {
            fromSettings: window.siteSettings?.logoIconSize,
            parsed: iconSize,
            default: DEFAULT_ICON_SIZE,
            logoCard: logoCard
        });
        
        // التحقق من حجم الشاشة
        const isMobile = window.innerWidth <= 768;
        
        // على الموبايل: تصغير البطاقة قليلاً (90%) لتجنب التداخل مع القائمة الجانبية
        // على الديسكتوب: البطاقة بنفس حجم اللوقو تماماً
        const cardSize = isMobile ? Math.round(iconSize * 0.9) : iconSize;
        
        // تطبيق حجم البطاقة
        logoCard.style.setProperty('width', cardSize + 'px', 'important');
        logoCard.style.setProperty('height', cardSize + 'px', 'important');
        logoCard.style.setProperty('min-width', cardSize + 'px', 'important');
        logoCard.style.setProperty('min-height', cardSize + 'px', 'important');
        logoCard.style.setProperty('max-width', cardSize + 'px', 'important');
        logoCard.style.setProperty('max-height', cardSize + 'px', 'important');
        logoCard.style.setProperty('padding', '0', 'important');
        
        // على الموبايل: تصغير اللوقو أيضاً بنفس النسبة
        const logoSize = isMobile ? Math.round(iconSize * 0.9) : iconSize;
        
        // تطبيق الحجم على الصورة إذا كانت موجودة
        const logoImage = logoCard.querySelector('.logo-card-image');
        if (logoImage) {
            console.log('Applying size to logo image:', logoSize);
            logoImage.style.setProperty('width', logoSize + 'px', 'important');
            logoImage.style.setProperty('height', logoSize + 'px', 'important');
            logoImage.style.setProperty('max-width', logoSize + 'px', 'important');
            logoImage.style.setProperty('max-height', logoSize + 'px', 'important');
        } else {
            console.log('Logo image not found');
        }
        
        // تطبيق الحجم على الأيقونة (Font Awesome) إذا كانت موجودة
        const logoIcon = logoCard.querySelector('.logo-card-icon i');
        if (logoIcon) {
            console.log('Applying size to logo icon (Font Awesome):', logoSize);
            // تحويل من px إلى rem (افتراضي 3.5rem = 70px تقريباً)
            const iconSizeRem = (logoSize / DEFAULT_ICON_SIZE) * 3.5;
            logoIcon.style.setProperty('font-size', iconSizeRem + 'rem', 'important');
        } else {
            console.log('Logo icon (Font Awesome) not found');
        }
        
        // تطبيق الحجم على صورة الأيقونة إذا كانت موجودة
        const logoIconImage = logoCard.querySelector('.logo-card-icon img');
        if (logoIconImage) {
            console.log('Applying size to logo icon image:', logoSize);
            logoIconImage.style.setProperty('width', logoSize + 'px', 'important');
            logoIconImage.style.setProperty('height', logoSize + 'px', 'important');
            logoIconImage.style.setProperty('max-width', logoSize + 'px', 'important');
            logoIconImage.style.setProperty('max-height', logoSize + 'px', 'important');
        } else {
            console.log('Logo icon image not found');
        }
        
        // تطبيق الحجم على div الأيقونة نفسه
        const logoIconDiv = logoCard.querySelector('.logo-card-icon');
        if (logoIconDiv) {
            console.log('Applying size to logo icon div:', logoSize);
            logoIconDiv.style.setProperty('width', logoSize + 'px', 'important');
            logoIconDiv.style.setProperty('height', logoSize + 'px', 'important');
            logoIconDiv.style.setProperty('min-width', logoSize + 'px', 'important');
            logoIconDiv.style.setProperty('min-height', logoSize + 'px', 'important');
        } else {
            console.log('Logo icon div not found');
        }
        
        // إعادة تطبيق الحجم عند تغيير حجم الشاشة
        if (!window.logoSizeResizeHandler) {
            window.logoSizeResizeHandler = function() {
                applyLogoIconSize();
            };
            window.addEventListener('resize', window.logoSizeResizeHandler);
        }
    }
    
    // تطبيق الحجم عند تحميل الصفحة
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', applyLogoIconSize);
    } else {
        applyLogoIconSize();
    }
    
    // تطبيق إضافي بعد تحميل الصفحة بالكامل
    window.addEventListener('load', applyLogoIconSize);
})();

// تطبيق شفافية الفيديو/الصورة في الهيرو من الإعدادات (يعمل على الاستضافة حتى مع كاش أو تجاوز CSS)
(function applyHeroOpacityFromSettings() {
    function run() {
        document.querySelectorAll('.hero-background-video[data-hero-opacity], .hero-background-overlay[data-hero-opacity]').forEach(function(el) {
            var val = el.getAttribute('data-hero-opacity');
            if (val !== null && val !== '') {
                el.style.setProperty('opacity', val, 'important');
            }
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
    window.addEventListener('load', run);
})();

// التحكم في حجم أيقونة الهيرو من قاعدة البيانات
(function initHeroIconSizeControl() {
    const DEFAULT_PLANET_SIZE = 200;
    const DEFAULT_ICON_SIZE = 4; // rem
    const DEFAULT_IMAGE_SIZE = 75; // percentage (زيادة من 60% إلى 75%)
    
    // تطبيق الحجم من قاعدة البيانات عند تحميل الصفحة
    function applyHeroIconSize() {
        const planet = document.getElementById('hero-planet-icon');
        const astroPlanet = document.getElementById('hero-planet');
        if (!planet || !astroPlanet) {
            setTimeout(applyHeroIconSize, 100);
            return;
        }
        
        // الحصول على الحجم من window.siteSettings (يتم تمريره من PHP)
        const size = window.siteSettings?.heroIconSize || DEFAULT_PLANET_SIZE;
        
        planet.style.width = size + 'px';
        planet.style.height = size + 'px';
        
        // تحديث حجم astro-planet بشكل متناسب
        const astroSize = (size / DEFAULT_PLANET_SIZE) * 300;
        astroPlanet.style.width = astroSize + 'px';
        astroPlanet.style.height = astroSize + 'px';
        
        // تحديث حجم الأيقونة بشكل متناسب
        const icon = planet.querySelector('i');
        if (icon) {
            const iconSize = (size / DEFAULT_PLANET_SIZE) * DEFAULT_ICON_SIZE;
            icon.style.fontSize = iconSize + 'rem';
        }
        
        // تحديث حجم الصورة بشكل متناسب (75% بدلاً من 60%)
        const image = planet.querySelector('.planet-icon-image');
        if (image) {
            const imageSize = (size / DEFAULT_PLANET_SIZE) * DEFAULT_IMAGE_SIZE;
            const maxImageSize = (size / DEFAULT_PLANET_SIZE) * 150; // زيادة من 120px إلى 150px
            image.style.width = imageSize + '%';
            image.style.height = imageSize + '%';
            image.style.maxWidth = maxImageSize + 'px';
            image.style.maxHeight = maxImageSize + 'px';
        }
    }
    
    // تطبيق الحجم عند تحميل الصفحة
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', applyHeroIconSize);
    } else {
        applyHeroIconSize();
    }
    
    // تطبيق إضافي بعد تحميل الصفحة بالكامل
    window.addEventListener('load', applyHeroIconSize);
})();

// تهيئة قائمة الجوال - يتم تنفيذها فوراً (قبل DOMContentLoaded)
(function initMobileMenu() {
    let menuInitialized = false;
    let retryCount = 0;
    const MAX_RETRIES = 50; // محاولة لمدة 5 ثواني (50 * 100ms)
    
    function setupMobileMenu() {
        // تجنب التهيئة المتعددة
        if (menuInitialized) return;
        
        const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
        const mobileMenuWrapper = document.querySelector('.mobile-menu-wrapper') || 
                                  document.querySelector('.nav-menu-wrapper.mobile-menu-wrapper');
        const body = document.body;
        
        if (!mobileNavToggle || !mobileMenuWrapper) {
            retryCount++;
            // محاولة مرة أخرى بعد 100ms إذا لم نتجاوز الحد الأقصى
            if (retryCount < MAX_RETRIES && !menuInitialized) {
                setTimeout(setupMobileMenu, 100);
            }
            return;
        }
        
        // منع التهيئة المتعددة
        menuInitialized = true;
        
        try {
            // إزالة أي event listeners سابقة
            const newToggle = mobileNavToggle.cloneNode(true);
            if (mobileNavToggle.parentNode) {
                mobileNavToggle.parentNode.replaceChild(newToggle, mobileNavToggle);
            }
            
            // إضافة event listener جديد
            newToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                
                const currentMenuWrapper = document.querySelector('.mobile-menu-wrapper') || 
                                          document.querySelector('.nav-menu-wrapper.mobile-menu-wrapper');
                if (currentMenuWrapper) {
                    currentMenuWrapper.classList.toggle('active');
                }
                if (body) {
                    body.classList.toggle('menu-open');
                }
                this.classList.toggle('active');
            });
            
            // إغلاق القائمة عند النقر خارجها (مرة واحدة فقط)
            if (!document.mobileMenuOutsideClickHandler) {
                document.mobileMenuOutsideClickHandler = function(e) {
                    if (window.innerWidth <= 768) {
                        const currentToggle = document.querySelector('.mobile-nav-toggle');
                        const currentMenuWrapper = document.querySelector('.mobile-menu-wrapper') || 
                                                 document.querySelector('.nav-menu-wrapper.mobile-menu-wrapper');
                        
                        if (currentToggle && currentMenuWrapper && 
                            !currentToggle.contains(e.target) && 
                            !currentMenuWrapper.contains(e.target)) {
                            currentMenuWrapper.classList.remove('active');
                            if (body) {
                                body.classList.remove('menu-open');
                            }
                            currentToggle.classList.remove('active');
                        }
                    }
                };
                document.addEventListener('click', document.mobileMenuOutsideClickHandler);
            }
        } catch (error) {
            console.error('Error setting up mobile menu:', error);
            menuInitialized = false; // السماح بمحاولة أخرى
        }
    }
    
    // محاولة التنفيذ فوراً
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupMobileMenu);
    } else {
        setupMobileMenu();
    }
    
    // محاولة إضافية بعد تحميل الصفحة بالكامل
    window.addEventListener('load', function() {
        if (!menuInitialized) {
            retryCount = 0; // إعادة تعيين العداد
            menuInitialized = false; // إعادة تعيين للسماح بمحاولة أخرى
            setupMobileMenu();
        }
    });
})();

// تأثير الكتابة المتحركة لوصف الهيرو
function typeWriter(element, text, speed = 100) {
    let i = 0;
    element.textContent = '';
    
    function type() {
        if (i < text.length) {
            element.textContent += text.charAt(i);
            i++;
            setTimeout(type, speed);
        } else {
            // إخفاء المؤشر بعد انتهاء الكتابة
            const cursor = document.querySelector('.typing-cursor');
            if (cursor) {
                setTimeout(() => {
                    cursor.style.opacity = '0';
                }, 1000);
            }
        }
    }
    
    type();
}

document.addEventListener('DOMContentLoaded', function() {
    // ضمان تشغيل فيديو الهيرو تلقائياً
    const heroVideo = document.querySelector('.hero-background-video');
    if (heroVideo) {
        // إزالة أي إمكانية للتحكم في الفيديو
        heroVideo.controls = false;
        heroVideo.disablePictureInPicture = true;
        
        // محاولة تشغيل الفيديو تلقائياً
        const playVideo = async () => {
            try {
                await heroVideo.play();
            } catch (error) {
                // إذا فشل autoplay، نحاول مرة أخرى بعد تفاعل المستخدم
                console.log('Video autoplay failed, will retry on user interaction');
                
                // إضافة event listener لتفاعل المستخدم
                const playOnInteraction = () => {
                    heroVideo.play().catch(e => console.log('Video play error:', e));
                    document.removeEventListener('click', playOnInteraction);
                    document.removeEventListener('scroll', playOnInteraction);
                    document.removeEventListener('touchstart', playOnInteraction);
                };
                
                document.addEventListener('click', playOnInteraction, { once: true });
                document.addEventListener('scroll', playOnInteraction, { once: true });
                document.addEventListener('touchstart', playOnInteraction, { once: true });
            }
        };
        
        // التأكد من أن الفيديو يعمل بشكل متكرر
        heroVideo.addEventListener('ended', () => {
            heroVideo.currentTime = 0;
            heroVideo.play().catch(e => console.log('Video loop error:', e));
        });
        
        // محاولة التشغيل عند تحميل الصفحة
        if (heroVideo.readyState >= 2) {
            // الفيديو محمل بالفعل
            playVideo();
        } else {
            // انتظار تحميل الفيديو
            heroVideo.addEventListener('loadeddata', playVideo, { once: true });
            heroVideo.addEventListener('canplay', playVideo, { once: true });
        }
        
        // إعادة التشغيل عند إعادة التركيز على الصفحة
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && heroVideo.paused) {
                heroVideo.play().catch(e => console.log('Video resume error:', e));
            }
        });
    }
    
    // تأثير الكتابة المتحركة
    const typingText = document.getElementById('typing-text');
    const heroDescription = document.getElementById('typing-description');
    
    if (typingText && heroDescription) {
        // الحصول على النص من data attribute أو من المتغير
        let textToType = heroDescription.getAttribute('data-text') || 
                        (typeof heroDescriptionText !== 'undefined' ? heroDescriptionText : '');
        
        // إذا لم يكن هناك نص، نستخدم النص الافتراضي
        if (!textToType) {
            textToType = heroDescription.textContent.trim() || 'لوحة تحكم احترافية';
        }
        
        // بدء الكتابة بعد تأخير قصير
        setTimeout(() => {
            typeWriter(typingText, textToType, 80);
        }, 500);
    }
    // دالة مشتركة: التمرير إلى قسم حسب الهاش (#about وغيرها)
    function scrollToSectionById(sectionId) {
        const section = document.getElementById(sectionId);
        if (section) {
            const offsetTop = section.offsetTop - 80;
            window.scrollTo({
                top: Math.max(0, offsetTop),
                behavior: 'smooth'
            });
            return true;
        }
        return false;
    }
    function getAnchorIdFromHref(href) {
        if (!href) return null;
        if (href.startsWith('#')) return href.substring(1) || null;
        if (href.indexOf('#') > 0) {
            const hashPart = href.substring(href.indexOf('#') + 1);
            return hashPart || null;
        }
        return null;
    }

    // التنقل السلس لروابط القائمة الرئيسية (.nav-link)
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href') || '';
            const targetId = getAnchorIdFromHref(href);
            if (targetId && scrollToSectionById(targetId)) {
                e.preventDefault();
                navLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            } else if (href.indexOf('#') > 0) {
                const currentPath = window.location.pathname || '/';
                const linkPath = href.split('#')[0].replace(/^https?:\/\/[^/]+/, '') || '/';
                if ((currentPath === linkPath || (currentPath === '' && linkPath === '/')) && targetId && scrollToSectionById(targetId)) {
                    e.preventDefault();
                }
            }
        });
    });

    // نفس التنقل لروابط القائمة المنسدلة (.dropdown-item) مثل "من نحن" تحت "عن الجمعية"
    document.querySelectorAll('.dropdown-item[href*="#"]').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href') || '';
            const targetId = getAnchorIdFromHref(href);
            if (targetId && scrollToSectionById(targetId)) {
                e.preventDefault();
                navLinks.forEach(l => l.classList.remove('active'));
                const parentDropdown = this.closest('.nav-item-dropdown');
                if (parentDropdown) {
                    const mainLink = parentDropdown.querySelector('.nav-link');
                    if (mainLink) mainLink.classList.add('active');
                }
                document.body.classList.remove('menu-open');
                document.querySelectorAll('.nav-menu-wrapper.active, .mobile-menu-wrapper.active').forEach(el => el.classList.remove('active'));
                document.querySelectorAll('.nav-toggle.active, .mobile-nav-toggle.active').forEach(el => el.classList.remove('active'));
            }
        });
    });

    // قائمة الجوال - تهيئة إضافية للتأكد من العمل
    const navToggle = document.querySelector('.nav-toggle');
    const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const navbar = document.querySelector('.navbar');
    const navMenuWrapper = document.querySelector('.nav-menu-wrapper');
    const mobileMenuWrapper = document.querySelector('.mobile-menu-wrapper');
    const body = document.body;
    
    // زر القائمة للجوال (المنفصل) - تهيئة إضافية
    if (mobileNavToggle && mobileMenuWrapper) {
        // إزالة أي event listeners سابقة
        const newMobileToggle = mobileNavToggle.cloneNode(true);
        mobileNavToggle.parentNode.replaceChild(newMobileToggle, mobileNavToggle);
        
        newMobileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            
            const currentMenuWrapper = document.querySelector('.mobile-menu-wrapper');
            if (currentMenuWrapper) {
                currentMenuWrapper.classList.toggle('active');
            }
            body.classList.toggle('menu-open');
            this.classList.toggle('active');
        });
    }
    
    // زر القائمة القديم (للديسكتوب)
    if (navToggle) {
        navToggle.addEventListener('click', function() {
            // على الديسكتوب: السلوك العادي
            if (window.innerWidth > 768) {
                if (navbar) {
                    navbar.classList.toggle('active');
                }
                if (navMenu) {
                    navMenu.classList.toggle('active');
                }
            }
        });
    }

    // القوائم المنسدلة على الموبايل
    function closeAllMobileDropdowns() {
        document.querySelectorAll('.nav-item-dropdown').forEach(item => {
            item.classList.remove('active');
            const menu = item.querySelector('.dropdown-menu');
            if (menu) {
                menu.style.maxHeight = '0';
            }
        });
    }
    
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            // على الموبايل فقط
            if (window.innerWidth <= 768) {
                e.preventDefault();
                e.stopPropagation(); // منع إغلاق القائمة الرئيسية
                
                const dropdown = this.closest('.nav-item-dropdown');
                if (!dropdown) return;
                
                const isActive = dropdown.classList.contains('active');
                const menu = dropdown.querySelector('.dropdown-menu');
                
                // إغلاق جميع القوائم المنسدلة الأخرى أولاً
                document.querySelectorAll('.nav-item-dropdown').forEach(item => {
                    if (item !== dropdown) {
                        item.classList.remove('active');
                        const otherMenu = item.querySelector('.dropdown-menu');
                        if (otherMenu) {
                            otherMenu.style.maxHeight = '0';
                        }
                    }
                });
                
                // تبديل حالة القائمة الحالية
                if (isActive) {
                    // إغلاق القائمة الحالية
                    dropdown.classList.remove('active');
                    if (menu) {
                        menu.style.maxHeight = '0';
                    }
                } else {
                    // فتح القائمة الحالية
                    dropdown.classList.add('active');
                    if (menu) {
                        menu.style.maxHeight = menu.scrollHeight + 'px';
                    }
                }
            }
        });
    });
    
    // إغلاق القوائم المنسدلة عند النقر خارجها على الموبايل
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            // إذا لم يكن النقر على dropdown toggle أو داخل القائمة المنسدلة
            if (!e.target.closest('.nav-item-dropdown') && !e.target.closest('.dropdown-toggle')) {
                closeAllMobileDropdowns();
            }
        }
    });
    
    // إغلاق جميع القوائم المنسدلة عند إغلاق القائمة الرئيسية على الموبايل
    if (mobileNavToggle) {
        mobileNavToggle.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                setTimeout(() => {
                    const mobileMenuWrapper = document.querySelector('.mobile-menu-wrapper');
                    if (mobileMenuWrapper && !mobileMenuWrapper.classList.contains('active')) {
                        // إذا كانت القائمة الرئيسية مغلقة، أغلق جميع القوائم المنسدلة
                        closeAllMobileDropdowns();
                    }
                }, 100);
            }
        });
    }
    
    // إغلاق القائمة عند النقر على رابط (غير dropdown)
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                // إذا لم يكن رابط dropdown، أغلق القائمة الرئيسية
                if (!this.closest('.nav-item-dropdown')) {
                    // إغلاق جميع القوائم المنسدلة
                    closeAllMobileDropdowns();
                    
                    // إغلاق القائمة الرئيسية
                    const mobileMenuWrapper = document.querySelector('.mobile-menu-wrapper');
                    const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
                    const body = document.body;
                    if (mobileMenuWrapper) {
                        mobileMenuWrapper.classList.remove('active');
                    }
                    if (body) {
                        body.classList.remove('menu-open');
                    }
                    if (mobileNavToggle) {
                        mobileNavToggle.classList.remove('active');
                    }
                }
            }
        });
    });
    
    // إغلاق القائمة عند النقر على زر تسجيل المستفيدين في الموبايل
    const mobileRegisterBtn = document.querySelector('.nav-mobile-register-btn');
    if (mobileRegisterBtn) {
        mobileRegisterBtn.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                // إغلاق جميع القوائم المنسدلة
                closeAllMobileDropdowns();
                
                // إغلاق القائمة الرئيسية
                if (navbar) {
                    navbar.classList.remove('active');
                }
                if (navMenu) {
                    navMenu.classList.remove('active');
                }
            }
        });
    }
    
    // على الديسكتوب: التحكم الكامل في القوائم المنسدلة
    let dropdownTimeouts = {};
    let currentOpenDropdown = null;
    
    function closeDropdown(item) {
        const menu = item.querySelector('.dropdown-menu');
        if (menu) {
            menu.style.opacity = '0';
            menu.style.visibility = 'hidden';
            menu.style.transform = 'translateY(-5px)';
            menu.style.pointerEvents = 'none';
        }
        if (dropdownTimeouts[item]) {
            clearTimeout(dropdownTimeouts[item]);
            delete dropdownTimeouts[item];
        }
    }
    
    function openDropdown(item) {
        const menu = item.querySelector('.dropdown-menu');
        if (!menu) return;
        
        // إغلاق جميع القوائم الأخرى أولاً
        document.querySelectorAll('.nav-item-dropdown').forEach(otherItem => {
            if (otherItem !== item) {
                closeDropdown(otherItem);
            }
        });
        
        // إلغاء أي timeout معلق لهذا العنصر
        if (dropdownTimeouts[item]) {
            clearTimeout(dropdownTimeouts[item]);
            delete dropdownTimeouts[item];
        }
        
        // فتح القائمة الحالية
        menu.style.opacity = '1';
        menu.style.visibility = 'visible';
        menu.style.transform = 'translateY(0)';
        menu.style.pointerEvents = 'auto';
        currentOpenDropdown = item;
    }
    
    function handleDesktopDropdowns() {
        // فقط على الديسكتوب
        if (window.innerWidth <= 768) {
            // إزالة جميع event listeners على الموبايل
            document.querySelectorAll('.nav-item-dropdown').forEach(item => {
                const menu = item.querySelector('.dropdown-menu');
                if (menu) {
                    // إعادة تعيين CSS
                    menu.style.opacity = '';
                    menu.style.visibility = '';
                    menu.style.transform = '';
                    menu.style.pointerEvents = '';
                }
            });
            return;
        }
        
        const dropdownItems = document.querySelectorAll('.nav-item-dropdown');
        
        dropdownItems.forEach(item => {
            const menu = item.querySelector('.dropdown-menu');
            if (!menu) return;
            
            // التأكد من أن القائمة مغلقة في البداية
            closeDropdown(item);
            
            // عند دخول المؤشر إلى القائمة الرئيسية
            function onMouseEnter() {
                openDropdown(item);
            }
            
            // عند خروج المؤشر - تأخير لإغلاق القائمة
            function onMouseLeave(e) {
                // التحقق من أن المؤشر لم ينتقل إلى القائمة المنسدلة
                const relatedTarget = e.relatedTarget;
                if (relatedTarget && (item.contains(relatedTarget) || menu.contains(relatedTarget))) {
                    return; // المؤشر لا يزال داخل القائمة
                }
                
                // إلغاء أي timeout سابق
                if (dropdownTimeouts[item]) {
                    clearTimeout(dropdownTimeouts[item]);
                }
                
                // تأخير قبل الإغلاق
                dropdownTimeouts[item] = setTimeout(() => {
                    // التحقق مرة أخرى قبل الإغلاق
                    if (!item.matches(':hover') && !menu.matches(':hover')) {
                        closeDropdown(item);
                        if (currentOpenDropdown === item) {
                            currentOpenDropdown = null;
                        }
                    }
                }, 200);
            }
            
            // إزالة أي event listeners سابقة
            const newOnMouseEnter = onMouseEnter.bind(item);
            const newOnMouseLeave = onMouseLeave.bind(item);
            
            item.removeEventListener('mouseenter', newOnMouseEnter);
            item.removeEventListener('mouseleave', newOnMouseLeave);
            menu.removeEventListener('mouseenter', newOnMouseEnter);
            menu.removeEventListener('mouseleave', newOnMouseLeave);
            
            // إضافة event listeners جديدة
            item.addEventListener('mouseenter', newOnMouseEnter);
            item.addEventListener('mouseleave', newOnMouseLeave);
            menu.addEventListener('mouseenter', newOnMouseEnter);
            menu.addEventListener('mouseleave', newOnMouseLeave);
        });
    }
    
    // تشغيل عند تحميل الصفحة
    handleDesktopDropdowns();
    
    // إعادة التشغيل عند تغيير حجم النافذة
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            // إغلاق جميع القوائم عند تغيير حجم النافذة
            document.querySelectorAll('.nav-item-dropdown').forEach(item => {
                closeDropdown(item);
            });
            currentOpenDropdown = null;
            // إعادة تهيئة
            handleDesktopDropdowns();
        }, 100);
    });

    // تحديث الرابط النشط عند التمرير
    const sections = document.querySelectorAll('section[id]');
    
    function updateActiveNav() {
        const scrollY = window.pageYOffset;
        
        sections.forEach(section => {
            const sectionHeight = section.offsetHeight;
            const sectionTop = section.offsetTop - 100;
            const sectionId = section.getAttribute('id');
            
            if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${sectionId}`) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }

    window.addEventListener('scroll', updateActiveNav);

    // معالجة الروابط التي تحتوي على hash عند تحميل الصفحة
    function scrollToHash() {
        if (window.location.hash) {
            const hash = window.location.hash.substring(1);
            // البحث عن العنصر بطرق متعددة
            let targetSection = document.getElementById(hash);
            
            // إذا لم يُوجد بالـ ID، جرب البحث بالـ class أو name
            if (!targetSection) {
                targetSection = document.querySelector(`section[id="${hash}"], [id="${hash}"]`);
            }
            
            if (targetSection) {
                // حساب الموضع مع مراعاة الـ header
                const headerOffset = 100;
                const elementPosition = targetSection.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
                
                // تحديث الرابط النشط
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${hash}`) {
                        link.classList.add('active');
                    }
                });
                
                return true;
            }
        }
        return false;
    }

    // محاولة التمرير عند تحميل الصفحة
    if (window.location.hash) {
        // محاولة فورية
        setTimeout(() => {
            if (!scrollToHash()) {
                // إذا فشلت، انتظر قليلاً ثم حاول مرة أخرى
                setTimeout(() => {
                    if (!scrollToHash()) {
                        // محاولة أخيرة بعد تحميل كامل
                        window.addEventListener('load', () => {
                            setTimeout(() => {
                                scrollToHash();
                            }, 500);
                        });
                    }
                }, 500);
            }
        }, 100);
    }
    
    // معالجة hashchange event
    window.addEventListener('hashchange', function() {
        setTimeout(scrollToHash, 100);
    });
    
    // معالجة الروابط الخارجية التي تحتوي على hash
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a[href^="#"]');
        if (link && link.getAttribute('href') !== '#') {
            const hash = link.getAttribute('href').substring(1);
            const targetSection = document.getElementById(hash);
            
            if (targetSection && link.hostname === window.location.hostname) {
                e.preventDefault();
                setTimeout(() => scrollToHash(), 50);
            }
        }
    });

    // تم إلغاء تأثير الظهور عند التمرير - الأقسام تظهر دائماً (يُتحكم بها من CSS)
    function initMobileAnimations() {
        const animatedElements = document.querySelectorAll('.news-card, .about-content, .contact-item, .media-card, .video-card, .slide-item');
        animatedElements.forEach(el => {
            el.style.opacity = '1';
            el.style.transform = 'none';
            el.classList.add('mobile-animated');
        });
    }

    // تهيئة جميع التأثيرات عند تحميل الصفحة
    function initAllMobileAnimations() {
        initMobileAnimations();
        initAboutAnimations();
        initVisionMissionAnimations();
        initServicesAnimations();
    }

    // تهيئة التأثيرات عند تحميل الصفحة
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initAllMobileAnimations, 300);
        });
    } else {
        setTimeout(initAllMobileAnimations, 300);
    }

    // إعادة تهيئة عند تحميل الصفحة بالكامل
    window.addEventListener('load', function() {
        setTimeout(initAllMobileAnimations, 500);
    });

    // دالة لتهيئة قسم "من نحن" - إظهار المحتوى مباشرة (بدون تأثير اختفاء عند التمرير)
    function initAboutAnimations() {
        const aboutSection = document.querySelector('.about-section');
        if (!aboutSection) return;

        const header = aboutSection.querySelector('.about-header');
        const textContent = aboutSection.querySelector('.about-text-content');
        const imageWrapper = aboutSection.querySelector('.about-image-wrapper');
        const description = aboutSection.querySelector('.about-description');
        const subtitle = aboutSection.querySelector('.about-subtitle-wrapper');
        const ctaWrapper = aboutSection.querySelector('.about-cta-wrapper');
        const stats = aboutSection.querySelectorAll('.stat-item');
        const featureCards = aboutSection.querySelectorAll('.feature-card');
        const achievementCard = aboutSection.querySelector('.achievement-card');

        [header, textContent, imageWrapper, description, subtitle, ctaWrapper, achievementCard].filter(Boolean).forEach(el => {
            el.classList.add('animated', 'mobile-animated');
        });
        stats.forEach(stat => {
            stat.classList.add('animated', 'mobile-animated');
            const numberEl = stat.querySelector('.stat-number');
            if (numberEl) animateCounter(numberEl);
        });
        featureCards.forEach(card => card.classList.add('animated', 'mobile-animated'));
    }

    // دالة أنيميشن الأرقام — تضمن الوصول للرقم النهائي (تجنب تعليق العداد)
    function animateCounter(element) {
        const raw = element.textContent || '';
        const target = parseInt(raw.replace(/\D/g, ''), 10);
        if (isNaN(target) || target < 0) return;
        const suffix = raw.replace(/[\d\s]/g, '') || ''; // حفظ + أو % أو غيره
        const duration = 2000;
        const steps = Math.max(1, Math.floor(duration / 16));
        const stepDuration = duration / steps;
        let step = 0;

        const timer = setInterval(() => {
            step++;
            const progress = Math.min(step / steps, 1);
            const current = Math.round(target * progress);
            element.textContent = current + suffix;

            if (step >= steps) {
                element.textContent = target + suffix;
                clearInterval(timer);
            }
        }, stepDuration);
    }

    // تأثير parallax للصورة
    window.addEventListener('scroll', function() {
        const aboutImage = document.querySelector('.about-image-container');
        if (aboutImage) {
            const scrolled = window.pageYOffset;
            const aboutSection = document.querySelector('.about-section');
            if (aboutSection) {
                const sectionTop = aboutSection.offsetTop;
                const sectionHeight = aboutSection.offsetHeight;
                const windowHeight = window.innerHeight;
                
                if (scrolled + windowHeight > sectionTop && scrolled < sectionTop + sectionHeight) {
                    const parallaxValue = (scrolled - sectionTop + windowHeight) * 0.1;
                    aboutImage.style.transform = `translateY(${parallaxValue}px)`;
                }
            }
        }
    });

    // تأثير parallax للنجوم
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const stars = document.querySelector('.stars');
        const stars2 = document.querySelector('.stars2');
        const stars3 = document.querySelector('.stars3');
        
        if (stars) stars.style.transform = `translateY(${scrolled * 0.5}px)`;
        if (stars2) stars2.style.transform = `translateY(${scrolled * 0.3}px)`;
        if (stars3) stars3.style.transform = `translateY(${scrolled * 0.1}px)`;
    });

    // دالة لتهيئة قسم الرؤية والرسالة - إظهار البطاقات مباشرة (بدون تأثير اختفاء عند التمرير)
    function initVisionMissionAnimations() {
        const visionMissionSection = document.querySelector('.vision-mission-section');
        if (!visionMissionSection) return;

        const visionCards = visionMissionSection.querySelectorAll('.vision-card, .mission-card');
        visionCards.forEach(card => {
            card.style.opacity = '1';
            card.style.transform = 'translateX(0) translateY(0)';
            card.classList.add('visible', 'mobile-animated', 'animated');
        });
    }

    // تهيئة أنيميشن قسم الرؤية والرسالة
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initVisionMissionAnimations, 100);
        });
    } else {
        setTimeout(initVisionMissionAnimations, 100);
    }

    window.addEventListener('load', function() {
        setTimeout(initVisionMissionAnimations, 200);
    });

    // تأثير 3D عند تحريك الماوس - فقط على الديسكتوب
    if (window.innerWidth > 768) {
        const visionCards = document.querySelectorAll('.vision-card, .mission-card');
        visionCards.forEach(card => {
            card.addEventListener('mousemove', function(e) {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = (y - centerY) / 30;
                const rotateY = (centerX - x) / 30;
                
                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.03)`;
            });
            
            card.addEventListener('mouseleave', function() {
                card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale(1)';
            });
        });
    }

    // دالة لتهيئة قسم الخدمات - إظهار البطاقات مباشرة (بدون تأثير اختفاء عند التمرير)
    function initServicesAnimations() {
        const servicesSection = document.querySelector('.services-section');
        if (!servicesSection) return;

        const serviceCards = servicesSection.querySelectorAll('.service-card');
        serviceCards.forEach(card => {
            card.style.opacity = '1';
            card.style.transform = 'translateX(0) translateY(0) scale(1)';
            card.classList.add('animated', 'mobile-animated');
        });
    }

    // تهيئة أنيميشن قسم الخدمات
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initServicesAnimations, 100);
        });
    } else {
        setTimeout(initServicesAnimations, 100);
    }

    window.addEventListener('load', function() {
        setTimeout(initServicesAnimations, 200);
    });

    // تأثير 3D عند تحريك الماوس على بطاقات الخدمات
    const serviceCards = document.querySelectorAll('.service-card');
    serviceCards.forEach(card => {
        card.addEventListener('mousemove', function(e) {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 25;
            const rotateY = (centerX - x) / 25;
            
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.05)`;
        });
        
        card.addEventListener('mouseleave', function() {
            card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale(1)';
        });
    });

    // تأثيرات متقدمة لقسم الشركاء
    const partnersSection = document.querySelector('.partners-section');
    if (partnersSection) {
        const partnersObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const cards = entry.target.querySelectorAll('.partner-card');
                    cards.forEach((card, index) => {
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0) scale(1)';
                        }, index * 100);
                    });
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        });
        
        partnersObserver.observe(partnersSection);
    }

    // تأثير hover على بطاقات الأخبار
    const newsCards = document.querySelectorAll('.news-card');
    newsCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Modal لعرض صورة الرخصة
    const licenseModal = document.getElementById('licenseModal');
    const licenseThumbnail = document.querySelector('.license-thumbnail');
    const licenseModalClose = document.querySelector('.license-modal-close');
    
    if (licenseThumbnail && licenseModal) {
        licenseThumbnail.addEventListener('click', function() {
            licenseModal.classList.add('show');
            document.body.style.overflow = 'hidden';
        });
    }
    
    if (licenseModalClose) {
        licenseModalClose.addEventListener('click', function() {
            licenseModal.classList.remove('show');
            document.body.style.overflow = '';
        });
    }
    
    // إغلاق Modal عند النقر خارج الصورة
    if (licenseModal) {
        licenseModal.addEventListener('click', function(e) {
            if (e.target === licenseModal) {
                licenseModal.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
        
        // إغلاق Modal عند الضغط على ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && licenseModal.classList.contains('show')) {
                licenseModal.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
    }

    // الأزرار المتحركة - إظهارها عند التمرير
    const floatingButtons = document.querySelectorAll('.floating-button');
    let lastScrollTop = 0;
    let scrollTimeout;

    function handleScroll() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // إظهار الأزرار بعد التمرير لأسفل 300px
        if (scrollTop > 300) {
            floatingButtons.forEach(button => {
                button.classList.add('show');
            });
        } else {
            floatingButtons.forEach(button => {
                button.classList.remove('show');
            });
        }
        
        lastScrollTop = scrollTop;
    }

    // استخدام throttle لتحسين الأداء
    window.addEventListener('scroll', function() {
        if (!scrollTimeout) {
            window.requestAnimationFrame(function() {
                handleScroll();
                scrollTimeout = null;
            });
            scrollTimeout = true;
        }
    }, { passive: true });

    // إظهار الأزرار عند تحميل الصفحة إذا كان المستخدم في منتصف الصفحة
    if (window.pageYOffset > 300) {
        floatingButtons.forEach(button => {
            button.classList.add('show');
        });
    }

    // السلايدر المتحرك
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide-item');
    const indicators = document.querySelectorAll('.slide-indicator');
    const slidesWrapper = document.getElementById('mediaSlides');
    let slideInterval;
    
    if (slides.length > 0 && slidesWrapper) {
        function pauseAllVideos() {
            slides.forEach((slide, index) => {
                const video = slide.querySelector('video');
                const iframe = slide.querySelector('iframe');
                
                if (video) {
                    video.pause();
                }
                if (iframe) {
                    // إيقاف YouTube video
                    iframe.src = iframe.src.replace('autoplay=1', 'autoplay=0');
                }
            });
        }
        
        function playCurrentVideo() {
            const currentSlideElement = slides[currentSlide];
            if (currentSlideElement) {
                const video = currentSlideElement.querySelector('video');
                const iframe = currentSlideElement.querySelector('iframe');
                
                if (video) {
                    video.play().catch(e => console.log('Video play error:', e));
                }
                if (iframe && iframe.dataset.type === 'youtube') {
                    // إعادة تشغيل YouTube video
                    const youtubeId = iframe.src.match(/embed\/([^?]+)/)?.[1];
                    if (youtubeId) {
                        iframe.src = `https://www.youtube.com/embed/${youtubeId}?autoplay=1&loop=1&playlist=${youtubeId}&mute=1&controls=0&showinfo=0&rel=0&modestbranding=1`;
                    }
                }
            }
        }
        
        function updateSlide() {
            pauseAllVideos();
            slidesWrapper.style.transform = `translateX(-${currentSlide * 100}%)`;
            
            indicators.forEach((indicator, index) => {
                if (index === currentSlide) {
                    indicator.classList.add('active');
                } else {
                    indicator.classList.remove('active');
                }
            });
            
            // تشغيل الفيديو في الشريحة الحالية بعد الانتقال
            setTimeout(() => {
                playCurrentVideo();
            }, 300);
        }
        
        function moveSlide(direction) {
            currentSlide += direction;
            
            if (currentSlide < 0) {
                currentSlide = slides.length - 1;
            } else if (currentSlide >= slides.length) {
                currentSlide = 0;
            }
            
            updateSlide();
            
            // إعادة تشغيل التبديل التلقائي
            resetAutoSlide();
        }
        
        function goToSlide(index) {
            currentSlide = index;
            updateSlide();
            
            // إعادة تشغيل التبديل التلقائي
            resetAutoSlide();
        }
        
        function resetAutoSlide() {
            clearInterval(slideInterval);
            
            // تبديل تلقائي كل 5 ثوان (فقط للصور، الفيديوهات تبقى تعمل)
            if (slides.length > 1) {
                slideInterval = setInterval(() => {
                    const currentSlideElement = slides[currentSlide];
                    const isVideo = currentSlideElement && currentSlideElement.dataset.type === 'video';
                    
                    // إذا كانت الشريحة الحالية فيديو، انتقل للشريحة التالية بعد 10 ثوان
                    if (isVideo) {
                        setTimeout(() => {
                            moveSlide(1);
                        }, 10000);
                    } else {
                        moveSlide(1);
                    }
                }, isVideo ? 10000 : 5000);
            }
        }
        
        // جعل الدوال متاحة عالمياً
        window.moveSlide = moveSlide;
        window.goToSlide = goToSlide;
        
        // تهيئة الشريحة الأولى
        updateSlide();
        
        // بدء التبديل التلقائي
        resetAutoSlide();
    }
});

// تم إلغاء تأثير الظهور عند التمرير - إظهار البطاقات مباشرة دون انتظار التمرير
document.addEventListener('DOMContentLoaded', function() {
    const allCards = document.querySelectorAll(
        '.about-features-section .feature-card, ' +
        '.vision-mission-section .vision-card, ' +
        '.vision-mission-section .mission-card, ' +
        '.services-section .service-card, ' +
        '.projects-section .project-card'
    );
    allCards.forEach((card) => {
        card.classList.add('animate-on-scroll', 'animated');
        card.style.opacity = '1';
        card.style.transform = 'none';
    });
});

// Hero Slider JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const heroSlider = document.getElementById('heroSlider');
    if (!heroSlider) return;
    
    const slides = heroSlider.querySelectorAll('.hero-slide');
    const indicators = document.querySelectorAll('.hero-slider-indicator');
    const prevBtn = document.querySelector('.hero-slider-prev');
    const nextBtn = document.querySelector('.hero-slider-next');
    
    if (slides.length <= 1) return; // لا حاجة للسلايدر إذا كانت هناك صورة واحدة فقط
    
    let currentSlide = 0;
    let slideInterval;
    const slideDuration = 5000; // 5 ثواني
    
    // عرض الشريحة الحالية
    function showSlide(index) {
        // إخفاء جميع الشرائح
        slides.forEach((slide, i) => {
            slide.classList.remove('active');
            if (indicators[i]) {
                indicators[i].classList.remove('active');
            }
        });
        
        // عرض الشريحة المحددة
        if (slides[index]) {
            slides[index].classList.add('active');
            if (indicators[index]) {
                indicators[index].classList.add('active');
            }
        }
        
        currentSlide = index;
    }
    
    // الانتقال للشريحة التالية
    function nextSlide() {
        const next = (currentSlide + 1) % slides.length;
        showSlide(next);
    }
    
    // الانتقال للشريحة السابقة
    function prevSlide() {
        const prev = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(prev);
    }
    
    // بدء التبديل التلقائي
    function startAutoSlide() {
        slideInterval = setInterval(nextSlide, slideDuration);
    }
    
    // إيقاف التبديل التلقائي
    function stopAutoSlide() {
        if (slideInterval) {
            clearInterval(slideInterval);
        }
    }
    
    // إعادة تشغيل التبديل التلقائي
    function restartAutoSlide() {
        stopAutoSlide();
        startAutoSlide();
    }
    
    // أحداث الأزرار
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextSlide();
            restartAutoSlide();
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevSlide();
            restartAutoSlide();
        });
    }
    
    // أحداث المؤشرات
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            showSlide(index);
            restartAutoSlide();
        });
    });
    
    // إيقاف التبديل التلقائي عند التمرير فوق السلايدر
    heroSlider.addEventListener('mouseenter', stopAutoSlide);
    heroSlider.addEventListener('mouseleave', startAutoSlide);
    
    // بدء التبديل التلقائي
    startAutoSlide();
    
    // الانتقال للشريحة التالية عند النقر على لوحة المفاتيح
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight') {
            nextSlide();
            restartAutoSlide();
        } else if (e.key === 'ArrowLeft') {
            prevSlide();
            restartAutoSlide();
        }
    });
});

