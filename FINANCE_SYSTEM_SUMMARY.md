# ملخص نظام المالية - Finance System

## الملفات المُنشأة

### 1. Migrations
- `2026_02_09_120135_create_chart_accounts_table.php` - دليل الحسابات
- `2026_02_09_121540_create_fiscal_years_table.php` - السنوات المالية
- `2026_02_09_121541_create_accounting_periods_table.php` - الفترات المحاسبية
- `2026_02_09_121541_create_journal_entries_table.php` - القيود اليومية
- `2026_02_09_121542_create_journal_lines_table.php` - سطور القيود
- `2026_02_09_121543_create_cost_centers_table.php` - مراكز التكلفة
- `2026_02_09_121543_create_audit_logs_table.php` - سجلات التدقيق
- `2026_02_09_121533_create_permission_tables.php` - جداول الصلاحيات (Spatie)

### 2. Models
- `app/Models/ChartAccount.php` - نموذج دليل الحسابات
- `app/Models/FiscalYear.php` - نموذج السنة المالية
- `app/Models/AccountingPeriod.php` - نموذج الفترة المحاسبية
- `app/Models/JournalEntry.php` - نموذج القيد اليومي
- `app/Models/JournalLine.php` - نموذج سطر القيد
- `app/Models/CostCenter.php` - نموذج مركز التكلفة
- `app/Models/AuditLog.php` - نموذج سجل التدقيق
- `app/Models/User.php` - تم تحديثه لإضافة HasRoles trait

### 3. Controllers
- `app/Http/Controllers/Finance/ChartAccountController.php` - إدارة دليل الحسابات
- `app/Http/Controllers/Finance/FiscalYearController.php` - إدارة السنوات المالية
- `app/Http/Controllers/Finance/PeriodController.php` - إدارة الفترات المحاسبية
- `app/Http/Controllers/Finance/JournalEntryController.php` - إدارة القيود اليومية
- `app/Http/Controllers/Finance/CostCenterController.php` - إدارة مراكز التكلفة

### 4. Requests (Validation)
- `app/Http/Requests/Finance/StoreChartAccountRequest.php` - التحقق من إضافة حساب
- `app/Http/Requests/Finance/StoreJournalEntryRequest.php` - التحقق من إضافة قيد

### 5. Policies
- `app/Policies/ChartAccountPolicy.php` - سياسات الوصول لدليل الحسابات

### 6. Seeders
- `database/seeders/ChartAccountsSeeder.php` - استيراد دليل الحسابات من CHART_TEXT
- `database/seeders/FinancePermissionsSeeder.php` - إنشاء الصلاحيات والأدوار

### 7. Views
- `resources/views/wesal/pages/finance.blade.php` - الصفحة الرئيسية للمالية
- `resources/views/wesal/pages/finance/chart-accounts.blade.php` - عرض دليل الحسابات
- `resources/views/wesal/pages/finance/partials/account-tree.blade.php` - عرض الشجرة

### 8. Routes
تم إضافة Routes في `routes/web.php` تحت `wesal/finance`:
- `/wesal/finance/chart-accounts` - دليل الحسابات
- `/wesal/finance/fiscal-years` - السنوات المالية
- `/wesal/finance/periods` - الفترات المحاسبية
- `/wesal/finance/journal-entries` - القيود اليومية

## الميزات المُنفذة

### ✅ دليل الحسابات (Chart of Accounts)
- شجرة حسابية هرمية
- حسابات ثابتة من CHART_TEXT (is_fixed=true)
- إضافة حسابات جديدة تحت أي حساب
- منع الترحيل على الحسابات التجميعية
- البحث والفلترة

### ✅ الفترات المحاسبية والتسويات
- إنشاء سنوات مالية
- إنشاء فترات شهرية تلقائياً
- إغلاق/فتح الترحيل العادي
- إغلاق/فتح التسويات
- إقفال الفترة والسنة المالية

### ✅ القيود اليومية
- إنشاء قيود (عادية/تسوية/افتتاحية/إقفال)
- التحقق من الاتزان (debit = credit)
- منع الترحيل على حسابات تجميعية
- التحقق من صلاحيات الفترة
- ترحيل وعكس القيود

### ✅ الصلاحيات (Spatie Permissions)
- FinanceViewer - عرض فقط
- FinanceAccountant - إنشاء وترحيل قيود
- FinanceAdmin - فتح/إغلاق الفترات
- FinanceSuperAdmin - جميع الصلاحيات

### ✅ Audit Logs
- تسجيل جميع العمليات الحساسة
- تتبع التغييرات
- معلومات المستخدم والـ IP

## الخطوات التالية

