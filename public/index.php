<?php
/**
 * HOPE Instructor Evaluation System
 * Entry point - redirects to home page
 */
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

// When running with PHP built-in server using this file as router,
// allow real files to be served directly.
if (PHP_SAPI === 'cli-server') {
    $requested = __DIR__ . $uri;
    if ($uri !== '/' && is_file($requested)) {
        return false;
    }
}

// Only redirect actual entrypoint requests.
if (in_array($uri, ['/', '/index.php', '/public/', '/public/index.php'], true)) {
    header('Location: /pages/common/home.php');
    exit;
}

// For non-entrypoint requests, don't force redirects.
if (PHP_SAPI === 'cli-server') {
    return false;
}
