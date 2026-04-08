@php $d = $editDonor ?? null; @endphp
@if(!$d)
<div class="content-card"><div class="alert alert-error">الجهة غير موجودة.</div><a href="{{ route('wesal.programs-projects.show', ['section' => 'donors', 'sub' => 'list']) }}" class="btn btn-secondary">العودة</a></div>
@else
<div class="content-card">
    <div class="page-header"><h1 class="page-title"><i class="fas fa-edit" style="color: var(--primary-color);"></i> تعديل: {{ $d->name_ar }}</h1></div>
    @if($errors->any())<div class="alert alert-error">@foreach($errors->all() as $e)<p style="margin:0;">{{ $e }}</p>@endforeach</div>@endif
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
        <form method="POST" action="{{ route('wesal.programs-projects.donors.update', $d) }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            @csrf @method('PUT')
            <div><label class="form-label">الاسم بالعربي <span style="color:#dc3545">*</span></label><input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $d->name_ar) }}" required></div>
            <div><label class="form-label">الاسم بالإنجليزي</label><input type="text" name="name_en" class="form-control" value="{{ old('name_en', $d->name_en) }}"></div>
            <div style="grid-column: 1 / -1;">
                <label class="form-label"><i class="fas fa-user-lock" style="color: var(--primary-color); margin-left: 0.35rem;"></i> حساب الدخول للجهة المانحة</label>
                <select name="user_id" class="form-control" style="max-width: 400px;">
                    <option value="">— لا يوجد حساب —</option>
                    @foreach($usersForDonor ?? [] as $u)
                        <option value="{{ $u->id }}" {{ old('user_id', $d->user_id) == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
                <small style="color: var(--text-secondary); font-size: 0.8rem; display: block; margin-top: 0.25rem;">اختر مستخدمًا لتمكين الجهة المانحة من الدخول ومتابعة مشاريعها. يجب تعيين دور «جهة مانحة» للمستخدم.</small>
            </div>
            <div><label class="form-label">جهة الاتصال</label><input type="text" name="contact_name" class="form-control" value="{{ old('contact_name', $d->contact_name) }}"></div>
            <div><label class="form-label">الجوال</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $d->phone) }}"></div>
            <div><label class="form-label">البريد</label><input type="email" name="email" class="form-control" value="{{ old('email', $d->email) }}"></div>
            <div><label class="form-label">العنوان</label><input type="text" name="address" class="form-control" value="{{ old('address', $d->address) }}"></div>
            <div style="grid-column: 1 / -1;"><label class="form-label">ملاحظات</label><textarea name="notes" class="form-control" rows="1">{{ old('notes', $d->notes) }}</textarea></div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button><a href="{{ route('wesal.programs-projects.show', ['section' => 'donors', 'sub' => 'list']) }}" class="btn btn-secondary">إلغاء</a></div>
        </form>
    </div>
</div>
@endif
