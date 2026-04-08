<div class="content-card" style="padding: 1.5rem;">
    <div class="page-header" style="margin-bottom: 1.5rem;">
        <h1 class="page-title">
            <i class="fas fa-pen-square" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            رسالة جديدة
        </h1>
        <p class="page-subtitle">إرسال رسالة داخلية إلى مستخدم مسجل في النظام</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1rem; background: rgba(76, 175, 80, 0.2); color: #a5d6a7; padding: 0.75rem; border-radius: 8px; border: 1px solid var(--primary-color);">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 1rem; background: rgba(244,67,54,0.15); color: #ff8a80; padding: 0.75rem; border-radius: 8px; border: 1px solid rgba(244,67,54,0.4);">
            <ul style="margin: 0; padding-right: 1.25rem;">
                @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
        </div>
    @endif

    @php
            $usersList = $users ?? [];
            $replyToMessage = $replyToMessage ?? null;
            $replyToIds = [];
            if ($replyToMessage) {
                if ($replyToMessage->from_user_id === auth()->id()) {
                    $replyToIds = $replyToMessage->recipients->where('type', 'to')->pluck('user_id')->all();
                } else {
                    $replyToIds = [$replyToMessage->from_user_id];
                }
            }
            $oldTo = old('to', $replyToIds);
            $oldCc = old('cc', []);
            $defaultSubject = old('subject', $replyToMessage ? 'Re: ' . $replyToMessage->subject : '');
        @endphp
        <form method="POST" action="{{ route('wesal.e-office.mail.store') }}" id="mail-compose-form" enctype="multipart/form-data" style="max-width: 800px;">
        @csrf
        @if(!empty($replyToMessage))
            <input type="hidden" name="reply_to" value="{{ $replyToMessage->id }}">
        @endif
        <div style="background: rgba(255,255,255,0.05); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 1rem;">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.35rem; color: var(--text-primary); font-weight: 600; font-size: 0.9rem;">
                    <i class="fas fa-user" style="color: var(--primary-color); margin-left: 0.35rem;"></i> إلى: <span style="color: #ff8a80;">*</span>
                </label>
                <div class="mail-multiselect" data-name="to" data-required="1">
                    <div class="mail-multiselect-input-wrap" style="position: relative;">
                        <input type="text" class="mail-multiselect-search" placeholder="ابحث بالاسم واختر المستلمين..." autocomplete="off" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(255,255,255,0.1); color: var(--text-primary); font-size: 0.9rem;">
                        <div class="mail-multiselect-dropdown" style="display: none; position: absolute; top: 100%; right: 0; left: 0; margin-top: 2px; max-height: 220px; overflow-y: auto; background: var(--sidebar-bg); border: 1px solid var(--border-color); border-radius: 8px; z-index: 100; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                            @foreach($usersList as $u)
                                <div class="mail-multiselect-option" data-id="{{ $u->id }}" data-name="{{ $u->name }}" style="padding: 0.5rem 0.75rem; cursor: pointer; color: var(--text-primary); font-size: 0.9rem; border-bottom: 1px solid var(--border-color);" tabindex="0">{{ $u->name }}</div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mail-multiselect-tags" style="display: flex; flex-wrap: wrap; gap: 0.35rem; margin-top: 0.5rem;"></div>
                    <div class="mail-multiselect-hidden"></div>
                </div>
                <small style="color: var(--text-secondary); font-size: 0.75rem;">ابحث واختر أكثر من مستلم</small>
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.35rem; color: var(--text-primary); font-weight: 600; font-size: 0.9rem;">
                    <i class="fas fa-copy" style="color: var(--primary-color); margin-left: 0.35rem;"></i> نسخة إلى:
                </label>
                <div class="mail-multiselect" data-name="cc" data-required="0">
                    <div class="mail-multiselect-input-wrap" style="position: relative;">
                        <input type="text" class="mail-multiselect-search" placeholder="ابحث بالاسم واختر نسخة إلى..." autocomplete="off" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(255,255,255,0.1); color: var(--text-primary); font-size: 0.9rem;">
                        <div class="mail-multiselect-dropdown" style="display: none; position: absolute; top: 100%; right: 0; left: 0; margin-top: 2px; max-height: 220px; overflow-y: auto; background: var(--sidebar-bg); border: 1px solid var(--border-color); border-radius: 8px; z-index: 100; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                            @foreach($usersList as $u)
                                <div class="mail-multiselect-option" data-id="{{ $u->id }}" data-name="{{ $u->name }}" style="padding: 0.5rem 0.75rem; cursor: pointer; color: var(--text-primary); font-size: 0.9rem; border-bottom: 1px solid var(--border-color);" tabindex="0">{{ $u->name }}</div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mail-multiselect-tags" style="display: flex; flex-wrap: wrap; gap: 0.35rem; margin-top: 0.5rem;"></div>
                    <div class="mail-multiselect-hidden"></div>
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.35rem; color: var(--text-primary); font-weight: 600; font-size: 0.9rem;">
                    <i class="fas fa-heading" style="color: var(--primary-color); margin-left: 0.35rem;"></i> عنوان الرسالة: <span style="color: #ff8a80;">*</span>
                </label>
                <input type="text" name="subject" value="{{ $defaultSubject }}" required placeholder="عنوان الرسالة" style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(255,255,255,0.1); color: var(--text-primary); font-size: 0.9rem;">
            </div>
        </div>

        <div style="background: rgba(255,255,255,0.05); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.35rem; color: var(--text-primary); font-weight: 600; font-size: 0.9rem;">
                <i class="fas fa-align-right" style="color: var(--primary-color); margin-left: 0.35rem;"></i> نص الرسالة: <span style="color: #ff8a80;">*</span>
            </label>
            <input type="hidden" name="body" id="mail-body-input" value="{{ e(old('body', '')) }}">
            <div class="mail-editor-wrap" style="background: rgba(255,255,255,0.08); border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden;">
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
                        <div class="mail-color-palette" id="mail-palette-fore" data-cmd="foreColor" aria-hidden="true"></div>
                    </div>
                    <div class="mail-editor-color-wrap">
                        <button type="button" class="mail-editor-btn" data-color="back" title="تمييز"><i class="fas fa-highlighter"></i></button>
                        <div class="mail-color-palette" id="mail-palette-back" data-cmd="backColor" aria-hidden="true"></div>
                    </div>
                    <span class="mail-editor-sep"></span>
                    <button type="button" class="mail-editor-btn" data-cmd="justifyRight" title="محاذاة لليمين"><i class="fas fa-align-right"></i></button>
                    <button type="button" class="mail-editor-btn" data-cmd="justifyCenter" title="توسيط"><i class="fas fa-align-center"></i></button>
                </div>
                <div id="mail-body-editor" class="mail-editor-body" contenteditable="true" data-placeholder="اكتب رسالتك هنا..." style="min-height: 220px; padding: 0.75rem 1rem; color: var(--text-primary); font-size: 0.95rem; direction: rtl; text-align: right; outline: none;"></div>
            </div>
        </div>

        <div style="background: rgba(255,255,255,0.05); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.35rem; color: var(--text-primary); font-weight: 600; font-size: 0.9rem;">
                <i class="fas fa-paperclip" style="color: var(--primary-color); margin-left: 0.35rem;"></i> مرفق (اختياري)
            </label>
            <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip" style="width: 100%; padding: 0.5rem; border: 1px dashed var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.15); color: var(--text-primary);">
            <small style="color: var(--text-secondary); font-size: 0.75rem;">يمكنك اختيار أكثر من ملف. الحجم الأقصى 10 ميجا للملف.</small>
        </div>

        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.5rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                <i class="fas fa-paper-plane"></i> إرسال
            </button>
            <a href="{{ route('wesal.e-office.mail.inbox') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.5rem; background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 8px; text-decoration: none; font-weight: 500;">إلغاء</a>
        </div>
    </form>
