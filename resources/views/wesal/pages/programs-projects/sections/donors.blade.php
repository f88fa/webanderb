<div class="content-card">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-handshake" style="color: var(--primary-color); margin-left: 0.5rem;"></i> الجهات المانحة</h1>
        <p class="page-subtitle">نظرة عامة على الجهات المانحة</p>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
        <a href="{{ route('wesal.programs-projects.show', ['section' => 'donors', 'sub' => 'list']) }}" class="btn btn-secondary" style="padding: 1.25rem; flex-direction: column; gap: 0.5rem; text-align: center;">
            <i class="fas fa-list" style="font-size: 1.5rem; color: var(--primary-color);"></i>
            <span>قائمة الجهات</span>
        </a>
        <a href="{{ route('wesal.programs-projects.show', ['section' => 'donors', 'sub' => 'agreements']) }}" class="btn btn-secondary" style="padding: 1.25rem; flex-direction: column; gap: 0.5rem; text-align: center;">
            <i class="fas fa-file-contract" style="font-size: 1.5rem; color: var(--primary-color);"></i>
            <span>الاتفاقيات</span>
        </a>
        <a href="{{ route('wesal.programs-projects.show', ['section' => 'donors', 'sub' => 'grants']) }}" class="btn btn-secondary" style="padding: 1.25rem; flex-direction: column; gap: 0.5rem; text-align: center;">
            <i class="fas fa-coins" style="font-size: 1.5rem; color: var(--primary-color);"></i>
            <span>المنح</span>
        </a>
    </div>
</div>
