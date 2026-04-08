<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-users" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            الموارد البشرية
        </h1>
        <p class="page-subtitle">نظرة عامة على قسم الموارد البشرية</p>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
        @foreach([
            ['section' => 'employees', 'icon' => 'fa-user-friends', 'label' => 'الموظفون'],
            ['section' => 'attendance', 'icon' => 'fa-clock', 'label' => 'الحضور والانصراف'],
            ['section' => 'leave', 'icon' => 'fa-calendar-alt', 'label' => 'الإجازات'],
            ['section' => 'payroll', 'icon' => 'fa-money-bill-wave', 'label' => 'الرواتب'],
            ['section' => 'contracts', 'icon' => 'fa-file-contract', 'label' => 'العقود'],
            ['section' => 'reports', 'icon' => 'fa-file-pdf', 'label' => 'التقارير'],
        ] as $item)
            <a href="{{ route('wesal.hr.show', ['section' => $item['section']]) }}" class="btn btn-secondary" style="padding: 1.25rem; flex-direction: column; gap: 0.5rem; text-align: center;">
                <i class="fas {{ $item['icon'] }}" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>
