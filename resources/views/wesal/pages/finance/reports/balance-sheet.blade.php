<div class="content-card">
    <div class="page-header" style="margin-bottom: 1.5rem;">
        <h1 class="page-title">
            <i class="fas fa-balance-scale-right" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            قائمة المركز المالي
        </h1>
        <p class="page-subtitle">الأصول والالتزامات وحقوق الملكية (لقطاع غير الربحي)</p>
    </div>

    @if(($periods ?? collect())->isEmpty())
        <div style="background: #fff3e0; border: 1px solid #ff9800; color: #e65100; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <i class="fas fa-exclamation-triangle" style="margin-left: 0.5rem;"></i>
            <strong>لا توجد سنوات مالية.</strong> يرجى إنشاء سنة مالية من قسم السنوات المالية أولاً.
        </div>
    @endif

    <div style="background: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <form method="GET" action="{{ route('wesal.finance.reports.balance-sheet') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
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
                <input type="date" name="as_of" value="{{ request('as_of', $asOf ? $asOf->format('Y-m-d') : '') }}" 
                       style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem; background: #fff; color: #333;">
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
                <a href="{{ route('wesal.finance.reports.balance-sheet.export', ['period_id' => optional($selectedPeriod)->id ?? '', 'as_of' => isset($asOf) && $asOf ? $asOf->format('Y-m-d') : '']) }}" 
                   style="display: block; width: 100%; padding: 0.5rem; background: #2e7d32; color: white; border-radius: 4px; text-align: center; text-decoration: none; font-weight: 600; font-size: 0.85rem;">
                    <i class="fas fa-file-excel"></i> تصدير Excel
                </a>
            </div>
        </form>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start;">
        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid #1976d2; color: #1565c0; font-size: 1.1rem;">الأصول</h3>
            <table style="width: 100%; border-collapse: collapse; direction: rtl; font-size: 0.9rem;">
                @foreach($assetLines as $row)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 0.5rem 0; color: #333;">{{ $row['account']->code }} - {{ $row['account']->name_ar }}</td>
                    <td style="padding: 0.5rem 0; text-align: left; font-weight: 600;">{{ number_format($row['amount'], 2) }}</td>
                </tr>
                @endforeach
                <tr style="border-top: 2px solid #1976d2; font-weight: 700; font-size: 1rem;">
                    <td style="padding: 0.75rem 0; color: #1565c0;">إجمالي الأصول</td>
                    <td style="padding: 0.75rem 0; text-align: left; color: #1565c0;">{{ number_format($totalAssets, 2) }}</td>
                </tr>
            </table>
        </div>
        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid #e65100; color: #bf360c; font-size: 1.1rem;">الالتزامات وحقوق الملكية</h3>
            <table style="width: 100%; border-collapse: collapse; direction: rtl; font-size: 0.9rem;">
                @if(count($liabilityLines) > 0)
                <tr style="background: #fff3e0;"><td colspan="2" style="padding: 0.35rem 0; font-weight: 600; color: #e65100;">الالتزامات</td></tr>
                @foreach($liabilityLines as $row)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 0.5rem 0; color: #333;">{{ $row['account']->code }} - {{ $row['account']->name_ar }}</td>
                    <td style="padding: 0.5rem 0; text-align: left; font-weight: 600;">{{ number_format($row['amount'], 2) }}</td>
                </tr>
                @endforeach
                @endif
                @if(count($equityLines) > 0)
                <tr style="background: #e8f5e9;"><td colspan="2" style="padding: 0.35rem 0; font-weight: 600; color: #2e7d32;">حقوق الملكية</td></tr>
                @foreach($equityLines as $row)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 0.5rem 0; color: #333;">{{ $row['account']->code }} - {{ $row['account']->name_ar }}</td>
                    <td style="padding: 0.5rem 0; text-align: left; font-weight: 600;">{{ number_format($row['amount'], 2) }}</td>
                </tr>
                @endforeach
                @endif
                <tr style="border-top: 2px solid #e65100; font-weight: 700; font-size: 1rem;">
                    <td style="padding: 0.75rem 0; color: #bf360c;">إجمالي الالتزامات وحقوق الملكية</td>
                    <td style="padding: 0.75rem 0; text-align: left; color: #bf360c;">{{ number_format($totalLiabilitiesEquity, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    @if(abs($totalAssets - $totalLiabilitiesEquity) >= 0.01)
    <div style="margin-top: 1rem; padding: 1rem; background: #ffebee; border-radius: 8px; color: #c62828; font-weight: 600;">
        ملاحظة: الفرق بين إجمالي الأصول وإجمالي الالتزامات وحقوق الملكية = {{ number_format($totalAssets - $totalLiabilitiesEquity, 2) }}
    </div>
    @endif

    <div style="margin-top: 1.5rem;">
        <a href="{{ route('wesal.finance.reports.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #6c757d; color: white; border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 0.9rem;">
            <i class="fas fa-arrow-right"></i> التقارير المالية
        </a>
    </div>
</div>

<style>@media print { .content-card form, button, a { display: none !important; } .content-card { display: grid !important; } }</style>
