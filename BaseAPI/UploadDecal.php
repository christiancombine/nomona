<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/config.php";

$assetDir = $_SERVER["DOCUMENT_ROOT"] . "/asset/assets/";
$thumbDir = $_SERVER["DOCUMENT_ROOT"] . "/thumbs/catalog/";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $file = $_FILES["file"];

    $name = $_POST["name"] ?? "Decal";
    $description = $_POST["description"] ?? "";
    $price = intval($_POST["price"] ?? 0);
    $currency = $_POST["buywith"] ?? "robux";
    $creator_id = $_SESSION["user_id"];
    $asset_type = "decal";

    if ($file["error"] === 0) {
        $files = scandir($assetDir);
        $assetId = 0;

        foreach ($files as $f) {
            if (is_numeric(pathinfo($f, PATHINFO_FILENAME))) {
                $assetId = max($assetId, intval(pathinfo($f, PATHINFO_FILENAME)) + 1);
            }
        }

        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        $assetPath = $assetDir . $assetId;

        if (move_uploaded_file($file["tmp_name"], $assetPath)) {
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

            $thumbid = $db->lastInsertId();

            $ownedStmt = $db->prepare("
            INSERT INTO owned_items (user_id, asset_id, asset_type)
            VALUES (?, ?, ?)
            ");

            $ownedStmt->execute([
                $_SESSION["user_id"],
                $assetId,
                $asset_type
            ]);

            $thumbPath = $thumbDir . $thumbid . ".png";
            copy($assetPath, $thumbPath);

            echo
            exit;

        } else {
            echo "Upload failed.";
        }

    } else {
        echo "Upload error: " . $file["error"];
    }
}
?>