<?php

use Illuminate\Http\Request;

// Forward static file requests straight to the public folder
if (php_sapi_name() !== 'cli') {
    $uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '');
    if ($uri !== '/' && file_exists(__DIR__ . '/../public' . $uri)) {
        return false;
    }
}

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
$app = require_once __DIR__.'/../bootstrap/app.php';

// Clear application internal state configs dynamically for Vercel
$app->useStoragePath('/tmp');

// Handle the incoming request (Laravel 11 syntax)
$app->handleRequest(Request::capture());