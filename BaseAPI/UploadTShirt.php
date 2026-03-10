<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/config.php";

$tshirtDir = $_SERVER["DOCUMENT_ROOT"] . "/UserGenerated/tshirts/";
$assetDir = $_SERVER["DOCUMENT_ROOT"] . "/asset/assets/";
$thumbDir = $_SERVER["DOCUMENT_ROOT"] . "/thumbs/catalog/";
$imageUrlBase = "http://www.nomona.fit/UserGenerated/tshirts/";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $file = $_FILES["file"];

    $name = $_POST["name"] ?? "T-Shirt";
    $description = $_POST["description"] ?? "";
    $price = intval($_POST["price"] ?? 0);
    $currency = $_POST["buywith"] ?? "robux";
    $creator_id = $_SESSION["user_id"];
    $asset_type = "tshirt";

    $filename = basename($file["name"]);
    $filename = str_replace(" ", "", $filename);

    $targetImage = $tshirtDir . $filename;

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
<Item class="ShirtGraphic" referent="RBX0">
<Properties>
<Content name="Graphic">
<url>'.$imageUrl.'</url>
</Content>
<string name="Name">Shirt Graphic</string>
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
        copy($targetImage, $thumbPath);

        echo "Upload successful! Asset ID: " . $assetId;
        header("Location: /Item.aspx?ID=".$thumbid);

    } else {
        echo "Upload failed. " . $file["error"];
    }
}
?>