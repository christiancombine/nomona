<?php
session_start();
require_once $_SERVER["DOCUMENT_ROOT"]."/core/db.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = $db->prepare("
SELECT id FROM bans
WHERE banneduser_id = ?
AND expired = 0
ORDER BY banned_at DESC
LIMIT 1
");

$stmt->execute([$user_id]);
$ban = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ban) {
    echo json_encode(["success" => false, "message" => "No active restriction"]);
    exit;
}

$update = $db->prepare("UPDATE bans SET expired = 1 WHERE id = ?");
$update->execute([$ban["id"]]);

echo json_encode([
    "success" => true,
    "message" => "Account reactivated"
]);