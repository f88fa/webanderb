<div class="content-card">
    <div class="page-header" style="margin-bottom: 1.5rem;">
        <h1 class="page-title">
            <i class="fas fa-exchange-alt" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            سجل القيود المالية
        </h1>
        <p class="page-subtitle">
            سجل تفصيلي لجميع القيود المرحلة (حركة مالية عامة) — يمكن التصفية حسب الفترة والتاريخ والحساب
        </p>
    </div>

    <!-- فلترة -->
    <div style="background: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <form method="GET" action="{{ route('wesal.finance.reports.financial-movement') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; align-items: end;">
            <div>
                <label style="display: block; margin-bottom: 0.25rem; color: #222; font-weight: 600; font-size: 0.8rem;">السنة المالية</label>
                <select name="period_id" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; background: #fff; color: #333;">
                    <option value="">الكل</option>
                    @foreach(($periods ?? collect())->groupBy('fiscal_year_id') as $fid => $periodsInYear)
                        @php $lastPeriod = $periodsInYear->sortByDesc('start_date')->first(); @endphp
                        @if($lastPeriod)
                            <option value="{{ $lastPeriod->id }}" {{ ($periodId ?? '') == $lastPeriod->id ? 'selected' : '' }}>{{ $lastPeriod->fiscalYear->year_name ?? $fid }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.25rem; color: #222; font-weight: 600; font-size: 0.8rem;">من تاريخ</label>
                <input type="date" name="from_date" value="{{ $fromDate ? $fromDate->format('Y-m-d') : '' }}" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; background: #fff; color: #333;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.25rem; color: #222; font-weight: 600; font-size: 0.8rem;">إلى تاريخ</label>
                <input type="date" name="to_date" value="{{ $toDate ? $toDate->format('Y-m-d') : '' }}" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; background: #fff; color: #333;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.25rem; color: #222; font-weight: 600; font-size: 0.8rem;">الحساب</label>
                <select name="account_id" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; background: #fff; color: #333;">
                    <option value="">الكل</option>
                    @foreach($accounts ?? [] as $a)
                        <option value="{{ $a->id }}" {{ ($accountId ?? '') == $a->id ? 'selected' : '' }}>{{ $a->code }} - {{ $a->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.25rem; color: #222; font-weight: 600; font-size: 0.8rem;">نوع القيد</label>
                <select name="entry_type" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; background: #fff; color: #333;">
                    <option value="">الكل</option>
                    <option value="manual" {{ ($entryType ?? '') == 'manual' ? 'selected' : '' }}>قيد يومية</option>
                    <option value="receipt" {{ ($entryType ?? '') == 'receipt' ? 'selected' : '' }}>سند قبض</option>
                    <option value="payment" {{ ($entryType ?? '') == 'payment' ? 'selected' : '' }}>سند صرف</option>
                    <option value="adjusting" {{ ($entryType ?? '') == 'adjusting' ? 'selected' : '' }}>قيد تسوية</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <button type="submit" style="padding: 0.5rem 1rem; background: var(--primary-color); color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-filter"></i> عرض
                </button>
                <a href="{{ route('wesal.finance.reports.financial-movement.export', request()->only(['period_id', 'from_date', 'to_date', 'account_id', 'entry_type'])) }}" 
                   style="padding: 0.5rem 1rem; background: #2e7d32; color: white; border-radius: 4px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-file-excel"></i> تصدير Excel
                </a>
            </div>
        </form>
    </div>

    <!-- الجدول -->
    <div style="background: white; padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; direction: rtl; font-size: 0.85rem;" class="financial-movement-table">
            <thead>
                <tr style="background: #333; color: white;">
                    <th style="padding: 0.6rem; text-align: center;">م</th>
                    <th style="padding: 0.6rem; text-align: center;">التاريخ</th>
                    <th style="padding: 0.6rem; text-align: center;">رقم القيد</th>
                    <th style="padding: 0.6rem; text-align: center;">نوع القيد</th>
                    <th style="padding: 0.6rem; text-align: right;">الحساب</th>
                    <th style="padding: 0.6rem; text-align: right;">البيان</th>
                    <th style="padding: 0.6rem; text-align: center;">مدين</th>
                    <th style="padding: 0.6rem; text-align: center;">دائن</th>
                    <th style="padding: 0.6rem; text-align: center;">مرجع</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movementLines ?? [] as $i => $line)
                    @php $entry = $line->journalEntry; $acc = $line->account; @endphp
                    <tr style="border-bottom: 1px solid #eee; background: #fff;">
                        <td style="padding: 0.6rem; text-align: center; color: #222;">{{ ($movementLines->currentPage() - 1) * $movementLines->perPage() + $i + 1 }}</td>
                        <td style="padding: 0.6rem; text-align: center; color: #222;">{{ $entry->entry_date?->format('Y-m-d') }}</td>
                        <td style="padding: 0.6rem; text-align: center;">
                            <a href="{{ route('wesal.finance.journal-entries.show', $entry) }}" style="color: #1976d2; text-decoration: none;">{{ $entry->entry_no }}</a>
                        </td>
                        <td style="padding: 0.6rem; text-align: center; color: #222;">
                            @if($entry->entry_type === 'receipt') سند قبض
                            @elseif($entry->entry_type === 'payment') سند صرف
                            @elseif($entry->entry_type === 'adjusting') قيد تسوية
                            @else قيد يومية @endif
                        </td>
                        <td style="padding: 0.6rem; text-align: right; color: #222;">{{ $acc->code ?? '' }} - {{ $acc->name_ar ?? '' }}</td>
                        <td style="padding: 0.6rem; text-align: right; color: #222;">{{ Str::limit($line->description ?: $entry->description, 40) }}</td>
                        <td style="padding: 0.6rem; text-align: center; color: #2e7d32;">{{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}</td>
                        <td style="padding: 0.6rem; text-align: center; color: #c62828;">{{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}</td>
                        <td style="padding: 0.6rem; text-align: center; color: #222;">{{ $line->reference ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="padding: 2rem; text-align: center; color: #666;">لا توجد حركات مطابقة للفلاتر المحددة</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if(isset($movementLines) && $movementLines->hasPages())
            <div style="margin-top: 1rem;">{{ $movementLines->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
