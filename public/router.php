<?php
$path = __DIR__ . parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
if (is_file($path)) {
    return false; // отдаём статический файл
}

// Все остальные запросы — через index.php
require __DIR__ . '/index.php';