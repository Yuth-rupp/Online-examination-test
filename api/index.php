<?php

// Forward static file requests straight to the public folder
if (php_sapi_name() !== 'cli') {
    $uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '');
    if ($uri !== '/' && file_exists(__DIR__ . '/../public' . $uri)) {
        return false;
    }
}

// Boot up Laravel framework components manually
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Clear application internal state configs dynamically for Vercel
$app->useStoragePath('/tmp');

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);