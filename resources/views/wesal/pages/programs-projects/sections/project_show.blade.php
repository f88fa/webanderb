@php $p = $showProject ?? null; @endphp
@if(!$p)
    <div class="content-card">
        <div class="alert alert-error">المشروع غير موجود.</div>
        <a href="{{ route('wesal.programs-projects.show', ['section' => 'projects', 'sub' => 'list']) }}" class="btn btn-secondary">العودة للقائمة</a>
    </div>
@else
<div class="content-card">
    <div class="page-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-project-diagram" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                {{ $p->name_ar }}
            </h1>
            <p class="page-subtitle">رقم المشروع: {{ $p->project_no }} — المراحل وسجلات التحديث والمرفقات</p>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('wesal.programs-projects.show', ['section' => 'edit-project', 'sub' => $p->id]) }}" class="btn btn-primary"><i class="fas fa-edit"></i> تعديل المشروع</a>
            <a href="{{ route('wesal.programs-projects.show', ['section' => 'projects', 'sub' => 'list']) }}" class="btn btn-secondary">العودة للقائمة</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error">@foreach($errors->all() as $e)<p style="margin:0;">{{ $e }}</p>@endforeach</div>
    @endif

    {{-- ملخص المشروع --}}
    <div style="background: rgba(255,255,255,0.05); padding: 1rem 1.5rem; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
            <div><span style="color: var(--text-secondary);">الجهة المانحة:</span> {{ $p->donor?->name_ar ?? '—' }}</div>
            <div><span style="color: var(--text-secondary);">تاريخ البداية:</span> {{ $p->start_date?->format('Y-m-d') ?? '—' }}</div>
            <div><span style="color: var(--text-secondary);">تاريخ النهاية:</span> {{ $p->end_date?->format('Y-m-d') ?? '—' }}</div>
            <div><span style="color: var(--text-secondary);">الميزانية:</span> {{ $p->budget_amount ? number_format($p->budget_amount, 2) : '—' }}</div>
            <div><span style="color: var(--text-secondary);">المنفق:</span> {{ $p->spent_amount ? number_format($p->spent_amount, 2) : '0.00' }}</div>
        </div>
    </div>

    {{-- إضافة مرحلة جديدة --}}
    <div style="background: rgba(255,255,255,0.05); padding: 1rem 1.5rem; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 1.5rem;">
        <h3 style="color: var(--primary-color); margin-bottom: 0.75rem;"><i class="fas fa-plus-circle"></i> إضافة مرحلة جديدة</h3>
        <form method="POST" action="{{ route('wesal.programs-projects.stages.store') }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 0.75rem; align-items: end;">
            @csrf
            <input type="hidden" name="project_id" value="{{ $p->id }}">
            <div><label class="form-label">اسم المرحلة <span style="color:#dc3545">*</span></label><input type="text" name="name_ar" class="form-control" required></div>
            <div><label class="form-label">التاريخ من</label><input type="date" name="start_date" class="form-control"></div>
            <div><label class="form-label">التاريخ إلى</label><input type="date" name="end_date" class="form-control"></div>
            <div><label class="form-label">الحالة</label><select name="status" class="form-control"><option value="pending">قيد الانتظار</option><option value="in_progress">جاري التنفيذ</option><option value="completed">مكتملة</option></select></div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة مرحلة</button></div>
        </form>
    </div>

    {{-- المراحل وسجلات التحديث (محادثة) --}}
    <h3 style="color: var(--text-primary); margin-bottom: 1rem;"><i class="fas fa-puzzle-piece" style="color: var(--primary-color);"></i> مراحل المشروع وسجلات التحديث</h3>

    @if($p->stages && $p->stages->count() > 0)
        @foreach($p->stages as $stage)
        <div class="stage-block" style="background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); border-radius: 12px; margin-bottom: 1rem; overflow: hidden;">
            {{-- عنوان المرحلة (قابل للنقر لفتح/إغلاق التحديثات) --}}
            <div class="stage-header-toggle" role="button" tabindex="0" data-stage-id="{{ $stage->id }}" style="padding: 1rem 1.5rem; background: rgba(0,0,0,0.15); display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; cursor: pointer; user-select: none;" onclick="document.getElementById('stage-body-{{ $stage->id }}').classList.toggle('stage-body-open'); this.querySelector('.stage-chevron').classList.toggle('stage-chevron-open');">
                <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1;">
                    <i class="fas fa-chevron-left stage-chevron" style="transition: transform 0.2s; color: var(--text-secondary); font-size: 0.9rem;"></i>
                    <strong style="font-size: 1.05rem;">{{ $stage->name_ar }}</strong>
                    <span style="color: var(--text-secondary); font-size: 0.9rem;">من {{ $stage->start_date?->format('Y-m-d') ?? '—' }} إلى {{ $stage->end_date?->format('Y-m-d') ?? '—' }}</span>
                    @if($stage->status === 'completed')
                        <span style="padding: 0.2rem 0.5rem; background: rgba(76,175,80,0.3); color: #4caf50; border-radius: 6px; font-size: 0.85rem;">مكتملة</span>
                    @elseif($stage->status === 'in_progress')
                        <span style="padding: 0.2rem 0.5rem; background: rgba(33,150,243,0.3); color: #2196f3; border-radius: 6px; font-size: 0.85rem;">غير مكتملة</span>
                    @else
                        <span style="padding: 0.2rem 0.5rem; background: rgba(255,152,0,0.3); color: #ff9800; border-radius: 6px; font-size: 0.85rem;">معلقة</span>
                    @endif
                    @if($stage->updates && $stage->updates->count() > 0)
                        <span style="color: var(--text-secondary); font-size: 0.85rem;">({{ $stage->updates->count() }} تحديث)</span>
                    @endif
                </div>
            </div>

            {{-- محتوى المرحلة (مخفي افتراضياً — يظهر عند الضغط على العنوان) --}}
            <div id="stage-body-{{ $stage->id }}" class="stage-body" style="display: none; border-top: 1px solid var(--border-color);">
            <div style="padding: 1rem 1.5rem; background: rgba(0,0,0,0.08); display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 0.75rem;">
                <span style="color: var(--text-secondary); font-size: 0.9rem;">تعديل الحالة والإجراءات</span>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    @if($stage->status !== 'completed')
                    <form method="POST" action="{{ route('wesal.programs-projects.stages.update', $stage) }}" style="display: inline;" id="stage-status-form-{{ $stage->id }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="name_ar" value="{{ $stage->name_ar }}">
                        <input type="hidden" name="name_en" value="{{ $stage->name_en ?? '' }}">
                        <input type="hidden" name="order" value="{{ $stage->order ?? 0 }}">
                        <input type="hidden" name="start_date" value="{{ $stage->start_date?->format('Y-m-d') }}">
                        <input type="hidden" name="end_date" value="{{ $stage->end_date?->format('Y-m-d') }}">
                        <input type="hidden" name="notes" value="{{ $stage->notes ?? '' }}">
                        <label class="form-label" style="font-size: 0.85rem; margin-left: 0.5rem;">حالة المرحلة</label>
                        <select name="status" class="form-control" style="display: inline-block; width: auto; padding: 0.35rem 0.6rem; font-size: 0.85rem;" onchange="toggleClosureReason({{ $stage->id }}, this.value)">
                            <option value="pending" {{ $stage->status === 'pending' ? 'selected' : '' }}>معلقة</option>
                            <option value="in_progress" {{ $stage->status === 'in_progress' ? 'selected' : '' }}>غير مكتملة</option>
                            <option value="completed" {{ $stage->status === 'completed' ? 'selected' : '' }}>مكتملة</option>
                        </select>
                        <div id="closure-reason-wrap-{{ $stage->id }}" class="closure-reason-box" style="display: none; margin-top: 1rem;">
                            <label class="form-label" style="font-size: 0.9rem; font-weight: 600; color: var(--text-primary);"><i class="fas fa-clipboard-list" style="margin-left: 0.35rem;"></i>سبب الإغلاق <span style="color:#dc3545">*</span></label>
                            <textarea name="closure_reason" class="form-control" rows="3" placeholder="اكتب سبب إغلاق المرحلة..." style="font-size: 0.95rem; border-radius: 8px; margin-top: 0.35rem;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" style="padding: 0.35rem 0.7rem; font-size: 0.85rem; margin-right: 0.25rem;"><i class="fas fa-save"></i> حفظ الحالة</button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('wesal.programs-projects.stages.destroy', $stage) }}" style="display: inline;" onsubmit="return confirm('حذف المرحلة وسجلات التحديث؟');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger" style="padding: 0.35rem 0.7rem; font-size: 0.85rem;"><i class="fas fa-trash"></i></button></form>
                </div>
            </div>

            @if($stage->status === 'completed')
            <div class="stage-closure-card">
                <div class="stage-closure-header">
                    <i class="fas fa-flag-checkered"></i>
                    <span>إغلاق المرحلة</span>
                </div>
                <div class="stage-closure-body">
                    <div class="stage-closure-row">
                        <span class="stage-closure-label">حالة المرحلة</span>
                        <span class="stage-closure-value stage-closure-status-completed">مكتملة</span>
                    </div>
                    @if($stage->closed_at)
                    <div class="stage-closure-row">
                        <span class="stage-closure-label">تاريخ الإغلاق</span>
                        <span class="stage-closure-value">{{ $stage->closed_at->format('Y-m-d') }} — {{ $stage->closed_at->format('H:i') }}</span>
                    </div>
                    @endif
                    @if($stage->closedByUser)
                    <div class="stage-closure-row">
                        <span class="stage-closure-label">أغلقها</span>
                        <span class="stage-closure-value">{{ $stage->closedByUser->name }}</span>
                    </div>
                    @endif
                    @if($stage->closure_reason)
                    <div class="stage-closure-reason-block">
                        <span class="stage-closure-label">سبب الإغلاق</span>
                        <p class="stage-closure-reason-text">{{ $stage->closure_reason }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <div style="padding: 1rem 1.5rem;">
                {{-- المحادثة: سجلات التحديث --}}
                <h4 style="color: var(--text-primary); margin-bottom: 0.75rem; font-size: 0.95rem;"><i class="fas fa-comments"></i> سجلات التحديث (محادثة الفريق)</h4>
                @if($stage->updates && $stage->updates->count() > 0)
                    <div class="stage-updates-chat" style="margin-bottom: 1.25rem;">
                        @foreach($stage->updates as $up)
                        <div class="chat-message" style="display: flex; gap: 0.75rem; margin-bottom: 1rem; align-items: flex-start;">
                            <div style="flex-shrink: 0; width: 40px; height: 40px; border-radius: 50%; background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700;">{{ mb_substr($up->updatedByUser?->name ?? '?', 0, 1) }}</div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                    <strong style="color: var(--text-primary);">{{ $up->updatedByUser?->name ?? 'غير معروف' }}</strong>
                                    <span style="color: var(--text-secondary); font-size: 0.85rem;">{{ $up->update_date?->format('Y-m-d') }} {{ $up->created_at?->format('H:i') }}</span>
                                    @if($up->progress_percentage !== null)<span style="padding: 0.15rem 0.4rem; background: rgba(33,150,243,0.2); color: #2196f3; border-radius: 4px; font-size: 0.8rem;">{{ $up->progress_percentage }}%</span>@endif
                                    <form method="POST" action="{{ route('wesal.programs-projects.stage-updates.destroy', $up) }}" style="display: inline; margin-right: auto;" onsubmit="return confirm('حذف هذا التحديث؟');">@csrf @method('DELETE')<button type="submit" class="btn btn-danger" style="padding: 0.2rem 0.4rem; font-size: 0.75rem;"><i class="fas fa-trash"></i></button></form>
                                </div>
                                @if($up->title)<p style="margin: 0 0 0.25rem 0; font-weight: 600; color: var(--primary-color);">{{ $up->title }}</p>@endif
                                <p style="margin: 0; color: var(--text-primary); white-space: pre-wrap;">{{ $up->description ?? '—' }}</p>
                                @if($up->attachments && $up->attachments->count() > 0)
                                <div style="margin-top: 0.5rem; display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                    @foreach($up->attachments as $att)
                                    <a href="{{ $att->url }}" target="_blank" rel="noopener" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.6rem; background: rgba(255,255,255,0.08); border-radius: 6px; color: var(--primary-color); text-decoration: none; font-size: 0.85rem;"><i class="fas fa-paperclip"></i> {{ $att->original_name ?: 'مرفق' }}</a>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p style="color: var(--text-secondary); margin-bottom: 1rem; font-size: 0.9rem;">لا توجد تحديثات بعد. اكتب أول تحديث أدناه.</p>
                @endif

                {{-- إضافة سجل تحديث (فقط إن لم تكن المرحلة مغلقة) --}}
                @if($stage->status !== 'completed')
                <form method="POST" action="{{ route('wesal.programs-projects.stage-updates.store') }}" enctype="multipart/form-data" style="background: rgba(255,255,255,0.03); padding: 1rem; border-radius: 10px; border: 1px dashed var(--border-color);">
                    @csrf
                    <input type="hidden" name="stage_id" value="{{ $stage->id }}">
                    <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 0.75rem; align-items: end; margin-bottom: 0.75rem;">
                        <div><label class="form-label">تاريخ التحديث <span style="color:#dc3545">*</span></label><input type="date" name="update_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                        <div><label class="form-label">عنوان (اختياري)</label><input type="text" name="title" class="form-control" placeholder="عنوان التحديث"></div>
                        <div><label class="form-label">نسبة الإنجاز %</label><input type="number" name="progress_percentage" class="form-control" min="0" max="100" placeholder="0-100" style="width: 80px;"></div>
                    </div>
                    <div style="margin-bottom: 0.75rem;"><label class="form-label">التحديث / الملاحظة</label><textarea name="description" class="form-control" rows="2" placeholder="اكتب التحديث أو الإنجاز..."></textarea></div>
                    <div style="margin-bottom: 0.75rem;"><label class="form-label">مرفقات (اختياري)</label><input type="file" name="attachments[]" class="form-control" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"></div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> إرسال التحديث</button>
                </form>
                @else
                <p style="color: var(--text-secondary); font-size: 0.9rem;">المرحلة مغلقة — لا يمكن إضافة تحديثات جديدة.</p>
                @endif
            </div>
            </div>{{-- نهاية stage-body --}}
        </div>
        @endforeach
    @else
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد مراحل. أضف مرحلة من النموذج أعلاه.</p>
    @endif
</div>

<style>
.stage-body.stage-body-open { display: block !important; }
.stage-chevron { transition: transform 0.2s ease; }
.stage-chevron.stage-chevron-open { transform: rotate(-90deg); }

/* بطاقة إغلاق المرحلة */
.stage-closure-card {
    margin: 1rem 1.5rem;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid rgba(76, 175, 80, 0.35);
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.08) 0%, rgba(76, 175, 80, 0.02) 100%);
}
.stage-closure-header {
    padding: 0.85rem 1.25rem;
    background: rgba(76, 175, 80, 0.2);
    color: #4caf50;
    font-weight: 700;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.stage-closure-header i { font-size: 1.1rem; }
.stage-closure-body { padding: 1.25rem 1.5rem; }
.stage-closure-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.85rem;
    flex-wrap: wrap;
}
.stage-closure-row:last-of-type { margin-bottom: 0; }
.stage-closure-label {
    color: var(--text-secondary);
    font-size: 0.9rem;
    min-width: 120px;
}
.stage-closure-value {
    color: var(--text-primary);
    font-weight: 600;
    font-size: 0.95rem;
}
.stage-closure-status-completed {
    padding: 0.25rem 0.75rem;
    background: rgba(76, 175, 80, 0.3);
    color: #4caf50;
    border-radius: 8px;
    font-size: 0.9rem;
}
.stage-closure-reason-block {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}
.stage-closure-reason-block .stage-closure-label {
    display: block;
    margin-bottom: 0.5rem;
}
.stage-closure-reason-text {
    margin: 0;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    color: var(--text-primary);
    line-height: 1.6;
    white-space: pre-wrap;
    font-size: 0.95rem;
}
.closure-reason-box {
    padding: 1rem;
    background: rgba(255, 255, 255, 0.04);
    border-radius: 10px;
    border: 1px solid var(--border-color);
}
</style>
<script>
function toggleClosureReason(stageId, status) {
    var wrap = document.getElementById('closure-reason-wrap-' + stageId);
    if (wrap) wrap.style.display = status === 'completed' ? 'block' : 'none';
}
@foreach($p->stages ?? [] as $s)
if (document.querySelector('#stage-status-form-{{ $s->id }} select[name=status]')?.value === 'completed') {
    var w = document.getElementById('closure-reason-wrap-{{ $s->id }}');
    if (w) w.style.display = 'block';
}
@endforeach
</script>
@endif
