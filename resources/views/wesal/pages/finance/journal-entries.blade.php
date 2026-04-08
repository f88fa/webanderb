<div class="content-card">
    <div class="page-header" style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-file-invoice" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                @if(($currentEntryType ?? '') === 'receipt')
                    سندات القبض
                @elseif(($currentEntryType ?? '') === 'payment')
                    سندات الصرف
                @else
                    القيود اليومية
                @endif
            </h1>
            <p class="page-subtitle">
                @if(($currentEntryType ?? '') === 'receipt')
                    عرض سندات القبض وإنشاء سند قبض جديد
                @elseif(($currentEntryType ?? '') === 'payment')
                    عرض سندات الصرف وإنشاء سند صرف جديد
                @else
                    عرض وإدارة جميع القيود اليومية والسندات
                @endif
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
            @if(!in_array($currentEntryType ?? '', ['receipt', 'payment']))
                <a href="{{ route('wesal.finance.journal-entries.select-period') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: var(--primary-color); color: white; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                    <i class="fas fa-plus-circle"></i>
                    <span>إنشاء قيد جديد</span>
                </a>
            @endif
            @if(!in_array($currentEntryType ?? '', ['receipt', 'payment']))
                <a href="{{ route('wesal.finance.receipt-voucher.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #4caf50; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span>سند قبض</span>
                </a>
                <a href="{{ route('wesal.finance.payment-voucher.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #f44336; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                    <i class="fas fa-money-check-alt"></i>
                    <span>سند صرف</span>
                </a>
            @endif
            @if(($currentEntryType ?? '') === 'receipt')
                <a href="{{ route('wesal.finance.receipt-voucher.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #4caf50; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span>إنشاء سند قبض جديد</span>
                </a>
            @elseif(($currentEntryType ?? '') === 'payment')
                <a href="{{ route('wesal.finance.payment-voucher.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #f44336; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                    <i class="fas fa-money-check-alt"></i>
                    <span>إنشاء سند صرف جديد</span>
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1rem; background: #e8f5e9; color: #2e7d32; padding: 0.75rem; border-radius: 6px; border: 1px solid #4caf50; font-size: 0.85rem;">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- الفلاتر -->
    <div style="background: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <form method="GET" action="{{ route('wesal.finance.journal-entries.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            <div>
                <label style="display: block; margin-bottom: 0.25rem; color: #222; font-weight: 600; font-size: 0.8rem;">السنة المالية:</label>
                <select name="fiscal_year_id" class="form-control" style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem; background: #fff; color: #333;">
                    <option value="" {{ !($defaultFiscalYearId ?? null) ? 'selected' : '' }}>جميع السنوات</option>
                    @foreach($fiscalYears ?? [] as $fy)
                        <option value="{{ $fy->id }}" {{ ($defaultFiscalYearId ?? '') == $fy->id ? 'selected' : '' }}>
                            {{ $fy->year_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.25rem; color: #222; font-weight: 600; font-size: 0.8rem;">من تاريخ:</label>
                <input type="date" name="from_date" value="{{ request('from_date', $filterFromDate ?? '') }}" class="form-control" style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem; background: #fff; color: #333;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.25rem; color: #222; font-weight: 600; font-size: 0.8rem;">إلى تاريخ:</label>
                <input type="date" name="to_date" value="{{ request('to_date', $filterToDate ?? '') }}" class="form-control" style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem; background: #fff; color: #333;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.25rem; color: #222; font-weight: 600; font-size: 0.8rem;">نوع القيد:</label>
                <select name="entry_type" class="form-control" style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem; background: #fff; color: #333;">
                    <option value="">جميع الأنواع</option>
                    <option value="manual" {{ request('entry_type') == 'manual' ? 'selected' : '' }}>قيد يومية</option>
                    <option value="receipt" {{ request('entry_type') == 'receipt' ? 'selected' : '' }}>سند قبض</option>
                    <option value="payment" {{ request('entry_type') == 'payment' ? 'selected' : '' }}>سند صرف</option>
                    <option value="adjusting" {{ request('entry_type') == 'adjusting' ? 'selected' : '' }}>قيد تسوية</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.25rem; color: #222; font-weight: 600; font-size: 0.8rem;">الحالة:</label>
                <select name="status" class="form-control" style="width: 100%; padding: 0.4rem 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem; background: #fff; color: #333;">
                    <option value="">جميع الحالات</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>مرحل</option>
                </select>
            </div>
            <div>
                <button type="submit" style="width: 100%; padding: 0.5rem; background: var(--primary-color); color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 0.85rem;">
                    <i class="fas fa-filter"></i> تصفية
                </button>
            </div>
        </form>
    </div>

    <!-- جدول القيود -->
    <div style="background: white; padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        @if($entries->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; direction: rtl; font-size: 0.85rem;">
                    <thead>
                        <tr style="background: #f5f5f5; border-bottom: 2px solid #ddd;">
                            <th style="padding: 0.75rem; text-align: center; color: #222; font-weight: 700; border-left: 1px solid #ccc; font-size: 0.85rem;">رقم القيد</th>
                            <th style="padding: 0.75rem; text-align: center; color: #222; font-weight: 700; border-left: 1px solid #ccc; font-size: 0.85rem;">التاريخ</th>
                            <th style="padding: 0.75rem; text-align: center; color: #222; font-weight: 700; border-left: 1px solid #ccc; font-size: 0.85rem;">النوع</th>
                            <th style="padding: 0.75rem; text-align: center; color: #222; font-weight: 700; border-left: 1px solid #ccc; font-size: 0.85rem;">الوصف</th>
                            <th style="padding: 0.75rem; text-align: center; color: #222; font-weight: 700; border-left: 1px solid #ccc; font-size: 0.85rem;">المدين</th>
                            <th style="padding: 0.75rem; text-align: center; color: #222; font-weight: 700; border-left: 1px solid #ccc; font-size: 0.85rem;">الدائن</th>
                            <th style="padding: 0.75rem; text-align: center; color: #222; font-weight: 700; border-left: 1px solid #ccc; font-size: 0.85rem;">الحالة</th>
                            <th style="padding: 0.75rem; text-align: center; color: #222; font-weight: 700; font-size: 0.85rem;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody style="color: #222;">
                        @foreach($entries as $entry)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 0.75rem; text-align: center; border-left: 1px solid #eee;">
                                <strong style="color: #2196f3;">{{ $entry->entry_no }}</strong>
                            </td>
                            <td style="padding: 0.75rem; text-align: center; border-left: 1px solid #eee; color: #222;">
                                {{ $entry->entry_date->format('Y-m-d') }}
                            </td>
                            <td style="padding: 0.75rem; text-align: center; border-left: 1px solid #eee;">
                                @if($entry->entry_type === 'receipt')
                                    <span style="color: #4caf50; font-weight: 600;">سند قبض</span>
                                @elseif($entry->entry_type === 'payment')
                                    <span style="color: #f44336; font-weight: 600;">سند صرف</span>
                                @elseif($entry->entry_type === 'adjusting')
                                    <span style="color: #ff9800; font-weight: 600;">قيد تسوية</span>
                                @else
                                    <span style="color: #2196f3; font-weight: 600;">قيد يومية</span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem; text-align: right; border-left: 1px solid #eee; color: #222;">
                                {{ Str::limit($entry->description, 50) }}
                            </td>
                            <td style="padding: 0.75rem; text-align: center; border-left: 1px solid #eee;">
                                <strong style="color: #2196f3;">{{ number_format($entry->total_debit, 2) }}</strong>
                            </td>
                            <td style="padding: 0.75rem; text-align: center; border-left: 1px solid #eee;">
                                <strong style="color: #f44336;">{{ number_format($entry->total_credit, 2) }}</strong>
                            </td>
                            <td style="padding: 0.75rem; text-align: center; border-left: 1px solid #eee;">
                                @if($entry->status === 'posted')
                                    <span style="color: #4caf50; font-weight: 600;">مرحل</span>
                                @elseif($entry->status === 'draft')
                                    <span style="color: #ff9800; font-weight: 600;">مسودة</span>
                                @else
                                    <span style="color: #666;">{{ $entry->status }}</span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem; text-align: center;">
                                <div style="display: flex; gap: 0.25rem; justify-content: center; align-items: center;">
                                    @if(in_array($entry->entry_type, ['receipt', 'payment']))
                                        <a href="{{ route('wesal.finance.journal-entries.print', $entry) }}" 
                                           style="padding: 0.4rem 0.6rem; background: #2196f3; color: white; border: none; border-radius: 4px; text-decoration: none; font-size: 0.75rem; font-weight: 500;"
                                           title="طباعة السند">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('wesal.finance.journal-entries.show', $entry) }}" 
                                       style="padding: 0.4rem 0.6rem; background: #6c757d; color: white; border: none; border-radius: 4px; text-decoration: none; font-size: 0.75rem; font-weight: 500;"
                                       title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
                {{ $entries->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 3rem; color: #999;">
                <i class="fas fa-file-invoice" style="font-size: 3rem; margin-bottom: 1rem; color: #ccc;"></i>
                <p style="font-size: 1rem; margin: 0;">لا توجد قيود</p>
            </div>
        @endif
    </div>
</div>
