<?php
$id = $_GET["gameId"]
?>

game:Load("http://nomona.fit/asset/?id=<?= $id ?>")
return game:GetService('ThumbnailGenerator'):Click("PNG", 1000, 500, false)