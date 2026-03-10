<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once $_SERVER["DOCUMENT_ROOT"] . "/core/config.php";

$id = $_GET["id"] ?? null;

$charapp = "http://nomona.fit/UserAPI/BodyColors.ashx?userId=$id";
$stmt = $db->prepare("
    SELECT asset_id 
    FROM wearing 
    WHERE user_id = ?
");
$stmt->execute([$id]);

while ($row = $stmt->fetch()) {
    $assetId = (int)$row['asset_id'];
    $charapp .= ";http://nomona.fit/asset/?id={$assetId}";
}

echo $charapp;