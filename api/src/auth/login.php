<?php
// ── Imports ──────────────────────────────────────────────────────────────────
use Firebase\JWT\JWT;                    // LINE 2: tells PHP where JWT class lives
// without this → fatal "Class not found"

require __DIR__ . "/../config/db.php";   // loads Database class + .env constants
$pdo = Database::getInstance();

// ── Headers ──────────────────────────────────────────────────────────────────
header("Content-Type: application/json");

// ── Parse Request Body ───────────────────────────────────────────────────────
$json = json_decode(file_get_contents("php://input"), true);
if (!is_array($json)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON body.']);
    exit;
}

// ── Extract & Sanitise ───────────────────────────────────────────────────────
$email    = trim($json['email']    ?? '');
$password =      $json['password'] ?? '';

if ($email === '' || $password === '') {
    http_response_code(422);
    echo json_encode(['error' => 'Email and password are required.']);
    exit;
}

// ── Fetch User ───────────────────────────────────────────────────────────────
$stmt = $pdo->prepare('SELECT id, username, email, password, role 
                        FROM users 
                        WHERE email = ?');
// ↑ RECOMMENDATION: select only needed columns, not SELECT *
// Avoids accidentally exposing columns you add later (e.g. reset_token)

$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ── Verify Credentials ───────────────────────────────────────────────────────
if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid email or password.']);
    exit;
}

// ── Load JWT Secret ──────────────────────────────────────────────────────────
$secretKey = $_ENV['JWT_SECRET'] ?? null;

if (!$secretKey) {
    http_response_code(500);
    echo json_encode(['error' => 'Server configuration error.']);
    exit;
}

// ── Build JWT Payload ────────────────────────────────────────────────────────
$issuedAt = time();
$expire   = $issuedAt + 3600;

$payload = [
    'iss'   => 'uni-mngmt-sys',   // issuer:    who created this token
    'aud'   => 'uni-mngmt-sys',   // audience:  who should ACCEPT this token
    // ↑ iss and aud should match — your middleware
    //   will validate aud; 'browser' is non-standard
    'iat'   => $issuedAt,         // issued at: when was it created
    'exp'   => $expire,           // expiry:    when does it die (Unix timestamp)
    'sub'   => $user['id'],       // subject:   standard claim for "who is this for"
    // ↑ use 'sub' not 'id' — it's the JWT standard
    'role'  => $user['role'],     // custom:    your RBAC needs this
    'email' => $user['email'],    // custom:    useful for frontend display
];
// 🚨 NEVER put password, reset_token, or any secret in payload

// ── Sign Token ───────────────────────────────────────────────────────────────
$jwt = JWT::encode($payload, $secretKey, 'HS256');
// ↑ HS256 = HMAC-SHA256, symmetric — same key signs and verifies

// ── Send Response ────────────────────────────────────────────────────────────
// ✅ ONE echo, AFTER all logic is complete
http_response_code(200);
echo json_encode([
    'message'    => 'Login successful.',
    'token'      => $jwt,
    'expires_in' => 3600,          // tells the frontend when to refresh (seconds)
    'user'       => [              
        'id'       => $user['id'],
        'username' => $user['username'],
        'role'     => $user['role'],
    ],
  
]);
?>