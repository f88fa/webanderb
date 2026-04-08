@php $sec = $requestSection ?? 'leave'; @endphp
@if($sec === 'leave-show')
    @include('wesal.pages.hr.sections.leave_show')
@elseif($sec === 'leave')
    @include('wesal.pages.requests.leave')
@elseif($sec === 'general')
    @include('wesal.pages.requests.general')
@elseif($sec === 'financial')
    @include('wesal.pages.requests.financial')
@elseif($sec === 'attendance')
    @include('wesal.pages.requests.attendance')
@else
    @include('wesal.pages.requests.leave')
@endif
