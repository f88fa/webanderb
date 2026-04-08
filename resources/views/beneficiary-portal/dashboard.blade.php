@extends('beneficiary-portal.layout')

@section('title', 'متابعة الدعم والطلبات')

@section('bp_card_class', 'bp-card--portal')

@section('content')
<div class="bp-dash-hero">
    <div class="bp-dash-hero__text">
        <p class="bp-dash-eyebrow">لوحة المستفيد</p>
        <h1 class="bp-dash-title"><i class="fas fa-hands-helping" style="color: var(--bp-primary); margin-inline-end: 0.35rem;" aria-hidden="true"></i>مرحباً {{ $beneficiary->displayNameForPortal() }}</h1>
        <div class="bp-dash-meta">
            <span class="bp-dash-badge-id"><i class="fas fa-id-badge" aria-hidden="true"></i> رقم المستفيد: {{ $beneficiary->beneficiary_no }}</span>
        </div>
    </div>
    <form method="POST" action="{{ route('logout') }}" class="bp-dash-hero__actions" style="flex-shrink: 0;">
        @csrf
        <button type="submit" class="bp-btn bp-btn-secondary"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> تسجيل الخروج</button>
    </form>
</div>

<p class="bp-dash-intro">
    تعرض هذه الصفحة فقط <strong>معلومات الدعم</strong> المسجّلة لك، و<strong>تقارير الدعم</strong> (تقييمات الأهلية والملاحظات)، و<strong>طلباتك</strong> المرتبطة بحسابك. لاستكمال بياناتك أو أي استفسار تواصل مع الجمعية عبر معلومات التواصل في أسفل الصفحة.
</p>

<section class="bp-dash-section" aria-labelledby="bp-section-services">
    <div class="bp-dash-section__head">
        <span class="bp-dash-section__icon" aria-hidden="true"><i class="fas fa-gift"></i></span>
        <h2 class="bp-dash-section__title" id="bp-section-services">معلومات الدعم — الخدمات المقدَّمة لك</h2>
    </div>
    @if($beneficiary->serviceRecords->count() > 0)
    <div class="bp-table-wrap">
        <table class="bp-table">
            <thead><tr><th>التاريخ</th><th>نوع الخدمة</th><th>المبلغ</th><th>ملاحظات</th></tr></thead>
            <tbody>
                @foreach($beneficiary->serviceRecords as $sr)
                <tr>
                    <td>{{ $sr->service_date?->format('Y-m-d') }}</td>
                    <td>{{ $sr->serviceType?->name_ar ?? '-' }}</td>
                    <td>{{ $sr->amount ? number_format((float) $sr->amount, 2) : '-' }}</td>
                    <td>{{ $sr->notes ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="bp-empty" role="status">
        <i class="fas fa-inbox" aria-hidden="true"></i>
        لا توجد خدمات أو دعم مسجَّل لك حالياً.
    </div>
    @endif
</section>

<section class="bp-dash-section" aria-labelledby="bp-section-assessments">
    <div class="bp-dash-section__head">
        <span class="bp-dash-section__icon" aria-hidden="true"><i class="fas fa-file-medical-alt"></i></span>
        <h2 class="bp-dash-section__title" id="bp-section-assessments">تقارير الدعم — التقييمات والملاحظات</h2>
    </div>
    @if($beneficiary->assessments->count() > 0)
    <div class="bp-table-wrap">
        <table class="bp-table">
            <thead><tr><th>تاريخ التقرير</th><th>درجة الأهلية</th><th>ملاحظات</th></tr></thead>
            <tbody>
                @foreach($beneficiary->assessments as $as)
                <tr>
                    <td>{{ $as->assessment_date?->format('Y-m-d') }}</td>
                    <td>{{ $as->eligibility_score !== null ? number_format((float) $as->eligibility_score, 2) : '-' }}</td>
                    <td>{{ $as->notes ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="bp-empty" role="status">
        <i class="fas fa-clipboard-check" aria-hidden="true"></i>
        لا توجد تقارير دعم أو تقييمات مسجَّلة لك حالياً.
    </div>
    @endif
</section>

<section class="bp-dash-section" aria-labelledby="bp-section-requests">
    <div class="bp-dash-section__head">
        <span class="bp-dash-section__icon" aria-hidden="true"><i class="fas fa-clipboard-list"></i></span>
        <h2 class="bp-dash-section__title" id="bp-section-requests">طلباتك</h2>
    </div>

    @if($beneficiary->requests->count() > 0)
    <p class="bp-dash-subtitle">طلبات المستفيد</p>
    <div class="bp-table-wrap" style="margin-bottom: 1.5rem;">
        <table class="bp-table">
            <thead><tr><th>المرجع</th><th>نوع الطلب</th><th>الحالة</th><th>تاريخ التقديم</th></tr></thead>
            <tbody>
                @foreach($beneficiary->requests as $r)
                <tr>
                    <td>#{{ $r->id }}</td>
                    <td>{{ $r->request_type ?? '-' }}</td>
                    <td>
                        @if($r->status === 'approved')
                            <span class="bp-badge bp-badge--success">معتمد</span>
                        @elseif($r->status === 'rejected')
                            <span class="bp-badge bp-badge--danger">مرفوض</span>
                        @elseif($r->status === 'under_study')
                            <span class="bp-badge bp-badge--warn">تحت الدراسة</span>
                        @else
                            <span class="bp-badge bp-badge--muted">جديد</span>
                        @endif
                    </td>
                    <td>{{ $r->submitted_at?->format('Y-m-d') ?? $r->created_at->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($beneficiary->paymentRequests->count() > 0)
    <p class="bp-dash-subtitle">طلبات الصرف والدعم المالي</p>
    <div class="bp-table-wrap">
        <table class="bp-table">
            <thead><tr><th>رقم الطلب</th><th>التاريخ</th><th>المبلغ</th><th>الوصف</th><th>الحالة</th></tr></thead>
            <tbody>
                @foreach($beneficiary->paymentRequests as $pr)
                <tr>
                    <td>{{ $pr->request_no ?? '#' . $pr->id }}</td>
                    <td>{{ $pr->request_date?->format('Y-m-d') ?? '-' }}</td>
                    <td>{{ $pr->amount !== null ? number_format((float) $pr->amount, 2) : '-' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($pr->description ?? '-', 80) }}</td>
                    <td>
                        @switch($pr->status)
                            @case(\App\Models\PaymentRequest::STATUS_APPROVED)
                                <span class="bp-badge bp-badge--success">معتمد</span>
                                @break
                            @case(\App\Models\PaymentRequest::STATUS_REJECTED)
                                <span class="bp-badge bp-badge--danger">مرفوض</span>
                                @break
                            @case(\App\Models\PaymentRequest::STATUS_PAID)
                                <span class="bp-badge bp-badge--info">تم الصرف</span>
                                @break
                            @default
                                <span class="bp-badge bp-badge--muted">قيد المراجعة</span>
                        @endswitch
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($beneficiary->requests->count() === 0 && $beneficiary->paymentRequests->count() === 0)
    <div class="bp-empty" role="status">
        <i class="fas fa-folder-open" aria-hidden="true"></i>
        لا توجد طلبات مسجَّلة لك حالياً.
    </div>
    @endif
</section>
@endsection
