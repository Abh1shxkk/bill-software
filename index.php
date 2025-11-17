<?php

/**
 * Laravel Application Entry Point (for subdomain/public_html deployment)
 * This file should be placed in public_html/subdomain root
 */

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
$app = require_once __DIR__.'/bootstrap/app.php';

// Set the public path to the public directory
$app->bind('path.public', function() {
    return __DIR__.'/public';
});

// Force correct URL scheme and root
$request = Request::capture();
$app->handleRequest($request);