<?php
session_start();
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/config.php";

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

if (!isset($db)) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection missing"]);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$action = $_GET['action'] ?? null;

$allowed = ["online", "heartbeat", "offline"];

if (!$action || !in_array($action, $allowed)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid action"]);
    exit;
}

try {

    if ($action === "offline") {
        $status = "offline";
    } else {
        $status = "website";
    }

    $stmt = $db->prepare("
        UPDATE users
        SET online = :status,
            last_seen = NOW()
        WHERE id = :id
    ");

    $stmt->execute([
        ":status" => $status,
        ":id" => $userId
    ]);

    echo json_encode([
        "success" => true,
        "status" => $status
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Database error"
    ]);
}