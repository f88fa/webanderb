<?php
header('Content-Type: text/plain; charset=utf-8');

echo "=== Laravel Shared Hosting Compatibility Check ===\n\n";

$basePath = __DIR__;

function check($condition, $okMsg, $failMsg) {
    if ($condition) {
        echo "[OK]  $okMsg\n";
    } else {
        echo "[FAIL] $failMsg\n";
    }
}

/* 1. Laravel Core Files */
check(
    file_exists($basePath.'/artisan'),
    "artisan file exists (Laravel detected)",
    "artisan file NOT found (Not a Laravel root)"
);

check(
    file_exists($basePath.'/composer.json'),
    "composer.json exists",
    "composer.json missing"
);

check(
    is_dir($basePath.'/app'),
    "app/ directory exists",
    "app/ directory missing"
);

/* 2. Public Folder */
check(
    is_dir($basePath.'/public'),
    "public/ directory exists",
    "public/ directory missing"
);

check(
    file_exists($basePath.'/public/index.php'),
    "public/index.php exists (Entry point OK)",
    "public/index.php missing"
);

/* 3. Forbidden Plain PHP Files */
$forbidden = ['config.php','database.sql','frontend.php','pages'];
foreach ($forbidden as $item) {
    check(
        !file_exists($basePath.'/'.$item),
        "$item not found (clean Laravel)",
        "$item FOUND (Plain PHP residue)"
    );
}

/* 4. Storage & Cache Permissions */
check(
    is_writable($basePath.'/storage'),
    "storage/ is writable",
    "storage/ NOT writable"
);

check(
    is_writable($basePath.'/bootstrap/cache'),
    "bootstrap/cache is writable",
    "bootstrap/cache NOT writable"
);

/* 5. Environment File */
check(
    file_exists($basePath.'/.env'),
    ".env file exists",
    ".env file missing"
);

/* 6. PHP Version */
$phpVersion = PHP_VERSION;
echo "\nPHP Version: $phpVersion\n";
check(
    version_compare($phpVersion, '8.0', '>='),
    "PHP version is compatible",
    "PHP version is TOO OLD"
);

/* 7. Extensions */
$extensions = ['pdo','pdo_mysql','mbstring','openssl','tokenizer','json','ctype','fileinfo'];
foreach ($extensions as $ext) {
    check(
        extension_loaded($ext),
        "Extension loaded: $ext",
        "Extension MISSING: $ext"
    );
}

echo "\n=== CHECK COMPLETED ===\n";
