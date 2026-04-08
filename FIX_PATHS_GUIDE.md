# 🔧 دليل إصلاح مسارات الصور القديمة

## المشكلة
الصور القديمة المرفوعة سابقاً لا تظهر لأن المسارات في قاعدة البيانات بصيغة خاطئة:
- ❌ `storage/uploads/file.jpg`
- ❌ `/storage/uploads/file.jpg`
- ✅ يجب أن تكون: `uploads/file.jpg`

## الحل

### الملف: `fix_old_image_paths.php`

### طريقة الاستخدام

#### الخطوة 1: ارفع الملف
ارفع `fix_old_image_paths.php` إلى `public_html/fix_old_image_paths.php`

#### الخطوة 2: افتح الملف في المتصفح
```
https://greenatmosphere.org.sa/fix_old_image_paths.php
```

#### الخطوة 3: راجع النتائج
- الملف سيعرض جميع الجداول والأعمدة التي تحتوي على صور
- سيعرض المسارات التي تحتاج إلى إصلاح
- سيعرض أمثلة على التحويلات

#### الخطوة 4: اضغط على "إصلاح جميع المسارات"
- ⚠️ **احذر:** هذا الإجراء دائم
- ✅ تأكد من عمل نسخة احتياطية أولاً
- ✅ بعد الإصلاح، جميع الصور ستظهر

## ما يصلحه الملف

### الجداول:
- ✅ `about_us` → `image`
- ✅ `news` → `image`
- ✅ `staff` → `image`
- ✅ `board_members` → `image`
- ✅ `partners` → `logo`
- ✅ `projects` → `image`
- ✅ `testimonials` → `image`
- ✅ `banner_sections` → `image`
- ✅ `media_videos` → `thumbnail`
- ✅ `media_slides` → `image`
- ✅ `policies` → `file`
- ✅ `regulations` → `file`
- ✅ `site_settings` → جميع حقول الصور

### التحويلات:
- `storage/uploads/file.jpg` → `uploads/file.jpg`
- `/storage/uploads/file.jpg` → `uploads/file.jpg`
- `http://domain.com/storage/uploads/file.jpg` → `uploads/file.jpg`

## بعد الإصلاح

1. ✅ جميع الصور القديمة ستظهر الآن
2. ✅ الصور الجديدة ستُرفع بالصيغة الصحيحة
3. ✅ لا حاجة لإعادة رفع الصور

---

**ملاحظة:** يمكنك حذف الملف بعد الانتهاء من الإصلاح.

