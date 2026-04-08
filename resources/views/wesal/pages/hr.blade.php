@php
    $formType = $formType ?? 'hr';
    $partialName = $formType === 'hr' ? 'dashboard' : str_replace(['hr-', '-'], ['', '_'], $formType);
@endphp
@include('wesal.pages.hr.sections.' . $partialName)
