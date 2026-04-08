# Chart Accounts Bootstrap System

## نظرة عامة

تم إنشاء نظام تلقائي للتشخيص والإصلاح لصفحة دليل الحسابات (`/wesal/finance/chart-accounts`). النظام يقوم تلقائياً بتشخيص المشاكل وإصلاحها بدون الحاجة لتدخل المستخدم.

## المكونات

### 1. Diagnostic Route
**المسار:** `GET /wesal/finance/dev/diag`

**الوظيفة:**
- يعرض معلومات قاعدة البيانات (اسم، host، username)
- يعرض عدد الحسابات وعدد القيود
- يعرض عينة من 5 حسابات
- يعرض آخر 10 migrations

**الحماية:**
- يتطلب تسجيل الدخول (`auth` middleware)
- يتطلب صلاحية `SuperAdmin` أو `FinanceAdmin`
- متاح فقط عندما `FINANCE_DEV_ROUTES=true` في `.env`

### 2. Bootstrap Route
**المسار:** `POST /wesal/finance/dev/bootstrap-accounts`

**الوظيفة:**
تنفيذ كامل للخطوات التالية:
1. تشغيل Migrations (يتجاهل أخطاء الجداول الموجودة)
2. تشغيل FinancePermissionsSeeder (يتجاهل الأخطاء)
3. استيراد الحسابات إذا كانت غير موجودة
4. تشغيل ChartAccountsSeeder
5. مسح Cache

**الحماية:** نفس حماية Diagnostic Route

### 3. ChartAccountImporterService
**المسار:** `app/Services/Finance/ChartAccountImporterService.php`

**الوظائف:**
- `importFromExcel($filePath, $sheetName)`: استيراد من ملف Excel
- `importFromCsv($filePath)`: استيراد من ملف CSV
- `updatePostableFlags()`: تحديث `is_postable` بناءً على وجود أبناء

**منطق الاستيراد:**
- يقرأ المستوى والكود والاسم من الملف
- يحدد النوع والطبيعة من الكود تلقائياً
- يبني `parent_id` بناءً على الكود والمستوى
- يحدد `is_postable`: `false` للحسابات التي لها أبناء، `true` للباقي

### 4. Import Command
**الأمر:** `php artisan finance:import-accounts`

**الوظيفة:**
- محاولة استيراد من `/mnt/data/dlleel.xlsx` (ورقة "م1أ دليل الحسابات الموحد كاملا")
- إذا فشل، محاولة من `/mnt/data/chart_accounts_unified.csv`
- إذا لم يوجد أي ملف، إنشاء حسابات أساسية (6 حسابات)

**الحسابات الأساسية:**
1. الأصول (كود: 1)
   - الأصول المتداولة (كود: 11)
     - النقدية وما في حكمها (كود: 111)
2. الالتزامات وصافي الأصول (كود: 2)
3. الإيرادات (كود: 3)
4. المصروفات (كود: 4)

## الاستخدام

### تفعيل Routes التطويرية
في ملف `.env`:
```env
FINANCE_DEV_ROUTES=true
```

### استخدام Diagnostic
```bash
curl -X GET http://localhost/wesal/finance/dev/diag \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### استخدام Bootstrap
```bash
curl -X POST http://localhost/wesal/finance/dev/bootstrap-accounts \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### استخدام Command مباشرة
```bash
php artisan finance:import-accounts
```

## التحقق من النجاح

### 1. التحقق من الحسابات
```bash
php artisan tinker --execute="use App\Models\ChartAccount; echo ChartAccount::count();"
```
يجب أن يعرض عدد أكبر من 0.

### 2. التحقق من Tree API
```bash
curl http://localhost/wesal/finance/chart-accounts/tree
```
يجب أن يعرض JSON يحتوي على `data` غير فارغ.

### 3. التحقق من الواجهة
افتح `/wesal/finance/chart-accounts` في المتصفح. يجب أن تظهر الشجرة مع الحسابات.

## الأمان

- Routes التطويرية محمية بـ:
  - `auth` middleware (تسجيل الدخول مطلوب)
  - فحص الصلاحيات (`SuperAdmin` أو `FinanceAdmin`)
  - متاحة فقط عندما `FINANCE_DEV_ROUTES=true`

**مهم:** في بيئة الإنتاج، تأكد من تعطيل `FINANCE_DEV_ROUTES` أو حذف هذه Routes.

## الملفات المُنشأة/المُعدّلة

### ملفات جديدة:
1. `app/Http/Controllers/Finance/DevDiagnosticController.php`
2. `app/Http/Controllers/Finance/DevBootstrapController.php`
3. `app/Services/Finance/ChartAccountImporterService.php`
4. `app/Console/Commands/ImportChartAccounts.php`

### ملفات مُعدّلة:
1. `routes/web.php` - إضافة Routes التطويرية
2. `.env` - إضافة `FINANCE_DEV_ROUTES=true`

## ملاحظات

- النظام يدعم الاستيراد من Excel و CSV تلقائياً
- إذا لم يوجد ملف، يتم إنشاء حسابات أساسية تلقائياً
- `is_postable` يتم تحديثه تلقائياً بناءً على وجود أبناء
- Cache يتم مسحه تلقائياً بعد الاستيراد
- النظام يتعامل مع الأخطاء بشكل آمن (لا يتوقف عند أخطاء migrations/seeders)

## الخطوات التالية

1. إضافة ملف Excel/CSV في `/mnt/data/` للاستيراد الكامل
2. تعطيل `FINANCE_DEV_ROUTES` في بيئة الإنتاج
3. إضافة المزيد من الحسابات الأساسية إذا لزم الأمر
