@php $b = $editBeneficiary ?? null; @endphp
@if(!$b)
    <div class="content-card">
        <div class="alert alert-error">المستفيد غير موجود.</div>
        <a href="{{ route('wesal.beneficiaries.show', ['section' => 'list']) }}" class="btn btn-secondary">العودة للقائمة</a>
    </div>
@else
@php
    $editFormModel = $b->beneficiaryForm;
    $fd = is_array($b->form_data) ? $b->form_data : [];
    $benFieldValue = function ($field) use ($b, $fd) {
        if ($field->isStandardKey()) {
            $v = $b->{$field->field_key};
            if ($field->field_key === 'birth_date' && $v) {
                return $v instanceof \Carbon\Carbon ? $v->format('Y-m-d') : $v;
            }

            return $v;
        }
        $raw = $fd[$field->field_key] ?? null;
        if ($field->field_type === 'multiselect') {
            return is_array($raw) ? $raw : (($raw !== null && $raw !== '') ? [(string) $raw] : []);
        }

        return $raw ?? '';
    };
@endphp
<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-edit" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            تعديل مستفيد: {{ $b->displayNameForPortal() }}
        </h1>
        <p class="page-subtitle">
            @if($editFormModel && $editFormModel->fields->isNotEmpty())
                النموذج: {{ $editFormModel->name_ar }} — يمكن تحديث الحقول بما فيها أي حقول أُضيفت لاحقاً
            @else
                تحديث بيانات المستفيد
            @endif
        </p>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            @foreach($errors->all() as $err) <p style="margin: 0;">{{ $err }}</p> @endforeach
        </div>
    @endif

    <style>
        .ben-form-stack { display: flex; flex-direction: column; gap: 0; max-width: 100%; }
        .ben-form-wrap { background: #fff; border-radius: 14px; margin-bottom: 2rem; overflow: hidden; box-shadow: 0 4px 20px rgba(31, 107, 79, 0.12), 0 0 0 1px rgba(95, 179, 142, 0.2); border: 1px solid rgba(95, 179, 142, 0.25); }
        .ben-form-wrap .ben-form-title { margin: 0; padding: 1rem 1.5rem; font-size: 1.05rem; font-weight: 700; background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%); color: #fff; display: flex; align-items: center; gap: 0.6rem; border-bottom: 3px solid var(--primary-color); }
        .ben-form-wrap .ben-form-title i { opacity: 0.95; font-size: 1rem; }
        .ben-form-field { padding: 1rem 1.5rem; border-bottom: 1px solid rgba(31, 107, 79, 0.08); background: #fff; transition: background 0.2s ease, border-right-color 0.2s ease; border-right: 3px solid transparent; }
        .ben-form-field:nth-child(even) { background: rgba(95, 179, 142, 0.03); }
        .ben-form-field:last-of-type { border-bottom: none; }
        .ben-form-field:hover { background: rgba(95, 179, 142, 0.06); border-right-color: rgba(95, 179, 142, 0.35); }
        .ben-form-field .form-label { display: block; font-weight: 600; font-size: 0.925rem; margin-bottom: 0.45rem; color: var(--primary-dark); }
        .ben-form-wrap .ben-form-field .form-control { width: 100%; max-width: 100%; box-sizing: border-box; color: #1a1a1a !important; background: #fff !important; border: 1px solid rgba(31, 107, 79, 0.2); border-radius: 8px; padding: 0.6rem 0.85rem; transition: border-color 0.2s, box-shadow 0.2s; }
        .ben-form-wrap .ben-form-field .form-control:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(95, 179, 142, 0.2); }
        .ben-form-wrap .ben-form-field textarea.form-control { color: #1a1a1a !important; background: #fff !important; }
        .ben-form-wrap .ben-form-field select.form-control { color: #1a1a1a !important; background: #fff !important; }
        .ben-form-field .form-help { margin: 0 0 0.35rem 0; font-size: 0.825rem; color: #4b5563 !important; line-height: 1.45; }
        .ben-form-field-actions { padding: 1.25rem 1.5rem; border-top: 2px solid rgba(95, 179, 142, 0.2); background: linear-gradient(180deg, rgba(95, 179, 142, 0.06) 0%, rgba(31, 107, 79, 0.04) 100%); display: flex; gap: 0.75rem; flex-wrap: wrap; }
        .ben-form-wrap .ben-form-radio label, .ben-form-wrap .ben-form-checkbox label { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.4rem 0; color: #1a1a1a !important; }
        .ben-form-radio, .ben-form-checkbox { display: flex; flex-direction: column; gap: 0.25rem; }
        .ben-form-wrap .ben-form-field small { color: #4b5563 !important; font-size: 0.825rem; }
        .ben-form-field .form-label span { color: #c62828; }
        @media (max-width: 576px) { .ben-form-field { padding: 0.875rem 1rem; } .ben-form-field-actions { padding: 1rem; } .ben-form-wrap .ben-form-title { padding: 0.875rem 1rem; font-size: 1rem; } }
    </style>
    <div class="ben-form-wrap">
        <h3 class="ben-form-title"><i class="fas fa-user-edit"></i> بيانات المستفيد</h3>
        <form id="beneficiary-edit-form" method="POST" action="{{ route('wesal.beneficiaries.beneficiaries.update', $b) }}" enctype="multipart/form-data" class="ben-form-stack">
            @csrf @method('PUT')
            <div class="ben-form-field">
                <label class="form-label">رقم المستفيد <span style="color: #ff8a80;">*</span></label>
                <input type="text" name="beneficiary_no" class="form-control" value="{{ old('beneficiary_no', $b->beneficiary_no) }}" required>
            </div>
            <div class="ben-form-field">
                <label class="form-label">الحالة <span style="color: #ff8a80;">*</span></label>
                <select name="status" class="form-control" required>
                    <option value="active" {{ old('status', $b->status) === 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="archived" {{ old('status', $b->status) === 'archived' ? 'selected' : '' }}>مؤرشف</option>
                </select>
            </div>
            @if($editFormModel && $editFormModel->fields->isNotEmpty())
                @foreach($editFormModel->fields as $field)
                    @php
                        $isCond = !empty($field->depends_on_field_id) && $field->relationLoaded('dependsOnField') && $field->dependsOnField;
                        $depKey = $isCond ? $field->dependsOnField->field_key : '';
                        $depVal = $isCond ? $field->depends_on_value : '';
                        $cur = old($field->field_key, $benFieldValue($field));
                        if ($field->field_type === 'multiselect' && ! is_array($cur)) {
                            $cur = [];
                        }
                    @endphp
                    <div class="ben-form-field @if($isCond) ben-form-conditional @endif" @if($isCond) data-conditional data-depends-on="{{ $depKey }}" data-depends-value="{{ $depVal }}" style="display: none;" @endif>
                        <label class="form-label">{{ $field->label_ar }} @if($field->is_required)<span style="color: #ff8a80;">*</span>@endif</label>
                        @if(!empty($field->help_text))
                            <p class="form-help">{{ $field->help_text }}</p>
                        @endif
                        @if($field->field_type === 'textarea')
                            <textarea name="{{ $field->field_key }}" class="form-control" rows="3" @if($field->is_required) required @endif>{{ is_scalar($cur) ? $cur : '' }}</textarea>
                        @elseif($field->field_type === 'select')
                            <select name="{{ $field->field_key }}" class="form-control" @if($field->is_required) required @endif>
                                <option value="">-- اختر --</option>
                                @foreach($field->options ?? [] as $opt)
                                    <option value="{{ $opt }}" {{ (string) $cur === (string) $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        @elseif($field->field_type === 'radio')
                            <div class="ben-form-radio">
                                @foreach($field->options ?? [] as $opt)
                                    <label>
                                        <input type="radio" name="{{ $field->field_key }}" value="{{ $opt }}" {{ (string) $cur === (string) $opt ? 'checked' : '' }} @if($field->is_required) required @endif>
                                        <span>{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @elseif($field->field_type === 'multiselect')
                            <div class="ben-form-checkbox">
                                @foreach($field->options ?? [] as $opt)
                                    <label>
                                        <input type="checkbox" name="{{ $field->field_key }}[]" value="{{ $opt }}" {{ in_array($opt, $cur, true) ? 'checked' : '' }}>
                                        <span>{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @elseif($field->field_type === 'file')
                            @php
                                $storedPath = $field->isStandardKey() ? null : ($fd[$field->field_key] ?? null);
                                if ($field->isStandardKey()) {
                                    $storedPath = null;
                                }
                            @endphp
                            @if(!empty($storedPath) && is_string($storedPath))
                                <p style="margin: 0 0 0.5rem 0; font-size: 0.9rem;"><a href="{{ asset($storedPath) }}" target="_blank" rel="noopener">المرفق الحالي</a> — اختر ملفاً جديداً للاستبدال @if($field->is_required) (مطلوب إن لم يكن هناك مرفق) @endif</p>
                            @endif
                            <input type="file" name="{{ $field->field_key }}" class="form-control" accept="{{ $field->file_accept }}" @if($field->is_required && empty($storedPath)) required @endif>
                            <small>الحد الأقصى: {{ $field->file_max_mb }} ميجابايت</small>
                        @elseif($field->field_type === 'email')
                            <input type="email" name="{{ $field->field_key }}" class="form-control" value="{{ is_scalar($cur) ? $cur : '' }}" @if($field->is_required) required @endif>
                        @elseif($field->field_type === 'number')
                            <input type="number" name="{{ $field->field_key }}" class="form-control" value="{{ is_scalar($cur) ? $cur : '' }}" @if($field->is_required) required @endif>
                        @elseif($field->field_type === 'date')
                            @include('partials.beneficiary-form-date-input', ['field' => $field, 'initial' => is_scalar($cur) ? $cur : ''])
                        @else
                            <input type="text" name="{{ $field->field_key }}" class="form-control" value="{{ is_scalar($cur) ? $cur : '' }}" @if($field->is_required) required @endif>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="ben-form-field">
                    <label class="form-label">الاسم بالعربي <span style="color: #ff8a80;">*</span></label>
                    <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $b->name_ar) }}" required>
                </div>
                <div class="ben-form-field">
                    <label class="form-label">الاسم بالإنجليزي</label>
                    <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $b->name_en) }}">
                </div>
                <div class="ben-form-field">
                    <label class="form-label">رقم الهوية</label>
                    <input type="text" name="national_id" class="form-control" value="{{ old('national_id', $b->national_id) }}">
                </div>
                <div class="ben-form-field">
                    <label class="form-label">الجوال</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $b->phone) }}">
                </div>
                <div class="ben-form-field">
                    <label class="form-label">البريد</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $b->email) }}">
                </div>
                <div class="ben-form-field">
                    <label class="form-label">العنوان</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $b->address) }}">
                </div>
                <div class="ben-form-field">
                    <label class="form-label">تاريخ الميلاد</label>
                    <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $b->birth_date?->format('Y-m-d')) }}">
                </div>
                <div class="ben-form-field">
                    <label class="form-label">الجنس</label>
                    <select name="gender" class="form-control">
                        <option value="">-- اختر --</option>
                        <option value="male" {{ old('gender', $b->gender) === 'male' ? 'selected' : '' }}>ذكر</option>
                        <option value="female" {{ old('gender', $b->gender) === 'female' ? 'selected' : '' }}>أنثى</option>
                    </select>
                </div>
                <div class="ben-form-field">
                    <label class="form-label">ملاحظات</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $b->notes) }}</textarea>
                </div>
            @endif
            <div class="ben-form-field ben-form-field-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ
                </button>
                <a href="{{ route('wesal.beneficiaries.show', ['section' => 'list']) }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
    @if($editFormModel && $editFormModel->fields->isNotEmpty())
    <script>
    (function(){
        var form = document.getElementById('beneficiary-edit-form');
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
</div>
@endif
