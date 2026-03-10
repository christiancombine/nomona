<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/config.php";
$authtoken = $_GET["authtoken"];
$port = $_GET["port"];

$sql = "SELECT * FROM users WHERE authtoken = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$authtoken]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$charapp = "http://nomona.fit/UserAPI/BodyColors.ashx?userId=$id";
$stmt = $db->prepare("
    SELECT asset_id 
    FROM wearing 
    WHERE user_id = ?
");
$stmt->execute([$id]);

while ($row = $stmt->fetch()) {
    $assetId = (int)$row['asset_id'];
    $charapp .= ";http://nomona.fit/asset/?id={$assetId}";
}
?>

local Visit = game:service("Visit")
local Players = game:service("Players")
local NetworkClient = game:service("NetworkClient")

local ContentProvider = nil
pcall(function() ContentProvider = game:service("ContentProvider") end)

local function onConnectionRejected()
    game:SetMessage("This game is not available. Please try another")
end

local function onConnectionFailed(_, id, reason)
    game:SetMessage("Failed to connect to the Game. (ID=17)")
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

    if player.Character then

       player.Character.Humanoid.Health = 0
    end


end

NetworkClient.ConnectionAccepted:connect(onConnectionAccepted)
NetworkClient.ConnectionRejected:connect(onConnectionRejected)
NetworkClient.ConnectionFailed:connect(onConnectionFailed)

game:SetMessage("Connecting to Server")

local success, errorMsg = pcall(function ()
    local player = Players.LocalPlayer
    if not player then
        player = Players:createLocalPlayer(0)
        player.CharacterAppearance = "<?= $charapp ?>"
        player.Name = "<?= $user["username"] ?>"
    else
        player.CharacterAppearance = "<?= $charapp ?>"
        player.Name = "<?= $user["username"] ?>"
    end

    NetworkClient:connect("45.248.37.221", <?= $port ?>, 0)
end)

if not success then
    game:SetMessage(errorMsg)
end