<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/core/components/metadata.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/core/config.php";

$gameId = intval($_GET["id"] ?? 0);

$stmt = $db->prepare("SELECT * FROM games WHERE id = ?");
$stmt->execute([$gameId]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$game){
    die("Game not found.");
}

if ($game["creator_id"] !== $_USER["id"]) {
    die("Not the owner of this game.");
}
?>

<link rel="stylesheet" href="/CSS/UploadCSS.ashx">

<div id="Container">
<?php require_once $_SERVER["DOCUMENT_ROOT"]."/core/components/header.php"; ?>

<div id="Body">

<h1 style="font-family: Verdana, Geneva, Tahoma, sans-serif;">
Update Game
</h1>

<big>

<table cellspacing="0px" width="100%" style="font-size:12px;">
<tbody>
<tr><th class="tablehead">Instructions</th></tr>
<tr><th class="tablebody">

<p>You can update your Roblox place file and change your game's information.</p>

<ol>
<li>Click "Browse" if you want to upload a new RBXL.</li>
<li>Edit the name or description.</li>
<li>Click <b>Update Game</b>.</li>
</ol>

<p>If you leave the file empty, only the information will update.</p>

</th></tr>
</tbody>
</table>

<br>

<table cellspacing="0px" width="100%" style="font-size:12px;">
<tbody>

<tr>
<th class="tablehead">
Update Game
</th>
</tr>

<tr>
<th class="tablebody">

<form method="post" enctype="multipart/form-data" action="/BaseAPI/UpdateGame.ashx" style="padding:25px; text-align:center;">

<input type="hidden" name="game_id" value="<?= $gameId ?>">

<div class="formrow">
<div>Name</div>
<input type="text" name="name" maxlength="50" value="<?= htmlspecialchars($game["name"]) ?>">
</div>

<div class="formrow">
<div>Description</div>
<textarea name="description" rows="4"><?= htmlspecialchars($game["description"]) ?></textarea>
</div>

<div class="formrow">
<div>RBXL File</div>
<input type="file" name="file" accept=".rbxl">
</div>

<br>

<input type="submit" value="Update Game">

</form>

</th>
</tr>

</tbody>
</table>

</big>

</div>

<?php require_once $_SERVER["DOCUMENT_ROOT"]."/core/components/footer.php"; ?>

</div>