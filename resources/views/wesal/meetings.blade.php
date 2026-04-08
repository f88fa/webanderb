@php
    $formType = $formType ?? 'meetings';
    $partialName = $formType === 'meetings' ? 'dashboard' : str_replace(['meetings-', '-'], ['', '_'], $formType);
@endphp
@include('wesal.pages.meetings.sections.' . $partialName)
