@php
    $letter = $letter ?? null;
    $preview = $preview ?? false;
    $settings = $settings ?? [];
    $headerType = $settings['letter_paper_header_type'] ?? 'none';
    $headerContent = $settings['letter_paper_header_content'] ?? '';
    $middleType = $settings['letter_paper_middle_type'] ?? 'none';
    $middleContent = $settings['letter_paper_middle_content'] ?? '';
    $footerType = $settings['letter_paper_footer_type'] ?? 'none';
    $footerContent = $settings['letter_paper_footer_content'] ?? '';
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $preview ? 'معاينة ورقة الخطاب' : 'طباعة خطاب — ' . ($letter ? $letter->subject : '') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    @if($preview)
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @endif
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Cairo', sans-serif;
            background: #f5f5f5;
            color: #1a1a1a;
            padding: 0;
            font-size: 15px;
            line-height: 1.6;
        }
        .letter-page {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            padding: 0;
            display: flex;
            flex-direction: column;
            background: #fff;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .letter-header {
            flex: 0 0 auto;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        .letter-header img {
            width: 100%;
            height: auto;
            max-height: none;
            object-fit: cover;
            object-position: top center;
            display: block;
            vertical-align: top;
        }
        .letter-content-area {
            flex: 1 1 auto;
            min-height: 0;
            position: relative;
            padding: 10px 18mm 10px;
        }
        .letter-middle-watermark {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            opacity: 0.1;
            z-index: 0;
            max-width: 55%;
        }
        .letter-middle-watermark img {
            max-width: 100%;
            max-height: 180px;
            object-fit: contain;
        }
        .letter-body {
            position: relative;
            z-index: 1;
            padding: 0;
        }
        .letter-body table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
            font-size: 14px;
        }
        .letter-body td {
            padding: 4px 0;
            vertical-align: top;
        }
        .letter-body .label {
            color: #555;
            width: 120px;
            font-size: 13px;
        }
        .letter-body .content {
            font-weight: 500;
            color: #1a1a1a;
        }
        .letter-body .body-text {
            white-space: pre-wrap;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #e8e8e8;
            font-size: 14px;
            text-align: justify;
        }
        .letter-footer {
            flex: 0 0 auto;
            width: 100%;
            margin: 0;
            padding: 0;
            margin-top: auto;
            border-top: 1px solid #e0e0e0;
        }
        .letter-footer img {
            width: 100%;
            height: auto;
            max-height: none;
            object-fit: cover;
            object-position: bottom center;
            display: block;
            vertical-align: bottom;
        }
        .preview-toolbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 9999;
            background: #1a1a1a;
            color: #fff;
            padding: 0.6rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            font-family: 'Cairo', sans-serif;
        }
        .preview-toolbar button {
            padding: 0.4rem 1rem;
            background: #5FB38E;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }
        body.has-preview-toolbar { padding-top: 48px; }
        @media print {
            body {
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                padding: 0 !important;
                margin: 0 !important;
            }
            .preview-toolbar { display: none !important; }
            .letter-page {
                width: 210mm !important;
                height: 297mm !important;
                min-height: 297mm !important;
                max-height: 297mm !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                page-break-inside: avoid;
            }
            .letter-content-area { padding: 10px 18mm !important; }
            .letter-header, .letter-footer { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="{{ $preview ? 'has-preview-toolbar' : '' }}">
    @if($preview)
    <div class="preview-toolbar">
        <span>معاينة ورقة الخطاب — التصميم الحالي من إعدادات النظام</span>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" onclick="window.print();"><i class="fas fa-print"></i> طباعة</button>
        </div>
    </div>
    @endif
    <div class="letter-page">
        @if($headerType === 'html' && !empty($headerContent))
            <div class="letter-header">{!! $headerContent !!}</div>
        @elseif($headerType === 'image' && !empty($headerContent))
            <div class="letter-header">
                <img src="{{ image_asset_url($headerContent) }}" alt="رأس الخطاب">
            </div>
        @else
            <div class="letter-header"></div>
        @endif

        <div class="letter-content-area">
        @if($middleType === 'html' && !empty($middleContent))
            <div class="letter-middle-watermark">{!! $middleContent !!}</div>
        @elseif($middleType === 'image' && !empty($middleContent))
            <div class="letter-middle-watermark">
                <img src="{{ image_asset_url($middleContent) }}" alt="ختم">
            </div>
        @endif

        <div class="letter-body">
            @if($letter)
                <table>
                    <tr><td class="label">رقم الخطاب</td><td class="content">{{ $letter->letter_no ?: '—' }}</td></tr>
                    <tr><td class="label">الموضوع</td><td class="content">{{ $letter->subject }}</td></tr>
                    <tr><td class="label">التاريخ</td><td class="content">{{ $letter->letter_date ? $letter->letter_date->format('Y-m-d') : ($letter->created_at ? $letter->created_at->format('Y-m-d') : '—') }}</td></tr>
                    <tr><td class="label">من</td><td class="content">{{ $letter->from_party ?: '—' }}</td></tr>
                    <tr><td class="label">إلى</td><td class="content">{{ $letter->to_party ?: '—' }}</td></tr>
                    @if($letter->reference_no)
                        <tr><td class="label">الرقم المرجعي</td><td class="content">{{ $letter->reference_no }}</td></tr>
                    @endif
                </table>
                @if($letter->body)
                    <div class="body-text">{{ $letter->body }}</div>
                @endif
            @elseif($preview)
                <table>
                    <tr><td class="label">رقم الخطاب</td><td class="content">—</td></tr>
                    <tr><td class="label">الموضوع</td><td class="content">نص تجريبي للمعاينة</td></tr>
                    <tr><td class="label">التاريخ</td><td class="content">{{ now()->format('Y-m-d') }}</td></tr>
                    <tr><td class="label">من</td><td class="content">الجهة المرسلة</td></tr>
                    <tr><td class="label">إلى</td><td class="content">الجهة المستقبلة</td></tr>
                </table>
                <div class="body-text">هذا نص تجريبي لمعاينة شكل ورقة الخطاب. المحتوى الفعلي يظهر عند طباعة خطاب حقيقي من الاتصالات الإدارية.</div>
            @endif
        </div>
        </div>

        @if($footerType === 'html' && !empty($footerContent))
            <div class="letter-footer">{!! $footerContent !!}</div>
        @elseif($footerType === 'image' && !empty($footerContent))
            <div class="letter-footer">
                <img src="{{ image_asset_url($footerContent) }}" alt="تذييل">
            </div>
        @else
            <div class="letter-footer"></div>
        @endif
    </div>
    @if(!$preview)
    <script>
        window.onload = function() { window.print(); };
    </script>
    @endif
</body>
</html>
