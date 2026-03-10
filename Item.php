<?php
require_once "core/components/metadata.php";

if (!isset($_GET["ID"]) || !ctype_digit($_GET["ID"])) {
    http_response_code(400);
    exit("Invalid Item");
}

$itemId = (int)$_GET["ID"];

$stmt = $db->prepare("SELECT * FROM catalog WHERE id = ?");
$stmt->execute([$itemId]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    http_response_code(404);
    exit("Item not found");
}

$stmt = $db->prepare("SELECT id, username FROM users WHERE id = ?");
$stmt->execute([$item["creator_id"]]);
$creator = $stmt->fetch(PDO::FETCH_ASSOC);

$currentUser = null;
$alreadyOwned = false;

if (isset($_SESSION["user_id"])) {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION["user_id"]]);
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT id FROM owned_items WHERE user_id = ? AND asset_id = ?");
    $stmt->execute([$_SESSION["user_id"], $item["asset_id"]]);
    $alreadyOwned = $stmt->rowCount() > 0;
}

$titleMap = [
    "hat" => "NOMONA Hat",
    "tshirt" => "NOMONA T-Shirt",
    "shirt" => "NOMONA Shirt",
    "pants" => "NOMONA Pants"
];

$title = $titleMap[$item["asset_type"]] ?? "NOMONA Item";

$currencyLabel = $item["currency"] === "robux" ? "M$" : "Tx";
?>

<div id="Container">
    <?php require_once "core/components/header.php"; ?>
    <div id="Body">
        <div id="ItemContainer" style="padding-left: 70px;">
            <div id="Item">
                <h2><?= $item["name"] ?></h2>
                <div id="Details">
                    <div id="Thumbnail">
				        <a id="ctl00_cphRoblox_AssetThumbnailImage" disabled="disabled" title="Stage Prop" onclick="return false" style="display:inline-block;"><img src="http://nomona.fit/thumbs/catalog/<?= $item["id"] ?>.png" style="width: 250px;" border="0" alt="Stage Prop"></a>
			        </div>
                    <div id="Actions">
		                <a id="ctl00_cphRoblox_FavoriteThisButton" disabled="disabled">Favorite</a>
		            </div>
                    <div id="Summary">
                        <h3><?= $title ?></h3>
                        <div id="ctl00_cphRoblox_TicketsPurchasePanel">
                            <div id="<?= $item['currency'] === 'robux' ? 'RobuxPurchase' : 'TicketsPurchase' ?>">
                                <?php if (!$item['is_for_sale']): ?>
                                    <div style="color:gray;">Off Sale</div>
                                <?php elseif (!$currentUser): ?>
                                    <div>Please login to purchase.</div>
                                <?php elseif ($alreadyOwned): ?>
                                    <div>You already own this item.</div>
                                <?php else: ?>
                                    <div id=<?= $item['currency'] === 'robux' ? 'PriceInRobux' : 'PriceInTickets' ?>>
                                        <?= $currencyLabel ?>: <?= $item['price'] ?>
                                    </div>
                                    <div>
                                        <a class="Button" href="/BuyItem.aspx?ID=<?= $item['id'] ?>">
                                            Buy with <?= $currencyLabel ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div id="Creator" class="Creator">
                            <div class="Avatar">
                                <a id="ctl00_cphRoblox_AvatarImage" title="ROBLOX" href="/User.aspx?ID=1" style="display:inline-block;cursor:pointer;"><img src="http://nomona.fit/thumbs/avatar/<?= $creator["id"] ?>.png" border="0" alt="<?= $creator["username"] ?>" width="98" height="100"></a>
                            </div>
                            Creator: 
                            <a id="ctl00_cphRoblox_CreatorHyperLink" href="/User.aspx?ID=<?= $creator["id"] ?>"><?= $creator["username"] ?></a>
                        </div>
                        <div id="LastUpdate">Updated: <?= $item["updated_at"] ?></div>
                        <div>
                            <div id="DescriptionLabel">Description:</div>
                            <div id="Description"><?= $item["description"] ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="clear: both;">
        </div>
    </div>
    <?php require_once "core/components/footer.php"; ?>
</div>