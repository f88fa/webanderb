<div class="content-card">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 class="page-title">
            <i class="fas fa-calendar-check" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            الفترات المحاسبية
        </h1>
        <p class="page-subtitle">إدارة الفترات المحاسبية (ترحيل عادي / تسويات)</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 1.5rem;">
            <i class="fas fa-exclamation-circle"></i>
            @foreach($errors->all() as $err) <p>{{ $err }}</p> @endforeach
        </div>
    @endif

    <!-- فلترة بالسنة المالية -->
    <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
        <form method="GET" action="{{ route('wesal.finance.periods.index') }}" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
            <div>
                <label class="form-label">السنة المالية</label>
                <select name="fiscal_year_id" class="form-control" onchange="this.form.submit()" style="min-width: 200px;">
                    <option value="">جميع السنوات</option>
                    @foreach($fiscalYears as $fy)
                        <option value="{{ $fy->id }}" {{ ($fiscalYearId ?? '') == $fy->id ? 'selected' : '' }}>
                            {{ $fy->year_name }} ({{ $fy->status === 'open' ? 'مفتوحة' : 'مغلقة' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="form-control" style="padding: 0.6rem 1.5rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer;">
                    <i class="fas fa-filter"></i> تصفية
                </button>
            </div>
        </form>
    </div>

    <!-- جدول الفترات -->
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
        @if($periods->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; direction: rtl;">
                    <thead>
                        <tr style="background: rgba(0,0,0,0.2);">
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">الفترة</th>
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">السنة المالية</th>
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">من تاريخ</th>
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">إلى تاريخ</th>
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">الترحيل</th>
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">التسويات</th>
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($periods as $p)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; text-align: center; color: var(--text-primary);"><strong>{{ $p->period_name }}</strong></td>
                            <td style="padding: 0.75rem; text-align: center; color: var(--text-primary);">{{ $p->fiscalYear->year_name ?? '-' }}</td>
                            <td style="padding: 0.75rem; text-align: center; color: var(--text-primary);">{{ $p->start_date?->format('Y-m-d') }}</td>
                            <td style="padding: 0.75rem; text-align: center; color: var(--text-primary);">{{ $p->end_date?->format('Y-m-d') }}</td>
                            <td style="padding: 0.75rem; text-align: center;">
                                @if($p->allow_posting)
                                    <span style="color: #4caf50;">مفتوح</span>
                                    <form method="POST" action="{{ route('wesal.finance.periods.close-posting', $p) }}" style="display: inline;" onsubmit="return confirm('إغلاق الترحيل العادي لهذه الفترة؟');">
                                        @csrf
                                        <button type="submit" style="padding: 0.25rem 0.5rem; background: #ff9800; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">إغلاق</button>
                                    </form>
                                @else
                                    <span style="color: #f44336;">مغلق</span>
                                    @if(auth()->user() && (method_exists(auth()->user(), 'hasPermissionTo') ? auth()->user()->hasPermissionTo('finance.admin') : true))
                                        <form method="POST" action="{{ route('wesal.finance.periods.open-posting', $p) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" style="padding: 0.25rem 0.5rem; background: #4caf50; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">فتح</button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                            <td style="padding: 0.75rem; text-align: center;">
                                @if($p->allow_adjustments)
                                    <span style="color: #4caf50;">مفتوح</span>
                                @else
                                    <span style="color: #f44336;">مغلق</span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem; text-align: center;">
                                @if($p->status === 'open' && $p->allow_posting)
                                    <a href="{{ route('wesal.finance.journal-entries.index', ['period_id' => $p->id]) }}" style="padding: 0.4rem 0.8rem; background: var(--primary-color); color: white; border-radius: 4px; text-decoration: none; font-size: 0.85rem;">القيود</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد فترات. أنشئ سنة مالية أولاً.</p>
        @endif
    </div>
</div>
