<div class="content-card">
    <div class="page-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-hands-helping" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                المستفيدين
            </h1>
            <p class="page-subtitle">نظرة عامة على قسم المستفيدين</p>
        </div>
        <a href="{{ url('/beneficiary-portal') }}" target="_blank" class="btn btn-primary">
            <i class="fas fa-external-link-alt"></i>
            <span>بوابة المستفيدين</span>
        </a>
    </div>
    <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; border: 2px solid var(--border-color);">
        <p style="margin: 0; color: var(--text-secondary);">
            <i class="fas fa-link"></i> رابط بوابة المستفيدين للتسجيل وتسجيل الدخول: <a href="{{ url('/beneficiary-portal') }}" target="_blank" style="color: var(--primary-color); font-weight: 600;">{{ url('/beneficiary-portal') }}</a>
        </p>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
        @foreach([
            ['section' => 'list', 'icon' => 'fa-list', 'label' => 'قائمة المستفيدين'],
            ['section' => 'create', 'icon' => 'fa-user-plus', 'label' => 'إضافة مستفيد'],
            ['section' => 'archive', 'icon' => 'fa-archive', 'label' => 'الأرشيف'],
            ['section' => 'registration-requests', 'icon' => 'fa-user-plus', 'label' => 'طلبات التسجيل من البوابة'],
            ['section' => 'requests', 'sub' => 'new', 'icon' => 'fa-inbox', 'label' => 'الطلبات الجديدة'],
            ['section' => 'services', 'icon' => 'fa-gift', 'label' => 'الخدمات والمساعدات'],
            ['section' => 'medical', 'icon' => 'fa-heartbeat', 'label' => 'المتابعة الطبية'],
            ['section' => 'assessment', 'icon' => 'fa-star', 'label' => 'التقييم والأهلية'],
            ['section' => 'documents', 'icon' => 'fa-folder-open', 'label' => 'المستندات'],
            ['section' => 'reports', 'icon' => 'fa-chart-line', 'label' => 'التقارير'],
            ['section' => 'programs', 'icon' => 'fa-bullseye', 'label' => 'البرامج والحملات'],
        ] as $item)
            <a href="{{ route('wesal.beneficiaries.show', ['section' => $item['section'], 'sub' => $item['sub'] ?? null]) }}" class="btn btn-secondary" style="padding: 1.25rem; flex-direction: column; gap: 0.5rem; text-align: center;">
                <i class="fas {{ $item['icon'] }}" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>
