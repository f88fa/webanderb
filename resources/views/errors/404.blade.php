@php
    try {
        $siteSettings = \App\Models\SiteSetting::getAllAsArray();
    } catch (\Throwable $e) {
        $siteSettings = [];
    }
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الصفحة غير موجودة — نظام Wesal</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: {{ $siteSettings['dashboard_primary_color'] ?? '#5FB38E' }};
            --sidebar-bg: {{ $siteSettings['dashboard_sidebar_bg'] ?? '#0f3d2e' }};
            --text-primary: {{ $siteSettings['dashboard_text_primary'] ?? '#FFFFFF' }};
            --text-secondary: {{ $siteSettings['dashboard_text_secondary'] ?? '#e0e0e0' }};
            --border-color: {{ $siteSettings['dashboard_border_color'] ?? 'rgba(255, 255, 255, 0.1)' }};
        }
        body { font-family: 'Cairo', sans-serif; background: var(--sidebar-bg); color: var(--text-primary); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem; margin: 0; }
        .error-card { max-width: 480px; width: 100%; background: rgba(255,255,255,0.06); border: 1px solid var(--border-color); border-radius: 16px; padding: 2rem; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.2); }
        .error-card .icon { font-size: 3.5rem; color: #ffb74d; margin-bottom: 1rem; }
        .error-card h1 { font-size: 1.35rem; margin: 0 0 0.5rem 0; color: var(--text-primary); }
        .error-card .code { font-size: 2rem; font-weight: 700; color: var(--primary-color); margin-bottom: 1rem; }
        .error-card .message { font-size: 0.95rem; color: var(--text-secondary); line-height: 1.7; margin-bottom: 1.5rem; }
        .error-card a { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: var(--primary-color); color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; }
        .error-card a:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon"><i class="fas fa-search"></i></div>
        <div class="code">404</div>
        <h1>الصفحة غير موجودة</h1>
        <p class="message">الرابط الذي طلبته غير صحيح أو تم نقل الصفحة.</p>
        <a href="{{ url()->previous() ?: route('wesal.page', 'home') }}"><i class="fas fa-arrow-right"></i> العودة</a>
    </div>
</body>
</html>
