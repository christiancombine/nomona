<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/core/config.php");

$game = (int)$_GET["gameid"];
$port = (int)$_GET["port"];

$stmt = $db->prepare("
UPDATE gameservers 
SET last_ping = NOW() 
WHERE game_id=? AND port=?
");

$stmt->execute([$game,$port]);

echo "OK";