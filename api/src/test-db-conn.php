<?php






require_once __DIR__ . '/../config/db.php';

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->query("SELECT 1");

    echo json_encode([
        "status"  => "success",
        "message" => "Database connected successfully!"
    ]);

} catch (RuntimeException $e) {
    // This is the safe, public-facing message from our catch block
    http_response_code(500);
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}