@php
    $siteSettings = \App\Models\SiteSetting::getAllAsArray();
    $permissionLabel = $permission_label ?? \App\Services\PermissionsRegistry::getPermissionLabelAr($permission ?? '');
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عدم صلاحية الوصول — نظام Wesal</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: {{ $siteSettings['dashboard_primary_color'] ?? '#5FB38E' }};
            --sidebar-bg: {{ $siteSettings['dashboard_sidebar_bg'] ?? 'rgba(15, 61, 46, 0.95)' }};
            --text-primary: {{ $siteSettings['dashboard_text_primary'] ?? '#FFFFFF' }};
            --text-secondary: {{ $siteSettings['dashboard_text_secondary'] ?? '#FFFFFF' }};
            --border-color: {{ $siteSettings['dashboard_border_color'] ?? 'rgba(255, 255, 255, 0.1)' }};
        }
        body { font-family: 'Cairo', sans-serif; background: var(--sidebar-bg); color: var(--text-primary); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem; margin: 0; }
        .forbidden-card { max-width: 520px; width: 100%; background: rgba(255,255,255,0.06); border: 1px solid var(--border-color); border-radius: 16px; padding: 2rem; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.2); }
        .forbidden-card .icon { font-size: 3.5rem; color: #e57373; margin-bottom: 1rem; }
        .forbidden-card h1 { font-size: 1.35rem; margin: 0 0 1rem 0; color: var(--text-primary); }
        .forbidden-card .permission-name { font-weight: 700; color: var(--primary-color); word-break: break-word; }
        .forbidden-card .message { font-size: 0.95rem; color: var(--text-secondary); line-height: 1.7; margin-bottom: 1.25rem; }
        .forbidden-card .contact-msg { font-size: 0.9rem; color: var(--text-secondary); background: rgba(95, 179, 142, 0.15); border: 1px solid var(--primary-color); border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; text-align: right; line-height: 1.6; }
        .forbidden-card a { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: var(--primary-color); color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; }
        .forbidden-card a:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="forbidden-card">
        <div class="icon"><i class="fas fa-lock"></i></div>
        <h1>ليس لديك صلاحية الوصول</h1>
        <p class="message">الصفحة أو القسم الذي تحاول الوصول إليه يتطلب صلاحية:</p>
        <p class="permission-name">«{{ $permissionLabel }}»</p>
        <div class="contact-msg">
            إذا تعتقد أن هذه الصلاحية من المفترض أن تكون لك، يرجى مراسلة قسم تقنية المعلومات لتعديل الصلاحيات لك.
        </div>
        <a href="{{ route('wesal.page', 'home') }}"><i class="fas fa-home"></i> العودة للرئيسية</a>
    </div>
</body>
</html>
