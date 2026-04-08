<div class="content-card">
    <div class="page-header" style="margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-file-invoice" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                    تفاصيل القيد - {{ $journalEntry->entry_no }}
                </h1>
                <p class="page-subtitle">{{ $journalEntry->entry_date?->format('Y-m-d') }} | {{ $journalEntry->description }}</p>
            </div>
            <a href="{{ route('wesal.finance.journal-entries.index') }}" style="padding: 0.6rem 1.25rem; background: rgba(255,255,255,0.1); color: var(--text-primary); text-decoration: none; border-radius: 8px; font-weight: 600; border: 1px solid var(--border-color);">
                <i class="fas fa-arrow-right"></i> العودة للقيود
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 1.5rem;">@foreach($errors->all() as $err)<p>{{ $err }}</p>@endforeach</div>
    @endif

    @if($journalEntry->status === 'posted')
        <div class="alert alert-info" style="margin-bottom: 1.5rem; background: rgba(33,150,243,0.15); border: 1px solid #2196f3; color: var(--text-primary); padding: 1rem; border-radius: 8px;">
            <i class="fas fa-lock" style="margin-left: 0.5rem;"></i>
            <strong>قيد مرحل:</strong> لا يمكن تعديل القيود المرحلة. لتصحيح خطأ، أنشئ قيد تسوية أو قيد عكسي.
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div style="padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px;">
            <label style="color: var(--text-secondary); font-size: 0.85rem;">رقم القيد</label>
            <div style="color: var(--text-primary); font-weight: 700;">{{ $journalEntry->entry_no }}</div>
        </div>
        <div style="padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px;">
            <label style="color: var(--text-secondary); font-size: 0.85rem;">التاريخ</label>
            <div style="color: var(--text-primary);">{{ $journalEntry->entry_date?->format('Y-m-d') }}</div>
        </div>
        <div style="padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px;">
            <label style="color: var(--text-secondary); font-size: 0.85rem;">النوع</label>
            <div style="color: var(--text-primary);">{{ match($journalEntry->entry_type ?? '') { 'receipt'=>'سند قبض', 'payment'=>'سند صرف', 'adjusting'=>'قيد تسوية', default=>'قيد يومية' } }}</div>
        </div>
        <div style="padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px;">
            <label style="color: var(--text-secondary); font-size: 0.85rem;">الحالة</label>
            <div style="color: {{ $journalEntry->status === 'posted' ? '#4caf50' : '#ff9800' }};">{{ $journalEntry->status === 'posted' ? 'مرحل' : 'مسودة' }}</div>
        </div>
    </div>

    <div style="background: #fff; padding: 1.5rem; border-radius: 12px; border: 2px solid #ddd; color: #222;">
        <h3 style="color: #222; margin-bottom: 1rem;">سطور القيد</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; direction: rtl;">
                <thead>
                    <tr style="background: #333;">
                        <th style="padding: 0.75rem; text-align: center; color: #fff;">الحساب</th>
                        <th style="padding: 0.75rem; text-align: center; color: #fff;">مدين</th>
                        <th style="padding: 0.75rem; text-align: center; color: #fff;">دائن</th>
                        <th style="padding: 0.75rem; text-align: right; color: #fff;">البيان</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($journalEntry->lines as $line)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 0.75rem; text-align: center; color: #222; background: #fff;">{{ $line->account->code ?? '' }} - {{ $line->account->name_ar ?? '' }}</td>
                        <td style="padding: 0.75rem; text-align: center; color: #2e7d32; background: #fff;">{{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}</td>
                        <td style="padding: 0.75rem; text-align: center; color: #c62828; background: #fff;">{{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}</td>
                        <td style="padding: 0.75rem; text-align: right; color: #222; background: #fff;">{{ $line->description }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem; padding: 1rem; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 1rem; color: #222;">
            <div><strong>إجمالي المدين:</strong> <span style="color: #2e7d32;">{{ number_format($journalEntry->total_debit ?? 0, 2) }}</span></div>
            <div><strong>إجمالي الدائن:</strong> <span style="color: #c62828;">{{ number_format($journalEntry->total_credit ?? 0, 2) }}</span></div>
        </div>
        @if($journalEntry->status === 'draft')
            <form method="POST" action="{{ route('wesal.finance.journal-entries.post', $journalEntry) }}" style="margin-top: 1rem; display: inline;">
                @csrf
                <button type="submit" style="padding: 0.6rem 1.5rem; background: #4caf50; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;"><i class="fas fa-check"></i> ترحيل القيد</button>
            </form>
        @endif
        @if(in_array($journalEntry->entry_type, ['receipt', 'payment']))
            <a href="{{ route('wesal.finance.journal-entries.print', $journalEntry) }}" style="display: inline-block; margin-top: 1rem; margin-right: 0.5rem; padding: 0.6rem 1.5rem; background: #2196f3; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;"><i class="fas fa-print"></i> طباعة</a>
        @endif
    </div>
</div>
