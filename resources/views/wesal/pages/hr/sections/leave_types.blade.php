<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-umbrella-beach" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            أنواع الإجازات
        </h1>
        <p class="page-subtitle">تحديد أنواع الإجازات وعدد أيامها السنوية</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> @foreach($errors->all() as $e)<p style="margin: 0;">{{ $e }}</p>@endforeach</div>
    @endif

    @php $edit = $editLeaveType ?? null; @endphp
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">{{ $edit ? 'تعديل نوع إجازة' : 'إضافة نوع إجازة' }}</h3>
        <form method="POST" action="{{ $edit ? route('wesal.hr.leave-types.update', $edit) : route('wesal.hr.leave-types.store') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; align-items: end;">
            @csrf
            @if($edit) @method('PUT') @endif
            <div>
                <label class="form-label">الاسم بالعربي <span style="color: #ff8a80;">*</span></label>
                <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $edit?->name_ar) }}" required>
            </div>
            <div>
                <label class="form-label">الكود <span style="color: #ff8a80;">*</span></label>
                <input type="text" name="code" class="form-control" value="{{ old('code', $edit?->code) }}" placeholder="ANNUAL" required maxlength="30" {{ $edit ? 'readonly' : '' }} style="{{ $edit ? 'background: rgba(255,255,255,0.06);' : '' }}">
                @if($edit)<small style="color: var(--text-secondary);">لا يُعدّل الكود</small>@endif
            </div>
            <div>
                <label class="form-label">أيام/سنة <span style="color: #ff8a80;">*</span></label>
                <input type="number" name="days_per_year" class="form-control" value="{{ old('days_per_year', $edit?->days_per_year ?? 21) }}" min="0" max="365" required>
            </div>
            <div>
                <label class="form-label">مدفوعة</label>
                <select name="is_paid" class="form-control">
                    <option value="1" {{ old('is_paid', $edit?->is_paid ?? true) ? 'selected' : '' }}>نعم</option>
                    <option value="0" {{ !old('is_paid', $edit?->is_paid ?? true) ? 'selected' : '' }}>لا</option>
                </select>
            </div>
            <div>
                <label class="form-label">نشط</label>
                <select name="is_active" class="form-control">
                    <option value="1" {{ old('is_active', $edit?->is_active ?? true) ? 'selected' : '' }}>نعم</option>
                    <option value="0" {{ !old('is_active', $edit?->is_active ?? true) ? 'selected' : '' }}>لا</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-{{ $edit ? 'save' : 'plus' }}"></i> {{ $edit ? 'تحديث' : 'إضافة' }}</button>
                @if($edit)
                    <a href="{{ route('wesal.hr.show', ['section' => 'leave-types']) }}" class="btn" style="background: rgba(255,255,255,0.1); color: var(--text-primary); margin-right: 0.5rem;">إلغاء</a>
                @endif
            </div>
        </form>
    </div>

    @if(isset($leaveTypes) && $leaveTypes->count() > 0)
        <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
            <h3 style="color: var(--text-primary); margin-bottom: 1rem;">قائمة أنواع الإجازات</h3>
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead><tr><th>الاسم</th><th>الكود</th><th style="text-align: center;">أيام/سنة</th><th style="text-align: center;">مدفوعة</th><th style="text-align: center;">نشط</th><th style="text-align: center;">الإجراءات</th></tr></thead>
                    <tbody>
                        @foreach($leaveTypes as $lt)
                        <tr>
                            <td>{{ $lt->name_ar }}</td>
                            <td>{{ $lt->code }}</td>
                            <td style="text-align: center;">{{ $lt->days_per_year }}</td>
                            <td style="text-align: center;">{{ $lt->is_paid ? 'نعم' : 'لا' }}</td>
                            <td style="text-align: center;">{{ $lt->is_active ? 'نعم' : 'لا' }}</td>
                            <td style="text-align: center;">
                                <a href="{{ route('wesal.hr.show', ['section' => 'leave-types', 'edit' => $lt->id]) }}" class="btn" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; background: rgba(255,255,255,0.1); color: var(--text-primary); margin-left: 0.25rem;"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('wesal.hr.leave-types.destroy', $lt) }}" style="display: inline;" onsubmit="return confirm('حذف نوع الإجازة؟');">@csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد أنواع إجازات. أضف نوعاً من النموذج أعلاه.</p>
    @endif
</div>
