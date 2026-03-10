<?php
header("Content-Type: application/json");

$gameid = isset($_POST["gameId"]) ? (int)$_POST["gameId"] : 0;

if ($gameid <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid gameId"
    ]);
    exit;
}

$basePort = 40000;
$maxRange = 10000;

$port = $basePort + ($gameid % $maxRange);

echo json_encode([
    "success" => true,
    "gameId" => $gameid,
    "port" => $port
]);