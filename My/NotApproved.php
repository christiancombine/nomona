<?php
require_once "../core/components/metadata.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/core/config.php";

if (empty($_USER)) {
    header("Location: /Login/Default.aspx");
}

$ban = null;

if (isset($_SESSION['user_id'])) {

    $stmt = $db->prepare("
        SELECT * FROM bans
        WHERE banneduser_id = ?
        AND expired = 0
        ORDER BY banned_at DESC
        LIMIT 1
    ");

    $stmt->execute([$_SESSION['user_id']]);
    $ban = $stmt->fetch(PDO::FETCH_ASSOC);
}

$canReactivate = false;

if ($ban) {

    if ($ban["bantype"] === "warning") {
        $canReactivate = true;
    }

    if ($ban["expires_at"] && strtotime($ban["expires_at"]) <= time()) {
        $canReactivate = true;
    }

    if (!$ban || $ban["expired"] == 1) {
        header("Location: /Default.aspx");
        exit;
    }
}
?>

<div id="Container">
    <?php require_once "../core/components/header.php"; ?>

<div id="Container">
    <?php require_once "../core/components/header.php"; ?>
    <div id="Body" style="
        margin: 150px auto 150px auto;
        width: 500px;
        border: black thin solid;
        font-family: Verdana, Geneva, Tahoma, sans-serif;
        padding: 22px;
      ">

        <h1 style="color:red;">
        <?= $ban['bantype'] === 'deletion'
            ? 'Account Deleted'
            : ($ban['bantype'] === 'warning'
                ? 'Warning'
                : 'Account Banned') ?>
        </h1>

        <?php if ($ban): ?>

            <p><strong>Reason:</strong> <?= htmlspecialchars($ban["reason"] ?? "No reason provided") ?></p>

            <?php if ($ban["bantype"] === "deletion"): ?>
                <p>This ban is <strong>permanent</strong>.</p>
            <?php elseif ($ban["expires_at"]): ?>
                <p>
                    Ban expires on:
                    <strong><?= htmlspecialchars($ban["expires_at"]) ?></strong>
                </p>
            <?php endif; ?>
        <?php else: ?>
            <p>Your account is currently restricted.</p>
        <?php endif; ?>
        <p>Next time, please read <a href="/Info/TermsOfService.aspx">NOMONA'S Terms of Service</a> carefully.</p>
        <p>If you believe this was a mistake, please contact any staff or admin in the NOMONA Discord Server.</p>
        <?php if ($canReactivate): ?>
        <hr>
        <h2>Reactivate Account</h2>
        <p>Your restriction has expired. You may now reactivate your account.</p>
        <button class="Button" id="ReactivateAccount" style="text-align: center;">Reactivate Account</button>
        <?php endif; ?>
    </div>
    <?php require_once "../core/components/footer.php"; ?>
</div>
<script>
const btn = document.getElementById("ReactivateAccount");

if (btn) {

    btn.addEventListener("click", function () {

        fetch("/UserAPI/ReactivateAccount.php", {
            method: "POST"
        })
        .then(r => r.json())
        .then(data => {

            alert(data.message);

            if (data.success) {
                window.location = "/";
            }

        })
        .catch(err => {
            console.error(err);
            alert("Failed to reactivate account");
        });

    });

}
</script>