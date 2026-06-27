<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require __DIR__ . "/../config/db.php";
$pdo = Database::getInstance();

$json = json_decode(file_get_contents("php://input"), true);
if (!is_array($json)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON body.']);
    exit;
}

$username = trim($json['username'] ?? '');
$email    = trim($json['email']    ?? '');
$password = $json['password'] ?? '';
$role     = strtolower(trim($json['role'] ?? 'student'));

$allowedRoles = ['student', 'lecturer', 'hod', 'admin'];

if ($username === '' || $email === '' || $password === '') {
    http_response_code(422);
    echo json_encode(['error' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['error' => 'Invalid email format.']);
    exit;
}

if (strlen($password) < 8) {
    http_response_code(422);
    echo json_encode(['error' => 'Password must be at least 8 characters.']);
    exit;
}

if (!in_array($role, $allowedRoles, true)) {
    http_response_code(422);
    echo json_encode(['error' => 'Invalid role selected.']);
    exit;
}

$check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$check->execute([$email]);
if ($check->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'Email already registered.']);
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

$stmt = $pdo->prepare(
    'INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)'
);
$stmt->execute([$username, $email, $hash, $role]);

http_response_code(201);
echo json_encode([
    'message' => 'Signup successful.',
    'user'    => [
        'id' => (int) $pdo->lastInsertId(),
        'username' => $username,
        'role' => $role,
    ],
]);

?>