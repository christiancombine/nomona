<?php
require_once "core/components/metadata.php";

$type  = $_GET['type'] ?? 'hats';
$mode  = $_GET['m'] ?? 'Featured';
$search = trim($_GET['search'] ?? '');

$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = 50;
$offset = ($page - 1) * $limit;

$typeMap = [
    'hats' => 'Hat',
    'shirts' => 'Shirt',
    'pants' => 'Pants',
    'tshirts' => 'TShirt'
];

$assetType = $typeMap[$type] ?? 'Hat';

$orderBy = "created_at DESC";
$title = "";
$typeTitles = [
    'hats'    => 'Hats',
    'shirts'  => 'Shirts',
    'pants'   => 'Pants',
    'tshirts' => 'T-Shirts'
];

$categoryTitle = $typeTitles[$type] ?? 'Catalog';
$modeTitles = [
    'Featured' => 'Featured',
    'BestSelling' => 'Best Selling',
    'RecentlyUpdated' => 'Recently Updated',
    'ForSale' => 'For Sale',
    'PriceHigh' => 'Most Expensive',
    'PriceLow' => 'Cheapest'
];

$modeTitle = $modeTitles[$mode] ?? 'Featured';

if (!empty($search)) {
    $title = "Search results for \"" . htmlspecialchars($search) . "\"";
} else {
    $title = "{$modeTitle} {$categoryTitle}";
}

switch ($mode) {

    case "BestSelling":
        $orderBy = "sold_times DESC";
        break;

    case "RecentlyUpdated":
        $orderBy = "updated_at DESC";
        break;

    case "ForSale":
        $orderBy = "is_for_sale DESC, created_at DESC";
        break;

    case "PriceHigh":
        $orderBy = "price DESC";
        break;

    case "PriceLow":
        $orderBy = "price ASC";
        break;
}

$where = "WHERE asset_type = ?";
$params = [$assetType];

if (!empty($search)) {
    $where .= " AND name LIKE ?";
    $params[] = "%{$search}%";
}

$query = "
    SELECT *
    FROM catalog
    {$where}
    ORDER BY {$orderBy}
    LIMIT {$limit} OFFSET {$offset}
";

$stmt = $db->prepare($query);
$stmt->execute($params);
$items = $stmt->fetchAll();

$countQuery = "
    SELECT COUNT(*)
    FROM catalog
    {$where}
";

$countStmt = $db->prepare($countQuery);
$countStmt->execute($params);
$totalItems = (int)$countStmt->fetchColumn();

$totalPages = max(1, ceil($totalItems / $limit));

function isActiveType($check, $type) {
    return $check === $type;
}

function isActiveMode($check, $mode) {
    return $check === $mode;
}
?>

