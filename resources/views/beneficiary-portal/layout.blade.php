@php
    $settings = $settings ?? \App\Models\SiteSetting::getAllAsArray();
    $menuItems = $menuItems ?? \App\Models\MenuItem::getRootItemsExcludingHome()->load('activeChildren');
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'بوابة المستفيدين') - {{ $settings['site_title'] ?? config('app.name') }}</title>
    @if(!empty($settings['site_icon_file']))
        <link rel="icon" type="image/png" href="{{ image_asset_url($settings['site_icon_file']) }}">
        <link rel="shortcut icon" type="image/png" href="{{ image_asset_url($settings['site_icon_file']) }}">
    @endif
    <link rel="stylesheet" href="{{ asset('assets/css/frontend.css') }}?v={{ $settings['settings_updated_at'] ?? '1' }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    @include('frontend.partials.site-theme-root')
    <style>
        :root {
            --bp-primary: var(--primary-dark);
            --bp-primary-light: var(--primary-color);
            --bp-surface: #ffffff;
            --bp-surface-muted: #f4f7f6;
            --bp-border: rgba(31, 107, 79, 0.12);
            --bp-text: #1a2e24;
            --bp-text-muted: #5c6f66;
            --bp-shadow: 0 4px 24px rgba(15, 61, 46, 0.08), 0 12px 48px rgba(15, 61, 46, 0.06);
            --bp-radius: 18px;
            --bp-radius-sm: 12px;
        }
        .bp-body { min-height: 100vh; font-family: 'Cairo', system-ui, sans-serif; padding: 0; margin: 0; display: flex; flex-direction: column; -webkit-font-smoothing: antialiased; }
        .bp-portal-content { flex: 1 1 auto; padding: 8rem 1rem 2.5rem; box-sizing: border-box; width: 100%; min-height: 0; }
        @media (min-width: 640px) {
            .bp-portal-content { padding: 8rem 1.25rem 3rem; }
        }
        @media (min-width: 768px) {
            .bp-portal-content { padding: 8rem 1.75rem 3rem; }
        }
        @media (min-width: 1024px) {
            .bp-portal-content { padding: 8rem 2rem 3rem; }
        }
        body.bp-body footer.footer { flex-shrink: 0; width: 100%; max-width: none; margin: 0; box-sizing: border-box; }
        .bp-card {
            background: var(--bp-surface);
            border-radius: var(--bp-radius);
            box-shadow: var(--bp-shadow);
            border: 1px solid var(--bp-border);
            padding: 1.5rem 1.25rem;
            max-width: 900px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }
        @media (min-width: 640px) {
            .bp-card { padding: 1.75rem 1.5rem; }
        }
        @media (min-width: 768px) {
            .bp-card { padding: 2rem 2rem; }
        }
        .bp-card.bp-card--portal {
            max-width: min(1100px, 100%);
        }
        .bp-header { text-align: center; margin-bottom: 2rem; }
        .bp-header h1 { color: var(--bp-primary); font-size: clamp(1.35rem, 4vw, 1.85rem); margin-bottom: 0.5rem; font-weight: 800; letter-spacing: -0.02em; }
        .bp-header p { color: var(--bp-text-muted); font-size: 0.95rem; line-height: 1.55; }
        .bp-nav { display: flex; gap: 0.75rem; justify-content: center; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .bp-nav a { padding: 0.55rem 1.1rem; background: rgba(var(--primary-dark-rgb, 31, 107, 79), 0.1); color: var(--bp-primary); border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: background 0.2s, color 0.2s, transform 0.15s; }
        .bp-nav a:hover { background: var(--bp-primary); color: #fff; transform: translateY(-1px); }
        .bp-form .form-group { margin-bottom: 1rem; }
        .bp-form .form-label { display: block; margin-bottom: 0.4rem; font-weight: 600; color: var(--bp-text); }
        .bp-form .form-control { width: 100%; padding: 0.75rem 0.9rem; border: 1.5px solid #e2e8e4; border-radius: var(--bp-radius-sm); font-size: 1rem; background: #fff; color: var(--bp-text); transition: border-color 0.2s, box-shadow 0.2s; }
        .bp-form .form-control:focus { outline: none; border-color: var(--bp-primary-light); box-shadow: 0 0 0 3px rgba(var(--primary-color-rgb, 95, 179, 142), 0.25); }
        .bp-btn { padding: 0.7rem 1.35rem; border: none; border-radius: var(--bp-radius-sm); font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: transform 0.15s, filter 0.2s, box-shadow 0.2s; font-family: inherit; display: inline-flex; align-items: center; justify-content: center; gap: 0.45rem; }
        .bp-btn-primary { background: var(--bp-primary); color: #fff; box-shadow: 0 4px 14px rgba(var(--primary-dark-rgb, 31, 107, 79), 0.35); }
        .bp-btn-primary:hover { filter: brightness(1.05); transform: translateY(-1px); }
        .bp-btn-secondary { background: var(--bp-surface-muted); color: var(--bp-text); border: 1px solid var(--bp-border); }
        .bp-btn-secondary:hover { background: #e8eeeb; }
        .bp-alert { padding: 0.9rem 1.1rem; border-radius: var(--bp-radius-sm); margin-bottom: 1rem; font-size: 0.95rem; line-height: 1.5; }
        .bp-alert-success { background: rgba(var(--primary-color-rgb, 95, 179, 142), 0.18); border: 1px solid rgba(var(--primary-color-rgb, 95, 179, 142), 0.45); color: var(--bp-primary); }
        .bp-alert-error { background: rgba(220, 53, 69, 0.08); border: 1px solid rgba(220, 53, 69, 0.35); color: #b02a37; }
        .bp-table-wrap { border-radius: var(--bp-radius-sm); border: 1px solid var(--bp-border); overflow: hidden; overflow-x: auto; -webkit-overflow-scrolling: touch; background: #fff; }
        .bp-table { width: 100%; min-width: 520px; border-collapse: collapse; font-size: 0.9rem; }
        .bp-table th, .bp-table td { padding: 0.85rem 1rem; text-align: right; border-bottom: 1px solid #eef2f0; vertical-align: top; }
        .bp-table tbody tr:last-child td { border-bottom: none; }
        .bp-table tbody tr:hover { background: rgba(var(--primary-dark-rgb, 31, 107, 79), 0.03); }
        .bp-table th { background: linear-gradient(180deg, rgba(var(--primary-dark-rgb, 31, 107, 79), 0.1) 0%, rgba(var(--primary-dark-rgb, 31, 107, 79), 0.06) 100%); color: var(--bp-primary); font-weight: 700; font-size: 0.82rem; text-transform: none; letter-spacing: 0.02em; white-space: nowrap; }
        .bp-link { color: var(--bp-primary); text-decoration: none; font-weight: 600; }
        .bp-link:hover { text-decoration: underline; }
        /* لوحة المستفيد */
        .bp-dash-hero__text { flex: 1; min-width: min(100%, 280px); }
        .bp-dash-hero {
            display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 1.25rem;
            padding: 1.25rem 1.35rem; margin: -0.25rem -0.5rem 1.5rem; border-radius: var(--bp-radius-sm);
            background: linear-gradient(135deg, rgba(var(--primary-dark-rgb, 31, 107, 79), 0.09) 0%, rgba(var(--primary-color-rgb, 95, 179, 142), 0.12) 100%);
            border: 1px solid var(--bp-border);
        }
        @media (min-width: 768px) {
            .bp-dash-hero { padding: 1.5rem 1.75rem; margin: -0.5rem -0.75rem 1.75rem; }
        }
        .bp-dash-eyebrow { margin: 0 0 0.35rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--bp-primary); opacity: 0.9; }
        .bp-dash-title { margin: 0 0 0.65rem; font-size: clamp(1.25rem, 3.5vw, 1.6rem); font-weight: 800; color: var(--bp-text); line-height: 1.35; }
        .bp-dash-meta { display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; }
        .bp-dash-badge-id {
            display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.85rem; border-radius: 999px;
            background: #fff; border: 1px solid var(--bp-border); font-size: 0.88rem; font-weight: 600; color: var(--bp-text);
            font-variant-numeric: tabular-nums;
        }
        .bp-dash-intro {
            margin: 0 0 1.75rem; padding: 1rem 1.15rem; border-radius: var(--bp-radius-sm); background: var(--bp-surface-muted);
            border: 1px solid var(--bp-border); color: var(--bp-text-muted); font-size: 0.92rem; line-height: 1.65;
        }
        .bp-dash-callout {
            margin: 0 0 1.5rem; padding: 1.15rem 1.25rem; border-radius: var(--bp-radius-sm);
            background: linear-gradient(135deg, rgba(var(--primary-dark-rgb, 31, 107, 79), 0.08) 0%, rgba(var(--primary-color-rgb, 95, 179, 142), 0.1) 100%);
            border: 1px solid var(--bp-border);
        }
        .bp-dash-callout h3 { margin: 0 0 0.85rem; font-size: 1.05rem; font-weight: 800; color: var(--bp-primary); }
        .bp-dash-callout p { margin: 0 0 0.5rem; font-size: 0.92rem; color: var(--bp-text); line-height: 1.55; }
        .bp-dash-callout p:last-child { margin-bottom: 0; }
        .bp-dash-section { margin-bottom: 1.5rem; }
        @media (min-width: 768px) { .bp-dash-section { margin-bottom: 1.75rem; } }
        .bp-dash-section:last-of-type { margin-bottom: 0; }
        .bp-dash-section__head {
            display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; padding-bottom: 0.65rem;
            border-bottom: 2px solid rgba(var(--primary-dark-rgb, 31, 107, 79), 0.15);
        }
        .bp-dash-section__icon {
            width: 2.5rem; height: 2.5rem; border-radius: 12px; display: flex; align-items: center; justify-content: center;
            background: rgba(var(--primary-color-rgb, 95, 179, 142), 0.25); color: var(--bp-primary); font-size: 1.1rem; flex-shrink: 0;
        }
        .bp-dash-section__title { margin: 0; font-size: clamp(1.05rem, 2.5vw, 1.2rem); font-weight: 800; color: var(--bp-primary); line-height: 1.35; }
        .bp-dash-subtitle { margin: 0.35rem 0 0.85rem; font-size: 0.88rem; font-weight: 600; color: var(--bp-text-muted); }
        .bp-empty {
            text-align: center; padding: 1.75rem 1.25rem; border-radius: var(--bp-radius-sm); border: 1.5px dashed var(--bp-border);
            background: rgba(255,255,255,0.6); color: var(--bp-text-muted); font-size: 0.92rem; line-height: 1.55;
        }
        .bp-empty i { display: block; font-size: 1.75rem; margin-bottom: 0.65rem; opacity: 0.45; color: var(--bp-primary); }
        .bp-badge { display: inline-block; padding: 0.2rem 0.55rem; border-radius: 6px; font-size: 0.8rem; font-weight: 700; }
        .bp-badge--success { background: rgba(25, 135, 84, 0.15); color: #146c43; }
        .bp-badge--danger { background: rgba(220, 53, 69, 0.12); color: #b02a37; }
        .bp-badge--warn { background: rgba(253, 126, 20, 0.15); color: #c35a00; }
        .bp-badge--info { background: rgba(13, 110, 253, 0.12); color: #0a58ca; }
        .bp-badge--muted { background: #e9ecef; color: #495057; }
    </style>
    @stack('styles')
</head>
<body class="bp-body">
    @include('frontend.partials.header')
    <main class="bp-portal-content">
    <div class="bp-card @yield('bp_card_class')">
        @if(session('success'))
            <div class="bp-alert bp-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bp-alert bp-alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="bp-alert bp-alert-error">
                @foreach($errors->all() as $e) <p style="margin:0;">{{ $e }}</p> @endforeach
            </div>
        @endif
        @yield('content')
    </div>
    </main>
    @include('frontend.partials.footer')

    @if(isset($settings['floating_whatsapp_enabled']) && $settings['floating_whatsapp_enabled'] == '1' && !empty($settings['floating_whatsapp_number']))
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['floating_whatsapp_number']) }}"
       target="_blank"
       rel="noopener noreferrer"
       class="floating-button floating-whatsapp"
       title="تواصل معنا عبر الواتساب">
        <i class="fab fa-whatsapp"></i>
        <span class="floating-button-text">واتساب</span>
    </a>
    @endif

    @if(isset($settings['floating_donate_enabled']) && $settings['floating_donate_enabled'] == '1' && !empty($settings['floating_donate_link']))
    <a href="{{ $settings['floating_donate_link'] }}"
       target="_blank"
       rel="noopener noreferrer"
       class="floating-button floating-donate"
       title="{{ $settings['floating_donate_text'] ?? 'تبرع الآن' }}">
        <i class="fas fa-heart"></i>
        <span class="floating-button-text">{{ $settings['floating_donate_text'] ?? 'تبرع الآن' }}</span>
    </a>
    @endif

    <script src="{{ asset('assets/js/frontend.js') }}?v={{ $settings['settings_updated_at'] ?? '1' }}"></script>
    <script>
    (function() {
        var ar = '\u0660\u0661\u0662\u0663\u0664\u0665\u0666\u0667\u0668\u0669';
        var fa = '\u06F0\u06F1\u06F2\u06F3\u06F4\u06F5\u06F6\u06F7\u06F8\u06F9';
        var en = '0123456789';
        function getEnglishDigit(char) {
            var ai = ar.indexOf(char);
            var fi = fa.indexOf(char);
            if (ai !== -1) return en[ai];
            if (fi !== -1) return en[fi];
            return null;
        }
        document.addEventListener('keypress', function(e) {
            var el = e.target;
            if (el.isContentEditable) return;
            var tag = (el.tagName || '').toLowerCase();
            if (tag !== 'input' && tag !== 'textarea') return;
            var eng = getEnglishDigit(e.key);
            if (eng !== null) {
                e.preventDefault();
                // لا ندخل أي رقم عند استخدام لوحة عربية؛ يجب التحويل للوحة إنجليزية
            }
        }, true);
    })();
    </script>
</body>
</html>
