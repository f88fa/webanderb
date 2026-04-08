<div class="content-card" style="padding: 1.5rem;">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 0;">
        <div>
            <h1 class="page-title" style="font-size: 1.5rem;">
                <i class="fas fa-home"></i> الرئيسية
            </h1>
            <p class="page-subtitle" style="font-size: 0.9rem;">مرحباً، {{ $userName ?? auth()->user()->name ?? 'مستخدم' }}</p>
        </div>
        <a href="{{ route('frontend') }}" target="_blank" class="btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: rgba(255,255,255,0.15); color: var(--text-primary); text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 0.9rem; border: 1px solid var(--border-color);">
            <i class="fas fa-external-link-alt"></i>
            <span>الموقع الإلكتروني</span>
        </a>
    </div>

    {{-- لوحة خاصة بالمستخدم: رسائل، مهام، طلبات --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-top: 1.5rem;">
        <a href="{{ route('wesal.e-office.mail.inbox') }}" class="stat-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 1px solid rgba(95, 179, 142, 0.3); border-radius: 12px; padding: 1.25rem; text-align: center; text-decoration: none; color: inherit; display: block; transition: opacity 0.2s;">
            <div style="width: 44px; height: 44px; margin: 0 auto 0.6rem; background: rgba(95, 179, 142, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-inbox" style="font-size: 1.25rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.25rem; font-size: 1.5rem; font-weight: 700;">{{ $inbox_count ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.85rem;">الرسائل الواردة</p>
        </a>

        <a href="{{ route('wesal.e-office.mail.sent') }}" class="stat-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 1px solid rgba(95, 179, 142, 0.3); border-radius: 12px; padding: 1.25rem; text-align: center; text-decoration: none; color: inherit; display: block; transition: opacity 0.2s;">
            <div style="width: 44px; height: 44px; margin: 0 auto 0.6rem; background: rgba(95, 179, 142, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-paper-plane" style="font-size: 1.25rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.25rem; font-size: 1.5rem; font-weight: 700;">{{ $sent_count ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.85rem;">الرسائل الصادرة</p>
        </a>

        <a href="{{ route('wesal.e-office.tasks.index') }}" class="stat-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 1px solid rgba(95, 179, 142, 0.3); border-radius: 12px; padding: 1.25rem; text-align: center; text-decoration: none; color: inherit; display: block; transition: opacity 0.2s;">
            <div style="width: 44px; height: 44px; margin: 0 auto 0.6rem; background: rgba(95, 179, 142, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-tasks" style="font-size: 1.25rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.25rem; font-size: 1.5rem; font-weight: 700;">{{ $tasks_open ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.85rem;">مهام مفتوحة</p>
        </a>

        <a href="{{ route('wesal.requests.show', ['section' => 'leave']) }}" class="stat-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 1px solid rgba(95, 179, 142, 0.3); border-radius: 12px; padding: 1.25rem; text-align: center; text-decoration: none; color: inherit; display: block; transition: opacity 0.2s;">
            <div style="width: 44px; height: 44px; margin: 0 auto 0.6rem; background: rgba(95, 179, 142, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-umbrella-beach" style="font-size: 1.25rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.25rem; font-size: 1.5rem; font-weight: 700;">{{ $leave_pending ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.85rem;">طلبات إجازة معلقة</p>
        </a>

        <a href="{{ route('wesal.requests.show', ['section' => 'leave']) }}" class="stat-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 1px solid rgba(95, 179, 142, 0.3); border-radius: 12px; padding: 1.25rem; text-align: center; text-decoration: none; color: inherit; display: block; transition: opacity 0.2s;">
            <div style="width: 44px; height: 44px; margin: 0 auto 0.6rem; background: rgba(95, 179, 142, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-file-alt" style="font-size: 1.25rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.25rem; font-size: 1.5rem; font-weight: 700;">{{ $leave_total ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.85rem;">إجمالي طلبات الإجازة</p>
        </a>

        <a href="{{ route('wesal.requests.show') }}" class="stat-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 1px solid rgba(95, 179, 142, 0.3); border-radius: 12px; padding: 1.25rem; text-align: center; text-decoration: none; color: inherit; display: block; transition: opacity 0.2s;">
            <div style="width: 44px; height: 44px; margin: 0 auto 0.6rem; background: rgba(95, 179, 142, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-clipboard-list" style="font-size: 1.25rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.25rem; font-size: 1.5rem; font-weight: 700;">{{ $requests_pending ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.85rem;">طلبات معلقة (عام / مالي)</p>
        </a>
    </div>

    <div class="content-card" style="background: rgba(255, 255, 255, 0.06); margin-top: 1.5rem; padding: 1.25rem; border: 1px solid var(--border-color); border-radius: 12px;">
        <h2 style="color: var(--text-primary); margin-bottom: 0.75rem; font-size: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-link" style="color: var(--primary-color); font-size: 0.95rem;"></i>
            روابط سريعة — خاص بك
        </h2>
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
            <a href="{{ route('wesal.e-office.mail.inbox') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; text-decoration: none; color: var(--text-primary); border: 1px solid rgba(255, 255, 255, 0.1); font-size: 0.9rem;">
                <i class="fas fa-inbox" style="color: var(--primary-color);"></i>
                <span>البريد الوارد</span>
            </a>
            <a href="{{ route('wesal.e-office.tasks.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; text-decoration: none; color: var(--text-primary); border: 1px solid rgba(255, 255, 255, 0.1); font-size: 0.9rem;">
                <i class="fas fa-tasks" style="color: var(--primary-color);"></i>
                <span>مهامي</span>
            </a>
            <a href="{{ route('wesal.requests.show', ['section' => 'leave']) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; text-decoration: none; color: var(--text-primary); border: 1px solid rgba(255, 255, 255, 0.1); font-size: 0.9rem;">
                <i class="fas fa-umbrella-beach" style="color: var(--primary-color);"></i>
                <span>طلب إجازة</span>
            </a>
            <a href="{{ route('wesal.requests.show') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; text-decoration: none; color: var(--text-primary); border: 1px solid rgba(255, 255, 255, 0.1); font-size: 0.9rem;">
                <i class="fas fa-clipboard-list" style="color: var(--primary-color);"></i>
                <span>الطلبات الإدارية</span>
            </a>
        </div>
    </div>
</div>
