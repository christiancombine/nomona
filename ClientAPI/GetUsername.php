<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once $_SERVER["DOCUMENT_ROOT"] . "/core/config.php";

$id = $_GET["id"] ?? null;

$charapp = "http://nomona.fit/UserAPI/BodyColors.ashx?userId=$id";
$stmt = $db->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$id]);

$username = $stmt->fetch(PDO::FETCH_ASSOC)["username"];

echo $username;