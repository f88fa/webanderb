<div class="content-card">
    <div class="page-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-wpforms" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                نماذج المستفيدين
            </h1>
            <p class="page-subtitle">إنشاء نماذج مختلفة واختيار نموذج واحد نشط يُستخدم لإضافة المستفيد من لوحة التحكم ولتسجيل المستفيدين في البوابة</p>
        </div>
        <a href="{{ route('wesal.beneficiaries.forms.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> نموذج جديد
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    {{-- إعدادات الربط --}}
    <div style="background: rgba(255,255,255,0.05); padding: 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin: 0 0 1rem 0; font-size: 1rem;">
            <i class="fas fa-link" style="margin-left: 0.35rem; color: var(--primary-color);"></i> ربط النماذج
        </h3>
        <form method="POST" action="{{ route('wesal.beneficiaries.forms.settings') }}" style="display: flex; flex-wrap: wrap; gap: 1.5rem; align-items: end;">
            @csrf
            <div>
                <label class="form-label">النموذج النشط (لوحة التحكم + بوابة المستفيدين)</label>
                <select name="active_form_id" class="form-control" style="min-width: 280px;">
                    <option value="">— النموذج الافتراضي (جميع الحقول القياسية) —</option>
                    @foreach($beneficiaryForms ?? [] as $f)
                        <option value="{{ $f->id }}" {{ ($activeFormId ?? null) == $f->id ? 'selected' : '' }}>{{ $f->name_ar }}</option>
                    @endforeach
                </select>
                <p style="margin: 0.5rem 0 0 0; font-size: 0.8rem; color: var(--text-secondary); line-height: 1.4;">يُستخدم هذا النموذج عند إضافة مستفيد من Wesal وعند التسجيل من بوابة المستفيدين.</p>
            </div>
            <div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button>
            </div>
        </form>
    </div>

    {{-- قائمة النماذج --}}
    <div style="background: rgba(255,255,255,0.05); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin: 0 0 1rem 0; font-size: 1rem;">النماذج المُنشأة</h3>
        @if(empty($beneficiaryForms) || $beneficiaryForms->isEmpty())
            <p style="color: var(--text-secondary); margin: 0;">لا توجد نماذج. <a href="{{ route('wesal.beneficiaries.forms.create') }}" style="color: var(--primary-color);">إنشاء نموذج</a></p>
        @else
            <table style="width: 100%; border-collapse: collapse; direction: rtl;">
                <thead>
                    <tr style="background: rgba(0,0,0,0.2);">
                        <th style="padding: 0.75rem; text-align: right;">اسم النموذج</th>
                        <th style="padding: 0.75rem; text-align: right;">المعرّف</th>
                        <th style="padding: 0.75rem; text-align: center;">عدد الحقول</th>
                        <th style="padding: 0.75rem; text-align: center;">المستفيدون</th>
                        <th style="padding: 0.75rem; text-align: left;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($beneficiaryForms as $f)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem;">{{ $f->name_ar }}</td>
                            <td style="padding: 0.75rem;"><code style="font-size: 0.85rem;">{{ $f->slug }}</code></td>
                            <td style="padding: 0.75rem; text-align: center;">{{ $f->fields_count ?? 0 }}</td>
                            <td style="padding: 0.75rem; text-align: center;">{{ $f->beneficiaries_count ?? 0 }}</td>
                            <td style="padding: 0.75rem;">
                                <a href="{{ route('wesal.beneficiaries.forms.edit', $f) }}" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;"><i class="fas fa-edit"></i> تعديل الحقول</a>
                                @if(($f->beneficiaries_count ?? 0) > 0)
                                    <button type="button" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.85rem; opacity: 0.55; cursor: not-allowed;" disabled title="لا يمكن الحذف لوجود مستفيدين مرتبطين بالنموذج"><i class="fas fa-trash"></i></button>
                                @else
                                    <form method="POST" action="{{ route('wesal.beneficiaries.forms.destroy', $f) }}" style="display: inline-block;" onsubmit="return confirm('حذف هذا النموذج؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.85rem; color: #e57373;"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
