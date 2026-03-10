<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once($_SERVER["DOCUMENT_ROOT"]."/core/config.php");

$game = (int)$_GET["gameid"];
$port = (int)$_GET["port"];

echo $game;
echo "<br>";
echo $port;
echo "<br>";
$stmt2 = $db->prepare("
SELECT id FROM gameservers
WHERE game_id=? AND port=?
");
$stmt2->execute([$game, $port]);
$server = $stmt2->fetch(PDO::FETCH_ASSOC);

$gameserverId = $server["id"] ?? null;
echo "gameserver_".$gameserverId;
$rcc->closeJob("gameserver_".$gameserverId);
echo "<br>";

$stmt = $db->prepare("
DELETE FROM gameservers 
WHERE game_id=? AND port=?
");

$stmt->execute([$game,$port]);

echo "OK";