### 1. إضافة CHART_TEXT
قم بتحديث `database/seeders/ChartAccountsSeeder.php` في دالة `getChartText()` وأضف جميع الحسابات بالصيغة:
```
LEVEL|CODE|NAME_AR|TYPE|NATURE
```

### 2. تشغيل Migrations
```bash
php artisan migrate
```

### 3. تشغيل Seeders
```bash
php artisan db:seed --class=FinancePermissionsSeeder
php artisan db:seed --class=ChartAccountsSeeder
```

### 4. إضافة Views المتبقية
- صفحة إنشاء/تعديل حساب
- صفحة السنوات المالية
- صفحة الفترات المحاسبية
- صفحة القيود اليومية (إنشاء/عرض)

### 5. إضافة Feature Tests
- منع ترحيل قيد في فترة مغلقة
- منع الترحيل على حساب تجميعي
- السماح بقيود التسوية فقط عند allow_adjustments=true

## ملاحظات مهمة

1. **CHART_TEXT**: يجب إضافة جميع الحسابات في `ChartAccountsSeeder::getChartText()`
2. **الصلاحيات**: تم إنشاء Seeders للصلاحيات، يجب تشغيلها
3. **الـ Views**: بعض الـ Views الأساسية تم إنشاؤها، الباقي يحتاج إكمال
4. **Tests**: لم يتم إنشاؤها بعد، يجب إضافتها

## الحالة الحالية

✅ **مكتمل:**
- Migrations
- Models مع العلاقات
- Controllers الأساسية
- Requests للتحقق
- Seeders (جاهز لـ CHART_TEXT)
- Routes
- Views الأساسية

### إضافات القطاع غير الربحي (NPO)
- **محاسبة الأموال**: جدول `funds` (غير مقيد، مقيد، وقف) وربط `fund_id` في سطور القيود.
- **مراكز التكلفة**: حقل `center_type` (برنامج، إداري، جمع تبرعات) لتقرير قائمة الأنشطة حسب الوظيفة.
- **الميزانيات التقديرية**: جدول `budgets` للمقارنة مع الفعلي لاحقاً.
- **أنواع قيود**: دعم `donation` و `grant` مع بادئات DN و GR.
- **تقرير قائمة الأنشطة حسب الوظيفة**: إيرادات ومصروفات (برامج / إدارية / جمع تبرعات) وصافي النشاط.
- **التوثيق**: `docs/NPO_ACCOUNTING.md` لشرح المعايير والإعداد.

### تحديثات فبراير 2026 (مراجعة قسم المالية)
- **توضيح القيود المرحلة**: إظهار رسالة واضحة عند عرض قيد مرحل بأنه لا يُعدّل (مع الإشارة لإنشاء قيد تسوية أو عكسي).
- **التحقق من المدخلات في التقارير**: عرض تنبيه عند عدم وجود فترات محاسبية، وتوضيح السلوك عند عدم اختيار الفترة (قائمة الدخل، ميزان المراجعة، الميزانية، قائمة الأنشطة).
- **واجهة إدارة الأموال (Funds)**: إكمال الواجهة مع إضافة إمكانية التعديل (Edit) لكل صنف مال، ووضوح النماذج.
- **القائمة الجانبية**: إضافة قائمة فرعية لـ "الفترات المالية" تشمل: السنوات المالية، الفترات المحاسبية.
- **زر إنشاء سند من صفحة القيود**: إظهار أزرار "سند قبض" و"سند صرف" مباشرة عند عرض قائمة القيود (جميع الأنواع) دون الحاجة للتصفية مسبقاً.

### التحديثات (رأي واقتراحات - فبراير 2026)
- **قيود الإقفال الآلية**: تعديل `FiscalYearClosingService` لاستخدام `calculateBalanceForFiscalYear` بدلاً من الرصيد التراكمي، لضمان إقفال إيرادات ومصروفات السنة المالية الحالية فقط.
- **ربط طلبات الصرف بالقيود**: إضافة اعتماد/رفض الطلبات، وزر "تنفيذ الصرف" يفتح نموذج سند الصرف مع البيانات المعبأة، وربط الطلب بالقيد تلقائياً بعد الحفظ.
- **تقرير التدفقات النقدية**: تقرير جديد يعرض رصيد النقدية أول/آخر المدة، المقبوضات، المدفوعات (حسابات 111xxx).
- **Feature Tests**: إضافة `tests/Feature/FinanceFeatureTest.php` لاختبار: منع ترحيل قيد في فترة مغلقة، نجاح الترحيل في فترة مفتوحة، حساب الرصيد حسب السنة المالية، إنشاء قيد الإقفال.

⏳ **قيد الإكمال:**
- إضافة CHART_TEXT الكامل
- إدخال الميزانيات التقديرية (Budgets) ومقارنتها مع الفعلي
