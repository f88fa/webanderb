<div class="content-card">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 class="page-title">
            <i class="fas fa-file-invoice-dollar" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            طلب صرف جديد
        </h1>
        <p class="page-subtitle">تسجيل طلب صرف جديد (سيتم مراجعته والموافقة عليه لاحقاً)</p>
    </div>

    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 1.5rem;">
            <i class="fas fa-exclamation-circle"></i>
            @foreach($errors->all() as $err) <p>{{ $err }}</p> @endforeach
        </div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color); max-width: 700px;">
        <form method="POST" action="{{ route('wesal.finance.payment-requests.store') }}" enctype="multipart/form-data" id="paymentRequestForm">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label class="form-label">رقم الطلب (تلقائي)</label>
                <input type="text" class="form-control" value="{{ $nextRequestNo ?? '' }}" readonly style="background: rgba(0,0,0,0.2); color: var(--text-primary);">
            </div>
            <div style="margin-bottom: 1rem;">
                <label class="form-label">تاريخ الطلب *</label>
                <input type="date" name="request_date" class="form-control" required value="{{ old('request_date', date('Y-m-d')) }}">
            </div>
            <div style="margin-bottom: 1rem;">
                <label class="form-label">المبلغ *</label>
                <input type="number" name="amount" class="form-control" required min="0.01" step="0.01" value="{{ old('amount') }}" placeholder="0.00" dir="ltr" style="text-align:left;">
            </div>

            {{-- نوع المستفيد: موظف أو جهة --}}
            <div style="margin-bottom: 1rem;">
                <label class="form-label">نوع المستفيد *</label>
                <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" name="beneficiary_type" value="employee" {{ old('beneficiary_type', 'entity') === 'employee' ? 'checked' : '' }} class="beneficiary-type-radio">
                        <span>موظف</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" name="beneficiary_type" value="entity" {{ old('beneficiary_type', 'entity') === 'entity' ? 'checked' : '' }} class="beneficiary-type-radio">
                        <span>جهة</span>
                    </label>
                </div>
            </div>

            {{-- موظف: قائمة مع بحث --}}
            <div id="beneficiary-employee-wrap" class="beneficiary-field" style="margin-bottom: 1rem; display: {{ old('beneficiary_type', 'entity') === 'employee' ? 'block' : 'none' }};">
                <label class="form-label">اختر الموظف *</label>
                <div class="searchable-employee-wrap" style="position: relative;">
                    <input type="text" id="employee_search" class="form-control" placeholder="ابحث بالاسم أو الرقم..." autocomplete="off"
                           style="background: rgba(255,255,255,0.1); color: var(--text-primary);">
                    <input type="hidden" name="beneficiary_employee_id" id="beneficiary_employee_id" value="{{ old('beneficiary_employee_id') }}">
                    <div id="employee_selected_display" style="margin-top: 0.5rem; padding: 0.5rem; background: rgba(95,179,142,0.2); border-radius: 6px; display: none;"></div>
                    <div id="employee_dropdown" class="employee-dropdown" style="display: none; position: absolute; top: 100%; right: 0; left: 0; margin-top: 4px; background: rgba(15,61,46,0.98); border: 1px solid var(--primary-color); border-radius: 8px; max-height: 220px; overflow-y: auto; z-index: 1000; box-shadow: 0 4px 12px rgba(0,0,0,0.3);"></div>
                </div>
            </div>

            {{-- جهة: اكتب اسم الجهة --}}
            <div id="beneficiary-entity-wrap" class="beneficiary-field" style="margin-bottom: 1rem; display: {{ old('beneficiary_type', 'entity') === 'entity' ? 'block' : 'none' }};">
                <label class="form-label">اسم الجهة *</label>
                <input type="text" name="beneficiary" id="beneficiary_entity_input" class="form-control" value="{{ old('beneficiary') }}" placeholder="اكتب اسم الجهة"
                       style="background: rgba(255,255,255,0.1); color: var(--text-primary);">
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">الغرض / الوصف</label>
                <textarea name="description" class="form-control" rows="3" placeholder="الغرض من الصرف">{{ old('description') }}</textarea>
            </div>

            {{-- المرفقات --}}
            <div style="margin-bottom: 1rem;">
                <label class="form-label"><i class="fas fa-paperclip" style="margin-left: 0.35rem;"></i> مرفقات (اختياري)</label>
                <input type="file" name="attachments[]" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xls,.xlsx"
                       style="background: rgba(255,255,255,0.1); color: var(--text-primary);">
                <small style="color: var(--text-secondary); font-size: 0.85rem;">يمكن اختيار أكثر من ملف. الحجم الأقصى 10 ميجا للملف.</small>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">السنة المالية (اختياري)</label>
                <select name="period_id" class="form-control" style="background: rgba(255,255,255,0.1); color: var(--text-primary);">
                    <option value="">-- اختر السنة المالية --</option>
                    @foreach($yearOptions ?? [] as $opt)
                        <option value="{{ $opt->period_id }}" {{ old('period_id') == $opt->period_id ? 'selected' : '' }}>{{ $opt->year_name }}</option>
                    @endforeach
                </select>
                <small style="color: var(--text-secondary); font-size: 0.85rem;">السنوات المفتوحة فقط</small>
            </div>
            <div style="display: flex; gap: 1rem;">
                <button type="submit" style="padding: 0.65rem 1.5rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-save"></i> حفظ الطلب
                </button>
                <a href="{{ route('wesal.finance.payment-requests.index') }}" style="padding: 0.65rem 1.5rem; background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 8px; text-decoration: none; font-weight: 500;">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const employees = @json($employees ?? []);
    const typeEmployee = document.getElementById('beneficiary-employee-wrap');
    const typeEntity = document.getElementById('beneficiary-entity-wrap');
    const entityInput = document.getElementById('beneficiary_entity_input');
    const employeeIdInput = document.getElementById('beneficiary_employee_id');
    const employeeSearch = document.getElementById('employee_search');
    const employeeDropdown = document.getElementById('employee_dropdown');
    const employeeSelectedDisplay = document.getElementById('employee_selected_display');
    const form = document.getElementById('paymentRequestForm');

    document.querySelectorAll('.beneficiary-type-radio').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const isEmployee = this.value === 'employee';
            typeEmployee.style.display = isEmployee ? 'block' : 'none';
            typeEntity.style.display = isEmployee ? 'none' : 'block';
            if (isEmployee) {
                entityInput.removeAttribute('required');
                entityInput.value = '';
                employeeIdInput.setAttribute('required', 'required');
            } else {
                employeeIdInput.removeAttribute('required');
                employeeIdInput.value = '';
                employeeSelectedDisplay.style.display = 'none';
                employeeSearch.value = '';
                entityInput.setAttribute('required', 'required');
            }
        });
    });
    if (document.querySelector('input[name="beneficiary_type"]:checked')?.value === 'employee') {
        employeeIdInput.setAttribute('required', 'required');
        entityInput.removeAttribute('required');
    } else {
        entityInput.setAttribute('required', 'required');
    }

    function renderEmployeeList(filter) {
        const term = (filter || '').toLowerCase().trim();
        const list = term
            ? employees.filter(function(e) {
                const nameAr = (e.name_ar || '').toLowerCase();
                const nameEn = (e.name_en || '').toLowerCase();
                const no = (e.employee_no || '').toString();
                return nameAr.indexOf(term) !== -1 || nameEn.indexOf(term) !== -1 || no.indexOf(term) !== -1;
              })
            : employees;
        employeeDropdown.innerHTML = '';
        if (list.length === 0) {
            employeeDropdown.innerHTML = '<div style="padding: 1rem; color: var(--text-secondary);">لا توجد نتائج</div>';
        } else {
            list.slice(0, 50).forEach(function(emp) {
                const div = document.createElement('div');
                div.className = 'employee-option';
                div.style.cssText = 'padding: 0.6rem 1rem; cursor: pointer; border-bottom: 1px solid rgba(255,255,255,0.08);';
                div.textContent = (emp.employee_no || '') + ' - ' + (emp.name_ar || emp.name_en || '');
                div.dataset.id = emp.id;
                div.dataset.name = emp.name_ar || emp.name_en || '';
                div.onmouseover = function() { this.style.background = 'rgba(95,179,142,0.3)'; };
                div.onmouseout = function() { this.style.background = ''; };
                div.onclick = function() {
                    employeeIdInput.value = emp.id;
                    employeeSelectedDisplay.textContent = 'المحدد: ' + (emp.name_ar || emp.name_en || '');
                    employeeSelectedDisplay.style.display = 'block';
                    employeeSearch.value = '';
                    employeeDropdown.style.display = 'none';
                };
                employeeDropdown.appendChild(div);
            });
        }
        employeeDropdown.style.display = 'block';
    }

    employeeSearch.addEventListener('focus', function() {
        renderEmployeeList(this.value);
    });
    employeeSearch.addEventListener('input', function() {
        renderEmployeeList(this.value);
    });
    employeeSearch.addEventListener('blur', function() {
        setTimeout(function() {
            employeeDropdown.style.display = 'none';
        }, 200);
    });

    var oldEmployeeId = '{{ old("beneficiary_employee_id") }}';
    if (oldEmployeeId && employees.length) {
        var emp = employees.find(function(e) { return e.id == oldEmployeeId; });
        if (emp) {
            employeeIdInput.value = emp.id;
            employeeSelectedDisplay.textContent = 'المحدد: ' + (emp.name_ar || emp.name_en || '');
            employeeSelectedDisplay.style.display = 'block';
        }
    }

    form.addEventListener('submit', function() {
        var type = document.querySelector('input[name="beneficiary_type"]:checked')?.value;
        if (type === 'employee' && !employeeIdInput.value) {
            event.preventDefault();
            alert('يرجى اختيار الموظف');
            return false;
        }
        if (type === 'entity' && !entityInput.value.trim()) {
            event.preventDefault();
            alert('يرجى كتابة اسم الجهة');
            return false;
        }
    });
});
</script>
