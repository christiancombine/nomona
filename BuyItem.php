<?php
require_once "core/components/metadata.php";

if (!isset($_SESSION["user_id"])) {
    exit("You must be logged in.");
}

if (!isset($_GET["ID"]) || !ctype_digit($_GET["ID"])) {
    exit("Invalid item.");
}

$userId = $_SESSION["user_id"];
$itemId = (int)$_GET["ID"];

try {
    $db->beginTransaction();

    $stmt = $db->prepare("SELECT * FROM catalog WHERE id = ? FOR UPDATE");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item || !$item["is_for_sale"]) {
        throw new Exception("Item not for sale.");
    }

    $stmt = $db->prepare("SELECT id FROM owned_items WHERE user_id = ? AND asset_id = ?");
    $stmt->execute([$userId, $item["asset_id"]]);
    if ($stmt->rowCount() > 0) {
        throw new Exception("You already own this item.");
    }

    $stmt = $db->prepare("SELECT * FROM users WHERE id = ? FOR UPDATE");
    $stmt->execute([$userId]);
    $buyer = $stmt->fetch(PDO::FETCH_ASSOC);

    $price = (int)$item["price"];
    $currency = $item["currency"];

    if ($currency === "robux") {
        if ($buyer["robux"] < $price) {
            throw new Exception("Not enough M$.");
        }

        $stmt = $db->prepare("UPDATE users SET robux = robux - ? WHERE id = ?");
        $stmt->execute([$price, $userId]);

        $stmt = $db->prepare("UPDATE users SET robux = robux + ? WHERE id = ?");
        $stmt->execute([$price, $item["creator_id"]]);

    } else {
        if ($buyer["tix"] < $price) {
            throw new Exception("Not enough Tickets.");
        }

        $stmt = $db->prepare("UPDATE users SET tix = tix - ? WHERE id = ?");
        $stmt->execute([$price, $userId]);

        $stmt = $db->prepare("UPDATE users SET tix = tix + ? WHERE id = ?");
        $stmt->execute([$price, $item["creator_id"]]);
    }

    $stmt = $db->prepare("
        INSERT INTO owned_items (user_id, asset_id, asset_type)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([
        $userId,
        $item["asset_id"],
        $item["asset_type"]
    ]);

    $stmt = $db->prepare("UPDATE catalog SET sold_times = sold_times + 1 WHERE id = ?");
    $stmt->execute([$itemId]);

    $db->commit();

    header("Location: /Item.aspx?ID=" . $itemId);
    exit;

} catch (Exception $e) {

    $db->rollBack();
    exit("Purchase failed: " . $e->getMessage());
}
?>

<div id="Container">
    <?php require_once "core/components/header.php" ?>
    <div id="Body">

    </div>
    <?php require_once "core/components/footer.php" ?>
</div>