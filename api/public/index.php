<?php

// Load Composer's autoloader — makes all installed packages available
require_once __DIR__ . '/../vendor/autoload.php';

// Load our app config (reads .env, defines constants)
require_once __DIR__ . '/../src/config/config.php';

// CORS Headers — must be sent before any output
header('Access-Control-Allow-Origin: http://localhost:5173'); // React dev server)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight requests (browsers send OPTIONS first before the real request)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Load the router
require_once __DIR__ . '/../routes/api.php';

// Note: Apache rewrite rules belong in an .htaccess file, not in PHP.
// If you need URL rewriting, place the following in c:\laragon\www\uni-mngmt-sys\api\public\.htaccess:
//
// RewriteEngine On
// RewriteBase /uni-mngmt-sys/api/public/
// RewriteCond %{REQUEST_FILENAME} !-f
// RewriteCond %{REQUEST_FILENAME} !-d
// RewriteRule ^ index.php [L]

?>