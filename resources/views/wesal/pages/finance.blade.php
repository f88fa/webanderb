<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-coins"></i> نظرة عامة على المالية
        </h1>
        <p class="page-subtitle">استخدم قائمة «المالية» في الشريط الجانبي للوصول إلى دليل الحسابات، القيود، التقارير، والأوقاف</p>
    </div>

    <!-- بطاقات الإحصائيات -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
        <div class="stat-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 2px solid rgba(95, 179, 142, 0.3); border-radius: 20px; padding: 2rem; text-align: center;">
            <div style="width: 60px; height: 60px; margin: 0 auto 1rem; background: rgba(95, 179, 142, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-sitemap" style="font-size: 1.75rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem; font-size: 2rem; font-weight: 700;">{{ $chartAccountsCount ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 1rem;">دليل الحسابات</p>
        </div>

        <div class="stat-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 2px solid rgba(95, 179, 142, 0.3); border-radius: 20px; padding: 2rem; text-align: center;">
            <div style="width: 60px; height: 60px; margin: 0 auto 1rem; background: rgba(95, 179, 142, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-calendar-alt" style="font-size: 1.75rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem; font-size: 2rem; font-weight: 700;">{{ $fiscalYearsCount ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 1rem;">السنوات المالية</p>
        </div>

        <div class="stat-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 2px solid rgba(95, 179, 142, 0.3); border-radius: 20px; padding: 2rem; text-align: center;">
            <div style="width: 60px; height: 60px; margin: 0 auto 1rem; background: rgba(95, 179, 142, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-file-invoice" style="font-size: 1.75rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem; font-size: 2rem; font-weight: 700;">{{ $journalEntriesCount ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 1rem;">القيود اليومية</p>
        </div>

        <div class="stat-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.2) 0%, rgba(31, 107, 79, 0.2) 100%); border: 2px solid rgba(95, 179, 142, 0.3); border-radius: 20px; padding: 2rem; text-align: center;">
            <div style="width: 60px; height: 60px; margin: 0 auto 1rem; background: rgba(95, 179, 142, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-unlock" style="font-size: 1.75rem; color: var(--primary-color);"></i>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem; font-size: 2rem; font-weight: 700;">{{ $openPeriodsCount ?? 0 }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 1rem;">الفترات المفتوحة</p>
        </div>
    </div>
</div>

<style>
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(95, 179, 142, 0.3);
    border-color: rgba(95, 179, 142, 0.5);
}

.content-card a:hover {
    background: rgba(95, 179, 142, 0.2) !important;
    border-color: rgba(95, 179, 142, 0.4) !important;
    transform: translateX(-5px);
}
</style>
