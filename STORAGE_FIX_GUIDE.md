# 🔧 دليل إصلاح مشكلة الصور

## المشكلة
الصور لا تظهر لأن النظام يبحث في المكان الخطأ.

## الحل

### الخطوة 1: ارفع ملف الفحص
ارفع `check_storage_path.php` إلى `public_html/check_storage_path.php`

### الخطوة 2: افتح الملف في المتصفح
```
https://greenatmosphere.org.sa/check_storage_path.php
```

هذا الملف سيعرض لك:
- ✅ المسار الصحيح حيث توجد الصور
- ✅ عدد الملفات الموجودة
- ✅ أمثلة على الملفات

### الخطوة 3: ارفع الملفات المحدثة

1. **ارفع `public/storage/index.php` إلى `public_html/storage/index.php`**
   - تأكد من وجود المجلد: `public_html/storage/`
   - ارفع الملف: `public/storage/index.php` → `public_html/storage/index.php`

2. **ارفع `public/.htaccess` المحدث**

### الخطوة 4: تحقق من الصلاحيات

```bash
chmod 755 public_html/storage/index.php
chmod 755 storage/app/public/uploads
```

### الخطوة 5: اختبر

افتح:
```
https://greenatmosphere.org.sa/storage/uploads/about_1770024869.jpg
```

يجب أن تظهر الصورة.

---

## إذا لم تعمل الصور:

### تحقق من:

1. ✅ **المسار الصحيح:**
   - افتح `check_storage_path.php` لمعرفة المسار
   - تأكد أن الصور موجودة في `storage/app/public/uploads/`

2. ✅ **الملف موجود:**
   - تأكد من وجود `public_html/storage/index.php`

3. ✅ **الصلاحيات:**
   ```bash
   chmod 755 public_html/storage/index.php
   chmod 755 storage/app/public/uploads
   ```

4. ✅ **الـ .htaccess:**
   - تأكد من رفع `public/.htaccess` المحدث

---

## هيكل الملفات المطلوب:

```
public_html/
├── storage/
│   └── index.php          ← ارفع هذا الملف
├── .htaccess             ← ارفع الملف المحدث
└── check_storage_path.php ← ارفع هذا للفحص

storage/
└── app/
    └── public/
        └── uploads/      ← هنا توجد الصور المرفوعة
            ├── about_1770024869.jpg
            └── ...
```

---

## ملاحظات مهمة:

1. **الصور تُرفع إلى:** `storage/app/public/uploads/` ✅
2. **النظام يبحث في:** `storage/app/public/uploads/` ✅
3. **الـ URL:** `/storage/uploads/filename.jpg` ✅
4. **الملف الذي يخدم الصور:** `public_html/storage/index.php` ✅

---

## بعد الإصلاح:

1. ✅ ارفع صورة جديدة من لوحة التحكم
2. ✅ تحقق من وجودها في `storage/app/public/uploads/`
3. ✅ افتح الصورة مباشرة من المتصفح
4. ✅ يجب أن تظهر الصورة في الموقع

