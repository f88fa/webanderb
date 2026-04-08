<div class="content-card" style="padding: 1.5rem;">
    <div class="page-header" style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-inbox" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                صندوق الوارد
            </h1>
            <p class="page-subtitle">الرسائل الواردة إليك</p>
        </div>
        <a href="{{ route('wesal.e-office.mail.compose') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: var(--primary-color); color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
            <i class="fas fa-pen"></i> رسالة جديدة
        </a>
    </div>

    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
        <a href="{{ route('wesal.e-office.mail.inbox') }}" style="padding: 0.5rem 1rem; background: var(--primary-color); color: white; border-radius: 6px; text-decoration: none; font-size: 0.9rem;">الوارد</a>
        <a href="{{ route('wesal.e-office.mail.sent') }}" style="padding: 0.5rem 1rem; background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; text-decoration: none; font-size: 0.9rem;">المرسل</a>
    </div>

    <div style="overflow-x: auto; border: 1px solid var(--border-color); border-radius: 8px;">
        <table style="width: 100%; border-collapse: collapse; direction: rtl;">
            <thead>
                <tr style="background: rgba(0,0,0,0.2);">
                    <th style="padding: 0.75rem; text-align: right; color: var(--text-primary); font-weight: 600;">المرسل</th>
                    <th style="padding: 0.75rem; text-align: right; color: var(--text-primary); font-weight: 600;">الموضوع</th>
                    <th style="padding: 0.75rem; text-align: center; color: var(--text-primary); font-weight: 600;">التاريخ</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($messages ?? []) as $msg)
                    @php $recipient = $msg->recipients->where('user_id', auth()->id())->first(); @endphp
                    <tr style="border-bottom: 1px solid var(--border-color); {{ $recipient && !$recipient->read_at ? 'background: rgba(95, 179, 142, 0.1);' : '' }}">
                        <td style="padding: 0.75rem; color: var(--text-primary);">{{ $msg->fromUser->name ?? '-' }}</td>
                        <td style="padding: 0.75rem;">
                            <a href="{{ route('wesal.e-office.mail.show', $msg) }}" style="color: var(--text-primary); text-decoration: none; font-weight: {{ $recipient && !$recipient->read_at ? '700' : '400' }};">{{ $msg->subject }}</a>
                            @if($msg->attachments->count() > 0)
                                <i class="fas fa-paperclip" style="color: var(--text-secondary); font-size: 0.75rem; margin-right: 0.25rem;"></i>
                            @endif
                        </td>
                        <td style="padding: 0.75rem; text-align: center; color: var(--text-secondary); font-size: 0.85rem;">{{ $msg->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="padding: 2rem; text-align: center; color: var(--text-secondary);">لا توجد رسائل واردة</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($messages) && $messages->hasPages())
        <div style="margin-top: 1rem;">{{ $messages->links() }}</div>
    @endif
</div>
