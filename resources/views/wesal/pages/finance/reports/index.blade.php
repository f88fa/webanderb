{{-- التقارير المالية - ترتيب محاسبي احترافي (GAAP للقطاع غير الربحي) --}}
<div class="content-card" dir="rtl">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 class="page-title">
            <i class="fas fa-chart-line" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            التقارير المالية
        </h1>
        <p class="page-subtitle">التقارير المحاسبية بترتيب مهني (ميزان مراجعة → قوائم مالية)</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- 1. ميزان المراجعة --}}
        <a href="{{ route('wesal.finance.chart-accounts.trial-balance') }}"
           class="block p-6 bg-white rounded-xl border-2 border-gray-200 hover:border-emerald-500 hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-emerald-100 flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-gray-800 text-lg mb-1">ميزان المراجعة</h3>
                    <p class="text-gray-600 text-sm">التحقق من توازن الحسابات المدينة والدائنة</p>
                    <span class="inline-flex items-center mt-2 text-emerald-600 text-sm font-medium">
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        عرض
                    </span>
                </div>
            </div>
        </a>

        {{-- 2. كشف حساب عام --}}
        <a href="{{ route('wesal.finance.reports.general-ledger') }}"
           class="block p-6 bg-white rounded-xl border-2 border-gray-200 hover:border-emerald-500 hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-gray-800 text-lg mb-1">كشف حساب عام</h3>
                    <p class="text-gray-600 text-sm">عرض حركة الحسابات مع الرصيد الافتتاحي والختامي</p>
                    <span class="inline-flex items-center mt-2 text-emerald-600 text-sm font-medium">
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        عرض
                    </span>
                </div>
            </div>
        </a>

        {{-- 3. قائمة المركز المالي --}}
        <a href="{{ route('wesal.finance.reports.balance-sheet') }}"
           class="block p-6 bg-white rounded-xl border-2 border-gray-200 hover:border-emerald-500 hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-indigo-100 flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-gray-800 text-lg mb-1">قائمة المركز المالي</h3>
                    <p class="text-gray-600 text-sm">الأصول والالتزامات وحقوق الملكية (لقطاع غير الربحي)</p>
                    <span class="inline-flex items-center mt-2 text-emerald-600 text-sm font-medium">
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        عرض
                    </span>
                </div>
            </div>
        </a>

        {{-- 4. قائمة الدخل (الفائض والعجز) --}}
        <a href="{{ route('wesal.finance.reports.income-statement') }}"
           class="block p-6 bg-white rounded-xl border-2 border-gray-200 hover:border-emerald-500 hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition-colors">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-gray-800 text-lg mb-1">قائمة الدخل (الفائض والعجز)</h3>
                    <p class="text-gray-600 text-sm">الإيرادات والمصروفات وصافي الفائض أو العجز</p>
                    <span class="inline-flex items-center mt-2 text-emerald-600 text-sm font-medium">
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        عرض
                    </span>
                </div>
            </div>
        </a>

        {{-- 5. قائمة الأنشطة حسب الوظيفة --}}
        <a href="{{ route('wesal.finance.reports.statement-activities-by-function') }}"
           class="block p-6 bg-white rounded-xl border-2 border-gray-200 hover:border-emerald-500 hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-amber-100 flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-gray-800 text-lg mb-1">قائمة الأنشطة حسب الوظيفة</h3>
                    <p class="text-gray-600 text-sm">إيرادات ومصروفات (برامج، إدارية، جمع تبرعات)</p>
                    <span class="inline-flex items-center mt-2 text-emerald-600 text-sm font-medium">
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        عرض
                    </span>
                </div>
            </div>
        </a>

        {{-- 6. قائمة التدفقات النقدية --}}
        <a href="{{ route('wesal.finance.reports.cash-flow') }}"
           class="block p-6 bg-white rounded-xl border-2 border-gray-200 hover:border-emerald-500 hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-cyan-100 flex items-center justify-center group-hover:bg-cyan-200 transition-colors">
                    <svg class="w-7 h-7 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-gray-800 text-lg mb-1">قائمة التدفقات النقدية</h3>
                    <p class="text-gray-600 text-sm">المقبوضات والمدفوعات والنقدية وما في حكمها</p>
                    <span class="inline-flex items-center mt-2 text-emerald-600 text-sm font-medium">
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        عرض
                    </span>
                </div>
            </div>
        </a>

        {{-- 7. قائمة التغيرات في صافي الأصول --}}
        <a href="{{ route('wesal.finance.reports.net-assets-changes') }}"
           class="block p-6 bg-white rounded-xl border-2 border-gray-200 hover:border-emerald-500 hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-purple-100 flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                    <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-gray-800 text-lg mb-1">قائمة التغيرات في صافي الأصول</h3>
                    <p class="text-gray-600 text-sm">التحليل الزمني لتغيرات حقوق الملكية (مطلوب للقطاع غير الربحي)</p>
                    <span class="inline-flex items-center mt-2 text-emerald-600 text-sm font-medium">
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        عرض
                    </span>
                </div>
            </div>
        </a>
    </div>

    {{-- سجل القيود المالية (إضافي) --}}
    <div class="mt-8 pt-6 border-t border-gray-200">
        <p class="text-gray-600 text-sm mb-4">تقرير إضافي:</p>
        <a href="{{ route('wesal.finance.reports.financial-movement') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 font-medium transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            سجل القيود المالية
        </a>
        <span class="text-gray-500 text-sm mr-2">— سجل تفصيلي لجميع القيود المرحلة مع تصدير Excel</span>
    </div>

    <div class="mt-8">
        <a href="{{ route('wesal.page', 'finance') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            العودة إلى المالية
        </a>
    </div>
</div>
