<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$thumbDir = $_SERVER["DOCUMENT_ROOT"] . "/thumbs/catalog/";
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/config.php";

$catalogId = $_GET["shirtid"] ?? null;

if (empty($catalogId)) {
    echo "NO";
}

$thumbPath = $thumbDir . $catalogId . ".png";

$stmt = $db->prepare("SELECT asset_id FROM catalog WHERE id = ?");
$stmt->execute([$catalogId]);

$assetId = $stmt->fetch(PDO::FETCH_ASSOC);

$b64 = $rcc->execScript('
local ThumbnailGenerator = game:GetService("ThumbnailGenerator")

game.Players:CreateLocalPlayer(0)
local plr = game.Players.Player
plr.CharacterAppearance = "http://nomona.fit/UserAPI/FakeBC.ashx;http://nomona.fit/asset/?id='.$assetId["asset_id"].'"
plr:LoadCharacter()

return ThumbnailGenerator:Click("PNG", 500, 500, true)
', "RENDER_SHIRT".$catalogId
);

$imagebinary = base64_decode($b64);
file_put_contents($thumbPath, $imagebinary);