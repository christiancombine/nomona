<?php
require_once "core/config.php";

$gameId = (int)$_GET['gameId'];

$visitStmt = $db->prepare("UPDATE games SET visits = visits + 1 WHERE id = ?");
$visitStmt->execute([$gameId]);

$basePort = 40000;
$maxRange = 10000;
$port = $basePort + ($gameId % $maxRange);

$stmt = $db->prepare("
SELECT * FROM gameservers 
WHERE game_id = ? AND players < max_players
ORDER BY id ASC
LIMIT 1
");
$stmt->execute([$gameId]);
$server = $stmt->fetch(PDO::FETCH_ASSOC);

if ($server) {
    $port = $server['port'];
} else {
    $basePort = 40000;
    $maxRange = 10000;

    $stmt = $db->prepare("INSERT INTO gameservers (game_id, port) VALUES (?, ?)");
    $stmt->execute([$gameId, $port]);
    $jobidfor = $db->lastInsertId();

    $_GET['port'] = $port;
    $_GET['game'] = $gameId;

    ob_start();
    include $_SERVER['DOCUMENT_ROOT'] . "/core/scripts/gameserver.php";
    $script = ob_get_clean();
    $rcc->execScript($script, "gameserver_".$jobidfor, 172800);
}

echo "Joining server...";
$url = "nomonaplayer://launch?gameid=$gameId&port=$port";

header("Location: $url");
exit;
?>