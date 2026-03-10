<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/config.php";
header('Content-Type:text/plain');

$id = (int)$_GET["game"];

$stmt = $db->prepare("SELECT * FROM games WHERE id = ?");
$stmt->execute([$id]);

$game = $stmt->fetch(PDO::FETCH_ASSOC);
$port = isset($_GET['port']) ? (int)$_GET['port'] : 53640;
?>
game:Load("http://nomona.fit/asset/?id=<?= $game["asset_id"] ?>")
Port = <?php echo $port; ?> 
Server =  game:GetService("NetworkServer") 
HostService = game:GetService("RunService")
Server:Start(Port,20) 
game:GetService("RunService"):Run() 
print("NOMONA game-server started!") 

function onJoined(NewPlayer) 
    print("New player found: "..NewPlayer.Name.."")
    game:httpGet("http://nomona.fit/ClientAPI/AddPlayer.ashx?gameid=<?php echo $id;?>",true)
    NewPlayer:LoadCharacter(true) 
    while wait() do 
        if NewPlayer.Character.Humanoid.Health == 0 then
            wait(5) 
            NewPlayer:LoadCharacter(true)
        elseif NewPlayer.Character.Parent  == nil then 
            wait(5) 
            NewPlayer:LoadCharacter(true)
        end 
    end 
end 

function onRemove(OldPlayer)
    game:httpGet("http://nomona.fit/ClientAPI/RemovePlayer.ashx?gameid=<?php echo $id;?>",true)
    
    if #game.Players:GetPlayers() == 0 then
        game:httpGet("http://nomona.fit/ClientAPI/RemoveServer.ashx?gameid=<?php echo $id;?>&port=<?php echo $port;?>&t="..tick(), true)
    end
end

game.Players.PlayerAdded:connect(onJoined) 
game.Players.PlayerRemoving:connect(onRemove)