<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/core/config.php");

$game = (int)$_GET["gameid"];

$stmt = $db->prepare("SELECT COUNT(*) FROM players WHERE game_id=?");
$stmt->execute([$game]);

echo $stmt->fetchColumn();