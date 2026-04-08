# دليل رفع المشروع على هوستنجر السحابية

## ✅ التحقق من الجاهزية

المشروع جاهز للرفع على هوستنجر السحابية. تأكد من تنفيذ الخطوات التالية:

---

## 📋 الخطوات المطلوبة للرفع

### 1. إعداد قاعدة البيانات على هوستنجر

1. سجل الدخول إلى لوحة تحكم هوستنجر
2. أنشئ قاعدة بيانات MySQL جديدة
3. سجل بيانات الاتصال:
   - اسم قاعدة البيانات
   - اسم المستخدم
   - كلمة المرور
   - عنوان الخادم (عادة `localhost`)

### 2. رفع الملفات

#### أ. الملفات التي يجب رفعها:
- ✅ جميع ملفات المشروع **ما عدا**:
  - مجلد `vendor/` (سيتم تثبيته على السيرفر)
  - مجلد `node_modules/` (غير مطلوب للإنتاج)
  - ملف `.env` (سيتم إنشاؤه على السيرفر)
  - ملف `database/database.sqlite` (إذا كان موجوداً)

#### ب. طريقة الرفع:
1. استخدم FTP أو File Manager في لوحة تحكم هوستنجر
2. ارفع جميع الملفات إلى المجلد الرئيسي للموقع (عادة `public_html` أو `www`)

**⚠️ ملاحظة مهمة:** في هوستنجر، يجب رفع محتويات مجلد `public/` إلى المجلد الرئيسي، ورفع باقي الملفات إلى مجلد أعلى.

### 3. إعداد ملف `.env` على السيرفر

1. انسخ ملف `.env.example` إلى `.env` على السيرفر
2. عدّل الملف بالبيانات التالية:

```env
APP_NAME="اسم_الموقع"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

APP_LOCALE=ar
APP_FALLBACK_LOCALE=ar

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=اسم_قاعدة_البيانات
DB_USERNAME=اسم_المستخدم
DB_PASSWORD=كلمة_المرور

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=database
QUEUE_CONNECTION=database

FILESYSTEM_DISK=public

LOG_CHANNEL=stack
LOG_LEVEL=error
```

### 4. تنفيذ الأوامر على السيرفر

اتصل بالسيرفر عبر SSH أو استخدم Terminal في لوحة تحكم هوستنجر، ثم نفّذ:

```bash
# الانتقال إلى مجلد المشروع
cd /path/to/your/project

# تثبيت المكتبات
composer install --no-dev --optimize-autoloader

# إنشاء مفتاح التطبيق
php artisan key:generate

# ربط مجلد التخزين
php artisan storage:link

# تشغيل Migrations
php artisan migrate --force

# تشغيل Seeders (لإضافة البيانات الافتراضية)
php artisan db:seed --force

# تحسين الأداء
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. إعداد الصلاحيات

تأكد من أن الصلاحيات صحيحة:

```bash
# إعطاء صلاحيات الكتابة لمجلدات التخزين
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# إنشاء مجلد uploads إذا لم يكن موجوداً
mkdir -p storage/app/public/uploads
chmod -R 775 storage/app/public/uploads
```

### 6. إعداد ملف `.htaccess` (إذا لزم الأمر)

تأكد من وجود ملف `.htaccess` في المجلد الرئيسي (مجلد `public/`) مع المحتوى الصحيح.

---

## 🔧 إعدادات إضافية لهوستنجر

### 1. إعداد PHP Version
- تأكد من أن إصدار PHP هو **8.2** أو أعلى
- يمكن تغيير الإصدار من لوحة تحكم هوستنجر

### 2. إعدادات PHP المطلوبة
تأكد من تفعيل الإضافات التالية:
- ✅ `mod_rewrite` (لإعادة التوجيه)
- ✅ `PDO` و `PDO_MySQL` (للاتصال بقاعدة البيانات)
- ✅ `OpenSSL` (للتشفير)
- ✅ `Mbstring` (للمعالجة النصية)
- ✅ `Tokenizer` (للمعالجة النصية)
- ✅ `XML` (للمعالجة)
- ✅ `Ctype` (للتحقق من الأنواع)
- ✅ `JSON` (للمعالجة)

### 3. إعدادات قاعدة البيانات
- تأكد من أن قاعدة البيانات MySQL جاهزة
- تأكد من أن المستخدم لديه جميع الصلاحيات المطلوبة

---

## ✅ قائمة التحقق النهائية

قبل الإطلاق، تأكد من:

- [ ] رفع جميع الملفات (ما عدا vendor و node_modules)
- [ ] إنشاء ملف `.env` مع البيانات الصحيحة
- [ ] تثبيت المكتبات عبر `composer install`
- [ ] إنشاء مفتاح التطبيق `php artisan key:generate`
- [ ] ربط مجلد التخزين `php artisan storage:link`
- [ ] تشغيل Migrations `php artisan migrate`
- [ ] تشغيل Seeders `php artisan db:seed`
- [ ] تعيين الصلاحيات الصحيحة للمجلدات
- [ ] تفعيل `APP_DEBUG=false` في ملف `.env`
- [ ] تحديث `APP_URL` بالرابط الصحيح
- [ ] اختبار الموقع بعد الرفع

---

## 🐛 حل المشاكل الشائعة

### مشكلة: خطأ 500 Internal Server Error
**الحل:**
1. تحقق من ملف `.env` وبيانات قاعدة البيانات
2. تحقق من الصلاحيات: `chmod -R 775 storage bootstrap/cache`
3. تحقق من ملف السجلات: `storage/logs/laravel.log`

### مشكلة: الصور لا تظهر
**الحل:**
1. تأكد من تنفيذ: `php artisan storage:link`
2. تحقق من الصلاحيات: `chmod -R 775 storage/app/public`

### مشكلة: خطأ في قاعدة البيانات
**الحل:**
1. تحقق من بيانات الاتصال في ملف `.env`
2. تأكد من أن قاعدة البيانات موجودة
3. تأكد من أن المستخدم لديه الصلاحيات المطلوبة

### مشكلة: الصفحات تعرض 404
**الحل:**
1. تأكد من وجود ملف `.htaccess` في المجلد الرئيسي
2. تحقق من تفعيل `mod_rewrite` في Apache
3. نفّذ: `php artisan route:cache`

---

## 📞 الدعم

إذا واجهت أي مشاكل، راجع:
- ملف السجلات: `storage/logs/laravel.log`
- وثائق Laravel: https://laravel.com/docs
- وثائق هوستنجر: https://www.hostinger.com/tutorials

---

## 🔒 الأمان

**تأكد من:**
- ✅ `APP_DEBUG=false` في الإنتاج
- ✅ `APP_ENV=production` في الإنتاج
- ✅ عدم رفع ملف `.env` إلى Git
- ✅ استخدام كلمات مرور قوية لقاعدة البيانات
- ✅ تحديث Laravel والمكتبات بانتظام

---

**تم إنشاء هذا الدليل في:** {{ date('Y-m-d') }}
**إصدار Laravel:** 12.x
**إصدار PHP المطلوب:** 8.2+

