<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once($_SERVER["DOCUMENT_ROOT"]."/core/config.php");

$game = (int)$_GET["gameid"];

$stmt = $db->prepare("
UPDATE gameservers 
SET players = players + 1 
WHERE game_id = ?
LIMIT 1
");

$stmt->execute([$game]);

echo "OK";