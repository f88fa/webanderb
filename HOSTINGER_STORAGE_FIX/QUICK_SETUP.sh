#!/bin/bash
# سكريبت سريع لإعداد Storage في Hostinger
# استخدم: bash QUICK_SETUP.sh

echo "🔧 إعداد Storage لـ Hostinger..."
echo ""

# إنشاء مجلد storage في public_html
echo "1. إنشاء مجلد storage..."
mkdir -p public_html/storage

# نسخ storage/index.php
echo "2. نسخ storage/index.php..."
cp storage_index.php public_html/storage/index.php

# تعديل الصلاحيات
echo "3. تعديل الصلاحيات..."
chmod 755 public_html/storage/index.php

echo ""
echo "✅ تم الإعداد بنجاح!"
echo ""
echo "الخطوات التالية:"
echo "1. أضف قواعد .htaccess من htaccess_storage_rules.txt"
echo "2. أضف helper functions من helpers_upload_functions.php"
echo "3. اختبر رفع صورة"

