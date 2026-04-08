<div class="content-card">
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1rem;"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    <div class="page-header" style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-paper-plane" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                الخطابات الصادرة
            </h1>
            <p class="page-subtitle">سجل الخطابات والمراسلات الصادرة من المنظمة</p>
        </div>
        <a href="{{ route('wesal.communications.outgoing.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: var(--primary-color); color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
            <i class="fas fa-plus"></i> خطاب صادر جديد
        </a>
    </div>

    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
        <a href="{{ route('wesal.communications.outgoing') }}" style="padding: 0.5rem 1rem; background: var(--primary-color); color: white; border-radius: 6px; text-decoration: none; font-size: 0.9rem;">الصادر</a>
        <a href="{{ route('wesal.communications.incoming') }}" style="padding: 0.5rem 1rem; background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; text-decoration: none; font-size: 0.9rem;">الوارد</a>
    </div>

    <div style="overflow-x: auto; border: 1px solid var(--border-color); border-radius: 8px;">
        <table style="width: 100%; border-collapse: collapse; direction: rtl;">
            <thead>
                <tr style="background: rgba(0,0,0,0.2);">
                    <th style="padding: 0.75rem; text-align: right; color: var(--text-primary); font-weight: 600;">رقم الخطاب</th>
                    <th style="padding: 0.75rem; text-align: right; color: var(--text-primary); font-weight: 600;">الموضوع</th>
                    <th style="padding: 0.75rem; text-align: right; color: var(--text-primary); font-weight: 600;">إلى</th>
                    <th style="padding: 0.75rem; text-align: center; color: var(--text-primary); font-weight: 600;">التاريخ</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($letters ?? []) as $letter)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 0.75rem; color: var(--text-primary);">{{ $letter->letter_no ?: '—' }}</td>
                        <td style="padding: 0.75rem;">
                            <a href="{{ route('wesal.communications.show', $letter) }}" style="color: var(--text-primary); font-weight: 500; text-decoration: none;">{{ $letter->subject }}</a>
                        </td>
                        <td style="padding: 0.75rem; color: var(--text-primary);">{{ $letter->to_party ?: '—' }}</td>
                        <td style="padding: 0.75rem; text-align: center; color: var(--text-secondary); font-size: 0.85rem;">{{ $letter->letter_date?->format('Y-m-d') ?: $letter->created_at->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 2rem; text-align: center; color: var(--text-secondary);">لا توجد خطابات صادرة. يمكنك إضافة خطاب جديد لاحقاً.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($letters) && $letters->hasPages())
        <div style="margin-top: 1rem;">{{ $letters->links() }}</div>
    @endif
</div>
