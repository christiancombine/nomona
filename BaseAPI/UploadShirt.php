<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER["DOCUMENT_ROOT"] . "/core/config.php";

$shirtDir = $_SERVER["DOCUMENT_ROOT"] . "/UserGenerated/shirts/";
$assetDir = $_SERVER["DOCUMENT_ROOT"] . "/asset/assets/";
$thumbDir = $_SERVER["DOCUMENT_ROOT"] . "/thumbs/catalog/";
$imageUrlBase = "http://www.nomona.fit/UserGenerated/shirts/";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $file = $_FILES["file"];

    $name = $_POST["name"] ?? "Shirt";
    $description = $_POST["description"] ?? "";
    $price = intval($_POST["price"] ?? 0);
    $currency = $_POST["buywith"] ?? "robux";
    $creator_id = $_SESSION["user_id"];
    $asset_type = "shirt";

    $filename = basename($file["name"]);
    $filename = str_replace(" ", "", $filename);

    $targetImage = $shirtDir . $filename;
    if (!file_exists($shirtDir)) {
        die("Shirt directory missing: " . $shirtDir);
    }

    if (move_uploaded_file($file["tmp_name"], $targetImage)) {

        $files = scandir($assetDir);
        $assetId = 0;

        foreach ($files as $f) {
            if (is_numeric(pathinfo($f, PATHINFO_FILENAME))) {
                $assetId = max($assetId, intval(pathinfo($f, PATHINFO_FILENAME)) + 1);
            }
        }

        $imageUrl = $imageUrlBase . $filename;
        $xmlPath = $assetDir . $assetId;

        $xmlContent = '<?xml version="1.0"?>
<roblox xmlns:xmime="http://www.w3.org/2005/05/xmlmime"
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xsi:noNamespaceSchemaLocation="http://www.roblox.com/roblox.xsd"
version="4">
<External>null</External>
<External>nil</External>
<Item class="Shirt" referent="RBX0">
<Properties>
<Content name="ShirtTemplate">
<url>'.$imageUrl.'</url>
</Content>
<string name="Name">Shirt</string>
<bool name="archivable">true</bool>
</Properties>
</Item>
</roblox>';

        file_put_contents($xmlPath, $xmlContent);

        $stmt = $db->prepare("
        INSERT INTO catalog
        (asset_id, creator_id, name, description, price, currency, asset_type, is_for_sale)
        VALUES (?, ?, ?, ?, ?, ?, ?, 1)
        ");

        $stmt->execute([
            $assetId,
            $creator_id,
            $name,
            $description,
            $price,
            $currency,
            $asset_type
        ]);

        $ownedStmt = $db->prepare("
        INSERT INTO owned_items (user_id, asset_id, asset_type)
        VALUES (?, ?, ?)
        ");

        $thumbid = $db->lastInsertId();

        $ownedStmt->execute([
            $_SESSION["user_id"],
            $assetId,
            $asset_type
        ]);

        $thumbPath = $thumbDir . $thumbid . ".png";
        $b64 = $rcc->execScript('
        local ThumbnailGenerator = game:GetService("ThumbnailGenerator")

        game.Players:CreateLocalPlayer(0)
        local plr = game.Players.Player
        plr.CharacterAppearance = "http://nomona.fit/UserAPI/FakeBC.ashx;http://nomona.fit/asset/?id='.$assetId.'"
        plr:LoadCharacter()

        return ThumbnailGenerator:Click("PNG", 500, 500, true)
        '
        );

        $imagebinary = base64_decode($b64);
        file_put_contents($thumbPath, $imagebinary);

        echo "Upload successful! Asset ID: " . $assetId;
        header("Location: /Item.aspx?ID=".$thumbid);

    } else {
        echo "Upload failed. " . $file["error"];
    }
}
?>