<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-sitemap" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            الأقسام
        </h1>
        <p class="page-subtitle">إدارة الأقسام والإدارات</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    @can('hr.departments.create')
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">إضافة قسم</h3>
        <form method="POST" action="{{ route('wesal.hr.departments.store') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; align-items: end;">
            @csrf
            <div><label class="form-label">الكود <span style="color: #ff8a80;">*</span></label><input type="text" name="code" class="form-control" value="{{ old('code') }}" required></div>
            <div><label class="form-label">الاسم بالعربي <span style="color: #ff8a80;">*</span></label><input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}" required></div>
            <div><label class="form-label">القسم الأب</label><select name="parent_id" class="form-control"><option value="">-- لا يوجد --</option>@foreach($departments ?? [] as $d)<option value="{{ $d->id }}" {{ old('parent_id') == $d->id ? 'selected' : '' }}>{{ $d->name_ar }}</option>@endforeach</select></div>
            <div><label class="form-label">الترتيب</label><input type="number" name="order" class="form-control" value="{{ old('order', 0) }}" min="0"></div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة</button></div>
        </form>
    </div>
    @endcan

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">قائمة الأقسام</h3>
        @if(isset($departments) && $departments->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead>
                        <tr>
                            <th>الكود</th>
                            <th>الاسم</th>
                            <th>الأب</th>
                            <th style="text-align: center;">عدد الموظفين</th>
                            <th style="text-align: center;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $d)
                        <tr>
                            <td><strong>{{ $d->code }}</strong></td>
                            <td>{{ $d->name_ar }}</td>
                            <td>{{ $d->parent?->name_ar ?? '-' }}</td>
                            <td style="text-align: center;">{{ $d->employees_count ?? 0 }}</td>
                            <td style="text-align: center;">
                                @can('hr.departments.delete')
                                <form method="POST" action="{{ route('wesal.hr.departments.destroy', $d) }}" style="display: inline;" onsubmit="return confirm('حذف القسم؟');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;"><i class="fas fa-trash"></i></button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد أقسام.</p>
        @endif
    </div>
</div>
