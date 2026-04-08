<div class="content-card" style="padding: 1.5rem;">
    <div class="page-header" style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title" style="font-size: 1.25rem;">{{ $task->subject }}</h1>
            <p class="page-subtitle" style="margin-top: 0.25rem;">
                أنشأها: {{ $task->creator->name ?? '-' }} — {{ $task->created_at->format('Y-m-d H:i') }}
                @if($task->isClosed() && $task->closedByUser)
                    <span style="color: var(--text-secondary);"> | أغلقت بواسطة {{ $task->closedByUser->name }} في {{ $task->closed_at->format('Y-m-d H:i') }}</span>
                @endif
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('wesal.e-office.tasks.index') }}" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.5rem 1rem; background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 8px; text-decoration: none; font-size: 0.85rem;">رجوع</a>
            @if($task->isOpen())
                <a href="{{ route('wesal.e-office.tasks.create') }}" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.5rem 1rem; background: var(--primary-color); color: white; border-radius: 8px; text-decoration: none; font-size: 0.85rem;">مهمة جديدة</a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: rgba(95, 179, 142, 0.2); border: 1px solid var(--primary-color); border-radius: 8px; color: var(--text-primary);">
            <i class="fas fa-check-circle" style="margin-left: 0.5rem;"></i>{{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: rgba(244,67,54,0.15); color: #ff8a80; border-radius: 8px;">
            <ul style="margin: 0; padding-right: 1.25rem;">@foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach</ul>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color);">
            <span style="color: var(--text-secondary); font-size: 0.85rem;">موعد الإنجاز</span>
            <div style="color: var(--text-primary); font-weight: 600; margin-top: 0.25rem;">{{ $task->due_at->format('Y-m-d H:i') }}</div>
        </div>
        <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color);">
            <span style="color: var(--text-secondary); font-size: 0.85rem;">المتبقي</span>
            <div style="margin-top: 0.25rem; {{ $task->isOpen() && $task->due_at->isPast() ? 'color: #ff8a80;' : 'color: var(--primary-color);' }} font-weight: 600;">
                @if($task->isClosed())
                    —
                @elseif($task->due_at->isPast())
                    منتهية (تأخر)
                @else
                    {{ $task->due_at->diffForHumans(null, true, true) }}
                @endif
            </div>
        </div>
        <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color);">
            <span style="color: var(--text-secondary); font-size: 0.85rem;">الحالة</span>
            <div style="margin-top: 0.25rem;">
                @if($task->isOpen())
                    <span style="padding: 0.25rem 0.5rem; background: rgba(95, 179, 142, 0.3); color: var(--primary-color); border-radius: 6px; font-size: 0.85rem;">مفتوحة</span>
                @else
                    <span style="padding: 0.25rem 0.5rem; background: rgba(255,255,255,0.1); color: var(--text-secondary); border-radius: 6px; font-size: 0.85rem;">مغلقة</span>
                @endif
            </div>
        </div>
    </div>

    <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 1.5rem;">
        <span style="color: var(--text-secondary); font-size: 0.9rem;">المسؤولون عن المهمة:</span>
        <span style="color: var(--text-primary); margin-right: 0.5rem;">{{ $task->assignees->pluck('name')->implode('، ') }}</span>
    </div>

    @if($task->description)
        <div style="background: rgba(255,255,255,0.03); padding: 1.25rem; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 1.5rem;">
            <strong style="color: var(--text-primary); font-size: 0.9rem;">التفاصيل:</strong>
            <div style="color: var(--text-primary); margin-top: 0.5rem; line-height: 1.6; white-space: pre-wrap;">{{ $task->description }}</div>
        </div>
    @endif

    {{-- سجل التحديثات --}}
    <h2 style="font-size: 1rem; color: var(--text-primary); margin-bottom: 0.75rem;"><i class="fas fa-history" style="margin-left: 0.35rem; color: var(--primary-color);"></i> سجل التحديثات</h2>

    @if($task->isOpen())
        <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 1rem;">
            <form method="POST" action="{{ route('wesal.e-office.tasks.updates.store', $task) }}" enctype="multipart/form-data">
                @csrf
                <label style="display: block; margin-bottom: 0.35rem; color: var(--text-primary); font-weight: 600; font-size: 0.9rem;">إضافة تحديث</label>
                <textarea name="body" rows="3" required placeholder="اكتب التحديث..." style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: rgba(0,0,0,0.2); color: var(--text-primary); margin-bottom: 0.5rem;"></textarea>
                <div style="margin-bottom: 0.5rem;">
                    <label style="font-size: 0.85rem; color: var(--text-secondary);">مرفقات (اختياري):</label>
                    <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip" style="width: 100%; padding: 0.35rem; font-size: 0.85rem; color: var(--text-primary);">
                </div>
                <button type="submit" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.5rem 1rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.9rem;">
                    <i class="fas fa-plus"></i> إضافة التحديث
                </button>
            </form>
        </div>
    @endif

    <div style="margin-bottom: 1rem;">
        @forelse(($task->updates ?? collect()) as $update)
            <div style="background: rgba(0,0,0,0.15); padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <span style="color: var(--primary-color); font-weight: 600; font-size: 0.9rem;">{{ $update->user->name ?? '-' }}</span>
                    <span style="color: var(--text-secondary); font-size: 0.8rem;">{{ $update->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <div style="color: var(--text-primary); font-size: 0.9rem; line-height: 1.5; white-space: pre-wrap;">{{ $update->body }}</div>
                @if($update->attachments->count() > 0)
                    <div style="margin-top: 0.5rem; font-size: 0.85rem;">
                        <i class="fas fa-paperclip" style="color: var(--primary-color); margin-left: 0.25rem;"></i>
                        @foreach($update->attachments as $att)
                            <a href="{{ asset('storage/' . $att->path) }}" target="_blank" rel="noopener" style="color: var(--primary-color); text-decoration: none; margin-left: 0.5rem;">{{ $att->original_name }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <p style="color: var(--text-secondary); font-size: 0.9rem;">لا توجد تحديثات بعد.</p>
        @endforelse
    </div>

    @if($task->isClosed() && ($task->evidence_path || $task->evidence_original_name))
        <div style="margin-top: 1rem; padding: 1rem; background: rgba(95, 179, 142, 0.1); border-radius: 8px; border: 1px solid var(--primary-color);">
            <strong style="color: var(--text-primary); font-size: 0.9rem;"><i class="fas fa-file-signature" style="margin-left: 0.35rem; color: var(--primary-color);"></i> شاهد الإنجاز (مرفق عند الإغلاق):</strong>
            <a href="{{ asset('storage/' . $task->evidence_path) }}" target="_blank" rel="noopener" style="display: inline-block; margin-top: 0.35rem; color: var(--primary-color); text-decoration: none;">{{ $task->evidence_original_name }}</a>
        </div>
    @endif

    @if($task->isOpen())
        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
            <h3 style="font-size: 0.95rem; color: var(--text-primary); margin-bottom: 0.5rem;">إغلاق المهمة (بعد الإنجاز)</h3>
            <form method="POST" action="{{ route('wesal.e-office.tasks.close', $task) }}" enctype="multipart/form-data" style="display: flex; flex-wrap: wrap; align-items: flex-end; gap: 0.75rem;">
                @csrf
                <div>
                    <label style="display: block; margin-bottom: 0.25rem; color: var(--text-secondary); font-size: 0.85rem;">مرفق شاهد الإنجاز (اختياري):</label>
                    <input type="file" name="evidence" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="padding: 0.35rem; color: var(--text-primary); font-size: 0.85rem;">
                </div>
                <button type="submit" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.5rem 1rem; background: rgba(76, 175, 80, 0.8); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-check-circle"></i> إغلاق المهمة
                </button>
            </form>
        </div>
    @endif
</div>
