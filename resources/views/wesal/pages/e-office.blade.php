<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-desktop" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            المكتب الإلكتروني
        </h1>
        <p class="page-subtitle">نظرة عامة — الرسائل والمهام</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-top: 1.5rem;">
        <a href="{{ route('wesal.e-office.mail.inbox') }}" class="stat-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 1px solid rgba(95, 179, 142, 0.3); border-radius: 12px; padding: 1.25rem; text-align: center; text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
            <div style="width: 44px; height: 44px; margin: 0 auto 0.6rem; background: rgba(95, 179, 142, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-inbox" style="font-size: 1.25rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.25rem; font-size: 1.5rem; font-weight: 700;">{{ $eoffice_inbox_count ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.85rem;">الرسائل الواردة</p>
        </a>

        <a href="{{ route('wesal.e-office.mail.sent') }}" class="stat-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 1px solid rgba(95, 179, 142, 0.3); border-radius: 12px; padding: 1.25rem; text-align: center; text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
            <div style="width: 44px; height: 44px; margin: 0 auto 0.6rem; background: rgba(95, 179, 142, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-paper-plane" style="font-size: 1.25rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.25rem; font-size: 1.5rem; font-weight: 700;">{{ $eoffice_sent_count ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.85rem;">الرسائل الصادرة</p>
        </a>

        <a href="{{ route('wesal.e-office.tasks.index', ['status' => 'open']) }}" class="stat-card" style="background: linear-gradient(135deg, rgba(255, 152, 0, 0.15) 0%, rgba(239, 108, 0, 0.15) 100%); border: 1px solid rgba(255, 152, 0, 0.35); border-radius: 12px; padding: 1.25rem; text-align: center; text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
            <div style="width: 44px; height: 44px; margin: 0 auto 0.6rem; background: rgba(255, 152, 0, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-tasks" style="font-size: 1.25rem; color: #ff9800;"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.25rem; font-size: 1.5rem; font-weight: 700;">{{ $eoffice_tasks_open ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.85rem;">مهام غير منجزة</p>
        </a>

        <a href="{{ route('wesal.e-office.tasks.index', ['status' => 'closed']) }}" class="stat-card" style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.2) 0%, rgba(56, 142, 60, 0.2) 100%); border: 1px solid rgba(76, 175, 80, 0.35); border-radius: 12px; padding: 1.25rem; text-align: center; text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s;">
            <div style="width: 44px; height: 44px; margin: 0 auto 0.6rem; background: rgba(76, 175, 80, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-check-circle" style="font-size: 1.25rem; color: #4caf50;"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.25rem; font-size: 1.5rem; font-weight: 700;">{{ $eoffice_tasks_closed ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.85rem;">مهام منجزة</p>
        </a>
    </div>

    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-top: 1.5rem; padding: 1.25rem;">
        <h2 style="color: var(--text-primary); margin-bottom: 0.75rem; font-size: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-link" style="color: var(--primary-color); font-size: 0.95rem;"></i>
            روابط سريعة
        </h2>
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
            <a href="{{ route('wesal.e-office.mail.inbox') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; text-decoration: none; color: var(--text-primary); border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; font-size: 0.9rem;">
                <i class="fas fa-inbox" style="color: var(--primary-color); font-size: 0.9rem;"></i>
                <span>صندوق الوارد</span>
            </a>
            <a href="{{ route('wesal.e-office.mail.sent') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; text-decoration: none; color: var(--text-primary); border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; font-size: 0.9rem;">
                <i class="fas fa-paper-plane" style="color: var(--primary-color); font-size: 0.9rem;"></i>
                <span>الرسائل المرسلة</span>
            </a>
            <a href="{{ route('wesal.e-office.mail.compose') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1rem; background: var(--primary-color); color: white; border-radius: 8px; text-decoration: none; border: none; transition: all 0.3s ease; font-size: 0.9rem;">
                <i class="fas fa-pen" style="font-size: 0.9rem;"></i>
                <span>رسالة جديدة</span>
            </a>
            <a href="{{ route('wesal.e-office.tasks.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; text-decoration: none; color: var(--text-primary); border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; font-size: 0.9rem;">
                <i class="fas fa-tasks" style="color: var(--primary-color); font-size: 0.9rem;"></i>
                <span>المهام</span>
            </a>
            <a href="{{ route('wesal.e-office.tasks.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1rem; background: var(--primary-color); color: white; border-radius: 8px; text-decoration: none; border: none; transition: all 0.3s ease; font-size: 0.9rem;">
                <i class="fas fa-plus" style="font-size: 0.9rem;"></i>
                <span>مهمة جديدة</span>
            </a>
        </div>
    </div>
</div>
