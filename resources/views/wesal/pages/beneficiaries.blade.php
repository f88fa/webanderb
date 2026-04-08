@php
    $formType = $formType ?? 'ben';
    $partialName = $formType === 'ben' ? 'dashboard' : str_replace(['ben-', '-'], ['', '_'], $formType);
@endphp
@include('wesal.pages.beneficiaries.sections.' . $partialName)
