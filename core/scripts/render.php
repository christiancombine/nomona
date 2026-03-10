<?php
$charapp = $_GET["charapp"]
?>

local ThumbnailGenerator = game:GetService("ThumbnailGenerator")

game.Players:CreateLocalPlayer(0)
local plr = game.Players.Player
plr.CharacterAppearance = "<?= $charapp ?>"
print("<?= $charapp ?>")
plr:LoadCharacter()

return ThumbnailGenerator:Click("PNG", 500, 500, true)