<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-project-diagram" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            البرامج والمشاريع
        </h1>
        <p class="page-subtitle">نظرة عامة على إدارة البرامج والمشاريع</p>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
        @foreach([
            ['section' => 'projects', 'sub' => 'list', 'icon' => 'fa-folder', 'label' => 'قائمة المشاريع'],
            ['section' => 'projects', 'sub' => 'add', 'icon' => 'fa-plus-circle', 'label' => 'إضافة مشروع'],
            ['section' => 'projects', 'sub' => 'archive', 'icon' => 'fa-archive', 'label' => 'أرشيف المشاريع'],
            ['section' => 'stages', 'icon' => 'fa-puzzle-piece', 'label' => 'المراحل'],
            ['section' => 'tasks', 'icon' => 'fa-tasks', 'label' => 'المهام'],
            ['section' => 'donors', 'sub' => 'list', 'icon' => 'fa-handshake', 'label' => 'قائمة الجهات المانحة'],
            ['section' => 'donors', 'sub' => 'agreements', 'icon' => 'fa-file-contract', 'label' => 'الاتفاقيات'],
            ['section' => 'donors', 'sub' => 'grants', 'icon' => 'fa-coins', 'label' => 'المنح'],
            ['section' => 'budgets', 'icon' => 'fa-money-bill-wave', 'label' => 'الميزانيات والمصروفات'],
            ['section' => 'documents', 'icon' => 'fa-folder-open', 'label' => 'المستندات'],
            ['section' => 'reports', 'icon' => 'fa-chart-line', 'label' => 'التقارير'],
            ['section' => 'settings', 'icon' => 'fa-cog', 'label' => 'الإعدادات'],
        ] as $item)
            <a href="{{ route('wesal.programs-projects.show', ['section' => $item['section'], 'sub' => $item['sub'] ?? null]) }}" class="btn btn-secondary" style="padding: 1.25rem; flex-direction: column; gap: 0.5rem; text-align: center;">
                <i class="fas {{ $item['icon'] }}" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>
