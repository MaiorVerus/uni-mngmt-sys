<?php

// Load the .env file into $_ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Required variables — app crashes if any are missing
$dotenv->required([
    'DB_HOST', 'DB_NAME', 'DB_USER',
    'JWT_SECRET', 'JWT_ACCESS_EXPIRY'
]);

// App constants — read from .env, never hardcoded
define('DB_HOST',     $_ENV['DB_HOST']);
define('DB_PORT',     $_ENV['DB_PORT']     ?? '3306');
define('DB_NAME',     $_ENV['DB_NAME']);
define('DB_USER',     $_ENV['DB_USER']);
define('DB_PASS',     $_ENV['DB_PASS']     ?? '');

define('JWT_SECRET',          $_ENV['JWT_SECRET']);
define('JWT_ACCESS_EXPIRY',   (int) $_ENV['JWT_ACCESS_EXPIRY']);
define('JWT_REFRESH_EXPIRY',  (int) ($_ENV['JWT_REFRESH_EXPIRY'] ?? 604800));

define('APP_ENV',  $_ENV['APP_ENV']  ?? 'production');
define('APP_NAME', $_ENV['APP_NAME'] ?? 'App');

?>