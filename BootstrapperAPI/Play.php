<?php
header("Content-Type: text/plain");

$token = $_REQUEST["token"] ?? null;
$gameId = isset($_REQUEST["gameId"]) ? (int)$_REQUEST["gameId"] : 0;
$port = isset($_REQUEST["port"]) ? (int)$_REQUEST["port"] : 53640;

if (!$token || $gameId <= 0) {
    http_response_code(400);
    exit("-- Invalid request");
}

$token = addslashes($token);
?>
dofile("http://nomona.fit/BootstrapperAPI/GetJoinScript.php?token=<?= $token ?>&gameId=<?= $gameId ?>&port=<?= $port ?>")