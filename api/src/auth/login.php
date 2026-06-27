<?php
// ── Imports ──────────────────────────────────────────────────────────────────
use Firebase\JWT\JWT;

require __DIR__ . "/../config/db.php";
$pdo = Database::getInstance();

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$json = json_decode(file_get_contents("php://input"), true);
if (!is_array($json)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON body.']);
    exit;
}

$email    = trim($json['email'] ?? '');
$password = $json['password'] ?? '';

if ($email === '' || $password === '') {
    http_response_code(422);
    echo json_encode(['error' => 'Email and password are required.']);
    exit;
}

$stmt = $pdo->prepare('SELECT id, username, email, password, role FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid email or password.']);
    exit;
}

$secretKey = $_ENV['JWT_SECRET'] ?? null;

if (!$secretKey) {
    http_response_code(500);
    echo json_encode(['error' => 'Server configuration error.']);
    exit;
}

$issuedAt = time();
$expire   = $issuedAt + 3600;

$payload = [
    'iss'   => 'uni-mngmt-sys',
    'aud'   => 'uni-mngmt-sys',
    'iat'   => $issuedAt,
    'exp'   => $expire,
    'sub'   => $user['id'],
    'role'  => $user['role'] ?? 'student',
    'email' => $user['email'],
];

$jwt = JWT::encode($payload, $secretKey, 'HS256');

http_response_code(200);
echo json_encode([
    'message'    => 'Login successful.',
    'token'      => $jwt,
    'expires_in' => 3600,
    'user'       => [
        'id'       => $user['id'],
        'username' => $user['username'],
        'role'     => $user['role'] ?? 'student',
    ],
]);
?>