</div>

<style>
    .mail-editor-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 2px; padding: 6px 8px; border-bottom: 1px solid var(--border-color); background: rgba(0,0,0,0.2); }
    .mail-editor-btn { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 30px; padding: 0; border: 1px solid var(--border-color); border-radius: 6px; background: rgba(255,255,255,0.08); color: var(--text-primary); cursor: pointer; font-size: 0.9rem; }
    .mail-editor-btn:hover { background: rgba(255,255,255,0.15); }
    .mail-editor-sep { width: 1px; height: 20px; background: var(--border-color); margin: 0 2px; flex-shrink: 0; }
    .mail-editor-color-wrap { position: relative; }
    .mail-color-palette { position: absolute; top: 100%; right: 0; margin-top: 4px; padding: 6px; background: var(--card-bg, #2a2a2e); border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); display: none; flex-wrap: wrap; gap: 4px; width: 136px; z-index: 50; }
    .mail-color-palette[aria-hidden="false"] { display: flex; }
    .mail-color-swatch { width: 20px; height: 20px; border-radius: 4px; border: 1px solid rgba(255,255,255,0.2); cursor: pointer; padding: 0; flex-shrink: 0; }
    .mail-color-swatch:hover { transform: scale(1.1); }
    .mail-editor-body:empty::before { content: attr(data-placeholder); color: var(--text-secondary); }
</style>
<script>
(function() {
    var bodyContent = @json(old('body', ''));
    var editor = document.getElementById('mail-body-editor');
    var hiddenInput = document.getElementById('mail-body-input');
    if (editor) {
        if (bodyContent) { editor.innerHTML = bodyContent; hiddenInput.value = bodyContent; }
        var savedRange = null;
        function saveSelection() {
            var sel = window.getSelection();
            if (sel.rangeCount && editor.contains(sel.anchorNode)) {
                savedRange = sel.getRangeAt(0).cloneRange();
            }
        }
        function restoreSelection() {
            if (savedRange) {
                var sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(savedRange);
            }
        }
        editor.addEventListener('mouseup', saveSelection);
        editor.addEventListener('blur', saveSelection);
        function applyCommand(cmd, value) {
            editor.focus();
            restoreSelection();
            document.execCommand(cmd, false, value || null);
            hiddenInput.value = editor.innerHTML;
        }
        var colors = ['#000000','#434343','#666666','#999999','#b7b7b7','#cccccc','#d9d9d9','#efefef','#ffffff','#980000','#ff0000','#ff9900','#ffff00','#00ff00','#00ffff','#4a86e8','#0000ff','#9900ff','#ff00ff','#e6b8af','#f4cccc','#fce5cd','#fff2cc','#d9ead3','#d0e0e3','#c9daf8','#cfe2f3','#d9d2e9','#ead1dc','#dd7e6b','#ea9999','#f9cb9c','#ffe599','#b6d7a8','#a2c4c9','#a4c2f4','#9fc5e8','#b4a7d6','#d5a6bd'];
        function buildPalette(el) {
            if (el.querySelector('.mail-color-swatch')) return;
            colors.forEach(function(hex) {
                var sw = document.createElement('button');
                sw.type = 'button';
                sw.className = 'mail-color-swatch';
                sw.style.background = hex;
                sw.title = hex;
                sw.dataset.hex = hex;
                el.appendChild(sw);
            });
        }
        function closePalettes() {
            var open = document.querySelector('.mail-color-palette[aria-hidden="false"]');
            if (open) open.setAttribute('aria-hidden', 'true');
        }
        document.querySelectorAll('.mail-editor-btn[data-color]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                var id = 'mail-palette-' + this.dataset.color;
                var pal = document.getElementById(id);
                if (!pal) return;
                buildPalette(pal);
                var isOpen = pal.getAttribute('aria-hidden') === 'false';
                closePalettes();
                if (!isOpen) pal.setAttribute('aria-hidden', 'false');
            });
        });
        document.querySelectorAll('.mail-color-palette').forEach(function(pal) {
            pal.addEventListener('click', function(e) { e.stopPropagation(); });
            pal.addEventListener('click', function(e) {
                var sw = e.target.closest('.mail-color-swatch');
                if (!sw) return;
                e.preventDefault();
                e.stopPropagation();
                applyCommand(this.getAttribute('data-cmd'), sw.dataset.hex);
                closePalettes();
            });
        });
        document.addEventListener('click', function() {
            if (document.querySelector('.mail-color-palette[aria-hidden="false"]')) closePalettes();
        });
        document.querySelectorAll('.mail-editor-btn[data-cmd]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var cmd = this.getAttribute('data-cmd');
                if (cmd === 'createLink') {
                    var url = prompt('أدخل الرابط:', 'https://');
                    if (url) applyCommand(cmd, url);
                } else {
                    applyCommand(cmd);
                }
            });
        });
        var inputTimer = null;
        editor.addEventListener('input', function() {
            if (inputTimer) clearTimeout(inputTimer);
            inputTimer = setTimeout(function() { hiddenInput.value = editor.innerHTML; inputTimer = null; }, 120);
        });
        editor.addEventListener('paste', function() { if (inputTimer) clearTimeout(inputTimer); hiddenInput.value = editor.innerHTML; inputTimer = null; });
        document.getElementById('mail-compose-form').addEventListener('submit', function(e) {
            hiddenInput.value = editor.innerHTML;
            var text = (editor.innerText || '').trim();
            if (!text) {
                e.preventDefault();
                alert('يرجى كتابة نص الرسالة');
                return;
            }
        });
    }
})();
</script>
<script>
(function() {
    @php
        $usersArray = collect($usersList)->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->values()->all();
    @endphp
    var usersTo = @json($usersArray);
    var usersCc = @json($usersArray);
    var oldTo = @json($oldTo);
    var oldCc = @json($oldCc);

    function initMultiselect(container, options, initialIds) {
        var wrap = container.querySelector('.mail-multiselect-input-wrap');
        var search = container.querySelector('.mail-multiselect-search');
        var dropdown = container.querySelector('.mail-multiselect-dropdown');
        var optionsAll = container.querySelectorAll('.mail-multiselect-option');
        var tagsEl = container.querySelector('.mail-multiselect-tags');
        var hiddenEl = container.querySelector('.mail-multiselect-hidden');
        var name = container.getAttribute('data-name');
        var selected = {};
        initialIds.forEach(function(id) {
            var opt = options.find(function(o) { return o.id == id; });
            if (opt) selected[opt.id] = opt.name;
        });

        function renderHidden() {
            hiddenEl.innerHTML = '';
            Object.keys(selected).forEach(function(id) {
                var inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = name + '[]';
                inp.value = id;
                hiddenEl.appendChild(inp);
            });
        }
        function renderTags() {
            tagsEl.innerHTML = '';
            Object.keys(selected).forEach(function(id) {
                var tag = document.createElement('span');
                tag.className = 'mail-multiselect-tag';
                tag.style.cssText = 'display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: rgba(95, 179, 142, 0.3); color: var(--text-primary); border-radius: 6px; font-size: 0.8rem;';
                tag.textContent = selected[id];
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.innerHTML = '&times;';
                btn.style.cssText = 'background: none; border: none; color: inherit; cursor: pointer; padding: 0; font-size: 1rem; line-height: 1;';
                btn.addEventListener('click', function(e) { e.preventDefault(); delete selected[id]; renderTags(); renderHidden(); });
                tag.appendChild(btn);
                tagsEl.appendChild(tag);
            });
        }
        function filterList(q) {
            q = (q || '').trim().toLowerCase();
            for (var i = 0; i < optionsAll.length; i++) {
                var opt = optionsAll[i];
                if (selected[opt.getAttribute('data-id')]) { opt.style.display = 'none'; continue; }
                var name = (opt.getAttribute('data-name') || '').toLowerCase();
                opt.style.display = !q || name.indexOf(q) !== -1 ? '' : 'none';
            }
        }
        function openDropdown() {
            dropdown.style.display = 'block';
            filterList(search.value);
        }
        function closeDropdown() {
            dropdown.style.display = 'none';
        }
        var filterTimer = null;
        optionsAll.forEach(function(opt) {
            opt.addEventListener('click', function() {
                var id = this.getAttribute('data-id');
                var n = this.getAttribute('data-name');
                if (selected[id]) { delete selected[id]; } else { selected[id] = n; }
                renderTags();
                renderHidden();
                filterList(search.value);
            });
        });
        search.addEventListener('focus', openDropdown);
        search.addEventListener('input', function() {
            if (filterTimer) clearTimeout(filterTimer);
            dropdown.style.display = 'block';
            filterTimer = setTimeout(function() { filterList(search.value); filterTimer = null; }, 80);
        });
        search.addEventListener('blur', function() { setTimeout(closeDropdown, 200); });
        dropdown.addEventListener('mousedown', function(e) { e.preventDefault(); });
        document.addEventListener('click', function(e) {
            if (!container.contains(e.target)) closeDropdown();
        });
        renderTags();
        renderHidden();
    }

    document.querySelectorAll('.mail-multiselect').forEach(function(container) {
        var name = container.getAttribute('data-name');
        var options = name === 'to' ? usersTo : usersCc;
        var initial = name === 'to' ? oldTo : oldCc;
        initMultiselect(container, options, initial);
    });

    document.getElementById('mail-compose-form').addEventListener('submit', function(e) {
        var toContainer = document.querySelector('.mail-multiselect[data-name="to"]');
        var required = toContainer.getAttribute('data-required') === '1';
        var count = toContainer.querySelectorAll('.mail-multiselect-hidden input').length;
        if (required && count === 0) {
            e.preventDefault();
            alert('يجب اختيار مستلم واحد على الأقل');
        }
    });
})();
</script>
