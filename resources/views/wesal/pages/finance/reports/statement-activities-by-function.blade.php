<div class="content-card">
    <div class="page-header" style="margin-bottom: 1.5rem;">
        <h1 class="page-title">
            <i class="fas fa-project-diagram" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            قائمة الأنشطة حسب الوظيفة
        </h1>
        <p class="page-subtitle">
            معيار القطاع غير الربحي: إيرادات ومصروفات (برامج، إدارية، جمع تبرعات)
        </p>
    </div>

    @if(($periods ?? collect())->isEmpty())
        <div style="background: #fff3e0; border: 1px solid #ff9800; color: #e65100; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <i class="fas fa-exclamation-triangle" style="margin-left: 0.5rem;"></i>
            <strong>لا توجد سنوات مالية.</strong> يرجى إنشاء سنة مالية من قسم السنوات المالية أولاً.
        </div>
    @endif

    <div style="background: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <form method="GET" action="{{ route('wesal.finance.reports.statement-activities-by-function') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            <div>
                <label style="display: block; margin-bottom: 0.25rem; color: #222; font-weight: 600; font-size: 0.8rem;">السنة المالية: <span style="color: #f44336;">*</span></label>
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
                <label style="display: block; margin-bottom: 0.25rem; color: #222; font-weight: 600; font-size: 0.8rem;">من تاريخ (اختياري):</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem; background: #fff; color: #333;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.25rem; color: #222; font-weight: 600; font-size: 0.8rem;">إلى تاريخ:</label>
                <input type="date" name="as_of" value="{{ request('as_of', isset($asOf) && $asOf ? $asOf->format('Y-m-d') : '') }}" style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem; background: #fff; color: #333;">
            </div>
            <div>
                <button type="submit" style="width: 100%; padding: 0.5rem; background: var(--primary-color); color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 0.85rem;">عرض</button>
            </div>
        </form>
    </div>

    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <table style="width: 100%; border-collapse: collapse; direction: rtl; font-size: 0.95rem;">
            <thead>
                <tr style="background: #333; color: white;">
                    <th style="padding: 0.75rem; text-align: right; border: 1px solid #555;">البيان</th>
                    <th style="padding: 0.75rem; text-align: left; border: 1px solid #555; width: 180px;">المبلغ</th>
                </tr>
            </thead>
            <tbody>
                <tr style="background: #e8f5e9;">
                    <td style="padding: 0.75rem; border: 1px solid #eee; font-weight: 700;">إجمالي الإيرادات</td>
                    <td style="padding: 0.75rem; border: 1px solid #eee; text-align: left; font-weight: 600;">{{ number_format($revenueTotal, 2) }}</td>
                </tr>
                <tr style="background: #fff3e0;">
                    <td style="padding: 0.75rem; border: 1px solid #eee; font-weight: 700;">المصروفات حسب الوظيفة</td>
                    <td style="padding: 0.75rem; border: 1px solid #eee;"></td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 0.5rem 0.75rem 0.5rem 2rem;">مصروفات البرامج</td>
                    <td style="padding: 0.5rem 0.75rem; border: 1px solid #eee; text-align: left;">{{ number_format($expenseProgram, 2) }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 0.5rem 0.75rem 0.5rem 2rem;">مصروفات إدارية</td>
                    <td style="padding: 0.5rem 0.75rem; border: 1px solid #eee; text-align: left;">{{ number_format($expenseAdministrative, 2) }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 0.5rem 0.75rem 0.5rem 2rem;">مصروفات جمع التبرعات</td>
                    <td style="padding: 0.5rem 0.75rem; border: 1px solid #eee; text-align: left;">{{ number_format($expenseFundraising, 2) }}</td>
                </tr>
                @if($expenseUnclassified > 0)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 0.5rem 0.75rem 0.5rem 2rem;">غير مصنف</td>
                    <td style="padding: 0.5rem 0.75rem; border: 1px solid #eee; text-align: left;">{{ number_format($expenseUnclassified, 2) }}</td>
                </tr>
                @endif
                <tr style="border-top: 2px solid #ddd;">
                    <td style="padding: 0.75rem; font-weight: 700;">إجمالي المصروفات</td>
                    <td style="padding: 0.75rem; text-align: left; font-weight: 700;">{{ number_format($totalExpenses, 2) }}</td>
                </tr>
                <tr style="background: #e3f2fd; border-top: 3px solid #1976d2;">
                    <td style="padding: 1rem; font-weight: 700; font-size: 1.05rem;">صافي النشاط (فائض / عجز)</td>
                    <td style="padding: 1rem; text-align: left; font-weight: 700; font-size: 1.05rem; color: {{ $netActivity >= 0 ? '#1565c0' : '#c62828' }};">{{ number_format($netActivity, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        <a href="{{ route('wesal.finance.reports.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #6c757d; color: white; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 0.9rem;">
            <i class="fas fa-arrow-right"></i> التقارير المالية
        </a>
    </div>
</div>
