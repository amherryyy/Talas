<?php

declare(strict_types=1);

http_response_code(403);
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, max-age=0, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');

$page = __DIR__ . '/403.html';

if (is_file($page) && is_readable($page)) {
    readfile($page);
    exit;
}

echo '<!doctype html><html lang="en"><meta charset="utf-8"><title>Forbidden</title><h1>403 Forbidden</h1></html>';
