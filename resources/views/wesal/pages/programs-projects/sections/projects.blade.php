<div class="content-card">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-folder" style="color: var(--primary-color); margin-left: 0.5rem;"></i> المشاريع</h1>
        <p class="page-subtitle">نظرة عامة على المشاريع</p>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
        <a href="{{ route('wesal.programs-projects.show', ['section' => 'projects', 'sub' => 'list']) }}" class="btn btn-secondary" style="padding: 1.25rem; flex-direction: column; gap: 0.5rem; text-align: center;">
            <i class="fas fa-list" style="font-size: 1.5rem; color: var(--primary-color);"></i>
            <span>قائمة المشاريع</span>
        </a>
        <a href="{{ route('wesal.programs-projects.show', ['section' => 'projects', 'sub' => 'add']) }}" class="btn btn-secondary" style="padding: 1.25rem; flex-direction: column; gap: 0.5rem; text-align: center;">
            <i class="fas fa-plus-circle" style="font-size: 1.5rem; color: var(--primary-color);"></i>
            <span>إضافة مشروع</span>
        </a>
        <a href="{{ route('wesal.programs-projects.show', ['section' => 'projects', 'sub' => 'archive']) }}" class="btn btn-secondary" style="padding: 1.25rem; flex-direction: column; gap: 0.5rem; text-align: center;">
            <i class="fas fa-archive" style="font-size: 1.5rem; color: var(--primary-color);"></i>
            <span>الأرشيف</span>
        </a>
    </div>
</div>
