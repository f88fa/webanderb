<div class="content-card">
    <div class="page-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-edit" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                تعديل النموذج: {{ $editForm->name_ar }}
            </h1>
            <p class="page-subtitle">إضافة أو تعديل حقول النموذج</p>
        </div>
        <a href="{{ route('wesal.beneficiaries.forms.index') }}" class="btn btn-secondary"><i class="fas fa-list"></i> قائمة النماذج</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error">@foreach($errors->all() as $e)<p style="margin: 0;">{{ $e }}</p>@endforeach</div>
    @endif

    @if(($formBeneficiariesCount ?? 0) > 0)
        <div class="alert alert-success" style="background: rgba(95,179,142,0.12); border: 1px solid rgba(95,179,142,0.45); color: var(--primary-dark);">
            <i class="fas fa-info-circle"></i>
            هذا النموذج مرتبط بـ <strong>{{ $formBeneficiariesCount }}</strong> مستفيد/مستفيدين — لا يمكن حذف النموذج، ويمكنك <strong>إضافة حقول جديدة</strong>؛ ستظهر للمستفيدين السابقين عند <strong>تعديل بياناتهم</strong> لتعبئة الحقول الناقصة.
        </div>
    @endif

    @php
        $allFieldTypesForAdd = \App\Models\Beneficiary\BeneficiaryFormField::typesForAddFieldDropdown();
        $conditionalSourceFields = $editForm->fields->filter(fn($f) => in_array($f->field_type, ['select', 'radio']));
        $conditionalOptionsMap = $conditionalSourceFields->mapWithKeys(function($f) {
            $opts = is_array($f->options) ? $f->options : [];
            return [(string)$f->id => $opts];
        })->all();
    @endphp

    <style>
        .form-edit-card { background: #fff; border-radius: 14px; margin-bottom: 1.5rem; overflow: hidden; box-shadow: 0 4px 20px rgba(31,107,79,0.12); border: 1px solid rgba(95,179,142,0.25); }
        .form-edit-card .card-head { margin: 0; padding: 1rem 1.5rem; font-size: 1rem; font-weight: 700; background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%); color: #fff; display: flex; align-items: center; gap: 0.5rem; }
        .form-edit-card .card-body { padding: 1.5rem; }
        .form-edit-card .form-label { color: #1a1a1a !important; font-weight: 600; margin-bottom: 0.35rem; }
        .form-edit-card .form-control { color: #1a1a1a !important; background: #fff !important; border: 1px solid rgba(31,107,79,0.2); border-radius: 8px; }
        .form-edit-card .form-control::placeholder { color: #6b7280 !important; }
        .form-edit-card .conditional-box { background: rgba(95,179,142,0.08); border: 1px solid rgba(95,179,142,0.3); border-radius: 10px; padding: 1rem 1.25rem; margin-top: 1.25rem; }
        .form-edit-card .conditional-box .conditional-note { font-size: 0.8rem; color: #1f6b4f; margin-bottom: 0.75rem; line-height: 1.4; }
        .fields-table-wrap { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 20px rgba(31,107,79,0.1); border: 1px solid rgba(95,179,142,0.2); }
        .fields-table-wrap .table-head { padding: 1rem 1.25rem; font-size: 1rem; font-weight: 700; background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%); color: #fff; }
        .fields-table-wrap table { width: 100%; border-collapse: collapse; direction: rtl; }
        .fields-table-wrap th { padding: 0.75rem 1rem; text-align: right; background: rgba(31,107,79,0.08); color: var(--primary-dark); font-weight: 600; font-size: 0.9rem; border-bottom: 2px solid rgba(95,179,142,0.3); }
        .fields-table-wrap td { padding: 0.75rem 1rem; border-bottom: 1px solid rgba(0,0,0,0.06); color: #1a1a1a; }
        .fields-table-wrap tbody tr:hover { background: rgba(95,179,142,0.04); }
    </style>

    {{-- تعديل اسم النموذج --}}
    <div class="form-edit-card">
        <h3 class="card-head"><i class="fas fa-pen"></i> تعديل اسم النموذج</h3>
        <div class="card-body">
            <form method="POST" action="{{ route('wesal.beneficiaries.forms.update', $editForm) }}" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: end;">
                @csrf
                @method('PUT')
                <div>
                    <label class="form-label">اسم النموذج</label>
                    <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $editForm->name_ar) }}" required style="min-width: 220px;">
                </div>
                <div>
                    <label class="form-label">المعرّف (slug)</label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug', $editForm->slug) }}" required style="min-width: 140px;">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button>
            </form>
        </div>
    </div>

    {{-- إضافة حقل جديد --}}
    <div class="form-edit-card">
        <h3 class="card-head"><i class="fas fa-plus"></i> إضافة حقل</h3>
        <div class="card-body">
        <form method="POST" action="{{ route('wesal.beneficiaries.forms.fields.store', $editForm) }}" id="add-field-form">
            @csrf
            <input type="hidden" name="field_key" id="field-key-auto" value="">
            <div style="margin-bottom: 1.25rem;">
                <label class="form-label">نوع الحقل <span style="color: #c62828;">*</span></label>
                <select name="field_type" id="new-field-type" class="form-control" style="max-width: 320px;">
                    @foreach($allFieldTypesForAdd as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label class="form-label">نص السؤال <span style="color: #c62828;">*</span></label>
                <input type="text" name="label_ar" id="label-ar-input" class="form-control" required placeholder="اكتب سؤال الحقل هنا" style="max-width: 100%;">
            </div>

            <div id="wrap-for-select" class="field-type-wrap" style="display: none;">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label">الخيارات — اضغط «إضافة خيار» لكل خيار</label>
                    <div id="options-container" style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 0.5rem;"></div>
                    <button type="button" id="btn-add-option" class="btn btn-primary btn-sm" style="border-radius: 8px;"><i class="fas fa-plus"></i> إضافة خيار</button>
                </div>
            </div>
            <div id="wrap-for-multiselect" class="field-type-wrap" style="display: none;">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label">الخيارات — اضغط «إضافة خيار» لكل خيار</label>
                    <div id="options-container-multi" style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 0.5rem;"></div>
                    <button type="button" id="btn-add-option-multi" class="btn btn-primary btn-sm" style="border-radius: 8px;"><i class="fas fa-plus"></i> إضافة خيار</button>
                </div>
            </div>
            <div id="wrap-for-file" class="field-type-wrap" style="display: none;">
                <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <label class="form-label">الملفات المقبولة</label>
                        <input type="text" name="file_accept" class="form-control" value="image/*,.pdf,.doc,.docx" style="max-width: 220px;">
                    </div>
                    <div>
                        <label class="form-label">الحد الأقصى (ميجابايت)</label>
                        <input type="number" name="file_max_mb" class="form-control" value="5" min="1" max="50" style="width: 100px;">
                    </div>
                </div>
            </div>

            <div id="wrap-for-date" class="field-type-wrap" style="display: none;">
                <p class="form-label" style="margin-bottom: 0.5rem;">نوع التقويم لحقل التاريخ</p>
                <div style="display: flex; flex-wrap: wrap; gap: 1.25rem; align-items: center;">
                    <label style="display: flex; align-items: center; gap: 0.45rem; cursor: pointer; color: #1a1a1a;">
                        <input type="radio" name="date_calendar" value="gregorian" checked> ميلادي (منتقي تاريخ المتصفح)
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.45rem; cursor: pointer; color: #1a1a1a;">
                        <input type="radio" name="date_calendar" value="hijri"> هجري (إدخال يدوي بصيغة سنة-شهر-يوم)
                    </label>
                </div>
            </div>

            <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid rgba(31,107,79,0.12);">
                <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; color: #1a1a1a !important;">
                    <input type="checkbox" name="is_required" value="1"> مطلوب
                </label>
            </div>

            <div class="conditional-box">
                <p class="conditional-note"><i class="fas fa-info-circle"></i> السؤال الشرطي مربوط بحقل «خيار واحد» فقط. اختر الحقل ثم الخيار الذي يظهر عنده هذا السؤال.</p>
                <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: end;">
                    <div>
                        <label class="form-label">يظهر فقط عندما (حقل خيار واحد)</label>
                        <select id="depends-on-field-id" name="depends_on_field_id" class="form-control" style="min-width: 200px;">
                            <option value="">— يظهر دائماً —</option>
                            @foreach($conditionalSourceFields as $f)
                                <option value="{{ $f->id }}" data-has-options="{{ (is_array($f->options) && count($f->options) > 0) ? '1' : '0' }}">{{ $f->label_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="depends-value-wrap">
                        <label class="form-label">= القيمة</label>
                        <select id="depends-on-value-select" class="form-control" style="min-width: 160px; display: none;">
                            <option value="">— اختر —</option>
                        </select>
                        <input type="text" id="depends-on-value-input" name="depends_on_value" class="form-control" placeholder="القيمة" style="width: 140px;">
                    </div>
                </div>
            </div>
            <div style="margin-top: 1.25rem;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة الحقل</button>
            </div>
        </form>
        </div>
    </div>
    <script>
    (function(){
        var form = document.getElementById('add-field-form');
        var sel = document.getElementById('new-field-type');
        var labelInput = document.getElementById('label-ar-input');
        var keyAuto = document.getElementById('field-key-auto');
        var wraps = {
            select: document.getElementById('wrap-for-select'),
            multiselect: document.getElementById('wrap-for-multiselect'),
            file: document.getElementById('wrap-for-file'),
            date: document.getElementById('wrap-for-date')
        };
        var conditionalOptions = @json($conditionalOptionsMap);
        var dependsSelect = document.getElementById('depends-on-field-id');
        var valueSelect = document.getElementById('depends-on-value-select');
        var valueInput = document.getElementById('depends-on-value-input');

        function showOnly(type) {
            Object.keys(wraps).forEach(function(k) {
                if (wraps[k]) wraps[k].style.display = (k === type) ? 'block' : 'none';
            });
            if (type !== 'date') {
                document.querySelectorAll('input[name="date_calendar"]').forEach(function(r) {
                    if (r.value === 'gregorian') r.checked = true;
                });
            }
            if (type === 'select') {
                var c1 = document.getElementById('options-container');
                if (c1 && !c1.children.length) addOption('options-container', 'options[]');
            }
            if (type === 'multiselect') {
                var c2 = document.getElementById('options-container-multi');
                if (c2 && !c2.children.length) addOption('options-container-multi', 'options_multi[]');
            }
        }
        sel.addEventListener('change', function() { showOnly(sel.value); });
        showOnly(sel.value);

        function addOption(containerId, namePrefix) {
            var container = document.getElementById(containerId);
            if (!container) return;
            var row = document.createElement('div');
            row.style.display = 'flex';
            row.style.gap = '0.5rem';
            row.style.alignItems = 'center';
            var inp = document.createElement('input');
            inp.type = 'text';
            inp.name = namePrefix;
            inp.className = 'form-control';
            inp.placeholder = 'خيار';
            inp.style.maxWidth = '280px';
            inp.style.color = '#1a1a1a';
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-danger btn-sm';
            btn.style.padding = '0.25rem 0.5rem';
            btn.style.fontSize = '0.8rem';
            btn.setAttribute('aria-label', 'حذف الخيار');
            btn.innerHTML = '<i class="fas fa-times"></i>';
            btn.addEventListener('click', function() { row.remove(); });
            row.appendChild(inp);
            row.appendChild(btn);
            container.appendChild(row);
        }
        document.getElementById('btn-add-option').addEventListener('click', function() {
            addOption('options-container', 'options[]');
        });
        document.getElementById('btn-add-option-multi').addEventListener('click', function() {
            addOption('options-container-multi', 'options_multi[]');
        });

        function updateDependsValue() {
            var fieldId = dependsSelect.value;
            valueSelect.removeAttribute('name');
            valueInput.setAttribute('name', 'depends_on_value');
            valueSelect.style.display = 'none';
            valueInput.style.display = '';
            valueInput.value = '';
            valueSelect.innerHTML = '<option value="">— اختر —</option>';
            if (!fieldId) return;
            var opts = conditionalOptions[fieldId];
            if (opts && opts.length > 0) {
                opts.forEach(function(opt) {
                    var o = document.createElement('option');
                    o.value = opt;
                    o.textContent = opt;
                    valueSelect.appendChild(o);
                });
                valueSelect.style.display = '';
                valueInput.style.display = 'none';
                valueInput.removeAttribute('name');
                valueSelect.setAttribute('name', 'depends_on_value');
            }
        }
        if (dependsSelect) dependsSelect.addEventListener('change', updateDependsValue);
        updateDependsValue();

        form.addEventListener('submit', function() {
            if (!keyAuto.value && labelInput && labelInput.value) {
                var slug = (labelInput.value || '').trim().replace(/\s+/g, '_').replace(/[^\w\u0600-\u06FF\-_]/g, '');
                keyAuto.value = slug || 'field_' + Date.now();
            }
            if (!keyAuto.value) keyAuto.value = 'field_' + Date.now();
            var type = sel.value;
            if (type === 'select' || type === 'multiselect') {
                var cid = type === 'select' ? 'options-container' : 'options-container-multi';
                var container = document.getElementById(cid);
                if (container) container.querySelectorAll('input').forEach(function(o) { o.name = 'options[]'; });
            }
        });
    })();
    </script>

    {{-- قائمة الحقول --}}
    <div class="fields-table-wrap">
        <h3 class="table-head"><i class="fas fa-list"></i> حقول النموذج</h3>
        <div style="padding: 1rem 1.25rem;">
        @if($editForm->fields->isEmpty())
            <p style="color: #555; margin: 0;">لا توجد حقول. استخدم النموذج أعلاه لإضافة حقول.</p>
        @else
            <table>
                @php $allTypeLabels = \App\Models\Beneficiary\BeneficiaryFormField::allTypeLabels(); @endphp
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المفتاح</th>
                        <th>التسمية</th>
                        <th>النوع</th>
                        <th>شرطي</th>
                        <th style="text-align: center;">مطلوب</th>
                        <th style="text-align: left;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($editForm->fields as $idx => $field)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td><code style="background: rgba(31,107,79,0.1); padding: 0.2rem 0.4rem; border-radius: 4px; font-size: 0.85rem;">{{ $field->field_key }}</code></td>
                            <td>{{ $field->label_ar }}</td>
                            <td>
                                {{ $allTypeLabels[$field->field_type] ?? $field->field_type }}
                                @if($field->field_type === 'date')
                                    <br><small style="color: #666;">{{ $field->dateCalendar() === 'hijri' ? 'تقويم هجري' : 'تقويم ميلادي' }}</small>
                                @endif
                            </td>
                            <td>@if($field->depends_on_field_id)<span style="color: var(--primary-color); font-weight: 600;">عند {{ $field->dependsOnField->label_ar ?? '' }} = {{ $field->depends_on_value }}</span>@else—@endif</td>
                            <td style="text-align: center;">{{ $field->is_required ? 'نعم' : '—' }}</td>
                            <td>
                                <form method="POST" action="{{ route('wesal.beneficiaries.forms.fields.destroy', $field) }}" style="display: inline-block;" onsubmit="return confirm('حذف هذا الحقل؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; color: #c62828; border-radius: 6px;"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        </div>
    </div>
</div>
