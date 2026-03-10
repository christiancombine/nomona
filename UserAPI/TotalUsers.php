<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/config.php";

header("Content-Type: text/plain");

$stmt = $db->prepare("SELECT COUNT(*) FROM users");
$stmt->execute();

$totalUsers = $stmt->fetchColumn();

echo $totalUsers;