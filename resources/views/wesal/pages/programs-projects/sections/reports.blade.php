<div class="content-card">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-chart-line" style="color: var(--primary-color); margin-left: 0.5rem;"></i> تقارير المشاريع</h1>
        <p class="page-subtitle">تقارير ملخصة أو تفصيلية أو تقرير رسمي للجهة المانحة وأي جهة خارجية</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color); margin-bottom: 1.5rem;">
        <h3 style="color: var(--primary-color); margin-bottom: 1rem;"><i class="fas fa-cog"></i> اختر المشروع ونوع التقرير</h3>
        <form method="GET" action="{{ route('wesal.programs-projects.show', ['section' => 'reports', 'sub' => '']) }}" id="report-form" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; align-items: end; flex-wrap: wrap;">
            <div>
                <label class="form-label">المشروع <span style="color:#dc3545">*</span></label>
                <select name="project_id" id="report-project-id" class="form-control" required>
                    <option value="">-- اختر المشروع --</option>
                    @foreach($projects ?? [] as $pr)
                        <option value="{{ $pr->id }}" {{ request('project_id') == $pr->id ? 'selected' : '' }}>{{ $pr->project_no }} — {{ $pr->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">نوع التقرير <span style="color:#dc3545">*</span></label>
                <select name="report_type" id="report-type" class="form-control" required>
                    <option value="">-- اختر النوع --</option>
                    @foreach($reportTypes ?? [] as $key => $label)
                        <option value="{{ $key }}" {{ request('report_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn btn-primary" id="btn-view-report"><i class="fas fa-file-alt"></i> عرض التقرير</button>
            </div>
        </form>
        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 0.75rem;">
            <strong>تقرير ملخص:</strong> أرقام ومؤشرات سريعة عن المشروع.<br>
            <strong>تقرير تفصيلي:</strong> تفاصيل كاملة تشمل المراحل وسجلات التحديث والمصروفات والمستندات.<br>
            <strong>تقرير للجهة المانحة:</strong> تقرير رسمي منسق جاهز للطباعة أو تقديمه للجهة المانحة أو أي جهة خارجية.
        </p>
    </div>
</div>

<script>
document.getElementById('btn-view-report').addEventListener('click', function() {
    var projectId = document.getElementById('report-project-id').value;
    var reportType = document.getElementById('report-type').value;
    if (!projectId || !reportType) {
        alert('يرجى اختيار المشروع ونوع التقرير.');
        return;
    }
    var base = "{{ route('wesal.programs-projects.show', ['section' => 'reports', 'sub' => 'REPLACE']) }}";
    var url = base.replace('REPLACE', reportType) + '?project_id=' + encodeURIComponent(projectId);
    window.location.href = url;
});
</script>