<div id="Container">
  <?php require_once "core/components/header.php" ?>
  <div id="Body">
    <div id="CatalogContainer">
      <div id="SearchBar" class="SearchBar">
        <span class="SearchBox"
          ><input
            name="ctl00$cphRoblox$rbxCatalog$SearchTextBox"
            type="text"
            maxlength="100"
            id="ctl00_cphRoblox_rbxCatalog_SearchTextBox"
            class="TextBox"
        /></span>
        <span class="SearchButton"
          ><input
            type="submit"
            name="ctl00$cphRoblox$rbxCatalog$SearchButton"
            value="Search"
            id="ctl00_cphRoblox_rbxCatalog_SearchButton"
        /></span>
      </div>
      <div class="DisplayFilters">
        <h2>Catalog</h2>
        <div id="BrowseMode">
          <h4>
            <a
              id="ctl00_cphRoblox_rbxCatalog_CafePressHyperLink"
              href="https://web.archive.org/web/20080621141955/http://www.cafepress.com/roblox"
              target="_blank"
              >Buy <?= $site["sitename"] ?> Stuff!</a
            >
          </h4>
          <h4>Browse</h4>
          <ul>
            <li>
            <?php if (isActiveMode($mode, 'Featured')): ?>
                <img class="GamesBullet" src="/images/games_bullet.png" />
                <b>Top Favorites</b>
            <?php else: ?>
                <a href="Catalog.aspx?type=<?= $type ?>&m=Featured">
                    Top Favorites
                </a>
            <?php endif; ?>
            </li>
            <?php if (isActiveMode($mode, 'BestSelling')): ?>
                <img class="GamesBullet" src="/images/games_bullet.png" />
                <b>Best Selling</b>
            <?php else: ?>
                <a href="Catalog.aspx?type=<?= $type ?>&m=BestSelling">
                    Best Selling
                </a>
            <?php endif; ?>
            </li>
            <li>
            <?php if (isActiveMode($mode, 'RecentlyUpdated')): ?>
                <img class="GamesBullet" src="/images/games_bullet.png" />
                <b>Recently Updated</b>
            <?php else: ?>
                <a href="Catalog.aspx?type=<?= $type ?>&m=RecentlyUpdated">
                    Recently Updated
                </a>
            <?php endif; ?>
            </li>
            <li>
            <?php if (isActiveMode($mode, 'ForSale')): ?>
                <img class="GamesBullet" src="/images/games_bullet.png" />
                <b>For Sale</b>
            <?php else: ?>
                <a href="Catalog.aspx?type=<?= $type ?>&m=ForSale">
                    For Sale
                </a>
            <?php endif; ?>
            </li>
          </ul>
        </div>
        <div id="Category">
          <h4>Category</h4>

          <ul>
            <li>
            <?php if (isActiveType($type, 'tshirts')): ?>
                <img class="GamesBullet" src="/images/games_bullet.png" />
                <b>T-Shirts</b>
            <?php else: ?>
                <a href="Catalog.aspx?type=tshirts&m=<?= $mode ?>">
                    T-Shirts
                </a>
            <?php endif; ?>
            </li>

            <li>
            <?php if (isActiveType($type, 'shirts')): ?>
                <img class="GamesBullet" src="/images/games_bullet.png" />
                <b>Shirts</b>
            <?php else: ?>
                <a href="Catalog.aspx?type=shirts&m=<?= $mode ?>">
                    Shirts
                </a>
            <?php endif; ?>
            </li>

            <li>
            <?php if (isActiveType($type, 'pants')): ?>
                <img class="GamesBullet" src="/images/games_bullet.png" />
                <b>Pants</b>
            <?php else: ?>
                <a href="Catalog.aspx?type=pants&m=<?= $mode ?>">
                    Pants
                </a>
            <?php endif; ?>
            </li>

            <li>
            <?php if (isActiveType($type, 'hats')): ?>
                <img class="GamesBullet" src="/images/games_bullet.png" />
                <b>Hats</b>
            <?php else: ?>
                <a href="Catalog.aspx?type=hats&m=<?= $mode ?>">
                    Hats
                </a>
            <?php endif; ?>
            </li>
          </ul>
        </div>
      </div>
      <div class="Assets">
        <span
          id="ctl00_cphRoblox_rbxCatalog_AssetsDisplaySetLabel"
          class="AssetsDisplaySet"
          ><?= $title ?></span
        >
        <div class="HeaderPager">
          <span class="PageSelector">
            <label>Pages:</label>
            <span><?= $page ?> / <?= $totalPages ?></span>

            <?php if ($page > 1): ?>
                <a href="?type=<?= $type ?>&m=<?= $mode ?>&page=<?= $page-1 ?>">
                    &lt;&lt; Previous
                </a>
            <?php else: ?>
                <span style="opacity:0.5;">&lt;&lt; Previous</span>
            <?php endif; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?type=<?= $type ?>&m=<?= $mode ?>&page=<?= $page+1 ?>">
                    Next &gt;&gt;
                </a>
            <?php else: ?>
                <span style="opacity:0.5;">Next &gt;&gt;</span>
            <?php endif; ?>
          </span>
        </div>
        <table
          id="ctl00_cphRoblox_rbxCatalog_AssetsDataList"
          cellspacing="0"
          align="Center"
          border="0"
          width="735"
        >
          <tbody>
            <tr>
            <?php foreach ($items as $item): ?>
            <td valign="top" style="display:inline-block; padding:11px;">

            <div class="Asset">
                <div class="AssetThumbnail">
                    <a href="/Item.aspx?ID=<?= $item['id'] ?>">
                        <img
                            src="http://nomona.fit/thumbs/catalog/<?= $item['id'] ?>.png"
                            width="120"
                            height="120"
                        />
                    </a>
                </div>

                <div class="AssetDetails" style="font-size:11px;">
                    <strong>
                        <a href="/Item.aspx?ID=<?= $item['id'] ?>">
                            <?= htmlspecialchars($item['name']) ?>
                        </a>
                    </strong>

                    <div class="AssetsSold">
                        <span class="Label">Number Sold:</span>
                        <span class="Detail"><?= $item['sold_times'] ?></span>
                    </div>

                    <div class="AssetPrice">
                        <?php if ($item['is_for_sale']): ?>
                          <?php if ($item['currency'] == "tix"): ?>
                            <span class="PriceInTickets">
                                Tx: <?= $item['price'] ?>
                            </span>
                          <?php elseif ($item['currency'] == "robux"): ?>
                            <span class="PriceInRobux">
                                M$: <?= $item['price'] ?>
                            </span>
                          <?php endif; ?>
                        <?php else: ?>
                            <span style="color:gray;">Off Sale</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            </td>
            <?php endforeach; ?>
            </tr>
          </tbody>
        </table>
        <div class="HeaderPager">
          <span class="PageSelector">
              <label>Pages:</label>
              <span><?= $page ?> / <?= $totalPages ?></span>

              <?php if ($page > 1): ?>
                  <a href="?type=<?= $type ?>&m=<?= $mode ?>&page=<?= $page-1 ?>">
                      &lt;&lt; Previous
                  </a>
              <?php else: ?>
                  <span style="opacity:0.5;">&lt;&lt; Previous</span>
              <?php endif; ?>

              <?php if ($page < $totalPages): ?>
                  <a href="?type=<?= $type ?>&m=<?= $mode ?>&page=<?= $page+1 ?>">
                      Next &gt;&gt;
                  </a>
              <?php else: ?>
                  <span style="opacity:0.5;">Next &gt;&gt;</span>
              <?php endif; ?>
          </span>
        </div>
      </div>
    </div>
  </div>
  <?php require_once "core/components/footer.php" ?>
</div>
