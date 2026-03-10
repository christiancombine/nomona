<?php
require_once "../../core/config.php";

$user_id = $_SESSION['user_id'];
$asset_id = intval($_GET['id'] ?? 0);
$wtype = $_GET['wtype'] ?? '';

$stmt = $db->prepare("
    SELECT 1 FROM owned_items
    WHERE user_id = ? AND asset_id = ?
");
$stmt->execute([$user_id, $asset_id]);

if (!$stmt->fetch()) {
    die("You don't own this item.");
}

$stmt = $db->prepare("
    SELECT COUNT(*) FROM wearing
    WHERE user_id = ? AND asset_type = ?
");
$stmt->execute([$user_id, $wtype]);
$count = $stmt->fetchColumn();

if ($wtype === "hat") {
    if ($count >= 3) {
        die("You can only wear 3 hats.");
    }
} else {
    $stmt = $db->prepare("
        DELETE FROM wearing
        WHERE user_id = ? AND asset_type = ?
    ");
    $stmt->execute([$user_id, $wtype]);
}

$stmt = $db->prepare("
    INSERT INTO wearing (user_id, asset_id, asset_type)
    VALUES (?, ?, ?)
");
$stmt->execute([$user_id, $asset_id, $wtype]);

header("Location: /My/Character.aspx?wtype=$wtype");
exit;