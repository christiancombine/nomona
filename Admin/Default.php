<link rel="stylesheet" href="/CSS/AdminCSS.ashx">
<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/core/config.php";
require_once "Security.php";
?>

<div id="AdminContainer">
    <?php require_once "Navbar.php"; ?>
    <br>
    <h2>Hello, <?= $_USER["username"] ?>!</h2>
    <hr>
    <p>Current Status (of ur nomona acc): <?= $_USER["role"] ?></p>
</div>