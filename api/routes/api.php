<?php

// ── Autoloader & Dependencies ─────────────────────────────────────────────
require_once __DIR__ . '/../vendor/autoload.php';
// ↑ Must be first — registers all namespaced classes (JWT, phpdotenv, yours)

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/CourseController.php';
require_once __DIR__ . '/../controllers/GradeController.php';

use App\Middleware\AuthMiddleware;

// ── Request Parsing ───────────────────────────────────────────────────────
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$base = '/uni-mngmt-sys/api/public';
$path = str_replace($base, '', $uri);

header("Content-Type: application/json");

// ── Route Matching ────────────────────────────────────────────────────────
match (true) {

    // 🔓 Public Routes — no token required
    $method === 'POST' && $path === '/auth/signup'
    => AuthController::signup(),

    $method === 'POST' && $path === '/auth/login'
    => AuthController::login(),

    $method === 'GET' && $path === '/ping'
    => (function () {
        http_response_code(200);
        echo json_encode(['status' => 'pong']);
    })(),


    // 🔒 Protected Routes ─────────────────────────────────────────────────

    // Any authenticated user — role doesn't matter, valid token does
    $method === 'GET' && $path === '/dashboard/profile'
    => (function () {
        $user = AuthMiddleware::authenticate();
        // ↑ authenticate() not authorize() — no role restriction here

        http_response_code(200);
        echo json_encode([
            'message' => 'Profile data retrieved successfully.',
            'user'    => [
                'id'    => $user->sub,    // ← 'sub' matches what login.php encoded
                'email' => $user->email,
                'role'  => $user->role,
            ]
        ]);
    })(),

    // Student routes — prefix match, not wildcard string
    $method === 'GET' && str_starts_with($path, '/student/')
    => (function () {
        $student = AuthMiddleware::authorize(['student']);
        // ↑ rejects anyone without a valid token AND without 'student' role

        CourseController::getEnrolled($student->sub);
        // ↑ pass the verified user ID from the token — never trust user input for ID
    })(),

    $method === 'POST' && $path === '/student/assignments/submit'
    => (function () {
        $student = AuthMiddleware::authorize(['student']);
        // ↑ specific path before the prefix catch — order matters in match()

        // CourseController::submitAssignment($student->sub);
        echo json_encode([
            'message' => "Assignment submitted by student ID: {$student->sub}"
        ]);
    })(),

    // Lecturer routes
    $method === 'GET' && str_starts_with($path, '/lecturer/')
    => (function () {
        $lecturer = AuthMiddleware::authorize(['lecturer', 'hod']);
        // ↑ OPINION: HOD can probably see lecturer views too — adjust to your rules

        GradeController::index($lecturer->sub);
    })(),

    // Admin-only
    $method === 'GET' && str_starts_with($path, '/admin/')
    => (function () {
        AuthMiddleware::authorize(['admin']);
        // ↑ admin routes don't need to return $user if controller handles it

        // AdminController::index();
        echo json_encode(['message' => 'Admin area']);
    })(),


    // 🚫 404
    default => (function () {
        http_response_code(404);
        echo json_encode(['error' => 'Route not found.']);
    })()
};
?>