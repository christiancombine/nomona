<link rel="stylesheet" href="/CSS/AdminCSS.ashx">
<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/core/config.php";
require_once "Security.php";

$stmt = $db->prepare("SELECT * FROM users");
$stmt->execute();

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="AdminContainer">
    <?php require_once "Navbar.php"; ?>
    <br>
    <h2>User List</h2>
    <p>This is a list of <b style="color: red;">ALL</b> the users on nomona.</p>
    <hr>
    <div id="UserList_Admin">
    <?php foreach ($users as $user): ?>
        <div id="<?= htmlspecialchars($user["username"]) ?>" class="UserList_Instance">
            <a href="/Admin/ManageUser.aspx?ID=<?= htmlspecialchars($user["id"]) ?>"><?= htmlspecialchars($user["username"]) ?> | Status : <?= htmlspecialchars($user["role"]) ?></a>
        </div>
    <?php endforeach; ?>
    </div>
</div>