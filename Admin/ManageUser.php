<link rel="stylesheet" href="/CSS/AdminCSS.ashx">
<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/core/config.php";
require_once "Security.php";

$id = $_GET["ID"] ?? null;

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div id="AdminContainer">
    <?php require_once "Navbar.php"; ?>
    <div id="UserItem">
        <?php
        if (empty($id)) {
            echo "<h2>Invalid ID</h2>";
            exit;
        }
        ?>
        <h2><?= $user["username"] ?></h2>
        <p><?= $user["blurb"] ?></p>
        <hr>
        <button id="ToggleAvatarView" class="Button">View Avatar</button>
        <button id="ViewPublicProfile" class="Button">View Public Profile</button>
        <br>
        <img id="avatar-view" src="http://nomona.fit/thumbs/avatar/<?= $user["id"] ?>.png" width="300" style="display: none;">
        <h2 style="color: red;">Moderation</h2>
        <hr>
        <p>(WARNING) please use this category for serious use.</p>
        <div id="BanPanel">
            <textarea id="BanReason" placeholder="Reason..."></textarea>
            <br><br>

            <label>Ban Type:</label>
            <select id="BanType">
                <option value="warning">Warning</option>
                <option value="regular">Temporary Ban</option>
                <option value="deletion">Permanent Ban</option>
            </select>

            <br><br>

            <div id="DurationPanel" style="display: none;">
                <label>Duration (hours)</label>
                <input type="number" id="BanDuration" placeholder="Leave empty for warning">
            </div>

            <button class="Button" id="BanUser">Ban User</button>
        </div>
        <h2>Currency</h2>
        <hr>
        <p>Use this category to update a users currency.</p>
        <span class="PriceInRobux" style="color: Green; font-weight: bold;">
            M$: <?= $user['robux'] ?>
        </span>
        <span class="PriceInTickets" style="color: #fbb117; font-weight: bold;">
            Tx: <?= $user['tix'] ?>
        </span>
        <form method="post" action="/Admin/API/UpdateCurrency.php">
            <label>Currency Type:</label>
             <input type="hidden" id="custId" name="userId" value="<?= $id ?>">
            <select id="CurrencyType" name="currencytype">
                <option value="robux">Monbux</option>
                <option value="tix">Tix</option>
            </select>

            <br><br>

            <label>Addition Method:</label>
            <select id="AdditionMethod" name="additionmethod">
                <option value="set">Set</option>
                <option value="update">Update</option>
            </select>

            <br><br>

            <label>Amount:</label>
            <input type="number" name="amount" required>

            <br><br>
            <button type="submit" class="Button">Update Currency</button>
        </form>
    </div>
</div>

<script>
document.getElementById("ToggleAvatarView").addEventListener("click", function() {
    const avatar = document.getElementById("avatar-view");

    if (avatar.style.display === "none") {
        avatar.style.display = "block";
        this.textContent = "Hide Avatar";
    } else {
        avatar.style.display = "none";
        this.textContent = "View Avatar";
    }
});

document.getElementById("ViewPublicProfile").addEventListener("click", function() {
    window.location = "/User.aspx?ID=<?= $user["id"] ?>";
});
</script>

<script>
document.getElementById("BanUser").addEventListener("click", function() {

    const reason = document.getElementById("BanReason").value
    const type = document.getElementById("BanType").value
    const duration = document.getElementById("BanDuration").value

    fetch("/Admin/API/Ban.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            user_id: <?= $user["id"] ?>,
            reason: reason,
            type: type,
            duration: duration
        })
    })
    .then(r => r.json())
    .then(data => {
        alert(data.message)
        location.reload()
    })
})

document.getElementById("BanType").addEventListener("change", function () {
    const selectedType = this.value;
    const duartionPanel = document.getElementById("DurationPanel");

    if (selectedType === "regular") {
        duartionPanel.style.display = "block";
    } else {
        duartionPanel.style.display = "none";
    }
});
</script>