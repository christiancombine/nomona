<?php
require_once "../../core/config.php";
session_start();

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) die("Not logged in.");

$allowedParts = [
    "head",
    "torso",
    "leftarm",
    "rightarm",
    "leftleg",
    "rightleg"
];

$part  = $_POST['part'] ?? '';
$color = intval($_POST['color'] ?? 0);

if (!in_array($part, $allowedParts)) {
    die("Invalid body part.");
}

if ($color <= 0) {
    die("Invalid color.");
}

$column = $part . "color";

$stmt = $db->prepare("UPDATE users SET $column = ? WHERE id = ?");
$stmt->execute([$color, $user_id]);

echo "OK";