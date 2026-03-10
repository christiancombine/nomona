<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/config.php";

$catalogId = $_GET["hat"] ?? null;

header("Content-Type: text/plain");

if (!ctype_digit($catalogId)) {
    http_response_code(400);
    echo "InvalidHat";
    exit;
}

$stmt = $db->prepare("SELECT asset_id FROM catalog WHERE id = ?");
$stmt->execute([$catalogId]);
$hat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$hat) {
    http_response_code(404);
    echo "HatNotFound";
    exit;
}

$assetId = $hat["asset_id"];
$_GET['hatId'] = $assetId;

ob_start();
include $_SERVER['DOCUMENT_ROOT'] . "/core/scripts/renderhat.php";
$script = ob_get_clean();

$result = $rcc->execScript($script, "RENDER_HAT_" . $assetId);
$base64 = $result;

if (str_contains($base64, 'base64,')) {
    $base64 = explode('base64,', $base64)[1];
}

$imageData = base64_decode($base64);

$path = $_SERVER['DOCUMENT_ROOT'] . "/thumbs/catalog/$catalogId.png";
$dir = dirname($path);

if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

file_put_contents($path, $imageData);

echo "OK";