#!/bin/bash

# ============================================
# سكريبت رفع الملفات المحدثة إلى الاستضافة السحابية
# ============================================
# 
# الاستخدام:
# 1. قم بتعديل المتغيرات التالية حسب معلومات الاستضافة:
#    - REMOTE_HOST: عنوان الخادم
#    - REMOTE_USER: اسم المستخدم
#    - REMOTE_PATH: المسار الكامل للموقع على الخادم
#    - SSH_PORT: منفذ SSH (افتراضي: 22)
#
# 2. قم بتشغيل السكريبت:
#    bash upload_updated_files.sh
#
# أو قم بمنحه صلاحيات التنفيذ:
#    chmod +x upload_updated_files.sh
#    ./upload_updated_files.sh
#
# ============================================

# ============================================
# إعدادات الاتصال - قم بتعديلها حسب معلومات الاستضافة
# ============================================

# عنوان الخادم (مثال: example.com أو 192.168.1.100)
REMOTE_HOST="your-server.com"

# اسم المستخدم SSH
REMOTE_USER="your-username"

# المسار الكامل للموقع على الخادم (مثال: /home/username/public_html أو /var/www/html)
REMOTE_PATH="/path/to/your/project"

# منفذ SSH (افتراضي: 22)
SSH_PORT="22"

# ============================================
# المسار المحلي للمشروع
# ============================================
LOCAL_PATH="$(pwd)"

# ============================================
# قائمة الملفات المحدثة
# ============================================
FILES=(
    "app/Http/Controllers/SettingsController.php"
    "resources/views/dashboard/pages/settings.blade.php"
    "resources/views/frontend/index.blade.php"
    "resources/views/frontend/partials/header.blade.php"
    "resources/views/frontend/hero-templates/default.blade.php"
    "public/assets/js/frontend.js"
    "public/assets/css/frontend.css"
    "routes/web.php"
)

# ============================================
# الألوان للرسائل
# ============================================
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# ============================================
# دالة طباعة الرسائل
# ============================================
print_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# ============================================
# التحقق من الإعدادات
# ============================================
if [ "$REMOTE_HOST" == "your-server.com" ] || [ -z "$REMOTE_HOST" ]; then
    print_error "يرجى تعديل إعدادات الاتصال في السكريبت أولاً!"
    exit 1
fi

if [ "$REMOTE_PATH" == "/path/to/your/project" ] || [ -z "$REMOTE_PATH" ]; then
    print_error "يرجى تعديل مسار المشروع على الخادم في السكريبت أولاً!"
    exit 1
fi

# ============================================
# اختبار الاتصال
# ============================================
print_info "جارٍ اختبار الاتصال بالخادم..."
if ssh -p $SSH_PORT -o ConnectTimeout=10 $REMOTE_USER@$REMOTE_HOST "echo 'Connection successful'" > /dev/null 2>&1; then
    print_info "✓ الاتصال ناجح!"
else
    print_error "✗ فشل الاتصال بالخادم!"
    print_warning "تأكد من:"
    echo "  1. عنوان الخادم صحيح: $REMOTE_HOST"
    echo "  2. اسم المستخدم صحيح: $REMOTE_USER"
    echo "  3. منفذ SSH صحيح: $SSH_PORT"
    echo "  4. لديك صلاحيات SSH على الخادم"
    exit 1
fi

# ============================================
# التحقق من وجود الملفات محلياً
# ============================================
print_info "جارٍ التحقق من وجود الملفات محلياً..."
MISSING_FILES=()
for file in "${FILES[@]}"; do
    if [ ! -f "$LOCAL_PATH/$file" ]; then
        MISSING_FILES+=("$file")
    fi
done

if [ ${#MISSING_FILES[@]} -gt 0 ]; then
    print_error "الملفات التالية غير موجودة محلياً:"
    for file in "${MISSING_FILES[@]}"; do
        echo "  - $file"
    done
    exit 1
fi
print_info "✓ جميع الملفات موجودة محلياً!"

# ============================================
# رفع الملفات
# ============================================
print_info "بدء رفع الملفات إلى الخادم..."
echo ""

SUCCESS_COUNT=0
FAILED_COUNT=0

for file in "${FILES[@]}"; do
    print_info "جارٍ رفع: $file"
    
    # إنشاء المجلد على الخادم إذا لم يكن موجوداً
    REMOTE_DIR=$(dirname "$REMOTE_PATH/$file")
    ssh -p $SSH_PORT $REMOTE_USER@$REMOTE_HOST "mkdir -p $REMOTE_DIR" > /dev/null 2>&1
    
    # رفع الملف
    if scp -P $SSH_PORT "$LOCAL_PATH/$file" "$REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH/$file" > /dev/null 2>&1; then
        print_info "  ✓ تم رفع $file بنجاح"
        SUCCESS_COUNT=$((SUCCESS_COUNT + 1))
    else
        print_error "  ✗ فشل رفع $file"
        FAILED_COUNT=$((FAILED_COUNT + 1))
    fi
done

echo ""
print_info "============================================"
print_info "ملخص عملية الرفع:"
print_info "  ✓ نجح: $SUCCESS_COUNT ملف"
if [ $FAILED_COUNT -gt 0 ]; then
    print_error "  ✗ فشل: $FAILED_COUNT ملف"
fi
print_info "============================================"

# ============================================
# تنظيف الكاش (اختياري)
# ============================================
read -p "هل تريد تنظيف كاش Laravel على الخادم؟ (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_info "جارٍ تنظيف الكاش على الخادم..."
    ssh -p $SSH_PORT $REMOTE_USER@$REMOTE_HOST "cd $REMOTE_PATH && php artisan view:clear && php artisan config:clear && php artisan cache:clear" 2>&1
    print_info "✓ تم تنظيف الكاش!"
fi

print_info "اكتملت العملية!"

