<div class="content-card" style="padding: 1.5rem;">
    <div class="page-header" style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-tasks" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                المهام
            </h1>
            <p class="page-subtitle">المهام المعنية بك (منشأة بواسطتك أو مسندة إليك)</p>
        </div>
        <a href="{{ route('wesal.e-office.tasks.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: var(--primary-color); color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
            <i class="fas fa-plus"></i> مهمة جديدة
        </a>
    </div>

    @if(session('success'))
        <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: rgba(95, 179, 142, 0.2); border: 1px solid var(--primary-color); border-radius: 8px; color: var(--text-primary);">
            <i class="fas fa-check-circle" style="margin-left: 0.5rem;"></i>{{ session('success') }}
        </div>
    @endif

    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
        <a href="{{ route('wesal.e-office.tasks.index') }}" style="padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-size: 0.9rem; {{ !request('status') ? 'background: var(--primary-color); color: white;' : 'background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color);' }}">الكل</a>
        <a href="{{ route('wesal.e-office.tasks.index', ['status' => 'open']) }}" style="padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-size: 0.9rem; {{ request('status') === 'open' ? 'background: var(--primary-color); color: white;' : 'background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color);' }}">مفتوحة</a>
        <a href="{{ route('wesal.e-office.tasks.index', ['status' => 'closed']) }}" style="padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-size: 0.9rem; {{ request('status') === 'closed' ? 'background: var(--primary-color); color: white;' : 'background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color);' }}">مغلقة</a>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; direction: rtl;">
            <thead>
                <tr style="background: rgba(0,0,0,0.2);">
                    <th style="padding: 0.75rem; text-align: right; color: var(--text-primary); font-weight: 600;">الموضوع</th>
                    <th style="padding: 0.75rem; text-align: right; color: var(--text-primary); font-weight: 600;">المسؤولون</th>
                    <th style="padding: 0.75rem; text-align: center; color: var(--text-primary); font-weight: 600;">موعد الإنجاز</th>
                    <th style="padding: 0.75rem; text-align: center; color: var(--text-primary); font-weight: 600;">المتبقي</th>
                    <th style="padding: 0.75rem; text-align: center; color: var(--text-primary); font-weight: 600;">الحالة</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($tasks ?? []) as $task)
                    @php
                        $due = $task->due_at;
                        $remaining = $task->status === 'open' && $due->isFuture() ? $due->diffForHumans(null, true, true) : ($task->status === 'closed' ? '—' : 'منتهية');
                    @endphp
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 0.75rem;">
                            <a href="{{ route('wesal.e-office.tasks.show', $task) }}" style="color: var(--text-primary); text-decoration: none; font-weight: 500;">{{ $task->subject }}</a>
                        </td>
                        <td style="padding: 0.75rem; color: var(--text-secondary); font-size: 0.9rem;">{{ $task->assignees->pluck('name')->implode('، ') }}</td>
                        <td style="padding: 0.75rem; text-align: center; color: var(--text-secondary); font-size: 0.85rem;">{{ $task->due_at->format('Y-m-d H:i') }}</td>
                        <td style="padding: 0.75rem; text-align: center; font-size: 0.85rem; {{ $task->status === 'open' && $due->isPast() ? 'color: #ff8a80;' : 'color: var(--text-secondary);' }}">{{ $remaining }}</td>
                        <td style="padding: 0.75rem; text-align: center;">
                            @if($task->status === 'open')
                                <span style="padding: 0.25rem 0.5rem; background: rgba(95, 179, 142, 0.3); color: var(--primary-color); border-radius: 6px; font-size: 0.8rem;">مفتوحة</span>
                            @else
                                <span style="padding: 0.25rem 0.5rem; background: rgba(255,255,255,0.1); color: var(--text-secondary); border-radius: 6px; font-size: 0.8rem;">مغلقة</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 2rem; text-align: center; color: var(--text-secondary);">لا توجد مهام معنية بك</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($tasks) && $tasks->hasPages())
        <div style="margin-top: 1rem;">{{ $tasks->links() }}</div>
    @endif
</div>
