<div class="content-card">
    <div class="page-header" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title" style="display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-coins" style="color: var(--primary-color);"></i>
                تفاصيل الحساب: {{ $chartAccount->code }} - {{ $chartAccount->name_ar }}
            </h1>
            <p class="page-subtitle">عرض تفاصيل الحساب والشجرة والقيود المرتبطة</p>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="{{ route('wesal.finance.chart-accounts.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: rgba(255,255,255,0.1); color: var(--text-primary); border-radius: 8px; text-decoration: none; font-weight: 500; border: 1px solid rgba(255,255,255,0.2);" onmouseover="this.style.background='rgba(95,179,142,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <i class="fas fa-arrow-right"></i>
                <span>العودة لدليل الحسابات</span>
            </a>
            @if(!$chartAccount->is_fixed)
            <a href="{{ route('wesal.finance.chart-accounts.edit', $chartAccount) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px; text-decoration: none; font-weight: 600; border: none;">
                <i class="fas fa-edit"></i>
                <span>تعديل</span>
            </a>
            @endif
            <a href="{{ route('wesal.finance.chart-accounts.ledger', $chartAccount) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white; border-radius: 8px; text-decoration: none; font-weight: 600; border: none;">
                <i class="fas fa-book"></i>
                <span>كشف حساب</span>
            </a>
        </div>
    </div>

    <!-- تفاصيل الحساب -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <div style="padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; border-right: 4px solid var(--primary-color);">
            <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.5rem;">كود الحساب</label>
            <div style="color: var(--primary-color); font-weight: 700; font-size: 1.1rem;">{{ $chartAccount->code }}</div>
        </div>
        <div style="padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; border-right: 4px solid var(--primary-color);">
            <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.5rem;">المستوى</label>
            <div style="color: var(--text-primary); font-weight: 600;">{{ $chartAccount->level ?? '-' }}</div>
        </div>
        <div style="padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; border-right: 4px solid var(--primary-color);">
            <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.5rem;">طبيعة الحساب</label>
            <div style="color: var(--text-primary); font-weight: 600;">{{ $chartAccount->nature === 'debit' ? 'مدين' : 'دائن' }}</div>
        </div>
        <div style="padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; border-right: 4px solid var(--primary-color);">
            <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.5rem;">نوع الحساب</label>
            <div style="color: var(--text-primary); font-weight: 600;">{{ $chartAccount->is_postable ? 'حساب فرعي' : 'حساب رئيسي' }}</div>
        </div>
        <div style="padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; border-right: 4px solid var(--primary-color);">
            <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.5rem;">الحالة</label>
            <div style="color: var(--text-primary); font-weight: 600;">{{ $chartAccount->status === 'active' ? 'مفعل' : 'غير مفعل' }}</div>
        </div>
        @if($chartAccount->parent)
        <div style="padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; border-right: 4px solid var(--primary-color);">
            <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.5rem;">الحساب الأب</label>
            <a href="{{ route('wesal.finance.chart-accounts.show', $chartAccount->parent) }}" style="color: var(--primary-color); font-weight: 600; text-decoration: none;">{{ $chartAccount->parent->code }} - {{ $chartAccount->parent->name_ar }}</a>
        </div>
        @endif
    </div>

    @if($chartAccount->description)
    <div style="padding: 1rem; background: rgba(255, 255, 255, 0.05); border-radius: 8px; margin-bottom: 2rem;">
        <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.5rem;">الوصف</label>
        <p style="color: var(--text-primary); margin: 0;">{{ $chartAccount->description }}</p>
    </div>
    @endif

    <!-- الحسابات الفرعية -->
    @if($chartAccount->children->isNotEmpty())
    <div style="margin-bottom: 2rem;">
        <h2 style="color: var(--text-primary); margin-bottom: 1rem; font-size: 1.2rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-sitemap" style="color: var(--primary-color);"></i>
            الحسابات الفرعية ({{ $chartAccount->children->count() }})
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 0.75rem;">
            @foreach($chartAccount->children as $child)
            <a href="{{ route('wesal.finance.chart-accounts.show', $child) }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: rgba(255,255,255,0.08); border-radius: 8px; text-decoration: none; color: var(--text-primary); border: 1px solid rgba(255,255,255,0.1); transition: all 0.2s;" onmouseover="this.style.background='rgba(95,179,142,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                <i class="fas fa-file-invoice" style="color: var(--primary-color);"></i>
                <span><strong>{{ $child->code }}</strong> - {{ $child->name_ar }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- القيود المرتبطة -->
    @php $lines = $chartAccount->journalLines->take(20); @endphp
    @if($lines->isNotEmpty())
    <div>
        <h2 style="color: var(--text-primary); margin-bottom: 1rem; font-size: 1.2rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-file-invoice" style="color: var(--primary-color);"></i>
            آخر القيود المرتبطة ({{ $lines->count() }})
        </h2>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.05); border-radius: 8px; overflow: hidden;">
                <thead>
                    <tr style="background: rgba(95, 179, 142, 0.2);">
                        <th style="padding: 0.75rem 1rem; text-align: right; color: var(--text-primary); font-weight: 600;">رقم القيد</th>
                        <th style="padding: 0.75rem 1rem; text-align: right; color: var(--text-primary); font-weight: 600;">التاريخ</th>
                        <th style="padding: 0.75rem 1rem; text-align: right; color: var(--text-primary); font-weight: 600;">مدين</th>
                        <th style="padding: 0.75rem 1rem; text-align: right; color: var(--text-primary); font-weight: 600;">دائن</th>
                        <th style="padding: 0.75rem 1rem; text-align: right; color: var(--text-primary); font-weight: 600;">إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lines as $line)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                        <td style="padding: 0.75rem 1rem; color: var(--text-primary);">{{ $line->journalEntry->entry_no ?? '-' }}</td>
                        <td style="padding: 0.75rem 1rem; color: var(--text-primary);">{{ $line->journalEntry?->entry_date?->format('Y-m-d') ?? '-' }}</td>
                        <td style="padding: 0.75rem 1rem; color: var(--primary-color);">{{ number_format($line->debit ?? 0, 2) }}</td>
                        <td style="padding: 0.75rem 1rem; color: #ff6b6b;">{{ number_format($line->credit ?? 0, 2) }}</td>
                        <td style="padding: 0.75rem 1rem;">
                            @if($line->journalEntry)
                            <a href="{{ route('wesal.finance.journal-entries.show', $line->journalEntry) }}" style="color: var(--primary-color); text-decoration: none;">عرض</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div style="text-align: center; padding: 2rem; color: var(--text-secondary); background: rgba(255,255,255,0.05); border-radius: 8px;">
        <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.6;"></i>
        <p style="margin: 0;">لا توجد قيود مرتبطة بهذا الحساب</p>
    </div>
    @endif
</div>
