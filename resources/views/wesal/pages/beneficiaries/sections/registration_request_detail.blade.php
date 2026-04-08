@php
    $r = $registrationRequestDetail ?? null;
@endphp
@if(!$r)
    <div class="content-card"><p class="page-subtitle">طلب غير موجود.</p></div>
@else
<div class="content-card">
    <div class="page-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-file-alt" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                تفاصيل طلب التسجيل #{{ $r->id }}
            </h1>
            <p class="page-subtitle">راجع <strong>البيانات الأساسية</strong> المعروضة من نموذج التسجيل ثم اعتمد أو ارفض الطلب</p>
        </div>
        <a href="{{ route('wesal.beneficiaries.show', ['section' => 'registration-requests']) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة لقائمة الطلبات
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 0 0 1rem;">
        <strong>تاريخ التقديم:</strong> {{ $r->created_at->format('Y-m-d H:i') }}
        @if($r->beneficiaryForm)
            — <strong>النموذج:</strong> {{ $r->beneficiaryForm->name_ar }}
        @endif
    </p>

    @php
        $formData = is_array($r->form_data) ? $r->form_data : [];
        $fieldLabels = [];
        if ($r->beneficiaryForm && $r->beneficiaryForm->fields) {
            foreach ($r->beneficiaryForm->fields as $f) {
                $fieldLabels[$f->field_key] = $f->label_ar ?? $f->field_key;
            }
        }
    @endphp
    @if(count($formData) > 0)
    <div style="background: rgba(255,255,255,0.05); padding: 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--primary-color); margin: 0 0 0.35rem; font-size: 1.05rem;"><i class="fas fa-id-card"></i> البيانات الأساسية</h3>
        <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0 0 1rem;">هذه هي البيانات الرئيسية للطلب — مأخوذة من <strong>نموذج التسجيل</strong> فقط (لا يوجد نموذج بيانات منفصل).</p>
        <div class="table-container">
            <table style="direction: rtl;">
                <thead>
                    <tr><th>الحقل</th><th>القيمة</th></tr>
                </thead>
                <tbody>
                    @foreach($formData as $key => $rawVal)
                        @php
                            $label = $fieldLabels[$key] ?? $key;
                            $isFile = is_string($rawVal) && (str_starts_with($rawVal, 'storage/') || str_starts_with($rawVal, 'beneficiary_uploads/'));
                        @endphp
                        <tr>
                            <td>{{ $label }}</td>
                            <td>
                                @if($isFile)
                                    <a href="{{ image_asset_url($rawVal) }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">
                                        <i class="fas fa-paperclip"></i> فتح المرفق
                                    </a>
                                @else
                                    @php $fd = format_beneficiary_choice_display($rawVal); @endphp
                                    {{ $fd !== '' ? $fd : '—' }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="alert alert-info" style="margin-bottom: 1.5rem;">
        لا توجد <strong>بيانات أساسية</strong> (نموذج التسجيل) لهذا الطلب. راجع إعدادات النموذج أو إعادة التقديم من البوابة.
    </div>
    @endif

    @if($r->status === 'pending')
        <div style="background: rgba(255,255,255,0.06); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
            <h3 style="margin: 0 0 1rem; color: var(--text-primary);"><i class="fas fa-gavel"></i> قرار الطلب</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
                <form method="post" action="{{ route('wesal.beneficiaries.registration-requests.approve', ['registration_request' => $r->id]) }}" style="display: inline-block;">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="padding: 0.55rem 1.2rem;">
                        <i class="fas fa-check"></i> اعتماد الطلب وإنشاء المستفيد
                    </button>
                </form>
                <form method="post" action="{{ route('wesal.beneficiaries.registration-requests.reject', ['registration_request' => $r->id]) }}" style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem;">
                    @csrf
                    <input type="text" name="rejection_reason" placeholder="سبب الرفض (اختياري)" class="form-control" style="min-width: 220px;">
                    <button type="submit" class="btn btn-danger" style="padding: 0.55rem 1.2rem;">
                        <i class="fas fa-times"></i> رفض الطلب
                    </button>
                </form>
            </div>
            <p style="margin: 1rem 0 0; font-size: 0.85rem; color: var(--text-secondary);">
                <i class="fas fa-shield-alt"></i> تأكد من مراجعة جميع الحقول والمرفقات قبل الاعتماد. عند الخطأ في الجلسة (419) أعد تحميل الصفحة من القائمة ثم حاول مجدداً.
            </p>
        </div>
    @else
        <div class="alert alert-info" style="margin-top: 1rem;">
            حالة هذا الطلب: <strong>{{ $r->status === 'approved' ? 'معتمد' : ($r->status === 'rejected' ? 'مرفوض' : $r->status) }}</strong>
            @if($r->status === 'rejected' && $r->rejection_reason)
                <div style="margin-top: 0.5rem;"><strong>سبب الرفض:</strong> {{ $r->rejection_reason }}</div>
            @endif
        </div>
    @endif
</div>
@endif
