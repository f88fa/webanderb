@php $b = $profileBeneficiary ?? null; @endphp
@if(!$b)
    <div class="content-card">
        <div class="alert alert-error">المستفيد غير موجود.</div>
        <a href="{{ route('wesal.beneficiaries.show', ['section' => 'list']) }}" class="btn btn-secondary">العودة للقائمة</a>
    </div>
@else
<div class="content-card">
    <div class="page-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-user-folder" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                ملف المستفيد: {{ $b->displayNameForPortal() }}
            </h1>
            <p class="page-subtitle">رقم المستفيد: {{ $b->beneficiary_no }}</p>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('wesal.beneficiaries.show', ['section' => 'edit', 'sub' => $b->id]) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> تعديل البيانات
            </a>
            <a href="{{ route('wesal.beneficiaries.show', ['section' => 'list']) }}" class="btn btn-secondary">العودة للقائمة</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="profile-tabs" style="margin-top: 1.5rem;">
        <div role="tablist" style="display: flex; flex-wrap: wrap; gap: 0.25rem; border-bottom: 2px solid var(--border-color); margin-bottom: 1.5rem;">
            <button type="button" class="profile-tab-btn active" data-tab="basic" style="padding: 0.75rem 1.25rem; background: rgba(255,255,255,0.08); border: none; border-bottom: 2px solid var(--primary-color); color: var(--text-primary); cursor: pointer; font-weight: 600; margin-bottom: -2px;">نموذج التسجيل</button>
            <button type="button" class="profile-tab-btn" data-tab="support" style="padding: 0.75rem 1.25rem; background: transparent; border: none; color: var(--text-secondary); cursor: pointer;">سجل الدعم</button>
            <button type="button" class="profile-tab-btn" data-tab="documents" style="padding: 0.75rem 1.25rem; background: transparent; border: none; color: var(--text-secondary); cursor: pointer;">الملفات</button>
            <button type="button" class="profile-tab-btn" data-tab="programs" style="padding: 0.75rem 1.25rem; background: transparent; border: none; color: var(--text-secondary); cursor: pointer;">البرامج</button>
        </div>

        @php
            $profileFormData = is_array($b->form_data) ? $b->form_data : [];
            $profileFormFieldKeys = ($b->beneficiaryForm && $b->beneficiaryForm->relationLoaded('fields'))
                ? $b->beneficiaryForm->fields->pluck('field_key')->all()
                : [];
        @endphp
        <div id="tab-basic" class="profile-tab-panel" style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-color);">
            @if($b->beneficiaryForm && $b->beneficiaryForm->fields && $b->beneficiaryForm->fields->isNotEmpty())
                <h3 style="color: var(--primary-color); margin: 0 0 0.35rem;">نموذج التسجيل</h3>
                <p style="color: var(--text-secondary); font-size: 0.88rem; margin: 0 0 1rem;">كما عبأه المستفيد — نفس ترتيب الحقول والتسميات في البوابة وطلب التسجيل، بما فيها المرفقات.</p>
                <div class="table-container">
                    <table style="direction: rtl; width: 100%;">
                        <thead>
                            <tr>
                                <th>الحقل</th>
                                <th>القيمة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($b->beneficiaryForm->fields as $field)
                                @php
                                    $k = $field->field_key;
                                    if ($field->isStandardKey()) {
                                        $raw = $b->{$k};
                                        if (($raw === null || $raw === '') && array_key_exists($k, $profileFormData)) {
                                            $raw = $profileFormData[$k];
                                        }
                                        if ($k === 'birth_date' && $raw) {
                                            $raw = $raw instanceof \Carbon\Carbon ? $raw->format('Y-m-d') : $raw;
                                        }
                                    } else {
                                        $raw = $profileFormData[$k] ?? null;
                                    }
                                    $isFile = $field->field_type === 'file'
                                        && is_string($raw)
                                        && $raw !== ''
                                        && (str_starts_with($raw, 'storage/') || str_starts_with($raw, 'beneficiary_uploads/'));
                                @endphp
                                <tr>
                                    <td style="vertical-align: top; white-space: nowrap;">{{ $field->label_ar }}</td>
                                    <td>
                                        @if($isFile)
                                            <a href="{{ image_asset_url($raw) }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">
                                                <i class="fas fa-paperclip"></i> فتح المرفق
                                            </a>
                                        @elseif(in_array($field->field_type, ['select', 'multiselect'], true))
                                            @php $choiceDisp = format_beneficiary_choice_display($raw); @endphp
                                            {{ $choiceDisp !== '' ? $choiceDisp : '—' }}
                                        @elseif($raw === null || $raw === '')
                                            —
                                        @else
                                            {{ is_scalar($raw) ? $raw : format_beneficiary_choice_display($raw) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @php
                    $orphanKeys = array_values(array_diff(array_keys($profileFormData), $profileFormFieldKeys));
                @endphp
                @if(count($orphanKeys) > 0)
                    <h4 style="color: var(--text-primary); margin: 1.25rem 0 0.5rem; font-size: 0.95rem;">قيم محفوظة إضافية (غير موجودة في تعريف النموذج الحالي)</h4>
                    <div class="table-container">
                        <table style="direction: rtl; width: 100%;">
                            <thead><tr><th>المفتاح</th><th>القيمة</th></tr></thead>
                            <tbody>
                                @foreach($orphanKeys as $ok)
                                    @php $rawVal = $profileFormData[$ok] ?? null; @endphp
                                    <tr>
                                        <td><code style="font-size: 0.85rem;">{{ $ok }}</code></td>
                                        <td>
                                            @if(is_string($rawVal) && (str_starts_with($rawVal, 'storage/') || str_starts_with($rawVal, 'beneficiary_uploads/')))
                                                <a href="{{ image_asset_url($rawVal) }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;"><i class="fas fa-paperclip"></i> فتح المرفق</a>
                                            @else
                                                @php $orphDisp = format_beneficiary_choice_display($rawVal); @endphp
                                                {{ $orphDisp !== '' ? $orphDisp : '—' }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @elseif(count($profileFormData) > 0)
                <h3 style="color: var(--primary-color); margin: 0 0 0.35rem;">نموذج التسجيل (بيانات محفوظة)</h3>
                <p style="color: var(--text-secondary); font-size: 0.88rem; margin: 0 0 1rem;">لا يوجد نموذج مرتبط حالياً أو تعريف الحقول تغيّر؛ تُعرض المفاتيح كما في السجل.</p>
                <div class="table-container">
                    <table style="direction: rtl; width: 100%;">
                        <thead>
                            <tr><th>المفتاح</th><th>القيمة</th></tr>
                        </thead>
                        <tbody>
                            @foreach($profileFormData as $key => $rawVal)
                                @php
                                    $isFile = is_string($rawVal) && (str_starts_with($rawVal, 'storage/') || str_starts_with($rawVal, 'beneficiary_uploads/'));
                                @endphp
                                <tr>
                                    <td><code style="font-size: 0.85rem;">{{ $key }}</code></td>
                                    <td>
                                        @if($isFile)
                                            <a href="{{ image_asset_url($rawVal) }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;"><i class="fas fa-paperclip"></i> فتح المرفق</a>
                                        @else
                                            @php $fbDisp = format_beneficiary_choice_display($rawVal); @endphp
                                            {{ $fbDisp !== '' ? $fbDisp : '—' }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد بيانات نموذج مسجّلة لهذا المستفيد.</p>
            @endif
        </div>

        <div id="tab-support" class="profile-tab-panel" style="display: none; background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-color);">
            <h3 style="color: var(--primary-color); margin-bottom: 1rem;">سجل الدعم (الخدمات والمساعدات)</h3>
            @if($b->serviceRecords && $b->serviceRecords->count() > 0)
                <div class="table-container">
                    <table style="direction: rtl; width: 100%;">
                        <thead>
                            <tr>
                                <th>نوع الخدمة</th>
                                <th>المبلغ</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>طلب الصرف / السند</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($b->serviceRecords as $sr)
                            <tr>
                                <td>{{ $sr->serviceType?->name_ar ?? '—' }}</td>
                                <td dir="ltr" style="text-align: left;">{{ $sr->amount ? number_format($sr->amount, 2) : '—' }}</td>
                                <td>{{ $sr->service_date?->format('Y-m-d') }}</td>
                                <td>{{ $sr->status_label }}</td>
                                <td>
                                    @if($sr->paymentRequest)
                                        <span>{{ $sr->paymentRequest->request_no }}</span>
                                        <span style="color: var(--text-secondary);">({{ $sr->paymentRequest->status_label }})</span>
                                        @if($sr->paymentRequest->journalEntry)
                                            <a href="{{ route('wesal.finance.journal-entries.print', $sr->paymentRequest->journalEntry) }}" target="_blank" rel="noopener" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; margin-right: 0.5rem;"><i class="fas fa-print"></i> طباعة السند</a>
                                        @endif
                                        <a href="{{ route('wesal.finance.payment-requests.index') }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;"><i class="fas fa-external-link-alt"></i> طلبات الصرف</a>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد سجلات دعم لهذا المستفيد.</p>
            @endif
        </div>

        <div id="tab-documents" class="profile-tab-panel" style="display: none; background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-color);">
            <h3 style="color: var(--primary-color); margin-bottom: 0.35rem;">الملفات والمستندات (سجل الجمعية)</h3>
            <p style="color: var(--text-secondary); font-size: 0.88rem; margin: 0 0 1rem;">المستندات التي تُضاف من لوحة التحكم. أما مرفقات <strong>نموذج التسجيل</strong> فتظهر في تبويب «نموذج التسجيل».</p>
            @if($b->documents && $b->documents->count() > 0)
                <div class="table-container">
                    <table style="direction: rtl; width: 100%;">
                        <thead>
                            <tr>
                                <th>نوع المستند</th>
                                <th>التاريخ</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($b->documents as $doc)
                            <tr>
                                <td>{{ $doc->document_type ?? '—' }}</td>
                                <td>{{ $doc->document_date?->format('Y-m-d') ?? '—' }}</td>
                                <td>{{ $doc->notes ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد مستندات مسجلة.</p>
            @endif
        </div>

        <div id="tab-programs" class="profile-tab-panel" style="display: none; background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-color);">
            <h3 style="color: var(--primary-color); margin-bottom: 1rem;">البرامج المسجل فيها</h3>
            @if($b->programEnrollments && $b->programEnrollments->count() > 0)
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach($b->programEnrollments as $enrollment)
                        <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border-color);">{{ $enrollment->program?->name_ar ?? 'برنامج' }}</li>
                    @endforeach
                </ul>
            @else
                <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">غير مسجل في أي برنامج.</p>
            @endif
        </div>
    </div>
</div>

<script>
(function() {
    var buttons = document.querySelectorAll('.profile-tab-btn');
    var panels = document.querySelectorAll('.profile-tab-panel');
    buttons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var tab = this.getAttribute('data-tab');
            buttons.forEach(function(b) {
                b.classList.remove('active');
                b.style.background = 'transparent';
                b.style.borderBottom = 'none';
                b.style.color = 'var(--text-secondary)';
            });
            this.classList.add('active');
            this.style.background = 'rgba(255,255,255,0.08)';
            this.style.borderBottom = '2px solid var(--primary-color)';
            this.style.marginBottom = '-2px';
            this.style.color = 'var(--text-primary)';
            panels.forEach(function(p) {
                p.style.display = p.id === 'tab-' + tab ? 'block' : 'none';
            });
        });
    });
})();
</script>
@endif
