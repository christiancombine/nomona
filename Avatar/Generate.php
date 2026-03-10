<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once $_SERVER["DOCUMENT_ROOT"] . "/core/config.php";

$id = $_GET["userId"] ?? null;

header("Content-Type: text/plain");

if (!ctype_digit($id)) {
    http_response_code(400);
    echo "InvalidUser";
    exit;
}

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
$_GET['charapp'] = $charapp;

ob_start();
include $_SERVER['DOCUMENT_ROOT'] . "/core/scripts/render.php";
$script = ob_get_clean();

$result = $rcc->execScript($script, "RENDER_USER$id");
$base64 = $result;

if (str_contains($base64, 'base64,')) {
    $base64 = explode('base64,', $base64)[1];
}

$imageData = base64_decode($base64);

$path = $_SERVER['DOCUMENT_ROOT'] . "/thumbs/avatar/$id.png";
$dir = dirname($path);

if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

file_put_contents($path, $imageData);
echo "OK";