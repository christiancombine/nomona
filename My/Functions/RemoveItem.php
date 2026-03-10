<?php
require_once "../../core/config.php";
session_start();

$user_id = $_SESSION['user_id'] ?? 0;
$asset_id = intval($_GET['id'] ?? 0);

if (!$user_id || !$asset_id) die("Invalid request");

$stmt = $db->prepare("
    DELETE FROM wearing
    WHERE user_id = ? AND asset_id = ?
");
$stmt->execute([$user_id, $asset_id]);

header("Location: /My/Character.aspx");
exit;