# تنفيذ صفحة دليل الحسابات - Chart of Accounts Implementation

## الملفات المُنشأة/المُحدّثة

### 1. Service Layer
- **`app/Services/Finance/ChartAccountBalanceService.php`** (جديد)
  - `calculateBalance()` - حساب رصيد حساب قابل للترحيل
  - `calculateBalanceWithRollup()` - حساب رصيد مع Rollup للحسابات التجميعية
  - `buildTreeWithBalances()` - بناء Tree مع الأرصدة
  - `clearBalanceCache()` - إلغاء Cache عند ترحيل قيد جديد
  - Cache لمدة 5 دقائق (300 ثانية)

### 2. Controllers
- **`app/Http/Controllers/Finance/ChartAccountController.php`** (محدّث)
  - `index()` - عرض الصفحة الرئيسية مع Tree
  - `tree()` - JSON endpoint للـ Tree مع الأرصدة
  - `getAccountDetails()` - JSON endpoint لتفاصيل حساب محدد
  - تم إضافة ChartAccountBalanceService injection

- **`app/Http/Controllers/Finance/LedgerController.php`** (جديد)
  - `index()` - عرض دفتر الأستاذ للحساب
  - حساب الرصيد الافتتاحي والجاري
  - فلترة حسب الفترة أو التاريخ

- **`app/Http/Controllers/Finance/JournalEntryController.php`** (محدّث)
  - إضافة `clearBalanceCache()` عند ترحيل قيد جديد
  - تحديث routes

### 3. Views
- **`resources/views/wesal/pages/finance/chart-accounts.blade.php`** (محدّث بالكامل)
  - Tree على اليمين مع أرصدة
  - بطاقة تفاصيل الحساب في الأعلى
  - أزرار الإجراءات (تحديث، إضافة ابن، دفتر الأستاذ)
  - JavaScript لتحميل Tree وتحديث التفاصيل
  - فلترة حسب الفترة والتاريخ

- **`resources/views/wesal/pages/finance/ledger.blade.php`** (جديد)
  - جدول الحركات مع الرصيد الجاري
  - الرصيد الافتتاحي
  - فلترة حسب الفترة أو التاريخ

- **`resources/views/wesal/index.blade.php`** (محدّث)
  - إضافة case للـ ledger

### 4. Routes
- **`routes/web.php`** (محدّث)
  - `GET /wesal/finance/chart-accounts/tree` - Tree JSON
  - `GET /wesal/finance/chart-accounts/{id}/details` - تفاصيل حساب JSON
  - `GET /wesal/finance/chart-accounts/{id}/ledger` - دفتر الأستاذ

## الميزات المُنفذة

### ✅ Tree مع الأرصدة
- عرض هرمي للحسابات
- أرصدة بجانب كل حساب (موجب/سالب)
- Rollup للأرصدة للحسابات التجميعية
- فتح/إغلاق الأبناء
- Highlight للحساب المحدد

### ✅ بطاقة تفاصيل الحساب
- عرض تفاصيل الحساب المحدد
- الرصيد الحالي
- معلومات الحساب (المستوى، الكود، النوع، الطبيعة، الحالة)
- تحديث تلقائي عند اختيار حساب جديد

### ✅ أزرار الإجراءات
- تحديث بيانات الحساب (Modal/Page)
- إضافة حساب ثانوي (مع parent_id)
- دفتر الأستاذ (صفحة منفصلة)

### ✅ حساب الأرصدة
- حساب من journal_lines المترحلة فقط
- حسب طبيعة الحساب (debit/credit)
- Rollup للحسابات التجميعية
- Cache لمدة 5 دقائق
- Invalidation عند ترحيل قيد جديد

### ✅ دفتر الأستاذ
- جدول الحركات مع الرصيد الجاري
- الرصيد الافتتاحي
- فلترة حسب الفترة أو التاريخ
- إجمالي مدين/دائن

### ✅ الأداء
- Cache للأرصدة (5 دقائق)
- تجنب N+1 queries
- Rollup فعال للحسابات التجميعية

## كيفية الاستخدام

1. **عرض دليل الحسابات:**
   ```
   GET /wesal/finance/chart-accounts
   ```

2. **جلب Tree مع الأرصدة (JSON):**
   ```
   GET /wesal/finance/chart-accounts/tree?period_id=1&as_of=2026-01-31
   ```

3. **جلب تفاصيل حساب (JSON):**
   ```
   GET /wesal/finance/chart-accounts/{id}/details?period_id=1
   ```

4. **عرض دفتر الأستاذ:**
   ```
   GET /wesal/finance/chart-accounts/{id}/ledger?period_id=1&from_date=2026-01-01&to_date=2026-01-31
   ```

## ملاحظات مهمة

1. **Cache:** يتم Cache الأرصدة لمدة 5 دقائق. يتم إلغاؤه تلقائياً عند ترحيل قيد جديد.

2. **Rollup:** للحسابات التجميعية (is_postable=false)، يتم حساب مجموع أرصدة الأبناء.

3. **الرصيد:** يظهر كرقم موجب/سالب حسب طبيعة الحساب.

4. **القيود:** لا يسمح بالترحيل على حسابات تجميعية (is_postable=false).

5. **الحسابات الثابتة:** لا يمكن حذفها أو تعديل code، يمكن تعديل الاسم فقط.

## الخطوات التالية

- [ ] إضافة Modal لتعديل الحساب
- [ ] إضافة Modal لإضافة حساب جديد
- [ ] إضافة بحث في Tree
- [ ] إضافة تصدير PDF لدفتر الأستاذ
- [ ] تحسين Cache باستخدام Tags
- [ ] إضافة Feature Tests
