# مراجعة وخطط المحاسبة - نظام Wesal

## 1. الأزرار والمسارات الحالية

| الزر | المسار | الحالة |
|------|--------|--------|
| ميزان المراجعة | chart-accounts.trial-balance | ✅ |
| التقارير المالية | reports.index | ✅ |
| دليل الحسابات | chart-accounts.index | ✅ |
| السنوات المالية | fiscal-years.index | ⚠️ يحتاج view |
| الفترات المحاسبية | periods.index | ⚠️ يحتاج view |
| القيود اليومية | journal-entries.index | ✅ |
| إنشاء قيد جديد | journal-entries.select-period | ✅ |
| سند قبض | receipt-voucher.create | ✅ |
| سند صرف | payment-voucher.create | ✅ |
| مراكز التكلفة | - | ❌ لا يوجد مسار |
| الأوقاف (النواقف) | - | ❌ لا يوجد مسار |

## 2. الجداول والأعمدة - حالة قاعدة البيانات

### journal_entries ✅
- entry_no, entry_date, description, entry_type, period_id, status, notes
- posted_at, posted_by, reversed_by, reversed_at, reversal_notes
- total_debit, total_credit, deleted_at
- entry_number (nullable)

### journal_lines ✅
- journal_entry_id, account_id, cost_center_id, fund_id, debit, credit, description

### chart_accounts ✅
- code, name_ar, name_en, parent_id, level, type, nature, is_postable, status

### fiscal_years ✅ (تم إصلاحه)
- year_name, status, start_date, end_date, closed_at, closed_by, notes

### cost_centers ✅
- code, name_ar, name_en, description, center_type, status

### funds ✅ (تم إنشاؤه)
- code, name_ar, name_en, restriction_type (unrestricted/restricted/endowment), status

## 3. المطلوب تنفيذه

1. إنشاء view للسنوات المالية (fiscal-years)
2. إنشاء view للفترات المحاسبية (periods)
3. تعديل FiscalYearController و PeriodController لإرجاع wesal.index مع formType
4. إضافة مراكز التكلفة: routes + controller + views
5. إضافة الأوقاف (Funds): routes + controller + views
6. إضافة روابط في صفحة المالية
