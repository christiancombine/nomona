<link rel="stylesheet" href="/CSS/AdminCSS.ashx">
<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/core/config.php";
require_once "Security.php";

$stmt = $db->prepare("SELECT * FROM invite_keys");
$stmt->execute();

$keys = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="AdminContainer">
    <?php require_once "Navbar.php"; ?>
    <h2>Invite Keys</h2>
    <hr>
    <p>This is the invite key manager.</p>

    <button id="GenerateKeyBTN" class="Button">Generate Key</button>
    <p style="display: none;" id="KeyPara">Your key: <b style="color: red;" id="KeyB"></b></p>

    <h2>All Invite Keys</h2>
    <hr>
    <?php foreach ($keys as $key): ?>
        <div id="<?= htmlspecialchars($key["content"]) ?>" class="KeyList_Instance<?= htmlspecialchars($key["id"]) ?>">
            <?php
            if ($key["used"]) {
                $stmt2 = $db->prepare("SELECT username FROM users WHERE id = ?");
                $stmt2->execute([$key["used_by"]]);

                $usedby = $stmt2->fetch(PDO::FETCH_ASSOC)["username"];
            }
            ?>
            <b style="color: <?= $key['used'] ? 'red' : 'green' ?>;"><?= htmlspecialchars($key["content"]) ?> - <?= $key['used'] ? "Used By $usedby" : 'Available' ?></b>
        </div>
    <?php endforeach; ?>
</div>

<script>
    let generateKeyBTN = document.getElementById("GenerateKeyBTN")

   async function generateKey() {
    let response = await fetch("/Invite/Create.ashx");
    let key = await response.text();

    document.getElementById("KeyPara").style.display = "block";
    document.getElementById("KeyB").innerText = key;
}

generateKeyBTN.addEventListener("click", generateKey);
</script>