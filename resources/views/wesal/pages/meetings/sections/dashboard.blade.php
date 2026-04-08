<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-users" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            الاجتماعات
        </h1>
        <p class="page-subtitle">نظرة عامة على إدارة الاجتماعات</p>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
        <a href="{{ route('wesal.meetings.show', ['section' => 'board-meetings']) }}" class="btn btn-secondary" style="padding: 1.25rem; flex-direction: column; gap: 0.5rem; text-align: center;">
            <i class="fas fa-gavel" style="font-size: 1.5rem; color: var(--primary-color);"></i>
            <span>اجتماعات المجلس</span>
        </a>
        <a href="{{ route('wesal.meetings.show', ['section' => 'staff-meetings']) }}" class="btn btn-secondary" style="padding: 1.25rem; flex-direction: column; gap: 0.5rem; text-align: center;">
            <i class="fas fa-user-friends" style="font-size: 1.5rem; color: var(--primary-color);"></i>
            <span>اجتماعات الموظفين</span>
        </a>
        <a href="{{ route('wesal.meetings.show', ['section' => 'board-decisions']) }}" class="btn btn-secondary" style="padding: 1.25rem; flex-direction: column; gap: 0.5rem; text-align: center;">
            <i class="fas fa-file-signature" style="font-size: 1.5rem; color: var(--primary-color);"></i>
            <span>قرارات المجلس</span>
        </a>
        <a href="{{ route('wesal.meetings.show', ['section' => 'board-members']) }}" class="btn btn-secondary" style="padding: 1.25rem; flex-direction: column; gap: 0.5rem; text-align: center;">
            <i class="fas fa-user-tie" style="font-size: 1.5rem; color: var(--primary-color);"></i>
            <span>أعضاء المجلس</span>
        </a>
        <a href="{{ route('wesal.meetings.show', ['section' => 'meeting-types']) }}" class="btn btn-secondary" style="padding: 1.25rem; flex-direction: column; gap: 0.5rem; text-align: center;">
            <i class="fas fa-list-alt" style="font-size: 1.5rem; color: var(--primary-color);"></i>
            <span>أنواع الاجتماعات</span>
        </a>
    </div>
</div>
