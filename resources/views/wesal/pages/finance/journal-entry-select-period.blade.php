<div class="content-card">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 class="page-title">
            <i class="fas fa-calendar-alt" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            اختيار السنة المالية
        </h1>
        <p class="page-subtitle">يرجى اختيار السنة المالية قبل إنشاء القيد. سيتم تحديد الفترة تلقائياً من تاريخ القيد</p>
    </div>

    @if(session('error'))
        <div class="alert alert-error" style="margin-bottom: 1.5rem; background: #ffebee; color: #c62828; padding: 0.75rem; border-radius: 6px; border: 1px solid #ef5350; font-size: 0.85rem;">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- السنوات المالية -->
    <div style="background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="color: #222; margin-bottom: 1rem; font-size: 1.1rem; font-weight: 600;">
            <i class="fas fa-calendar-alt" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            السنوات المالية المتاحة
        </h2>
        
        @if($fiscalYears->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
                @foreach($fiscalYears as $year)
                    @php $isCurrent = isset($currentFiscalYear) && $currentFiscalYear && $currentFiscalYear->id === $year->id; @endphp
                    <a href="{{ route('wesal.finance.journal-entries.create', ['fiscal_year_id' => $year->id]) }}" 
                       style="display: block; padding: 1.25rem; background: {{ $isCurrent ? '#e8f5e9' : '#f8f9fa' }}; border: 2px solid {{ $isCurrent ? 'var(--primary-color)' : '#e0e0e0' }}; border-radius: 8px; text-decoration: none; color: #333; transition: all 0.3s ease;"
                       onmouseover="this.style.borderColor='var(--primary-color)'; this.style.background='#f0f7f4'; this.style.transform='translateY(-2px)'"
                       onmouseout="this.style.borderColor='{{ $isCurrent ? 'var(--primary-color)' : '#e0e0e0' }}'; this.style.background='{{ $isCurrent ? '#e8f5e9' : '#f8f9fa' }}'; this.style.transform='translateY(0)'">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                            <h3 style="color: #222; margin: 0; font-size: 1.1rem; font-weight: 700;">
                                {{ $year->year_name }}
                                @if($isCurrent)
                                    <span style="font-size: 0.75rem; font-weight: 600; color: var(--primary-color); background: rgba(95, 179, 142, 0.2); padding: 0.2rem 0.5rem; border-radius: 6px; margin-right: 0.5rem;">السنة الحالية</span>
                                @endif
                            </h3>
                            <i class="fas fa-chevron-left" style="color: var(--primary-color); font-size: 1rem;"></i>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="color: #666; font-size: 0.85rem;">
                                <strong>من:</strong> {{ $year->start_date->format('Y-m-d') }}
                            </span>
                            <span style="color: #666; font-size: 0.85rem;">
                                <strong>إلى:</strong> {{ $year->end_date->format('Y-m-d') }}
                            </span>
                        </div>
                        @php
                            $yearPeriods = \App\Models\AccountingPeriod::where('fiscal_year_id', $year->id)->get();
                            $openPeriods = $yearPeriods->where('status', 'open');
                        @endphp
                        <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #e0e0e0;">
                            <p style="color: #666; margin: 0 0 0.5rem 0; font-size: 0.85rem;">
                                <strong>عدد الفترات:</strong> {{ $yearPeriods->count() }}
                                @if($openPeriods->count() > 0)
                                    <span style="color: #4caf50; font-weight: 600;">({{ $openPeriods->count() }} مفتوحة)</span>
                                @endif
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 2rem; color: #999;">
                <i class="fas fa-calendar-times" style="font-size: 3rem; margin-bottom: 1rem; color: #ccc;"></i>
                <p style="font-size: 1rem; margin: 0;">لا توجد سنوات مالية متاحة</p>
            </div>
        @endif
    </div>

    <!-- زر الرجوع -->
    <div style="text-align: center; margin-top: 2rem;">
        <a href="{{ route('wesal.finance.journal-entries.index') }}" 
           style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.5rem; background: #6c757d; color: white; border: none; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 0.9rem;">
            <i class="fas fa-arrow-right"></i>
            <span>الرجوع إلى القيود</span>
        </a>
    </div>
</div>
