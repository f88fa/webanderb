@php
    $formType = $formType ?? 'pp';
    $partialName = $formType === 'pp' ? 'dashboard' : str_replace(['pp-', '-'], ['', '_'], $formType);
@endphp
@include('wesal.pages.programs-projects.sections.' . $partialName)
