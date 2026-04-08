<div class="content-card">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-home"></i> الرئيسية
            </h1>
            <p class="page-subtitle">إحصائيات الموقع</p>
        </div>
        <a href="{{ route('frontend') }}" target="_blank" class="btn-primary" style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.75rem; background: var(--primary-color); color: white; text-decoration: none; border-radius: 12px; font-weight: 600; transition: all 0.3s ease; border: none; box-shadow: 0 4px 15px rgba(95, 179, 142, 0.3);">
            <i class="fas fa-external-link-alt"></i>
            <span>عرض الموقع</span>
        </a>
    </div>

    <!-- بطاقات الإحصائيات -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
        <!-- الأخبار -->
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 2px solid rgba(95, 179, 142, 0.3); border-radius: 20px; padding: 2rem; text-align: center; transition: all 0.3s ease;">
            <div style="width: 70px; height: 70px; margin: 0 auto 1.5rem; background: rgba(95, 179, 142, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-newspaper" style="font-size: 2rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem; font-size: 2.5rem; font-weight: 700;">{{ $stats['news'] ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem; font-size: 1.1rem;">إجمالي الأخبار</p>
            <p style="color: rgba(95, 179, 142, 0.8); font-size: 0.95rem; margin: 0;">
                <i class="fas fa-check-circle"></i> {{ $stats['active_news'] ?? 0 }} نشط
            </p>
        </div>

        <!-- الخدمات -->
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 2px solid rgba(95, 179, 142, 0.3); border-radius: 20px; padding: 2rem; text-align: center; transition: all 0.3s ease;">
            <div style="width: 70px; height: 70px; margin: 0 auto 1.5rem; background: rgba(95, 179, 142, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-concierge-bell" style="font-size: 2rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem; font-size: 2.5rem; font-weight: 700;">{{ $stats['services'] ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem; font-size: 1.1rem;">إجمالي الخدمات</p>
            <p style="color: rgba(95, 179, 142, 0.8); font-size: 0.95rem; margin: 0;">
                <i class="fas fa-check-circle"></i> {{ $stats['active_services'] ?? 0 }} نشط
            </p>
        </div>

        <!-- الشركاء -->
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 2px solid rgba(95, 179, 142, 0.3); border-radius: 20px; padding: 2rem; text-align: center; transition: all 0.3s ease;">
            <div style="width: 70px; height: 70px; margin: 0 auto 1.5rem; background: rgba(95, 179, 142, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-handshake" style="font-size: 2rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem; font-size: 2.5rem; font-weight: 700;">{{ $stats['partners'] ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem; font-size: 1.1rem;">إجمالي الشركاء</p>
            <p style="color: rgba(95, 179, 142, 0.8); font-size: 0.95rem; margin: 0;">
                <i class="fas fa-check-circle"></i> {{ $stats['active_partners'] ?? 0 }} نشط
            </p>
        </div>

        <!-- مجلس الإدارة -->
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 2px solid rgba(95, 179, 142, 0.3); border-radius: 20px; padding: 2rem; text-align: center; transition: all 0.3s ease;">
            <div style="width: 70px; height: 70px; margin: 0 auto 1.5rem; background: rgba(95, 179, 142, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-users" style="font-size: 2rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem; font-size: 2.5rem; font-weight: 700;">{{ $stats['board_members'] ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem; font-size: 1.1rem;">أعضاء مجلس الإدارة</p>
            <p style="color: rgba(95, 179, 142, 0.8); font-size: 0.95rem; margin: 0;">
                <i class="fas fa-check-circle"></i> {{ $stats['active_board_members'] ?? 0 }} نشط
            </p>
        </div>

        <!-- اللوائح والسياسات -->
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 2px solid rgba(95, 179, 142, 0.3); border-radius: 20px; padding: 2rem; text-align: center; transition: all 0.3s ease;">
            <div style="width: 70px; height: 70px; margin: 0 auto 1.5rem; background: rgba(95, 179, 142, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-file-alt" style="font-size: 2rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem; font-size: 2.5rem; font-weight: 700;">{{ $stats['policies'] ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem; font-size: 1.1rem;">إجمالي اللوائح/السياسات</p>
            <p style="color: rgba(95, 179, 142, 0.8); font-size: 0.95rem; margin: 0;">
                <i class="fas fa-check-circle"></i> {{ $stats['active_policies'] ?? 0 }} نشط
            </p>
            <p style="color: rgba(255, 255, 255, 0.6); font-size: 0.85rem; margin-top: 0.5rem;">
                في {{ $stats['policies_categories'] ?? 0 }} تصنيف
            </p>
        </div>

        <!-- المشاريع -->
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 2px solid rgba(95, 179, 142, 0.3); border-radius: 20px; padding: 2rem; text-align: center; transition: all 0.3s ease;">
            <div style="width: 70px; height: 70px; margin: 0 auto 1.5rem; background: rgba(95, 179, 142, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-project-diagram" style="font-size: 2rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem; font-size: 2.5rem; font-weight: 700;">{{ $stats['projects'] ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem; font-size: 1.1rem;">إجمالي المشاريع</p>
            <p style="color: rgba(95, 179, 142, 0.8); font-size: 0.95rem; margin: 0;">
                <i class="fas fa-check-circle"></i> {{ $stats['active_projects'] ?? 0 }} نشط
            </p>
        </div>

        <!-- عناصر القائمة -->
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 2px solid rgba(95, 179, 142, 0.3); border-radius: 20px; padding: 2rem; text-align: center; transition: all 0.3s ease;">
            <div style="width: 70px; height: 70px; margin: 0 auto 1.5rem; background: rgba(95, 179, 142, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-bars" style="font-size: 2rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem; font-size: 2.5rem; font-weight: 700;">{{ $stats['menu_items'] ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem; font-size: 1.1rem;">عناصر القائمة</p>
            <p style="color: rgba(95, 179, 142, 0.8); font-size: 0.95rem; margin: 0;">
                <i class="fas fa-check-circle"></i> {{ $stats['active_menu_items'] ?? 0 }} نشط
            </p>
        </div>
    </div>

    <!-- روابط سريعة -->
    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-top: 2rem;">
        <h2 style="color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-link" style="color: var(--primary-color);"></i>
            روابط سريعة
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <a href="{{ route('dashboard', ['page' => 'news']) }}" class="quick-link-btn" style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; background: rgba(255, 255, 255, 0.1); border-radius: 12px; text-decoration: none; color: var(--text-primary); transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.1);">
                <i class="fas fa-newspaper" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                <span>الأخبار</span>
            </a>
            <a href="{{ route('dashboard', ['page' => 'services']) }}" class="quick-link-btn" style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; background: rgba(255, 255, 255, 0.1); border-radius: 12px; text-decoration: none; color: var(--text-primary); transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.1);">
                <i class="fas fa-concierge-bell" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                <span>الخدمات</span>
            </a>
            <a href="{{ route('dashboard', ['page' => 'partners']) }}" class="quick-link-btn" style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; background: rgba(255, 255, 255, 0.1); border-radius: 12px; text-decoration: none; color: var(--text-primary); transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.1);">
                <i class="fas fa-handshake" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                <span>الشركاء</span>
            </a>
            <a href="{{ route('dashboard', ['page' => 'board-members']) }}" class="quick-link-btn" style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; background: rgba(255, 255, 255, 0.1); border-radius: 12px; text-decoration: none; color: var(--text-primary); transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.1);">
                <i class="fas fa-users" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                <span>مجلس الإدارة</span>
            </a>
            <a href="{{ route('dashboard', ['page' => 'policies']) }}" class="quick-link-btn" style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; background: rgba(255, 255, 255, 0.1); border-radius: 12px; text-decoration: none; color: var(--text-primary); transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.1);">
                <i class="fas fa-file-alt" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                <span>اللوائح والسياسات</span>
            </a>
            <a href="{{ route('dashboard', ['page' => 'projects']) }}" class="quick-link-btn" style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; background: rgba(255, 255, 255, 0.1); border-radius: 12px; text-decoration: none; color: var(--text-primary); transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.1);">
                <i class="fas fa-project-diagram" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                <span>مشاريعنا</span>
            </a>
            <a href="{{ route('dashboard', ['page' => 'testimonials']) }}" class="quick-link-btn" style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; background: rgba(255, 255, 255, 0.1); border-radius: 12px; text-decoration: none; color: var(--text-primary); transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.1);">
                <i class="fas fa-quote-left" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                <span>ماذا قالوا عنا</span>
            </a>
            <a href="{{ route('dashboard', ['page' => 'menu']) }}" class="quick-link-btn" style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; background: rgba(255, 255, 255, 0.1); border-radius: 12px; text-decoration: none; color: var(--text-primary); transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.1);">
                <i class="fas fa-bars" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                <span>القائمة العلوية</span>
            </a>
        </div>
    </div>
</div>

<style>
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(95, 179, 142, 0.3);
    border-color: rgba(95, 179, 142, 0.5);
}

.quick-link-btn:hover {
    background: rgba(95, 179, 142, 0.2) !important;
    border-color: rgba(95, 179, 142, 0.4) !important;
    transform: translateX(-5px);
}

.page-header a.btn-primary:hover {
    background: var(--primary-dark) !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(95, 179, 142, 0.4);
}

@media (max-width: 768px) {
    .stat-card {
        padding: 1.5rem !important;
    }
    
    .stat-card h3 {
        font-size: 2rem !important;
    }
}
</style>

