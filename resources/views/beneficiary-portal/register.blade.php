@extends('beneficiary-portal.layout')

@section('title', 'تسجيل مستفيد جديد')

@section('content')
<div class="bp-header">
    <h1><i class="fas fa-user-plus" style="color: var(--bp-primary);"></i> تسجيل مستفيد جديد</h1>
    <p>
        @if(!empty($portalForm))
            النموذج: {{ $portalForm->name_ar }} — املأ البيانات لتقديم طلب التسجيل.
        @else
            املأ البيانات التالية لتقديم طلب التسجيل. سيتم مراجعة طلبك وإعلامك بالنتيجة.
        @endif
    </p>
</div>
<div class="bp-nav">
    <a href="{{ route('beneficiary-portal.index') }}"><i class="fas fa-home"></i> الرئيسية</a>
    <a href="{{ route('beneficiary-portal.login') }}"><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</a>
</div>
@push('styles')
<style>
    .bp-form-stack { display: flex; flex-direction: column; gap: 0; max-width: 100%; }
    .bp-form-stack .bp-form-field { padding: 1.1rem 0; border-bottom: 1px solid rgba(31, 107, 79, 0.12); }
    .bp-form-stack .bp-form-field:first-of-type { padding-top: 0; }
    .bp-form-stack .bp-form-field:nth-child(even) { background: rgba(31, 107, 79, 0.03); }
    .bp-form-stack .bp-form-field:last-of-type { border-bottom: none; }
    .bp-form-stack .bp-form-field .form-label { display: block; font-weight: 600; font-size: 0.95rem; margin-bottom: 0.5rem; color: var(--bp-primary); }
    .bp-form-stack .bp-form-field .form-control { width: 100%; max-width: 100%; box-sizing: border-box; color: #1a1a1a !important; background: #fff !important; border: 1px solid rgba(31, 107, 79, 0.25); border-radius: 8px; transition: border-color 0.2s, box-shadow 0.2s; }
    .bp-form-stack .bp-form-field .form-control::placeholder { color: #6b7280 !important; }
    .bp-form-stack .bp-form-field .form-control:focus { border-color: var(--bp-primary); box-shadow: 0 0 0 3px rgba(31, 107, 79, 0.15); }
    .bp-form-stack .bp-form-field select.form-control option { color: #1a1a1a; background: #fff; }
    .bp-form-stack .bp-form-field .form-help { margin: 0 0 0.4rem 0; font-size: 0.85rem; color: #4b5563 !important; line-height: 1.4; }
    .bp-form-stack .bp-form-field-actions { padding-top: 1.5rem; margin-top: 0.5rem; border-top: 2px solid rgba(31, 107, 79, 0.15); background: rgba(31, 107, 79, 0.04); margin-left: -2rem; margin-right: -2rem; margin-bottom: -2rem; padding: 1.5rem 2rem 2rem; }
    .bp-form-stack .bp-form-radio label, .bp-form-stack .bp-form-checkbox label { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.35rem 0; color: #1a1a1a !important; }
    .bp-form-stack .bp-form-radio label span, .bp-form-stack .bp-form-checkbox label span { color: #1a1a1a !important; }
    .bp-form-stack .bp-form-radio input:checked + span, .bp-form-stack .bp-form-checkbox input:checked + span { color: var(--bp-primary) !important; font-weight: 600; }
    .bp-form-stack .bp-form-radio, .bp-form-stack .bp-form-checkbox { display: flex; flex-direction: column; gap: 0.15rem; }
    .bp-form-stack .bp-form-field small { color: #4b5563 !important; }
    @media (max-width: 576px) { .bp-form-stack .bp-form-field { padding: 0.9rem 0; } .bp-form-stack .bp-form-field-actions { margin-left: -1rem; margin-right: -1rem; margin-bottom: -1rem; padding: 1rem; } }
</style>
@endpush
<form method="POST" action="{{ route('beneficiary-portal.register.store') }}" class="bp-form bp-form-stack" enctype="multipart/form-data">
    @csrf
    @if(!empty($portalForm))
        <input type="hidden" name="beneficiary_form_id" value="{{ $portalForm->id }}">
    @endif
    @if(!empty($portalForm) && $portalForm->fields->isNotEmpty())
        @foreach($portalForm->fields as $field)
            @php
                $isCond = !empty($field->depends_on_field_id) && $field->relationLoaded('dependsOnField') && $field->dependsOnField;
                $depKey = $isCond ? $field->dependsOnField->field_key : '';
                $depVal = $isCond ? $field->depends_on_value : '';
            @endphp
            <div class="bp-form-field form-group @if($isCond) bp-form-conditional @endif" @if($isCond) data-conditional data-depends-on="{{ $depKey }}" data-depends-value="{{ $depVal }}" style="display: none;" @endif>
                <label class="form-label">{{ $field->label_ar }} @if($field->is_required)<span style="color:#dc3545">*</span>@endif</label>
                @if(!empty($field->help_text))
                    <p class="form-help">{{ $field->help_text }}</p>
                @endif
                @if($field->field_type === 'textarea')
                    <textarea name="{{ $field->field_key }}" class="form-control" rows="3" @if($field->is_required) required @endif>{{ old($field->field_key, is_array(old($field->field_key)) ? '' : '') }}</textarea>
                @elseif($field->field_type === 'select')
                    <select name="{{ $field->field_key }}" class="form-control" @if($field->is_required) required @endif>
                        <option value="">-- اختر --</option>
                        @foreach($field->options ?? [] as $opt)
                            <option value="{{ $opt }}" {{ old($field->field_key) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                @elseif($field->field_type === 'radio')
                    <div class="bp-radio-group bp-form-radio">
                        @foreach($field->options ?? [] as $opt)
                            <label>
                                <input type="radio" name="{{ $field->field_key }}" value="{{ $opt }}" {{ old($field->field_key) === $opt ? 'checked' : '' }} @if($field->is_required) required @endif>
                                <span>{{ $opt }}</span>
                            </label>
                        @endforeach
                    </div>
                @elseif($field->field_type === 'multiselect')
                    <div class="bp-checkbox-group bp-form-checkbox">
                        @foreach($field->options ?? [] as $opt)
                            <label>
                                <input type="checkbox" name="{{ $field->field_key }}[]" value="{{ $opt }}" {{ in_array($opt, old($field->field_key, [])) ? 'checked' : '' }}>
                                <span>{{ $opt }}</span>
                            </label>
                        @endforeach
                    </div>
                @elseif($field->field_type === 'file')
                    <input type="file" name="{{ $field->field_key }}" class="form-control" accept="{{ $field->file_accept }}"
                        @if($field->is_required) required @endif
                        data-max-mb="{{ $field->file_max_mb }}">
                    <small style="color: #666; font-size: 0.85rem;">الحد الأقصى: {{ $field->file_max_mb }} ميجابايت</small>
                @elseif($field->field_type === 'email')
                    <input type="email" name="{{ $field->field_key }}" class="form-control" value="{{ old($field->field_key) }}" @if($field->is_required) required @endif>
                @elseif($field->field_type === 'number')
                    <input type="number" name="{{ $field->field_key }}" class="form-control" value="{{ old($field->field_key) }}" @if($field->is_required) required @endif>
                @elseif($field->field_type === 'date')
                    @include('partials.beneficiary-form-date-input', ['field' => $field])
                @else
                    <input type="text" name="{{ $field->field_key }}" class="form-control" value="{{ old($field->field_key) }}" @if($field->is_required) required @endif>
                @endif
            </div>
        @endforeach
    @else
        <div class="bp-form-field form-group">
            <label class="form-label">الاسم بالعربي <span style="color:#dc3545">*</span></label>
            <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}" required>
        </div>
        <div class="bp-form-field form-group">
            <label class="form-label">الاسم بالإنجليزي</label>
            <input type="text" name="name_en" class="form-control" value="{{ old('name_en') }}">
        </div>
        <div class="bp-form-field form-group">
            <label class="form-label">رقم الهوية</label>
            <input type="text" name="national_id" class="form-control" value="{{ old('national_id') }}">
        </div>
        <div class="bp-form-field form-group">
            <label class="form-label">البريد الإلكتروني <span style="color:#dc3545">*</span></label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="bp-form-field form-group">
            <label class="form-label">الجوال</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
        </div>
        <div class="bp-form-field form-group">
            <label class="form-label">العنوان</label>
            <input type="text" name="address" class="form-control" value="{{ old('address') }}">
        </div>
        <div class="bp-form-field form-group">
            <label class="form-label">تاريخ الميلاد</label>
            <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}">
        </div>
        <div class="bp-form-field form-group">
            <label class="form-label">الجنس</label>
            <select name="gender" class="form-control">
                <option value="">-- اختر --</option>
                <option value="male" {{ old('gender')=='male'?'selected':'' }}>ذكر</option>
                <option value="female" {{ old('gender')=='female'?'selected':'' }}>أنثى</option>
            </select>
        </div>
        <div class="bp-form-field form-group">
            <label class="form-label">ملاحظات</label>
            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
        </div>
    @endif
    <div class="bp-form-field form-group">
        <label class="form-label">كلمة المرور <span style="color:#dc3545">*</span></label>
        <input type="password" name="password" class="form-control" required minlength="8">
    </div>
    <div class="bp-form-field form-group">
        <label class="form-label">تأكيد كلمة المرور <span style="color:#dc3545">*</span></label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>
    @if(!empty($portalForm) && $portalForm->fields->isNotEmpty() && !$portalForm->fields->contains('field_key', 'email'))
        <div class="bp-form-field form-group">
            <label class="form-label">البريد الإلكتروني <span style="color:#dc3545">*</span></label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
    @endif
    <div class="bp-form-field bp-form-field-actions form-group">
        <button type="submit" class="bp-btn bp-btn-primary"><i class="fas fa-paper-plane"></i> تقديم الطلب</button>
    </div>
</form>
@if(!empty($portalForm) && $portalForm->fields->isNotEmpty())
<script>
(function(){
    var form = document.querySelector('.bp-form');
    if (!form) return;
    function getFieldValue(name) {
        var sel = form.querySelector('select[name="' + name + '"]');
        if (sel) return sel.value || '';
        var radios = form.querySelectorAll('input[type="radio"][name="' + name + '"]');
        for (var i = 0; i < radios.length; i++) if (radios[i].checked) return radios[i].value || '';
        var inp = form.querySelector('input[name="' + name + '"]');
        return inp ? (inp.value || '') : '';
    }
    function updateConditional() {
        form.querySelectorAll('[data-conditional]').forEach(function(div) {
            var depKey = div.getAttribute('data-depends-on');
            var depVal = div.getAttribute('data-depends-value') || '';
            var current = getFieldValue(depKey);
            var show = (current === depVal);
            div.style.display = show ? 'block' : 'none';
            div.querySelectorAll('input, select, textarea').forEach(function(el) {
                if (show) {
                    if (div.hasAttribute('data-was-required')) el.setAttribute('required', 'required');
                } else {
                    if (el.hasAttribute('required')) { div.setAttribute('data-was-required', '1'); el.removeAttribute('required'); }
                    if (el.type !== 'hidden') { if (el.type === 'checkbox' || el.type === 'radio') el.checked = false; else el.value = ''; }
                }
            });
        });
    }
    form.addEventListener('change', updateConditional);
    form.addEventListener('input', updateConditional);
    updateConditional();
})();
</script>
@endif
@endsection
