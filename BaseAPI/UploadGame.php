<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER["DOCUMENT_ROOT"] . "/core/config.php";

$assetDir = $_SERVER["DOCUMENT_ROOT"] . "/asset/assets/";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $file = $_FILES["file"];

    $name = $_POST["name"] ?? "My Game";
    $description = $_POST["description"] ?? "";

    $creator_id = $_SESSION["user_id"];

    if ($file["error"] !== 0) {
        die("Upload error.");
    }

    $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

    if ($ext !== "rbxl") {
        die("Only .rbxl files allowed.");
    }

    // Generate next asset id
    $files = scandir($assetDir);
    $assetId = 0;

    foreach ($files as $f) {
        if (is_numeric(pathinfo($f, PATHINFO_FILENAME))) {
            $assetId = max($assetId, intval(pathinfo($f, PATHINFO_FILENAME)) + 1);
        }
    }

    $targetFile = $assetDir . $assetId;

    if (move_uploaded_file($file["tmp_name"], $targetFile)) {

        $stmt = $db->prepare("
        INSERT INTO games
        (creator_id, name, description, asset_id)
        VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $creator_id,
            $name,
            $description,
            $assetId
        ]);

        $gameId = $db->lastInsertId();

        echo "Game uploaded!";

        $ch = curl_init("http://nomona.fit/Game/Generate.ashx?id=" . $gameId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 200);
        curl_exec($ch);
        curl_close($ch);

        header("Location: /PlaceItem.aspx?ID=".$gameId);
        exit;

    } else {
        echo "Upload failed.";
    }
}
?>