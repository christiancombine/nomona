<?php
require_once "../core/config.php";
$gameID = $_GET["id"] ?? null;

if (empty($gameID)) {
    http_response_code(400);
    echo "InvalidGameID";
}

$stmt = $db->prepare("SELECT * FROM games WHERE id = ?");
$stmt->execute([$gameID]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

$assetId = $game["asset_id"];
$_GET['gameId'] = $assetId;

ob_start();
include $_SERVER['DOCUMENT_ROOT'] . "/core/scripts/rendergame.php";
$script = ob_get_clean();

$result = $rcc->execScript($script, "RENDER_HAT_" . $assetId);
$base64 = $result;

if (str_contains($base64, 'base64,')) {
    $base64 = explode('base64,', $base64)[1];
}

$imageData = base64_decode($base64);

$path = $_SERVER['DOCUMENT_ROOT'] . "/thumbs/games/$gameID.png";
$dir = dirname($path);

if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

file_put_contents($path, $imageData);

echo "OK";