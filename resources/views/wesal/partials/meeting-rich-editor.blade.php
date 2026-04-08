@php
    $name = $name ?? 'content';
    $placeholder = $placeholder ?? 'اكتب هنا...';
    $content = $content ?? '';
    $id = 'meeting-editor-' . preg_replace('/[^a-z0-9]/', '-', $name);
@endphp
<input type="hidden" name="{{ $name }}" id="{{ $id }}-input" value="{{ e($content) }}">
<div class="meeting-editor-wrap" data-field="{{ $name }}" style="background: rgba(255,255,255,0.08); border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden;">
    <div class="mail-editor-toolbar">
        <button type="button" class="mail-editor-btn" data-cmd="bold" title="عريض"><i class="fas fa-bold"></i></button>
        <button type="button" class="mail-editor-btn" data-cmd="italic" title="مائل"><i class="fas fa-italic"></i></button>
        <button type="button" class="mail-editor-btn" data-cmd="underline" title="تحته خط"><i class="fas fa-underline"></i></button>
        <button type="button" class="mail-editor-btn" data-cmd="strikeThrough" title="يتوسطه خط"><i class="fas fa-strikethrough"></i></button>
        <span class="mail-editor-sep"></span>
        <button type="button" class="mail-editor-btn" data-cmd="insertUnorderedList" title="قائمة نقطية"><i class="fas fa-list-ul"></i></button>
        <button type="button" class="mail-editor-btn" data-cmd="insertOrderedList" title="قائمة مرقمة"><i class="fas fa-list-ol"></i></button>
        <span class="mail-editor-sep"></span>
        <button type="button" class="mail-editor-btn" data-cmd="createLink" title="رابط"><i class="fas fa-link"></i></button>
        <span class="mail-editor-sep"></span>
        <div class="mail-editor-color-wrap">
            <button type="button" class="mail-editor-btn" data-color="fore" title="لون النص"><i class="fas fa-font"></i></button>
            <div class="mail-color-palette" data-cmd="foreColor" aria-hidden="true"></div>
        </div>
        <div class="mail-editor-color-wrap">
            <button type="button" class="mail-editor-btn" data-color="back" title="تمييز"><i class="fas fa-highlighter"></i></button>
            <div class="mail-color-palette" data-cmd="backColor" aria-hidden="true"></div>
        </div>
        <span class="mail-editor-sep"></span>
        <button type="button" class="mail-editor-btn" data-cmd="justifyRight" title="محاذاة لليمين"><i class="fas fa-align-right"></i></button>
        <button type="button" class="mail-editor-btn" data-cmd="justifyCenter" title="توسيط"><i class="fas fa-align-center"></i></button>
    </div>
    <div id="{{ $id }}-body" class="meeting-editor-body" contenteditable="true" data-placeholder="{{ $placeholder }}" style="min-height: {{ $minHeight ?? 140 }}px; padding: 0.75rem 1rem; color: var(--text-primary); font-size: 0.95rem; direction: rtl; text-align: right; outline: none;"></div>
</div>
