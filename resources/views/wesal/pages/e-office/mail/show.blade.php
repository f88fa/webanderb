<div class="content-card" style="padding: 1.5rem;">
    <div class="page-header" style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title" style="font-size: 1.25rem;">{{ $message->subject }}</h1>
            <p class="page-subtitle" style="margin-top: 0.25rem;">
                من: {{ $message->fromUser->name ?? '-' }} — {{ $message->created_at->format('Y-m-d H:i') }}
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ $isSent ? route('wesal.e-office.mail.sent') : route('wesal.e-office.mail.inbox') }}" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.5rem 1rem; background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 8px; text-decoration: none; font-size: 0.85rem;">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
            <a href="{{ route('wesal.e-office.mail.compose', ['reply_to' => $message->id]) }}" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.5rem 1rem; background: var(--primary-color); color: white; border-radius: 8px; text-decoration: none; font-size: 0.85rem;">
                <i class="fas fa-reply"></i> رد
            </a>
        </div>
    </div>

    <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 1rem;">
        <div style="display: grid; grid-template-columns: auto 1fr; gap: 0.5rem 1rem; font-size: 0.9rem;">
            <span style="color: var(--text-secondary);">من:</span>
            <span style="color: var(--text-primary);">{{ $message->fromUser->name ?? '-' }}</span>
            <span style="color: var(--text-secondary);">إلى:</span>
            <span style="color: var(--text-primary);">
                {{ $message->recipients->where('type', 'to')->map(fn($r) => $r->user->name ?? '')->filter()->implode('، ') ?: '-' }}
            </span>
            @if($message->recipients->where('type', 'cc')->count() > 0)
                <span style="color: var(--text-secondary);">نسخة إلى:</span>
                <span style="color: var(--text-primary);">{{ $message->recipients->where('type', 'cc')->map(fn($r) => $r->user->name ?? '')->filter()->implode('، ') }}</span>
            @endif
        </div>
    </div>

    <div style="background: rgba(255,255,255,0.03); padding: 1.25rem; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 1rem;">
        <div class="mail-message-body" style="color: var(--text-primary); font-size: 0.95rem; line-height: 1.7; direction: rtl; text-align: right;">{!! nl2br(e($message->body)) !!}</div>
    </div>

    @if($message->attachments->count() > 0)
        <div style="margin-top: 1rem;">
            <strong style="color: var(--text-primary); font-size: 0.9rem;"><i class="fas fa-paperclip" style="color: var(--primary-color); margin-left: 0.35rem;"></i> المرفقات:</strong>
            <ul style="margin: 0.5rem 0 0; padding-right: 1.5rem; color: var(--text-primary);">
                @foreach($message->attachments as $att)
                    <li style="margin-bottom: 0.25rem;">
                        <a href="{{ asset('storage/' . $att->path) }}" target="_blank" rel="noopener" style="color: var(--primary-color); text-decoration: none;">{{ $att->original_name }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @php $replies = $replies ?? collect(); @endphp
    @if($replies->count() > 0)
        <div class="mail-replies-wrap" style="margin-top: 1.25rem; border: 1px solid var(--border-color); border-radius: 10px; overflow: hidden;">
            <button type="button" class="mail-replies-toggle" aria-expanded="true" aria-controls="mail-replies-list" style="width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.85rem 1rem; background: rgba(95, 179, 142, 0.15); border: none; color: var(--text-primary); font-size: 0.95rem; font-weight: 600; cursor: pointer; text-align: right;">
                <i class="fas fa-chevron-down mail-replies-chevron" style="transition: transform 0.25s ease; transform: rotate(180deg);"></i>
                <span><i class="fas fa-reply" style="margin-left: 0.35rem; color: var(--primary-color);"></i> الردود ({{ $replies->count() }})</span>
            </button>
            <div id="mail-replies-list" class="mail-replies-list" style="background: rgba(0,0,0,0.15);">
                @foreach($replies as $reply)
                    @php
                        $preview = strip_tags($reply->body);
                        $preview = mb_strlen($preview) > 80 ? mb_substr($preview, 0, 80) . '…' : $preview;
                    @endphp
                    <a href="{{ route('wesal.e-office.mail.show', $reply) }}" class="mail-reply-item" style="display: block; padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); text-decoration: none; color: inherit; transition: background 0.2s;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                            <span style="color: var(--primary-color); font-weight: 600; font-size: 0.9rem;">{{ $reply->fromUser->name ?? '-' }}</span>
                            <span style="color: var(--text-secondary); font-size: 0.8rem;">{{ $reply->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <p style="margin: 0; color: var(--text-secondary); font-size: 0.85rem; line-height: 1.4;">{{ $preview ?: '—' }}</p>
                    </a>
                @endforeach
            </div>
        </div>
        <style>.mail-reply-item:hover { background: rgba(255,255,255,0.06) !important; }</style>
        <script>
        (function() {
            var btn = document.querySelector('.mail-replies-toggle');
            var list = document.getElementById('mail-replies-list');
            var chevron = document.querySelector('.mail-replies-chevron');
            if (btn && list) {
                btn.addEventListener('click', function() {
                    var open = list.hidden;
                    list.hidden = !open;
                    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
                    if (chevron) chevron.style.transform = open ? 'rotate(180deg)' : 'rotate(0deg)';
                });
            }
        })();
        </script>
    @endif
</div>
