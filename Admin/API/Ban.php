<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/core/config.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/Admin/Security.php";

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data["user_id"] ?? null;
$reason = $data["reason"] ?? "";
$type = $data["type"] ?? "warning";
$duration = $data["duration"] ?? null;

if (!$user_id) {
    echo json_encode(["message" => "Invalid user"]);
    exit;
}

$expires_at = null;

if ($type === "regular") {

    if (!$duration || $duration <= 0) {
        echo json_encode(["message" => "Duration required for temporary bans"]);
        exit;
    }

    $expires_at = date("Y-m-d H:i:s", time() + ($duration * 3600));
}

if ($type === "deletion") {
    $expires_at = null;
}

if ($type === "warning") {
    $expires_at = null;
}

$stmt = $db->prepare("
INSERT INTO bans (banneduser_id, reason, bantype, serious, banned_at, expires_at, expired)
VALUES (?, ?, ?, 1, NOW(), ?, 0)
");

$stmt->execute([
    $user_id,
    $reason,
    $type,
    $expires_at
]);

echo json_encode(["message" => "Moderation action applied"]);