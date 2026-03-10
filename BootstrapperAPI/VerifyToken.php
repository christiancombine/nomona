<?php
require_once "../core/config.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]);
    exit;
}

$token = trim($_POST["token"] ?? '');

if (empty($token)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Missing token"
    ]);
    exit;
}

try {
    $stmt = $db->prepare("
        SELECT id, username, role
        FROM users
        WHERE authtoken = :token
        LIMIT 1
    ");

    $stmt->bindParam(":token", $token, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode([
            "success" => true,
            "user_id" => $user["id"],
            "username" => $user["username"],
            "role" => $user["role"]
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Invalid or expired token"
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Server error"
    ]);
}