<link rel="stylesheet" href="/CSS/AdminCSS.ashx">

<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/core/config.php";
require_once "Security.php";

if (!isset($_GET["ID"])) {
    die("Game ID missing.");
}

$id = intval($_GET["ID"]);

/* Get game */
$stmt = $db->prepare("SELECT * FROM games WHERE id = ?");
$stmt->execute([$id]);

$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    die("Game not found.");
}

if (isset($_POST["toggle_cool"])) {

    $stmt = $db->prepare("
        UPDATE games 
        SET is_cool = NOT is_cool 
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    header("Location: ManageGame.aspx?ID=".$id);
    exit;
}

if (isset($_POST["rename"])) {

    $stmt = $db->prepare("
        UPDATE games
        SET name = ?
        WHERE id = ?
    ");

    $stmt->execute([$_POST["new_name"], $id]);

    header("Location: ManageGame.aspx?ID=".$id);
    exit;
}

if (isset($_POST["delete"])) {

    $stmt = $db->prepare("DELETE FROM games WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: /Admin/AdminGames.aspx");
    exit;
}
?>

<div id="AdminContainer">
<?php require_once "Navbar.php"; ?>

<br>
<h2>Manage Game</h2>
<hr>

<h3><?= htmlspecialchars($game["name"]) ?></h3>

<img width="200"
src="http://nomona.fit/thumbs/games/<?= $game["id"] ?>.png">

<br><br>

<b>Game ID:</b> <?= $game["id"] ?><br>
<b>Cool Status:</b> 
<span style="color:<?= $game["is_cool"] ? "green" : "red" ?>">
<?= $game["is_cool"] ? "COOL PLACE" : "Not Cool" ?>
</span>

<br><br>

<form method="POST">
<button name="toggle_cool">
<?= $game["is_cool"] ? "Remove Cool Status" : "Make Cool Place" ?>
</button>
</form>

<h3>Rename Game</h3>

<form method="POST">
<input type="text" name="new_name" value="<?= htmlspecialchars($game["name"]) ?>">
<button name="rename">Rename</button>
</form>

<hr>

<h3 style="color:red;">Danger Zone</h3>

<form method="POST" onsubmit="return confirm('Delete this game permanently?');">
<button name="delete" style="background:red;color:white;">
Delete Game Permanently
</button>
</form>

</div>