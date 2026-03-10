<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/core/config.php");

$game = (int)$_GET["gameid"];
$port = (int)$_GET["port"];

$stmt = $db->prepare("
SELECT last_ping 
FROM gameservers
WHERE game_id=? AND port=?
");

$stmt->execute([$game,$port]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$row){
    echo "shutdown";
    exit;
}

$last = strtotime($row["last_ping"]);

if(time() - $last > 60){
    echo "shutdown";
}else{
    echo "ok";
}