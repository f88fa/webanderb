@php
    $isOutgoing = ($direction ?? '') === 'outgoing';
    $action = $isOutgoing ? route('wesal.communications.outgoing.store') : route('wesal.communications.incoming.store');
    $backUrl = $isOutgoing ? route('wesal.communications.outgoing') : route('wesal.communications.incoming');
    $title = $isOutgoing ? 'خطاب صادر جديد' : 'تسجيل خطاب وارد';
@endphp
<div class="content-card" style="padding: 1.5rem;">
    <div class="page-header" style="margin-bottom: 1.5rem;">
        <h1 class="page-title">
            <i class="fas {{ $isOutgoing ? 'fa-paper-plane' : 'fa-inbox' }}" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            {{ $title }}
        </h1>
        <p class="page-subtitle">{{ $isOutgoing ? 'تسجيل خطاب صادر من المنظمة' : 'تسجيل خطاب وارد إلى المنظمة' }}</p>
    </div>

    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 1rem; background: rgba(244,67,54,0.15); color: #ff8a80; padding: 0.75rem; border-radius: 8px; border: 1px solid rgba(244,67,54,0.4);">
            <ul style="margin: 0; padding-right: 1.25rem;">
                @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $action }}" style="max-width: 800px;" dir="rtl">
        @csrf
        <div style="background: rgba(255,255,255,0.05); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 1rem;">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">رقم الخطاب</label>
                <input type="text" name="letter_no" value="{{ old('letter_no') }}" class="form-control" placeholder="مثال: ١/٢٠٢٥" style="background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color);">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">الموضوع <span style="color: #ff8a80;">*</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}" class="form-control" required placeholder="موضوع الخطاب" style="background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color);">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">تاريخ الخطاب</label>
                <input type="date" name="letter_date" value="{{ old('letter_date', date('Y-m-d')) }}" class="form-control" style="background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color);">
            </div>
            @if($isOutgoing)
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">إلى (الجهة / المستلم)</label>
                    <input type="text" name="to_party" value="{{ old('to_party') }}" class="form-control" placeholder="اسم الجهة أو الشخص" style="background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color);">
                </div>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">من (جهتنا)</label>
                    <input type="text" name="from_party" value="{{ old('from_party') }}" class="form-control" placeholder="اختياري" style="background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color);">
                </div>
            @else
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">من (الجهة المرسلة)</label>
                    <input type="text" name="from_party" value="{{ old('from_party') }}" class="form-control" placeholder="اسم الجهة أو الشخص الوارد منه الخطاب" style="background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color);">
                </div>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">إلى (جهتنا)</label>
                    <input type="text" name="to_party" value="{{ old('to_party') }}" class="form-control" placeholder="اختياري" style="background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color);">
                </div>
            @endif
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">الرقم المرجعي</label>
                <input type="text" name="reference_no" value="{{ old('reference_no') }}" class="form-control" placeholder="إن وُجد" style="background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color);">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">محتوى الخطاب</label>
                <textarea name="body" rows="6" class="form-control" placeholder="نص الخطاب أو ملخصه..." style="background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color);">{{ old('body') }}</textarea>
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">ملاحظات</label>
                <textarea name="notes" rows="2" class="form-control" placeholder="ملاحظات داخلية..." style="background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color);">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <button type="submit" style="padding: 0.6rem 1.5rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                <i class="fas fa-save" style="margin-left: 0.5rem;"></i> حفظ
            </button>
            <a href="{{ $backUrl }}" style="padding: 0.6rem 1.5rem; background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 8px; text-decoration: none; font-weight: 600;">
                <i class="fas fa-arrow-right" style="margin-left: 0.5rem;"></i> إلغاء
            </a>
        </div>
    </form>
</div>
