<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة {{ $entry->entry_type === 'receipt' ? 'سند قبض' : ($entry->entry_type === 'payment' ? 'سند صرف' : 'قيد') }} - {{ $entry->entry_no }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', 'Tahoma', sans-serif;
            background: #f5f5f5;
            padding: 20px;
            color: #333;
        }
        
        .print-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .print-header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .print-header h1 {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .print-header .voucher-type {
            font-size: 24px;
            color: #2196f3;
            font-weight: 700;
            margin-top: 10px;
        }
        
        .voucher-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }
        
        .info-value {
            color: #333;
            font-size: 15px;
            font-weight: 500;
        }
        
        .recipient-info {
            background: #f0f7ff;
            border: 2px solid #2196f3;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .recipient-info h3 {
            color: #2196f3;
            font-size: 18px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2196f3;
        }
        
        .recipient-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .recipient-details .detail-item {
            display: flex;
            gap: 10px;
        }
        
        .recipient-details .detail-label {
            font-weight: 600;
            color: #666;
            min-width: 100px;
        }
        
        .recipient-details .detail-value {
            color: #333;
        }
        
        .voucher-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: 2px solid #333;
        }
        
        .voucher-table thead {
            background: #333;
            color: white;
        }
        
        .voucher-table th {
            padding: 12px;
            text-align: center;
            font-weight: 700;
            font-size: 14px;
            border: 1px solid #555;
        }
        
        .voucher-table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            font-size: 13px;
            color: #222;
            background: #fff;
        }
        
        .voucher-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .voucher-table tbody tr:hover {
            background: #f0f0f0;
        }
        
        .amount-cell {
            font-weight: 600;
            color: #2196f3;
        }
        
        .totals-section {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f9f9f9;
            border: 2px solid #333;
            border-radius: 8px;
        }
        
        .total-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 6px;
            border: 1px solid #ddd;
        }
        
        .total-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .total-value {
            font-size: 24px;
            font-weight: 700;
            color: #333;
        }
        
        .total-value.balance {
            color: #4caf50;
        }
        
        .signatures-table-wrap {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 2px solid #333;
        }
        
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #333;
            font-size: 14px;
        }
        
        .signatures-table th,
        .signatures-table td {
            border: 1px solid #ddd;
            padding: 12px 8px;
            text-align: center;
            vertical-align: middle;
        }
        
        .signatures-table th {
            background: #333;
            color: white;
            font-weight: 700;
            padding: 10px 8px;
        }
        
        .signatures-table .sig-name {
            font-weight: 600;
            color: #222;
            margin-bottom: 4px;
        }
        
        .signatures-table .sig-title {
            font-size: 12px;
            color: #555;
            margin-bottom: 8px;
        }
        
        .signatures-table .sig-img {
            max-height: 52px;
            max-width: 120px;
            object-fit: contain;
        }
        
        .signatures-table .sig-empty {
            display: inline-block;
            min-height: 52px;
            min-width: 100px;
            border: 1px dashed #999;
            border-radius: 4px;
            background: #fafafa;
            vertical-align: middle;
        }
        
        .signatures-table {
            table-layout: fixed;
        }
        
        .signatures-table th,
        .signatures-table td {
            width: auto;
        }
        
        .notes-section {
            margin-top: 30px;
            padding: 15px;
            background: #fff9e6;
            border: 1px solid #ffc107;
            border-radius: 6px;
        }
        
        .notes-section h4 {
            color: #f57c00;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .notes-content {
            color: #333;
            font-size: 14px;
            line-height: 1.6;
            white-space: pre-line;
        }
        
        .print-actions {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 0 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-print {
            background: #2196f3;
            color: white;
        }
        
        .btn-print:hover {
            background: #1976d2;
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
        }
        
        .btn-back:hover {
            background: #5a6268;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .print-actions {
                display: none;
            }
            
            .print-container {
                box-shadow: none;
                padding: 20px;
            }
            
            @page {
                margin: 15mm;
            }
        }
        
        .voucher-number {
            font-size: 20px;
            color: #2196f3;
            font-weight: 700;
            margin-top: 5px;
        }
        
        .date-info {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- رأس السند -->
        <div class="print-header">
            <h1>{{ $settings['site_name'] ?? 'النظام المالي' }}</h1>
            <div class="voucher-type">
                {{ $entry->entry_type === 'receipt' ? 'سند قبض' : ($entry->entry_type === 'payment' ? 'سند صرف' : 'قيد يومية') }}
            </div>
            <div class="voucher-number">رقم السند: {{ $entry->entry_no }}</div>
            <div class="date-info">
                <span>التاريخ الميلادي: {{ $entry->entry_date->format('Y-m-d') }}</span>
                <span>الفترة: {{ $entry->period->period_name ?? '' }}</span>
            </div>
        </div>

        <!-- معلومات السند -->
        <div class="voucher-info">
            <div class="info-item">
                <span class="info-label">رقم السند:</span>
                <span class="info-value">{{ $entry->entry_no }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">تاريخ السند:</span>
                <span class="info-value">{{ $entry->entry_date->format('Y-m-d') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">نوع السند:</span>
                <span class="info-value">{{ $entry->entry_type === 'receipt' ? 'سند قبض' : ($entry->entry_type === 'payment' ? 'سند صرف' : 'قيد يومية') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">الحالة:</span>
                <span class="info-value" style="color: {{ $entry->status === 'posted' ? '#4caf50' : '#ff9800' }}; font-weight: 700;">
                    {{ $entry->status === 'posted' ? 'مرحل' : ($entry->status === 'draft' ? 'مسودة' : $entry->status) }}
                </span>
            </div>
            @if($entry->period && $entry->period->fiscalYear)
            <div class="info-item">
                <span class="info-label">السنة المالية:</span>
                <span class="info-value">{{ $entry->period->fiscalYear->year_name }}</span>
            </div>
            @endif
            @if($entry->posted_at)
            <div class="info-item">
                <span class="info-label">تاريخ الترحيل:</span>
                <span class="info-value">{{ $entry->posted_at->format('Y-m-d H:i') }}</span>
            </div>
            @endif
        </div>

        <!-- معلومات المستلم/المستفيد (للسندات فقط) -->
        @if(in_array($entry->entry_type, ['receipt', 'payment']) && $entry->notes)
        @php
            $notesLines = explode("\n", $entry->notes);
            $recipientInfo = [];
            foreach ($notesLines as $line) {
                if (strpos($line, 'الاسم:') !== false) {
                    $recipientInfo['name'] = trim(str_replace('الاسم:', '', $line));
                } elseif (strpos($line, 'رقم الهوية/السجل:') !== false) {
                    $recipientInfo['id'] = trim(str_replace('رقم الهوية/السجل:', '', $line));
                } elseif (strpos($line, 'الهاتف:') !== false) {
                    $recipientInfo['phone'] = trim(str_replace('الهاتف:', '', $line));
                } elseif (strpos($line, 'العنوان:') !== false) {
                    $recipientInfo['address'] = trim(str_replace('العنوان:', '', $line));
                }
            }
        @endphp
        @if(!empty($recipientInfo))
        <div class="recipient-info">
            <h3>
                <i class="fas fa-user"></i>
                {{ $entry->entry_type === 'receipt' ? 'معلومات المستلم' : 'معلومات المستفيد' }}
            </h3>
            <div class="recipient-details">
                @if(isset($recipientInfo['name']))
                <div class="detail-item">
                    <span class="detail-label">الاسم:</span>
                    <span class="detail-value">{{ $recipientInfo['name'] }}</span>
                </div>
                @endif
                @if(isset($recipientInfo['id']))
                <div class="detail-item">
                    <span class="detail-label">رقم الهوية/السجل:</span>
                    <span class="detail-value">{{ $recipientInfo['id'] }}</span>
                </div>
                @endif
                @if(isset($recipientInfo['phone']))
                <div class="detail-item">
                    <span class="detail-label">الهاتف:</span>
                    <span class="detail-value">{{ $recipientInfo['phone'] }}</span>
                </div>
                @endif
                @if(isset($recipientInfo['address']))
                <div class="detail-item">
                    <span class="detail-label">العنوان:</span>
                    <span class="detail-value">{{ $recipientInfo['address'] }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif
        @endif

        <!-- جدول سطور السند -->
        <table class="voucher-table">
            <thead>
                <tr>
                    <th style="width: 35%; text-align: right;">اسم البند</th>
                    <th style="width: 45%; text-align: right;">البيان</th>
                    <th style="width: 20%;">المبلغ</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $entryLines = $entry->relationLoaded('lines') ? $entry->lines : $entry->lines()->with('account')->orderBy('line_order')->get();
                    $cashAccountId = $entry->cash_account_id ?? null;
                    // عرض السطور المضافة فقط: استبعاد سطر حساب الصندوق/البنك إن وُجد، وإلا عرض كل السطور
                    $printLines = $entryLines->filter(function ($line) use ($cashAccountId) {
                        if ($cashAccountId !== null && $cashAccountId !== '') {
                            if ((int) $line->account_id === (int) $cashAccountId) {
                                return false;
                            }
                        }
                        return true;
                    });
                    if ($printLines->isEmpty()) {
                        $printLines = $entryLines;
                    }
                @endphp
                @foreach($printLines as $line)
                @php $account = $line->relationLoaded('account') ? $line->account : $line->account()->first(); @endphp
                <tr>
                    <td style="text-align: right; color: #222; background: #fff;">{{ $account->name_ar ?? '' }}</td>
                    <td style="text-align: right;">{{ $line->description ?? $entry->description ?? '—' }}</td>
                    <td class="amount-cell">
                        @if((float) $line->debit > 0)
                            {{ number_format((float) $line->debit, 2) }} <span style="font-size: 11px; color: #666;">مدين</span>
                        @else
                            {{ number_format((float) $line->credit, 2) }} <span style="font-size: 11px; color: #666;">دائن</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- ملاحظات -->
        @if($entry->notes && !in_array($entry->entry_type, ['receipt', 'payment']))
        <div class="notes-section">
            <h4><i class="fas fa-sticky-note"></i> ملاحظات:</h4>
            <div class="notes-content">{{ $entry->notes }}</div>
        </div>
        @endif

        <!-- جدول تسلسل الموافقات: أعمدة بجانب بعض (اسم، منصب، توقيع أو خانة فارغة) -->
        @php
            $paymentRequest = $entry->paymentRequest ?? null;
            $approvers = $paymentRequest ? $paymentRequest->approvers_for_display : [];
        @endphp
        <div class="signatures-table-wrap">
            <table class="signatures-table">
                <thead>
                    <tr>
                        <th>المحاسب</th>
                        @foreach($approvers as $approver)
                        <th>الموافق {{ count($approvers) > 1 ? ' (خطوة ' . $approver['step'] . ')' : '' }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="sig-name">{{ $entry->postedByUser->name ?? auth()->user()?->name ?? '—' }}</div>
                            <div class="sig-title">—</div>
                            <span class="sig-empty" aria-label="خانة توقيع فارغة"></span>
                        </td>
                        @forelse($approvers as $approver)
                        <td>
                            <div class="sig-name">{{ $approver['name_ar'] }}</div>
                            <div class="sig-title">{{ $approver['job_title'] ?? '—' }}</div>
                            @if(!empty($approver['signature_url']))
                                <img src="{{ $approver['signature_url'] }}" alt="توقيع" class="sig-img">
                            @else
                                <span class="sig-empty" aria-label="خانة توقيع فارغة"></span>
                            @endif
                        </td>
                        @empty
                        {{-- سند بدون طلب صرف (قيد/قبض) قد لا يكون له موافقون --}}
                        @endforelse
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- أزرار الإجراءات -->
        <div class="print-actions">
            <button onclick="window.print()" class="btn btn-print">
                <i class="fas fa-print"></i> طباعة
            </button>
            <a href="{{ route('wesal.finance.journal-entries.index', array_filter(['entry_type' => $entry->entry_type, 'fiscal_year_id' => $entry->period->fiscal_year_id ?? null])) }}" class="btn btn-back">
                <i class="fas fa-arrow-right"></i> {{ $entry->entry_type === 'payment' ? 'العودة لسندات الصرف' : ($entry->entry_type === 'receipt' ? 'العودة لسندات القبض' : 'العودة للقيود') }}
            </a>
        </div>
    </div>

    <script>
        // طباعة تلقائية عند تحميل الصفحة (اختياري)
        // window.onload = function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 500);
        // };
    </script>
</body>
</html>
