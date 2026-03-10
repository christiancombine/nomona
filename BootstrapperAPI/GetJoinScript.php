<?php
require_once "../core/config.php";
header("Content-Type: text/plain");

$data = $_SERVER["REQUEST_METHOD"] === "POST" ? $_POST : $_GET;

$token = $data["token"] ?? null;
$requestedPort = isset($data["port"]) ? (int)$data["port"] : 0;
$gameId = isset($data["gameId"]) ? (int)$data["gameId"] : 0;
$userId = null;

if (!$token || $gameId <= 0) {
    http_response_code(400);
    exit("Invalid request.");
}

try {
    $stmt = $db->prepare("
        SELECT id, username
        FROM users
        WHERE authtoken = :token
        LIMIT 1
    ");
    $stmt->bindParam(":token", $token, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(403);
        exit("Invalid token.");
    }

    $userId = (int)$user["id"];

    $currentHost = "45.248.37.221";

    $basePort = 40000;
    $maxRange = 10000;

    if ($requestedPort) {
        $currentPort = $requestedPort;
    } else {
        $currentPort = $basePort + ($gameId % $maxRange);
    }

} catch (PDOException $e) {
    http_response_code(500);
    exit("Server error.");
}
?>

-- NOMONA JOIN SCRIPT @ 2026

-- How to join a game?
-- Open Nomona.exe, go to Tools -> Execute Script and then execute join.txt

-- EXPERIMENTAL: How to join a game via cmd?
-- CD to C:/nomona and do Nomona.exe -script "join.txt"

-- How to change my character and name?
-- Change the userId variable

local Visit = game:service("Visit")
local Players = game:service("Players")
local NetworkClient = game:service("NetworkClient")

local userId = <?= $userId ?> -- here
local charapp = game:httpGet("http://nomona.fit/ClientAPI/CharacterAppearance.ashx?id="..userId,true)
local playerName = game:httpGet("http://nomona.fit/ClientAPI/GetUsername.ashx?id="..userId,true)

local currentHost = "<?= $currentHost ?>" -- change this if naco provided you with another ip
local currentPort = <?= $currentPort ?> -- change this if naco provided you with another port

local ContentProvider = nil
pcall(function() ContentProvider = game:service("ContentProvider") end)

local function onConnectionRejected()
    game:SetMessage("This game is not available. Please try another")
end

local function onConnectionFailed(_, id, reason)
    game:SetMessage("Failed to connect to the Game. (ID="..id..")")
end

local function onConnectionAccepted(peer, replicator)
    local worldReceiver = replicator:SendMarker()
    local received = false

    local function onWorldReceived()
        received = true
    end

    worldReceiver.Received:connect(onWorldReceived)
    game:SetMessageBrickCount()

    while not received do
        wait(0.3)
    end

    local player = Players.LocalPlayer
    game:SetMessage("Requesting character")

    replicator:RequestCharacter()

    wait(1.2)
    game:ClearMessage()
end

NetworkClient.ConnectionAccepted:connect(onConnectionAccepted)
NetworkClient.ConnectionRejected:connect(onConnectionRejected)
NetworkClient.ConnectionFailed:connect(onConnectionFailed)

game:SetMessage("Connecting to Server")

local success, errorMsg = pcall(function ()
    local player = Players.LocalPlayer
    if not player then
        player = Players:createLocalPlayer(0)
        player.CharacterAppearance = charapp
        player.Name = playerName
    else
        player.CharacterAppearance = charapp
	player.Name = playerName
    end

    NetworkClient:connect(currentHost, currentPort, 0)
end)

if not success then
    game:SetMessage(errorMsg)
end