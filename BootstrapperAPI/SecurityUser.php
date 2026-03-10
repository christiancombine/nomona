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

$username = trim($_POST["username"] ?? '');
$password = $_POST["password"] ?? '';

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Missing credentials"
    ]);
    exit;
}

try {
    $stmt = $db->prepare("
        SELECT id, username, password, role, authtoken
        FROM users 
        WHERE username = :username 
        LIMIT 1
    ");
    
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password === $user["password"]) {
        echo json_encode([
            "success" => true,
            "user_id" => $user["id"],
            "username" => $user["username"],
            "role" => $user["role"],
            "token" => $user["authtoken"]
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Invalid credentials"
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Server error $e"
    ]);
}