<link rel="stylesheet" href="/CSS/AdminCSS.ashx">

<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/core/config.php";
require_once "Security.php";

$stmt = $db->prepare("SELECT * FROM games ORDER BY id DESC");
$stmt->execute();

$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="AdminContainer">
<?php require_once "Navbar.php"; ?>

<br>
<h2>Game List</h2>
<p>This is a list of <b style="color:red;">ALL</b> games.</p>
<hr>

<div id="GamesList_Admin">

<?php foreach ($games as $game): ?>
<div class="UserList_Instance">

<a href="/Admin/ManageGame.aspx?ID=<?= htmlspecialchars($game["id"]) ?>">
<?= htmlspecialchars($game["name"]) ?>
</a>

| Cool: 
<b style="color:<?= $game["is_cool"] ? "green" : "red" ?>">
<?= $game["is_cool"] ? "YES" : "NO" ?>
</b>

</div>
<?php endforeach; ?>

</div>
</div>