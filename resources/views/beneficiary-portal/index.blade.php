@extends('beneficiary-portal.layout')

@section('title', 'بوابة المستفيدين')

@section('content')
<div class="bp-header">
    <h1><i class="fas fa-users" style="color: var(--bp-primary); margin-left: 0.5rem;"></i> بوابة المستفيدين</h1>
    <p>مرحباً بك في بوابة المستفيدين. يمكنك تسجيل الدخول لمتابعة طلباتك وسجلك، أو التسجيل كمستفيد جديد.</p>
</div>
<div class="bp-nav">
    <a href="{{ route('beneficiary-portal.login') }}"><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</a>
    <a href="{{ route('beneficiary-portal.register') }}"><i class="fas fa-user-plus"></i> تسجيل مستفيد جديد</a>
</div>
@endsection
