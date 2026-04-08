<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-list-ol" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            إعدادات الطلبات — تسلسل الموافقات
        </h1>
        <p class="page-subtitle">
            تعيين تسلسل الموافقات لكل نوع طلب. الخطوة الأولى: المدير المباشر (من ملف الموظف المُنشئ للطلب).
            يمكن إضافة أدوار أو موظفين للخطوات التالية حتى الاعتماد. بعد الاعتماد الكامل، طلبات دعم المستفيدين تذهب للمالية لتنفيذ الصرف.
        </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> @foreach($errors->all() as $e)<p style="margin: 0;">{{ $e }}</p>@endforeach</div>
    @endif

    <form method="POST" action="{{ route('wesal.hr.request-settings.store') }}" id="request-settings-form">
        @csrf
        @php
            $types = $requestTypes ?? \App\Models\HR\RequestApprovalSequence::TYPES;
            $sequencesByType = $sequencesByType ?? collect();
        @endphp
        @foreach($types as $typeKey => $typeLabel)
            <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid var(--border-color);">
                <h3 style="color: var(--primary-color); margin: 0 0 1rem 0; font-size: 1.1rem;">
                    <i class="fas fa-cog" style="margin-left: 0.35rem;"></i> {{ $typeLabel }}
                </h3>
                <table style="width: 100%; border-collapse: collapse; direction: rtl;">
                    <thead>
                        <tr style="background: rgba(0,0,0,0.2);">
                            <th style="padding: 0.6rem; text-align: right; width: 80px;">الخطوة</th>
                            <th style="padding: 0.6rem; text-align: right;">الموافق</th>
                            <th style="padding: 0.6rem; text-align: right; min-width: 200px;">الدور أو الموظف</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($step = 1; $step <= 6; $step++)
                            @php
                                $seq = $sequencesByType->get($typeKey)?->firstWhere('step', $step);
                                $isFirst = $step === 1;
                            @endphp
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.6rem;">{{ $step }}</td>
                                <td style="padding: 0.6rem;">
                                    @if($isFirst)
                                        <span style="color: var(--primary-color);">المدير المباشر</span>
                                        <input type="hidden" name="sequences[{{ $typeKey }}_{{ $step }}][request_type]" value="{{ $typeKey }}">
                                        <input type="hidden" name="sequences[{{ $typeKey }}_{{ $step }}][step]" value="1">
                                        <input type="hidden" name="sequences[{{ $typeKey }}_{{ $step }}][approver_type]" value="direct_manager">
                                    @else
                                        <select name="sequences[{{ $typeKey }}_{{ $step }}][approver_type]" class="form-control step-approver-type" data-type="{{ $typeKey }}" data-step="{{ $step }}" style="min-width: 140px;">
                                            <option value="">— لا موافق إضافي —</option>
                                            <option value="role" {{ ($seq->approver_type ?? '') === 'role' ? 'selected' : '' }}>دور</option>
                                            <option value="employee" {{ ($seq->approver_type ?? '') === 'employee' ? 'selected' : '' }}>موظف</option>
                                        </select>
                                        <input type="hidden" name="sequences[{{ $typeKey }}_{{ $step }}][request_type]" value="{{ $typeKey }}">
                                        <input type="hidden" name="sequences[{{ $typeKey }}_{{ $step }}][step]" value="{{ $step }}">
                                    @endif
                                </td>
                                <td style="padding: 0.6rem;">
                                    @if($isFirst)
                                        <span style="color: var(--text-secondary); font-size: 0.9rem;">— من ملف الموظف —</span>
                                    @else
                                        <div class="step-role-wrap" data-type="{{ $typeKey }}" data-step="{{ $step }}" style="display: {{ ($seq->approver_type ?? '') === 'role' ? 'block' : 'none' }};">
                                            <select name="sequences[{{ $typeKey }}_{{ $step }}][role_name]" class="form-control" style="min-width: 180px;">
                                                <option value="">— اختر الدور —</option>
                                                @foreach($roles ?? [] as $r)
                                                    <option value="{{ $r->name }}" {{ ($seq->role_name ?? '') === $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="step-employee-wrap" data-type="{{ $typeKey }}" data-step="{{ $step }}" style="display: {{ ($seq->approver_type ?? '') === 'employee' ? 'block' : 'none' }};">
                                            <select name="sequences[{{ $typeKey }}_{{ $step }}][employee_id]" class="form-control" style="min-width: 200px;">
                                                <option value="">— اختر الموظف —</option>
                                                @foreach($employeesForApprover ?? [] as $emp)
                                                    <option value="{{ $emp->id }}" {{ ($seq->employee_id ?? '') == $emp->id ? 'selected' : '' }}>{{ $emp->name_ar }} ({{ $emp->employee_no }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- لا نضع حقل مخفي مكرر لـ role_name أو employee_id حتى لا يلغي القيمة المختارة --}}
                                    @endif
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        @endforeach
        <div style="margin-top: 1rem;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ التسلسل</button>
            <a href="{{ route('wesal.hr.show') }}" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>
<script>
(function() {
    document.querySelectorAll('.step-approver-type').forEach(function(sel) {
        sel.addEventListener('change', function() {
            var type = this.dataset.type;
            var step = this.dataset.step;
            var roleWrap = document.querySelector('.step-role-wrap[data-type="' + type + '"][data-step="' + step + '"]');
            var empWrap = document.querySelector('.step-employee-wrap[data-type="' + type + '"][data-step="' + step + '"]');
            if (roleWrap) { roleWrap.style.display = this.value === 'role' ? 'block' : 'none'; var r = roleWrap.querySelector('select'); if (r && this.value !== 'role') r.value = ''; }
            if (empWrap) { empWrap.style.display = this.value === 'employee' ? 'block' : 'none'; var e = empWrap.querySelector('select'); if (e && this.value !== 'employee') e.value = ''; }
        });
    });
    // قبل الإرسال: تعطيل الصفوف الفارغة (خطوة 2-6 بدون موافق) حتى لا تُرسل
    document.getElementById('request-settings-form').addEventListener('submit', function() {
        document.querySelectorAll('.step-approver-type').forEach(function(sel) {
            var type = sel.dataset.type;
            var step = sel.dataset.step;
            var row = sel.closest('tr');
            if (!row) return;
            var roleWrap = document.querySelector('.step-role-wrap[data-type="' + type + '"][data-step="' + step + '"]');
            var empWrap = document.querySelector('.step-employee-wrap[data-type="' + type + '"][data-step="' + step + '"]');
            var roleSelect = roleWrap ? roleWrap.querySelector('select[name*="role_name"]') : null;
            var empSelect = empWrap ? empWrap.querySelector('select[name*="employee_id"]') : null;
            var isFilled = sel.value === 'direct_manager' || (sel.value === 'role' && roleSelect && roleSelect.value) || (sel.value === 'employee' && empSelect && empSelect.value);
            row.querySelectorAll('input, select').forEach(function(inp) {
                inp.disabled = !isFilled;
            });
        });
    });
})();
</script>
