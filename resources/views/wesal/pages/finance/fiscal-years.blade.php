<div class="content-card">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 class="page-title">
            <i class="fas fa-calendar-alt" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            الفترات المالية
        </h1>
        <p class="page-subtitle">اختر السنة لعرض الفترات المحاسبية أو ميزان المراجعة</p>
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

    <!-- السنوات فقط: 2024، 2025، 2026 ... -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        @forelse($fiscalYears as $fy)
            <div style="background: rgba(255,255,255,0.08); border: 2px solid var(--border-color); border-radius: 16px; padding: 1.5rem; text-align: center;">
                <div style="font-size: 2rem; font-weight: 800; color: var(--primary-color); margin-bottom: 1rem;">{{ $fy->year_name }}</div>
                @if($fy->status === 'closed')
                    <span style="display: inline-block; margin-bottom: 1rem; padding: 0.25rem 0.75rem; background: rgba(244,67,54,0.2); color: #f44336; border-radius: 8px; font-size: 0.85rem; font-weight: 600;">مغلقة</span>
                @else
                    <span style="display: inline-block; margin-bottom: 1rem; padding: 0.25rem 0.75rem; background: rgba(76,175,80,0.2); color: #4caf50; border-radius: 8px; font-size: 0.85rem; font-weight: 600;">مفتوحة</span>
                @endif
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <a href="{{ route('wesal.finance.chart-accounts.trial-balance', ['fiscal_year_id' => $fy->id]) }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.65rem 1rem; background: var(--primary-color); color: white; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                        <i class="fas fa-balance-scale"></i>
                        <span>ميزان المراجعة</span>
                    </a>
                    <a href="{{ route('wesal.finance.periods.index', ['fiscal_year_id' => $fy->id]) }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.65rem 1rem; background: rgba(255,255,255,0.15); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                        <i class="fas fa-calendar-check"></i>
                        <span>عرض الفترات</span>
                    </a>
                </div>
            </div>
        @empty
            <p style="grid-column: 1 / -1; text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد سنوات مالية. أضف سنة من القسم أدناه.</p>
        @endforelse
    </div>

    <!-- إدارة السنوات: إضافة سنة + جدول التفاصيل -->
    <details style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 1rem;">
        <summary style="color: var(--text-primary); font-weight: 700; cursor: pointer; font-size: 1.05rem;">
            <i class="fas fa-cog" style="margin-left: 0.5rem;"></i> إدارة السنوات المالية (إضافة سنة / تفاصيل)
        </summary>
        <div style="margin-top: 1.5rem;">
            <h3 style="color: var(--text-primary); margin-bottom: 1rem;">إضافة سنة مالية جديدة</h3>
            <form method="POST" action="{{ route('wesal.finance.fiscal-years.store') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; align-items: end;">
                @csrf
                <div>
                    <label class="form-label">اسم السنة *</label>
                    <input type="text" name="year_name" class="form-control" required placeholder="مثال: 2026" value="{{ old('year_name') }}">
                </div>
                <div>
                    <label class="form-label">تاريخ البداية *</label>
                    <input type="date" name="start_date" class="form-control" required value="{{ old('start_date') }}">
                </div>
                <div>
                    <label class="form-label">تاريخ النهاية *</label>
                    <input type="date" name="end_date" class="form-control" required value="{{ old('end_date') }}">
                </div>
                <div>
                    <button type="submit" style="padding: 0.6rem 1.5rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                        <i class="fas fa-plus"></i> إنشاء السنة المالية
                    </button>
                </div>
            </form>
        </div>
        @if($fiscalYears->count() > 0)
            <div style="margin-top: 1.5rem;">
                <h3 style="color: var(--text-primary); margin-bottom: 1rem;">جدول التفاصيل</h3>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; direction: rtl;">
                        <thead>
                            <tr style="background: rgba(0,0,0,0.2);">
                                <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">السنة</th>
                                <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">من تاريخ</th>
                                <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">إلى تاريخ</th>
                                <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">الحالة</th>
                                <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fiscalYears as $fy)
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.75rem; text-align: center; color: var(--text-primary);"><strong>{{ $fy->year_name }}</strong></td>
                                <td style="padding: 0.75rem; text-align: center; color: var(--text-primary);">{{ $fy->start_date?->format('Y-m-d') }}</td>
                                <td style="padding: 0.75rem; text-align: center; color: var(--text-primary);">{{ $fy->end_date?->format('Y-m-d') }}</td>
                                <td style="padding: 0.75rem; text-align: center;">
                                    @if($fy->status === 'closed')
                                        <span style="color: #f44336; font-weight: 600;">مغلقة</span>
                                    @else
                                        <span style="color: #4caf50; font-weight: 600;">مفتوحة</span>
                                    @endif
                                </td>
                                <td style="padding: 0.75rem; text-align: center;">
                                    <a href="{{ route('wesal.finance.fiscal-years.show', $fy) }}" style="padding: 0.4rem 0.8rem; background: #6c757d; color: white; border-radius: 4px; text-decoration: none; font-size: 0.85rem;">عرض</a>
                                    @if($fy->status === 'open')
                                        <form method="POST" action="{{ route('wesal.finance.fiscal-years.close', $fy) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من إغلاق السنة المالية؟');">
                                            @csrf
                                            <button type="submit" style="padding: 0.4rem 0.8rem; background: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem;">إغلاق السنة</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </details>
</div>
