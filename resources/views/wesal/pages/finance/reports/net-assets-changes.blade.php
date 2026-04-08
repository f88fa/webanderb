<div class="content-card">
    <div class="page-header" style="margin-bottom: 1.5rem;">
        <h1 class="page-title">
            <i class="fas fa-chart-line" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            قائمة التغيرات في صافي الأصول
        </h1>
        <p class="page-subtitle">
            التحليل الزمني لتغيرات حقوق الملكية (مطلوب للقطاع غير الربحي)
        </p>
    </div>

    @if(($periods ?? collect())->isEmpty())
        <div style="background: #fff3e0; border: 1px solid #ff9800; color: #e65100; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <i class="fas fa-exclamation-triangle" style="margin-left: 0.5rem;"></i>
            <strong>لا توجد سنوات مالية.</strong> يرجى إنشاء سنة مالية من قسم السنوات المالية أولاً.
        </div>
    @else
    <div style="background: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <form method="GET" action="{{ route('wesal.finance.reports.net-assets-changes') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            <div>
                <label style="display: block; margin-bottom: 0.25rem; color: #222; font-weight: 600; font-size: 0.8rem;">السنة المالية: <span style="color: #f44336;">*</span></label>
                <select name="period_id" class="form-control" style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem; background: #fff; color: #333;" required>
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
                <label style="display: block; margin-bottom: 0.25rem; color: #222; font-weight: 600; font-size: 0.8rem;">من تاريخ (اختياري):</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem; background: #fff; color: #333;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.25rem; color: #222; font-weight: 600; font-size: 0.8rem;">إلى تاريخ:</label>
                <input type="date" name="as_of" value="{{ request('as_of', $asOf ? $asOf->format('Y-m-d') : '') }}" style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem; background: #fff; color: #333;">
            </div>
            <div>
                <button type="submit" style="width: 100%; padding: 0.5rem; background: var(--primary-color); color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 0.85rem;">
                    <i class="fas fa-filter"></i> عرض
                </button>
            </div>
            <div>
                <button type="button" onclick="window.print()" style="width: 100%; padding: 0.5rem; background: #2196f3; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 0.85rem;">
                    <i class="fas fa-print"></i> طباعة
                </button>
            </div>
            <div>
                <a href="{{ route('wesal.finance.reports.net-assets-changes.export', ['period_id' => optional($selectedPeriod)->id ?? '', 'as_of' => isset($asOf) && $asOf ? $asOf->format('Y-m-d') : '']) }}" style="display: block; width: 100%; padding: 0.5rem; background: #2e7d32; color: white; border-radius: 4px; text-align: center; text-decoration: none; font-weight: 600; font-size: 0.85rem;">
                    <i class="fas fa-file-excel"></i> تصدير Excel
                </a>
            </div>
        </form>
    </div>
    @endif

    @if($selectedPeriod ?? null)
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 1rem; font-size: 0.85rem; color: #666;">
            <strong>السنة المالية:</strong> {{ $selectedPeriod->fiscalYear->year_name ?? '' }}
            @if($asOf ?? null)
                | <strong>حتى:</strong> {{ $asOf->format('Y-m-d') }}
            @endif
        </div>
        <table style="width: 100%; border-collapse: collapse; direction: rtl; font-size: 0.95rem;">
            <thead>
                <tr style="background: #333; color: white;">
                    <th style="padding: 0.75rem; text-align: right; border: 1px solid #555;">الحساب</th>
                    <th style="padding: 0.75rem; text-align: left; border: 1px solid #555; width: 140px;">الرصيد الافتتاحي</th>
                    <th style="padding: 0.75rem; text-align: left; border: 1px solid #555; width: 140px;">الرصيد الختامي</th>
                    <th style="padding: 0.75rem; text-align: left; border: 1px solid #555; width: 140px;">التغير</th>
                </tr>
            </thead>
            <tbody>
                @foreach($changes ?? [] as $row)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 0.75rem; border: 1px solid #eee;">{{ $row['account']->code }} - {{ $row['account']->name_ar }}</td>
                    <td style="padding: 0.75rem; border: 1px solid #eee; text-align: left;">{{ number_format($row['beginning'], 2) }}</td>
                    <td style="padding: 0.75rem; border: 1px solid #eee; text-align: left;">{{ number_format($row['ending'], 2) }}</td>
                    <td style="padding: 0.75rem; border: 1px solid #eee; text-align: left; color: {{ $row['change'] >= 0 ? '#2e7d32' : '#c62828' }};">{{ number_format($row['change'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #f5f5f5; font-weight: 700;">
                    <td style="padding: 0.75rem; border: 1px solid #333;">إجمالي صافي الأصول</td>
                    <td style="padding: 0.75rem; border: 1px solid #333; text-align: left;">{{ number_format($beginningBalance ?? 0, 2) }}</td>
                    <td style="padding: 0.75rem; border: 1px solid #333; text-align: left;">{{ number_format(($beginningBalance ?? 0) + ($totalChanges ?? 0), 2) }}</td>
                    <td style="padding: 0.75rem; border: 1px solid #333; text-align: left; color: {{ ($totalChanges ?? 0) >= 0 ? '#2e7d32' : '#c62828' }};">{{ number_format($totalChanges ?? 0, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <div style="margin-top: 1.5rem;">
        <a href="{{ route('wesal.finance.reports.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #6c757d; color: white; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 0.9rem;">
            <i class="fas fa-arrow-right"></i> التقارير المالية
        </a>
    </div>
</div>

<style>@media print { .content-card form, button, a { display: none !important; } }</style>
