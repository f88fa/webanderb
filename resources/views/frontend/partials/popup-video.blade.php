@php
    $popupEnabled = ($settings['popup_video_enabled'] ?? '0') == '1';
    $popupUrl = trim($settings['popup_video_url'] ?? '');
    $popupVideoId = $popupUrl ? youtube_video_id($popupUrl) : null;
    $popupVideoFile = trim($settings['popup_video_file'] ?? '');
    $popupVideoFileUrl = $popupVideoFile ? image_asset_url($popupVideoFile) : '';
    $hasVideo = $popupVideoId || $popupVideoFileUrl;
    $popupPosition = ($settings['popup_video_position'] ?? 'right') === 'left' ? 'left' : 'right';
    $popupSize = in_array($settings['popup_video_size'] ?? 'medium', ['small', 'medium', 'large'], true) ? ($settings['popup_video_size'] ?? 'medium') : 'medium';
@endphp
@if($popupEnabled && $hasVideo)
<div id="frontend-popup-video" class="frontend-popup-video frontend-popup-video--{{ $popupPosition }} frontend-popup-video--size-{{ $popupSize }}" role="dialog" aria-label="فيديو منبثق" data-video-key="{{ $popupVideoId ?: 'file-' . md5($popupVideoFile) }}">
    <div class="frontend-popup-video__box frontend-popup-video__box--{{ $popupSize }}">
        <button type="button" class="frontend-popup-video__close" id="frontend-popup-video-close" aria-label="إغلاق">
            <i class="fas fa-times"></i>
        </button>
        @if($popupVideoId)
        <div class="frontend-popup-video__iframe-wrap">
            <iframe src="https://www.youtube.com/embed/{{ $popupVideoId }}?autoplay=1&mute=1" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
        @else
        @php
            $ext = strtolower(pathinfo($popupVideoFile, PATHINFO_EXTENSION));
            $mime = $ext === 'webm' ? 'video/webm' : ($ext === 'ogg' ? 'video/ogg' : 'video/mp4');
        @endphp
        <div class="frontend-popup-video__video-wrap">
            <video autoplay muted loop playsinline>
                <source src="{{ $popupVideoFileUrl }}" type="{{ $mime }}">
            </video>
        </div>
        @endif
    </div>
</div>
<script>
(function() {
    var popup = document.getElementById('frontend-popup-video');
    var closeBtn = document.getElementById('frontend-popup-video-close');
    if (!popup || !closeBtn) return;
    var forceShow = typeof URLSearchParams !== 'undefined' && new URLSearchParams(window.location.search).get('show_popup') === '1';
    var scrollThreshold = 150;
    function closePopup() {
        popup.classList.add('frontend-popup-video--hidden');
    }
    closeBtn.addEventListener('click', closePopup);
    function showOnScroll() {
        var scrolled = window.pageYOffset || document.documentElement.scrollTop;
        if (scrolled >= scrollThreshold) {
            popup.classList.remove('frontend-popup-video--waiting');
            window.removeEventListener('scroll', showOnScroll);
        }
    }
    if (forceShow) {
        showOnScroll();
    } else {
        popup.classList.add('frontend-popup-video--waiting');
        window.addEventListener('scroll', showOnScroll);
    }
})();
</script>
@endif
