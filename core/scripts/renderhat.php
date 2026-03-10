<?php
$id = $_GET["hatId"]
?>

local ThumbnailGenerator = game:GetService("ThumbnailGenerator")

local hat = game:GetObjects("http://nomona.fit/asset/?id=<?= $id ?>")[1]
hat.Parent = workspace

return ThumbnailGenerator:Click("PNG", 500, 500, true)