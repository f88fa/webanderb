#!/bin/bash

# سكريبت تصدير قاعدة البيانات

DB_NAME="dashlaravel"
DB_USER="root"
DB_PASS=""
OUTPUT_FILE="dashlaravel_backup_$(date +%Y%m%d_%H%M%S).sql"

echo "🚀 بدء تصدير قاعدة البيانات..."

if [ -z "$DB_PASS" ]; then
    mysqldump -u $DB_USER $DB_NAME > $OUTPUT_FILE
else
    mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $OUTPUT_FILE
fi

if [ $? -eq 0 ]; then
    echo "✅ تم تصدير قاعدة البيانات بنجاح!"
    echo "📁 الملف: $OUTPUT_FILE"
    echo "📊 حجم الملف: $(du -h $OUTPUT_FILE | cut -f1)"
else
    echo "❌ فشل تصدير قاعدة البيانات!"
    exit 1
fi

