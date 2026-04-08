<div class="content-card">
    <div class="page-header" style="margin-bottom: 1.5rem;">
        <h1 class="page-title">
            <i class="fas fa-balance-scale" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            ميزان المراجعة
        </h1>
        <p class="page-subtitle">تقرير ميزان المراجعة لجميع الحسابات القابلة للترحيل</p>
    </div>

    @if(($periods ?? collect())->isEmpty())
        <div style="background: #fff3e0; border: 1px solid #ff9800; color: #e65100; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <i class="fas fa-exclamation-triangle" style="margin-left: 0.5rem;"></i>
            <strong>لا توجد سنوات مالية.</strong> يرجى إنشاء سنة مالية من قسم السنوات المالية أولاً.
        </div>
    @endif

    <!-- الفلاتر -->
    <div style="background: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <form method="GET" action="{{ route('wesal.finance.chart-accounts.trial-balance') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            @if(!empty($fiscalYearId))
                <input type="hidden" name="fiscal_year_id" value="{{ $fiscalYearId }}">
            @endif
            <div>
                <label style="display: block; margin-bottom: 0.25rem; color: #222; font-weight: 600; font-size: 0.8rem;">السنة المالية:</label>
                <select name="period_id" class="form-control" style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem; background: #fff; color: #333;">
                    <option value="">-- اختر السنة المالية --</option>
                    @foreach(($periods ?? collect())->groupBy('fiscal_year_id') as $fid => $periodsInYear)
                        @php $lastPeriod = $periodsInYear->sortByDesc('start_date')->first(); @endphp
                        @if($lastPeriod)
                            <option value="{{ $lastPeriod->id }}" {{ ($selectedPeriod && $selectedPeriod->id == $lastPeriod->id) ? 'selected' : '' }}>{{ $lastPeriod->fiscalYear->year_name ?? $fid }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.25rem; color: #222; font-weight: 600; font-size: 0.8rem;">من تاريخ:</label>
                <input type="date" name="from_date" value="{{ request('from_date', $filterFromDate ?? '') }}" class="form-control" style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem; background: #fff; color: #333;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.25rem; color: #222; font-weight: 600; font-size: 0.8rem;">إلى تاريخ (فلترة اختيارية):</label>
                <input type="date" name="as_of" value="{{ request('as_of', $asOf ? $asOf->format('Y-m-d') : '') }}" class="form-control" style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem; background: #fff; color: #333;">
            </div>
            <div>
                <button type="submit" style="width: 100%; padding: 0.5rem; background: var(--primary-color); color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 0.85rem;">
                    <i class="fas fa-filter"></i> تصفية
                </button>
            </div>
            <div>
                <button type="button" onclick="window.print()" style="width: 100%; padding: 0.5rem; background: #2196f3; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 0.85rem;">
                    <i class="fas fa-print"></i> طباعة
                </button>
            </div>
            <div>
                <a href="{{ route('wesal.finance.chart-accounts.trial-balance.export', ['period_id' => optional($selectedPeriod)->id ?? '', 'as_of' => isset($asOf) && $asOf ? $asOf->format('Y-m-d') : '']) }}" 
                   style="display: block; width: 100%; padding: 0.5rem; background: #2e7d32; color: white; border: none; border-radius: 4px; text-align: center; text-decoration: none; font-weight: 600; font-size: 0.85rem;">
                    <i class="fas fa-file-excel"></i> تصدير Excel
                </a>
            </div>
        </form>
    </div>

    <!-- معلومات التقرير -->
    <div style="background: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-size: 0.85rem;">
            <div>
                <strong style="color: #666;">اسم التقرير:</strong>
                <span style="color: #222; margin-right: 0.5rem;">ميزان المراجعة</span>
            </div>
            @if($selectedPeriod && $selectedPeriod->fiscalYear)
            <div>
                <strong style="color: #666;">السنة المالية:</strong>
                <span style="color: #222; margin-right: 0.5rem;">{{ $selectedPeriod->fiscalYear->year_name }}</span>
            </div>
            @endif
            @if($asOf)
            <div>
                <strong style="color: #666;">حتى تاريخ:</strong>
                <span style="color: #222; margin-right: 0.5rem;">{{ $asOf->format('Y-m-d') }}</span>
            </div>
            @endif
            <div>
                <strong style="color: #666;">تاريخ الطباعة:</strong>
                <span style="color: #222; margin-right: 0.5rem;">{{ now()->format('Y-m-d H:i') }}</span>
            </div>
        </div>
    </div>

    <!-- جدول ميزان المراجعة -->
    <div class="trial-balance-table-wrap" style="background: white; padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); color: #222;">
        @if(count($trialBalanceData) > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; direction: rtl; font-size: 0.85rem; background: #fff;" id="trial-balance-table">
                    <thead>
                        <tr style="background: #333; color: white;">
                            <th style="padding: 0.75rem; text-align: center; border: 1px solid #555; font-weight: 700; font-size: 0.9rem;">م</th>
                            <th style="padding: 0.75rem; text-align: center; border: 1px solid #555; font-weight: 700; font-size: 0.9rem;">رقم الحساب</th>
                            <th style="padding: 0.75rem; text-align: right; border: 1px solid #555; font-weight: 700; font-size: 0.9rem;">اسم الحساب</th>
                            <th style="padding: 0.75rem; text-align: center; border: 1px solid #555; font-weight: 700; font-size: 0.9rem;">طبيعة الحساب</th>
                            <th style="padding: 0.75rem; text-align: center; border: 1px solid #555; font-weight: 700; font-size: 0.9rem; background: #e3f2fd; color: #1976d2;">مدين</th>
                            <th style="padding: 0.75rem; text-align: center; border: 1px solid #555; font-weight: 700; font-size: 0.9rem; background: #fff3e0; color: #e65100;">دائن</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trialBalanceData as $index => $row)
                        <tr style="border-bottom: 1px solid #eee; background: #fff;">
                            <td style="padding: 0.75rem; text-align: center; border: 1px solid #eee; color: #222; background: #fff;">
                                {{ $index + 1 }}
                            </td>
                            <td style="padding: 0.75rem; text-align: center; border: 1px solid #eee; background: #fff;">
                                <strong style="color: #1976d2; font-family: 'Courier New', monospace;">{{ $row['account']->code }}</strong>
                            </td>
                            <td style="padding: 0.75rem; text-align: right; border: 1px solid #eee; color: #111; background: #fff; font-weight: 500;">
                                {{ $row['account']->name_ar }}
                            </td>
                            <td style="padding: 0.75rem; text-align: center; border: 1px solid #eee; background: #fff;">
                                <span style="color: {{ $row['account']->nature === 'debit' ? '#1976d2' : '#e65100' }}; font-weight: 600;">
                                    {{ $row['account']->nature === 'debit' ? 'مدين' : 'دائن' }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem; text-align: left; border: 1px solid #eee; font-family: 'Courier New', monospace; font-weight: 600; color: #1976d2; background: #fff;">
                                @if($row['debit'] > 0)
                                    {{ number_format($row['debit'], 2) }}
                                @else
                                    <span style="color: #999;">-</span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem; text-align: left; border: 1px solid #eee; font-family: 'Courier New', monospace; font-weight: 600; color: #e65100; background: #fff;">
                                @if($row['credit'] > 0)
                                    {{ number_format($row['credit'], 2) }}
                                @else
                                    <span style="color: #999;">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background: #f5f5f5; border-top: 3px solid #333; border-bottom: 3px solid #333;">
                            <td colspan="4" style="padding: 1rem; text-align: left; font-weight: 700; font-size: 1rem; color: #222;">
                                <strong>الإجمالي</strong>
                            </td>
                            <td style="padding: 1rem; text-align: left; border-left: 1px solid #333; font-family: 'Courier New', monospace; font-weight: 700; font-size: 1rem; color: #1976d2; background: #e3f2fd;">
                                <strong>{{ number_format($totalDebit, 2) }}</strong>
                            </td>
                            <td style="padding: 1rem; text-align: left; border-left: 1px solid #333; font-family: 'Courier New', monospace; font-weight: 700; font-size: 1rem; color: #e65100; background: #fff3e0;">
                                <strong>{{ number_format($totalCredit, 2) }}</strong>
                            </td>
                        </tr>
                        @php
                            $difference = abs($totalDebit - $totalCredit);
                            $isBalanced = $difference < 0.01; // تحمل خطأ صغير في التقريب
                        @endphp
                        <tr style="background: {{ $isBalanced ? '#e8f5e9' : '#ffebee' }}; border-bottom: 3px solid #333;">
                            <td colspan="4" style="padding: 1rem; text-align: left; font-weight: 700; font-size: 1rem; color: {{ $isBalanced ? '#2e7d32' : '#c62828' }};">
                                <strong>{{ $isBalanced ? '✓ متوازن' : '✗ غير متوازن' }}</strong>
                            </td>
                            <td colspan="2" style="padding: 1rem; text-align: center; border-left: 1px solid #333; font-family: 'Courier New', monospace; font-weight: 700; font-size: 1rem; color: {{ $isBalanced ? '#2e7d32' : '#c62828' }};">
                                <strong>الفرق: {{ number_format($difference, 2) }}</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 3rem; color: #999;">
                <i class="fas fa-balance-scale" style="font-size: 3rem; margin-bottom: 1rem; color: #ccc;"></i>
                <p style="font-size: 1rem; margin: 0;">لا توجد بيانات لعرضها</p>
            </div>
        @endif
    </div>
</div>

<style>
    /* إجبار خلفية بيضاء ونص غامق لجدول ميزان المراجعة حتى يظهر اسم الحساب وغيره بوضوح */
    .trial-balance-table-wrap,
    .trial-balance-table-wrap #trial-balance-table,
    .trial-balance-table-wrap #trial-balance-table tbody,
    .trial-balance-table-wrap #trial-balance-table tbody tr,
    .trial-balance-table-wrap #trial-balance-table tbody td {
        background: #fff !important;
    }
    .trial-balance-table-wrap #trial-balance-table tbody td {
        color: #222;
    }
    .trial-balance-table-wrap #trial-balance-table thead th {
        background: #333 !important;
        color: #fff !important;
    }
    @media print {
        body {
            background: white;
        }
        
        .content-card {
            background: white !important;
            padding: 0 !important;
        }
        
        .page-header {
            margin-bottom: 1rem !important;
        }
        
        .page-header h1 {
            font-size: 1.2rem !important;
        }
        
        .page-subtitle {
            display: none;
        }
        
        /* إخفاء الفلاتر وأزرار الطباعة */
        form,
        button[onclick*="print"] {
            display: none !important;
        }
        
        /* تحسين جدول الطباعة */
        #trial-balance-table {
            font-size: 0.75rem !important;
        }
        
        #trial-balance-table th,
        #trial-balance-table td {
            padding: 0.5rem !important;
        }
        
        @page {
            margin: 15mm;
            size: A4 landscape;
        }
    }
</style>
