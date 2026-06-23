
<?php

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Strip the base path so /api/public/ping becomes /ping
$uri = str_replace('/uni-mngmt-sys/api/public', '', $uri);

if ($uri === '/ping' && $method === 'GET') {
    echo json_encode([
        'status'  => 'ok',
        'message' => 'API is alive',
        'app'     => APP_NAME,
        'env'     => APP_ENV,
    ]);
    exit();
}

// 404 fallback
http_response_code(404);
echo json_encode(['error' => 'Route not found']);
?>