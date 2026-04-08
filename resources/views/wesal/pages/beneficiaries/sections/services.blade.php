<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-gift" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            الخدمات والمساعدات
        </h1>
        <p class="page-subtitle">
            إدارة الخدمات والمساعدات المقدمة للمستفيدين.
            عند طلب دعم مالي: يمر الطلب بتسلسل الموافقات (موظف المستفيدين → المدير المباشر → …) حتى الاعتماد، ثم يُرسل للمالية لتنفيذ الصرف.
            <a href="{{ route('wesal.hr.show', ['section' => 'request-settings']) }}" style="color: var(--primary-color); text-decoration: underline;">تعديل تسلسل الموافقات</a>
        </p>
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
    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $err) <p style="margin: 0;">{{ $err }}</p> @endforeach
        </div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">إضافة نوع خدمة جديد</h3>
        <form method="POST" action="{{ route('wesal.beneficiaries.service-types.store') }}" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: end; margin-bottom: 2rem;">
            @csrf
            <div>
                <label class="form-label">اسم الخدمة <span style="color: #ff8a80;">*</span></label>
                <input type="text" name="name_ar" class="form-control" required>
            </div>
            <div>
                <label class="form-label">الوصف</label>
                <input type="text" name="description" class="form-control">
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" name="is_financial" id="is_financial" value="1">
                <label class="form-label" for="is_financial" style="margin: 0;">دعم مالي (يُمرر لطلب صرف)</label>
            </div>
            <div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة</button>
            </div>
        </form>

        <h3 style="color: var(--text-primary); margin-bottom: 0.75rem;">تسجيل خدمة / دعم</h3>
        <div class="support-mode-tabs" style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
            <button type="button" class="support-mode-btn active" data-mode="single" style="padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--primary-color); color: white; cursor: pointer; font-weight: 600;">
                <i class="fas fa-user"></i> دعم فردي
            </button>
            <button type="button" class="support-mode-btn" data-mode="group" style="padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid var(--border-color); background: rgba(255,255,255,0.1); color: var(--text-primary); cursor: pointer;">
                <i class="fas fa-users"></i> دعم جماعي (قروب / برنامج)
            </button>
        </div>

        {{-- دعم فردي --}}
        <div id="form-single" class="support-form-panel">
            <form method="POST" action="{{ route('wesal.beneficiaries.beneficiary-services.store') }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; align-items: end; margin-bottom: 2rem;">
                @csrf
                <div>
                    <label class="form-label">المستفيد <span style="color: #ff8a80;">*</span></label>
                    <select name="beneficiary_id" class="form-control" required>
                        <option value="">-- اختر --</option>
                        @foreach($beneficiaries ?? [] as $b)
                            <option value="{{ $b->id }}">{{ $b->beneficiary_no }} - {{ $b->displayNameForPortal() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">نوع الخدمة <span style="color: #ff8a80;">*</span></label>
                    <select name="service_type_id" class="form-control" required>
                        <option value="">-- اختر --</option>
                        @foreach($serviceTypes ?? [] as $st)
                            <option value="{{ $st->id }}">{{ $st->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">التاريخ <span style="color: #ff8a80;">*</span></label>
                    <input type="date" name="service_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div>
                    <label class="form-label">المبلغ</label>
                    <input type="number" name="amount" class="form-control" step="0.01" min="0">
                </div>
                <div>
                    <label class="form-label">ملاحظات</label>
                    <input type="text" name="notes" class="form-control">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> تسجيل</button>
                </div>
            </form>
        </div>

        {{-- دعم جماعي: برنامج أو مجموعة مستفيدين — طلب صرف واحد --}}
        <div id="form-group" class="support-form-panel" style="display: none;">
            <form method="POST" action="{{ route('wesal.beneficiaries.beneficiary-services.store') }}" id="group-support-form" style="display: grid; grid-template-columns: 1fr; gap: 1rem; margin-bottom: 2rem;">
                @csrf
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
                    <div>
                        <label class="form-label">البرنامج (اختياري — لتصفية القائمة أو تعيين الدعم للبرنامج)</label>
                        <select id="group-program" class="form-control" name="program_id">
                            <option value="">-- الكل — تحديد مستفيدين يدوياً --</option>
                            @foreach($programs ?? [] as $p)
                                <option value="{{ $p->id }}">{{ $p->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">نوع الخدمة <span style="color: #ff8a80;">*</span></label>
                        <select name="service_type_id" class="form-control" required>
                            <option value="">-- اختر --</option>
                            @foreach($serviceTypes ?? [] as $st)
                                <option value="{{ $st->id }}">{{ $st->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">التاريخ <span style="color: #ff8a80;">*</span></label>
                        <input type="date" name="service_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div>
                        <label class="form-label">المبلغ لكل مستفيد <span style="color: #ff8a80;">*</span></label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0" required placeholder="نفس المبلغ لكل فرد">
                    </div>
                </div>
                <div>
                    <label class="form-label">المستفيدون <span style="color: #ff8a80;">*</span> — اختر مجموعة أو استخدم البرنامج ثم «تحديد الكل»</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <button type="button" id="btn-select-by-program" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; display: none;">تحديد كل مستفيدي البرنامج</button>
                        <button type="button" id="btn-clear-beneficiaries" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">إلغاء التحديد</button>
                    </div>
                    <select name="beneficiary_ids[]" id="group-beneficiary-ids" class="form-control" multiple style="min-height: 160px;">
                        @foreach($beneficiaries ?? [] as $b)
                            <option value="{{ $b->id }}">{{ $b->beneficiary_no }} - {{ $b->displayNameForPortal() }}</option>
                        @endforeach
                    </select>
                    <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 0.25rem;">اضغط Ctrl/Cmd للنقر المتعدد. عند اختيار برنامج يمكنك «تحديد كل مستفيدي البرنامج».</p>
                </div>
                <div>
                    <label class="form-label">ملاحظات</label>
                    <input type="text" name="notes" class="form-control">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-users"></i> تسجيل الدعم الجماعي</button>
                </div>
            </form>
        </div>

        <script>
        (function() {
            var programBeneficiaryIds = @json($programBeneficiaryIds ?? []);
            var modeSingle = document.getElementById('form-single');
            var modeGroup = document.getElementById('form-group');
            var groupProgram = document.getElementById('group-program');
            var groupBeneficiaryIds = document.getElementById('group-beneficiary-ids');
            var btnSelectByProgram = document.getElementById('btn-select-by-program');
            var btnClearBeneficiaries = document.getElementById('btn-clear-beneficiaries');
            var groupForm = document.getElementById('group-support-form');

            document.querySelectorAll('.support-mode-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var mode = this.getAttribute('data-mode');
                    document.querySelectorAll('.support-mode-btn').forEach(function(b) {
                        b.style.background = 'rgba(255,255,255,0.1)';
                        b.style.color = 'var(--text-primary)';
                    });
                    this.style.background = 'var(--primary-color)';
                    this.style.color = 'white';
                    modeSingle.style.display = mode === 'single' ? 'block' : 'none';
                    modeGroup.style.display = mode === 'group' ? 'block' : 'none';
                    if (mode === 'group') {
                        toggleProgramSelectBtn();
                    }
                });
            });

            function toggleProgramSelectBtn() {
                var pid = groupProgram.value;
                btnSelectByProgram.style.display = pid && programBeneficiaryIds[pid] && programBeneficiaryIds[pid].length ? 'inline-block' : 'none';
            }

            groupProgram.addEventListener('change', function() {
                toggleProgramSelectBtn();
                groupBeneficiaryIds.querySelectorAll('option').forEach(function(opt) {
                    opt.style.display = '';
                });
                var pid = this.value;
                if (pid && programBeneficiaryIds[pid] && programBeneficiaryIds[pid].length) {
                    var ids = programBeneficiaryIds[pid];
                    groupBeneficiaryIds.querySelectorAll('option').forEach(function(opt) {
                        opt.style.display = ids.indexOf(parseInt(opt.value, 10)) !== -1 ? '' : 'none';
                    });
                }
            });

            btnSelectByProgram.addEventListener('click', function() {
                var pid = groupProgram.value;
                if (!pid || !programBeneficiaryIds[pid]) return;
                groupBeneficiaryIds.querySelectorAll('option').forEach(function(opt) {
                    opt.selected = programBeneficiaryIds[pid].indexOf(parseInt(opt.value, 10)) !== -1;
                });
            });

            btnClearBeneficiaries.addEventListener('click', function() {
                groupBeneficiaryIds.querySelectorAll('option').forEach(function(opt) { opt.selected = false; });
            });

            groupForm.addEventListener('submit', function(e) {
                var selected = Array.from(groupBeneficiaryIds.selectedOptions).map(function(o) { return o.value; });
                if (selected.length === 0) {
                    e.preventDefault();
                    alert('يجب اختيار مستفيد واحد على الأقل.');
                    return false;
                }
            });
        })();
        </script>

        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">أنواع الخدمات</h3>
        @if(isset($serviceTypes) && $serviceTypes->count() > 0)
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 2rem;">
                @foreach($serviceTypes as $st)
                    <span style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.8rem; background: rgba(255,255,255,0.08); border-radius: 8px; font-size: 0.9rem;">
                        {{ $st->name_ar }}@if($st->is_financial ?? false)<span style="color: var(--primary-color);" title="دعم مالي"> (مالي)</span>@endif
                        <form method="POST" action="{{ route('wesal.beneficiaries.service-types.destroy', $st) }}" style="display: inline;" onsubmit="return confirm('حذف نوع الخدمة؟');">
                            @csrf @method('DELETE')
                            <button type="submit" style="background: none; border: none; color: #ff8a80; cursor: pointer; padding: 0;"><i class="fas fa-times"></i></button>
                        </form>
                    </span>
                @endforeach
            </div>
        @else
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">لا توجد أنواع خدمات. أضف نوع خدمة من النموذج أعلاه.</p>
        @endif

        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">سجل الخدمات</h3>
        @if(isset($serviceRecords) && $serviceRecords->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead>
                        <tr>
                            <th>المستفيد</th>
                            <th>نوع الخدمة</th>
                            <th>التاريخ</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>طلب الصرف</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($serviceRecords as $sr)
                        <tr>
                            <td>
                                <a href="{{ route('wesal.beneficiaries.show', ['section' => 'profile', 'sub' => $sr->beneficiary_id]) }}" style="color: var(--primary-color);">{{ $sr->beneficiary?->displayNameForPortal() }}</a>
                            </td>
                            <td>{{ $sr->serviceType?->name_ar }}</td>
                            <td>{{ $sr->service_date?->format('Y-m-d') }}</td>
                            <td dir="ltr" style="text-align: left;">{{ $sr->amount ? number_format($sr->amount, 2) : '-' }}</td>
                            <td>{{ $sr->status_label ?? '—' }}</td>
                            <td>
                                @if($sr->paymentRequest)
                                    {{ $sr->paymentRequest->request_no }} ({{ $sr->paymentRequest->status_label }})
                                    @if($sr->paymentRequest->journalEntry)
                                        <a href="{{ route('wesal.finance.journal-entries.print', $sr->paymentRequest->journalEntry) }}" target="_blank" rel="noopener" style="margin-right: 0.25rem;"><i class="fas fa-print"></i></a>
                                    @endif
                                @else — @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $serviceRecords->links() }}
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد سجلات خدمات.</p>
        @endif
    </div>
</div>
