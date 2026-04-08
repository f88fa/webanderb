@extends('beneficiary-portal.layout')

@section('title', 'طلبك قيد المراجعة')

@section('content')
<div class="bp-header">
    <h1><i class="fas fa-hourglass-half" style="color: var(--bp-primary);"></i> طلبك قيد المراجعة</h1>
    <p>تم استلام طلبك بنجاح. يجري حالياً مراجعة بياناتك، وسيتم إعلامك فور اتخاذ القرار.</p>
</div>
@if($registrationRequest)
<div class="bp-dash-callout">
    <h3>بيانات الطلب</h3>
    <p><strong>الاسم:</strong> {{ $registrationRequest->name_ar }}</p>
    <p><strong>البريد:</strong> {{ $registrationRequest->email }}</p>
    <p><strong>تاريخ التقديم:</strong> {{ $registrationRequest->created_at->format('Y-m-d H:i') }}</p>
</div>
@endif
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="bp-btn bp-btn-secondary"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</button>
</form>
@endsection
