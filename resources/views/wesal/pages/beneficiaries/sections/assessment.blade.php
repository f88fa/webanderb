<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-star" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            التقييم والأهلية
        </h1>
        <p class="page-subtitle">تقييم المستفيدين وتحديد الأهلية</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">إضافة تقييم</h3>
        <form method="POST" action="{{ route('wesal.beneficiaries.assessments.store') }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; align-items: end; margin-bottom: 2rem;">
            @csrf
            <div>
                <label class="form-label">المستفيد <span style="color: #ff8a80;">*</span></label>
                <select name="beneficiary_id" class="form-control" required>
                    <option value="">-- اختر --</option>
                    @foreach($beneficiaries ?? [] as $b)
                        <option value="{{ $b->id }}">{{ $b->beneficiary_no }} - {{ $b->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">تاريخ التقييم <span style="color: #ff8a80;">*</span></label>
                <input type="date" name="assessment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div>
                <label class="form-label">درجة الأهلية (0-100)</label>
                <input type="number" name="eligibility_score" class="form-control" min="0" max="100" step="0.01">
            </div>
            <div style="grid-column: 1 / -1;">
                <label class="form-label">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>
            <div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button>
            </div>
        </form>

        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">سجل التقييمات</h3>
        @if(isset($assessments) && $assessments->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead>
                        <tr>
                            <th>المستفيد</th>
                            <th>التاريخ</th>
                            <th>درجة الأهلية</th>
                            <th>ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assessments as $a)
                        <tr>
                            <td>{{ $a->beneficiary?->name_ar }}</td>
                            <td>{{ $a->assessment_date?->format('Y-m-d') }}</td>
                            <td>{{ $a->eligibility_score !== null ? number_format($a->eligibility_score, 1) : '-' }}</td>
                            <td>{{ Str::limit($a->notes, 40) ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $assessments->links() }}
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد تقييمات.</p>
        @endif
    </div>
</div>
