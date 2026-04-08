# 🔍 دليل العثور على مسار storage في Hostinger

## الملف: `find_storage_path.php`

### طريقة الاستخدام

1. **ارفع الملف إلى `public_html/find_storage_path.php`**
2. **افتحه في المتصفح:**
   ```
   https://greenatmosphere.org.sa/find_storage_path.php
   ```

## ما يفعله الملف

1. ✅ يفحص جميع المسارات المحتملة لـ `storage/app/public/uploads`
2. ✅ يتحقق من وجود المجلدات
3. ✅ يتحقق من الصلاحيات
4. ✅ يعرض المسار الصحيح للاستخدام

## المسارات المحتملة في Hostinger

### النمط الشائع:
```
/home/u298155993/domains/greenatmosphere.org.sa/public_html/  (DOCUMENT_ROOT)
/home/u298155993/domains/greenatmosphere.org.sa/               (Project Root)
/home/u298155993/domains/greenatmosphere.org.sa/storage/app/public/uploads/  (المسار الصحيح)
```

### المسارات التي يفحصها الملف:

1. **من الجذر الرئيسي (المشروع):**
   ```
   /home/u298155993/domains/greenatmosphere.org.sa/storage/app/public/uploads
   ```

2. **من DOCUMENT_ROOT:**
   ```
   /home/u298155993/domains/greenatmosphere.org.sa/public_html/../storage/app/public/uploads
   ```

3. **من DOCUMENT_ROOT/storage (إذا كان موجود):**
   ```
   /home/u298155993/domains/greenatmosphere.org.sa/public_html/storage/app/public/uploads
   ```

4. **نمط Hostinger المحدد:**
   ```
   /home/[USER]/domains/[DOMAIN]/storage/app/public/uploads
   ```

## بعد العثور على المسار

الملف سيعرض:
- ✅ المسار الصحيح الكامل
- ✅ المسار الحقيقي (realpath)
- ✅ الجذر الرئيسي للمشروع
- ✅ كيفية استخدامه في `storage/index.php`

---

**ارفع الملف وافتحه لمعرفة المسار الصحيح في استضافتك!**

