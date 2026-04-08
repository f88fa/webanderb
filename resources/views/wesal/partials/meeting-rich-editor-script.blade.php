<style>
.meeting-editor-wrap .mail-editor-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 2px; padding: 6px 8px; border-bottom: 1px solid var(--border-color); background: rgba(0,0,0,0.2); }
.meeting-editor-wrap .mail-editor-btn { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 30px; padding: 0; border: 1px solid var(--border-color); border-radius: 6px; background: rgba(255,255,255,0.08); color: var(--text-primary); cursor: pointer; font-size: 0.9rem; }
.meeting-editor-wrap .mail-editor-btn:hover { background: rgba(255,255,255,0.15); }
.meeting-editor-wrap .mail-editor-sep { width: 1px; height: 20px; background: var(--border-color); margin: 0 2px; flex-shrink: 0; }
.meeting-editor-wrap .mail-editor-color-wrap { position: relative; }
.meeting-editor-wrap .mail-color-palette { position: absolute; top: 100%; right: 0; margin-top: 4px; padding: 6px; background: var(--card-bg, #2a2a2e); border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); display: none; flex-wrap: wrap; gap: 4px; width: 136px; z-index: 50; }
.meeting-editor-wrap .mail-color-palette[aria-hidden="false"] { display: flex; }
.meeting-editor-wrap .mail-color-swatch { width: 20px; height: 20px; border-radius: 4px; border: 1px solid rgba(255,255,255,0.2); cursor: pointer; padding: 0; flex-shrink: 0; }
.meeting-editor-wrap .mail-color-swatch:hover { transform: scale(1.1); }
.meeting-editor-body:empty::before { content: attr(data-placeholder); color: var(--text-secondary); }
</style>
<script>
(function() {
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
    document.querySelectorAll('.meeting-editor-wrap').forEach(function(wrap) {
        var body = wrap.querySelector('.meeting-editor-body');
        var input = document.getElementById(body.id.replace('-body','-input'));
        if (!body || !input) return;
        var content = input.value || '';
        if (content) { body.innerHTML = content; }
        var savedRange = null;
        function saveSelection() {
            var sel = window.getSelection();
            if (sel.rangeCount && body.contains(sel.anchorNode)) savedRange = sel.getRangeAt(0).cloneRange();
        }
        function restoreSelection() {
            if (savedRange) {
                var sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(savedRange);
            }
        }
        function applyCommand(cmd, val) {
            body.focus();
            restoreSelection();
            document.execCommand(cmd, false, val || null);
            input.value = body.innerHTML;
        }
        body.addEventListener('mouseup', saveSelection);
        body.addEventListener('blur', saveSelection);
        wrap.querySelectorAll('.mail-editor-btn[data-cmd]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var cmd = this.getAttribute('data-cmd');
                if (cmd === 'createLink') {
                    var url = prompt('أدخل الرابط:', 'https://');
                    if (url) applyCommand(cmd, url);
                } else applyCommand(cmd);
            });
        });
        wrap.querySelectorAll('.mail-editor-btn[data-color]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                var pal = this.nextElementSibling;
                if (!pal) return;
                buildPalette(pal);
                var open = wrap.querySelector('.mail-color-palette[aria-hidden="false"]');
                if (open && open !== pal) open.setAttribute('aria-hidden','true');
                pal.setAttribute('aria-hidden', pal.getAttribute('aria-hidden') === 'false' ? 'true' : 'false');
            });
        });
        wrap.querySelectorAll('.mail-color-palette').forEach(function(pal) {
            pal.addEventListener('click', function(e) {
                var sw = e.target.closest('.mail-color-swatch');
                if (!sw) return;
                e.preventDefault();
                e.stopPropagation();
                applyCommand(this.getAttribute('data-cmd'), sw.dataset.hex);
                pal.setAttribute('aria-hidden','true');
            });
        });
        var inputTimer = null;
        body.addEventListener('input', function() {
            if (inputTimer) clearTimeout(inputTimer);
            inputTimer = setTimeout(function() { input.value = body.innerHTML; inputTimer = null; }, 120);
        });
        body.addEventListener('paste', function() { if (inputTimer) clearTimeout(inputTimer); input.value = body.innerHTML; inputTimer = null; });
        var form = wrap.closest('form');
        if (form) form.addEventListener('submit', function() { input.value = body.innerHTML; });
    });
})();
</script>
