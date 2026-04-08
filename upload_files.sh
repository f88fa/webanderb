#!/bin/bash

# ============================================
# أمر رفع الملفات المحدثة إلى الاستضافة السحابية
# ============================================
# 
# قم بتعديل المعلومات التالية فقط:
# ============================================

# عنوان الخادم (مثال: example.com أو 192.168.1.100)
SERVER="your-server.com"

# اسم المستخدم SSH
USER="your-username"

# المسار الكامل للمشروع على الخادم (مثال: /home/username/public_html)
REMOTE_PATH="/path/to/your/project"

# منفذ SSH (افتراضي: 22)
PORT="22"

# ============================================
# لا تعدل أي شيء بعد هذا السطر
# ============================================

echo "جارٍ رفع الملفات المحدثة..."

# رفع الملفات
scp -P $PORT \
    app/Http/Controllers/SettingsController.php \
    resources/views/dashboard/pages/settings.blade.php \
    resources/views/frontend/index.blade.php \
    public/assets/css/frontend.css \
    routes/web.php \
    $USER@$SERVER:$REMOTE_PATH/

echo "تم رفع الملفات بنجاح!"

# تنظيف الكاش
echo "جارٍ تنظيف الكاش..."
ssh -p $PORT $USER@$SERVER "cd $REMOTE_PATH && php artisan view:clear && php artisan config:clear && php artisan cache:clear"

echo "اكتملت العملية!"

