<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/config.php";

$assetDir = $_SERVER["DOCUMENT_ROOT"] . "/asset/assets/";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $gameId = intval($_POST["game_id"]);
    $name = $_POST["name"] ?? "";
    $description = $_POST["description"] ?? "";

    $creator_id = $_SESSION["user_id"];

    $stmt = $db->prepare("SELECT * FROM games WHERE id = ?");
    $stmt->execute([$gameId]);
    $game = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$game) {
        die("Game not found.");
    }

    if ($game["creator_id"] != $creator_id) {
        die("You don't own this game.");
    }

    $assetId = $game["asset_id"];
    $targetFile = $assetDir . $assetId;

    if (isset($_FILES["file"]) && $_FILES["file"]["error"] === 0) {

        $file = $_FILES["file"];

        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        if ($ext !== "rbxl") {
            die("Only .rbxl files allowed.");
        }

        if (!move_uploaded_file($file["tmp_name"], $targetFile)) {
            die("Failed to replace place file.");
        }
    }

    $stmt = $db->prepare("
        UPDATE games
        SET name = ?, description = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $name,
        $description,
        $gameId
    ]);
    
    $ch = curl_init("http://nomona.fit/Game/Generate.ashx?id=" . $gameId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 200);
    curl_exec($ch);
    curl_close($ch);

    header("Location: /PlaceItem.aspx?ID=" . $gameId);
    exit;
}
?>