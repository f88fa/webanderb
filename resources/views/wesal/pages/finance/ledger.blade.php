<div class="content-card">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-book"></i> كشف حساب
            </h1>
            <p class="page-subtitle">{{ $chartAccount->code }} - {{ $chartAccount->name_ar }}</p>
        </div>
        <a href="{{ route('wesal.finance.chart-accounts.index') }}" style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.75rem; background: rgba(255, 255, 255, 0.1); color: var(--text-primary); text-decoration: none; border-radius: 12px; font-weight: 600; transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.1);">
            <i class="fas fa-arrow-right"></i>
            <span>العودة لدليل الحسابات</span>
        </a>
    </div>

    <!-- معلومات الحساب -->
    <div style="margin-top: 1.5rem; padding: 1.5rem; background: rgba(255, 255, 255, 0.05); border-radius: 12px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <div>
            <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.25rem;">كود الحساب</label>
            <div style="color: var(--text-primary); font-weight: 600;">{{ $chartAccount->code }}</div>
        </div>
        <div>
            <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.25rem;">اسم الحساب</label>
            <div style="color: var(--text-primary); font-weight: 600;">{{ $chartAccount->name_ar }}</div>
        </div>
        <div>
            <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.25rem;">طبيعة الحساب</label>
            <div style="color: var(--text-primary); font-weight: 600;">{{ $chartAccount->nature === 'debit' ? 'مدين' : 'دائن' }}</div>
        </div>
        <div>
            <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.25rem;">الرصيد الافتتاحي</label>
            @php $openBal = $openingBalance['raw_balance'] ?? 0; $openColor = $openBal >= 0 ? 'var(--primary-color)' : '#ff6b6b'; @endphp
            <div style="color: {{ $openColor }}; font-weight: 700; font-size: 1.1rem;">
                {{ number_format($openBal, 2) }}
            </div>
        </div>
    </div>

    <!-- الفلترة -->
    <div style="margin-top: 1.5rem; padding: 1rem; background: rgba(255, 255, 255, 0.05); border-radius: 12px;">
        <form method="GET" action="{{ route('wesal.finance.chart-accounts.ledger', $chartAccount) }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: end;">
            <div style="min-width: 200px;">
                <label style="display: block; color: var(--text-secondary); margin-bottom: 0.5rem; font-size: 0.9rem;">السنة المالية</label>
                <select name="period_id" style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.1); color: var(--text-primary);">
                    <option value="">الكل</option>
                    @foreach(($periods ?? collect())->groupBy('fiscal_year_id') as $fid => $periodsInYear)
                        @php $lastPeriod = $periodsInYear->sortByDesc('start_date')->first(); @endphp
                        @if($lastPeriod)
                            <option value="{{ $lastPeriod->id }}" {{ ($periodId ?? null) == $lastPeriod->id ? 'selected' : '' }}>{{ $lastPeriod->fiscalYear->year_name ?? $fid }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div style="min-width: 150px;">
                <label style="display: block; color: var(--text-secondary); margin-bottom: 0.5rem; font-size: 0.9rem;">من تاريخ</label>
                <input type="date" name="from_date" value="{{ $fromDate ? $fromDate->format('Y-m-d') : '' }}" style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.1); color: var(--text-primary);">
            </div>
            <div style="min-width: 150px;">
                <label style="display: block; color: var(--text-secondary); margin-bottom: 0.5rem; font-size: 0.9rem;">إلى تاريخ</label>
                <input type="date" name="to_date" value="{{ $toDate ? $toDate->format('Y-m-d') : '' }}" style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.1); color: var(--text-primary);">
            </div>
            <button type="submit" style="padding: 0.75rem 1.5rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                <i class="fas fa-filter"></i> فلترة
            </button>
            <a href="{{ route('wesal.finance.chart-accounts.ledger.export', $chartAccount) }}?period_id={{ $periodId ?? '' }}&from_date={{ $fromDate ? $fromDate->format('Y-m-d') : '' }}&to_date={{ $toDate ? $toDate->format('Y-m-d') : '' }}" 
               style="padding: 0.75rem 1.5rem; background: #2e7d32; color: white; border: none; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-excel"></i> تصدير Excel
            </a>
        </form>
    </div>

    <!-- جدول الحركات -->
    <div style="margin-top: 1.5rem; overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; background: rgba(255, 255, 255, 0.05); border-radius: 12px; overflow: hidden;">
            <thead>
                <tr style="background: rgba(95, 179, 142, 0.2);">
                    <th style="padding: 1rem; text-align: right; color: var(--text-primary); font-weight: 600;">التاريخ</th>
                    <th style="padding: 1rem; text-align: right; color: var(--text-primary); font-weight: 600;">رقم القيد</th>
                    <th style="padding: 1rem; text-align: right; color: var(--text-primary); font-weight: 600;">الوصف</th>
                    <th style="padding: 1rem; text-align: left; color: var(--text-primary); font-weight: 600;">مدين</th>
                    <th style="padding: 1rem; text-align: left; color: var(--text-primary); font-weight: 600;">دائن</th>
                    <th style="padding: 1rem; text-align: left; color: var(--text-primary); font-weight: 600;">الرصيد الجاري</th>
                    <th style="padding: 1rem; text-align: right; color: var(--text-primary); font-weight: 600;">مرجع</th>
                </tr>
            </thead>
            <tbody>
                @if(count($transactions) > 0 || ($openingBalance['raw_balance'] ?? 0) != 0)
                    {{-- صف الرصيد الافتتاحي --}}
                    @if(($openingBalance['raw_balance'] ?? 0) != 0)
                        <tr style="background: rgba(95, 179, 142, 0.1); border-bottom: 2px solid rgba(95, 179, 142, 0.3);">
                            <td style="padding: 0.75rem 1rem; color: var(--text-secondary); font-weight: 600;">رصيد افتتاحي</td>
                            <td colspan="2" style="padding: 0.75rem 1rem; color: var(--text-secondary);"></td>
                            <td colspan="2" style="padding: 0.75rem 1rem;"></td>
                            <td style="padding: 0.75rem 1rem; text-align: left; color: {{ $openingBalance['raw_balance'] >= 0 ? 'var(--primary-color)' : '#ff6b6b' }}; font-weight: 700;">
                                {{ number_format($openingBalance['raw_balance'], 2) }}
                            </td>
                            <td></td>
                        </tr>
                    @endif
                    
                    @foreach($transactions as $transaction)
                        @php
                            $runningBalance = $transaction['running_balance'];
                        @endphp
                        <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                            <td style="padding: 0.75rem 1rem; color: var(--text-secondary);">{{ $transaction['entry_date']->format('Y-m-d') }}</td>
                            <td style="padding: 0.75rem 1rem; color: var(--text-secondary);">{{ $transaction['entry_no'] }}</td>
                            <td style="padding: 0.75rem 1rem; color: var(--text-secondary);">{{ $transaction['description'] }}</td>
                            <td style="padding: 0.75rem 1rem; text-align: left; color: {{ $transaction['debit'] > 0 ? 'var(--primary-color)' : 'var(--text-secondary)' }}; font-weight: {{ $transaction['debit'] > 0 ? '600' : '400' }};">
                                {{ $transaction['debit'] > 0 ? number_format($transaction['debit'], 2) : '-' }}
                            </td>
                            <td style="padding: 0.75rem 1rem; text-align: left; color: {{ $transaction['credit'] > 0 ? '#ff6b6b' : 'var(--text-secondary)' }}; font-weight: {{ $transaction['credit'] > 0 ? '600' : '400' }};">
                                {{ $transaction['credit'] > 0 ? number_format($transaction['credit'], 2) : '-' }}
                            </td>
                            <td style="padding: 0.75rem 1rem; text-align: left; color: {{ $transaction['running_balance'] >= 0 ? 'var(--primary-color)' : '#ff6b6b' }}; font-weight: 700;">
                                {{ number_format($transaction['running_balance'], 2) }}
                            </td>
                            <td style="padding: 0.75rem 1rem; color: var(--text-secondary);">{{ $transaction['reference'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                            <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.5; margin-bottom: 0.5rem;"></i>
                            <p>لا توجد حركات في هذه الفترة</p>
                        </td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr style="background: rgba(95, 179, 142, 0.1); border-top: 2px solid rgba(95, 179, 142, 0.3);">
                    <td colspan="3" style="padding: 1rem; text-align: right; color: var(--text-primary); font-weight: 700;">الإجمالي</td>
                    <td style="padding: 1rem; text-align: left; color: var(--primary-color); font-weight: 700;">{{ number_format($totalDebit, 2) }}</td>
                    <td style="padding: 1rem; text-align: left; color: #ff6b6b; font-weight: 700;">{{ number_format($totalCredit, 2) }}</td>
                    <td style="padding: 1rem; text-align: left; color: {{ ($finalBalance ?? 0) >= 0 ? 'var(--primary-color)' : '#ff6b6b' }}; font-weight: 700; font-size: 1.1rem;">
                        {{ number_format($finalBalance ?? 0, 2) }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
