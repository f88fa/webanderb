# ✅ قائمة التحقق السريعة للرفع على هوستنجر

## قبل الرفع

- [ ] نسخ احتياطي من قاعدة البيانات المحلية
- [ ] التأكد من أن جميع الملفات محفوظة
- [ ] التأكد من عدم وجود ملفات حساسة في المشروع

## خطوات الرفع السريعة

### 1. قاعدة البيانات
```
[ ] إنشاء قاعدة بيانات MySQL على هوستنجر
[ ] تسجيل بيانات الاتصال (اسم، مستخدم، كلمة مرور)
```

### 2. رفع الملفات
```
[ ] رفع جميع الملفات (ما عدا vendor و node_modules)
[ ] التأكد من رفع ملف .env.example
```

### 3. على السيرفر
```bash
[ ] composer install --no-dev --optimize-autoloader
[ ] cp .env.example .env
[ ] تعديل ملف .env بالبيانات الصحيحة
[ ] php artisan key:generate
[ ] php artisan storage:link
[ ] php artisan migrate --force
[ ] php artisan db:seed --force
[ ] php artisan config:cache
[ ] php artisan route:cache
[ ] php artisan view:cache
[ ] chmod -R 775 storage bootstrap/cache
```

### 4. التحقق
```
[ ] فتح الموقع والتحقق من عمله
[ ] التحقق من لوحة التحكم /dashboard
[ ] التحقق من رفع الصور
[ ] التحقق من قاعدة البيانات
```

## إعدادات ملف .env المطلوبة

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

FILESYSTEM_DISK=public
```

## الصلاحيات المطلوبة

```bash
storage/          → 775
bootstrap/cache/  → 775
storage/app/public/uploads/ → 775
```

## الأوامر السريعة

```bash
# تثبيت المكتبات
composer install --no-dev --optimize-autoloader

# إعداد Laravel
php artisan key:generate
php artisan storage:link
php artisan migrate --force
php artisan db:seed --force

# تحسين الأداء
php artisan config:cache
php artisan route:cache
php artisan view:cache

# الصلاحيات
chmod -R 775 storage bootstrap/cache
```

---

**ملاحظة:** راجع ملف `DEPLOYMENT_GUIDE.md` للتفاصيل الكاملة.

