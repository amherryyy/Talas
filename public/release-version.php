<?php

declare(strict_types=1);

$markerFile = __DIR__ . '/assets/release-version.txt';

if (! is_file($markerFile) || ! is_readable($markerFile)) {
    http_response_code(404);
    exit;
}

header('Content-Type: text/plain; charset=UTF-8');
header('Cache-Control: no-store, max-age=0, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
header('X-Content-Type-Options: nosniff');

readfile($markerFile);
