<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "core/components/metadata.php";

$usersPerPage = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $usersPerPage;

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$where = "";
$params = [];

if (!empty($search)) {
    $where = "WHERE username LIKE :search";
    $params[':search'] = "%$search%";
}

$countStmt = $db->prepare("SELECT COUNT(*) FROM users $where");
$countStmt->execute($params);
$totalUsers = $countStmt->fetchColumn();

$totalPages = ceil($totalUsers / $usersPerPage);

$stmt = $db->prepare("
    SELECT * FROM users
    $where
    ORDER BY id DESC
    LIMIT :limit OFFSET :offset
");

foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}

$stmt->bindValue(':limit', $usersPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="Container">
    <?php require_once "core/components/header.php" ?>
    <div id="Body">
        <div id="ctl00_cphRoblox_Panel1">
            <div id="BrowseContainer" style="text-align: center;">
                <form method="GET">
                    <input type="text" name="search" maxlength="100" 
                        value="<?= htmlspecialchars($search) ?>">
                    <button type="submit">Search</button>
                </form>
                <br>
                <br>
                <div style="padding-left: 50px;">
                    <table class="Grid" cellspacing="0" cellpadding="4" border="0" id="ctl00_cphRoblox_gvUsersBrowsed" style="text-align: center;">
                        <tbody>
                            <tr class="GridHeader" style="font-size: 11px;">
				                <th scope="col">Avatar</th><th scope="col"><a href="javascript:__doPostBack('ctl00$cphRoblox$gvUsersBrowsed','Sort$userName')">Name</a></th><th scope="col">Status</th><th scope="col"><a href="javascript:__doPostBack('ctl00$cphRoblox$gvUsersBrowsed','Sort$lastActivity')">Location / Last Seen</a></th>
			                </tr>
                            <?php foreach ($users as $user): ?>
                            <tr class="GridItem" style="font-size: 11px;">
                                <td>
                                    <a href="User.aspx?ID=<?= $user['id'] ?>">
                                        <img src="http://www.nomona.fit/thumbs/avatar/<?= $user['id'] ?>.png" width="100">
                                    </a>
                                </td>

                                <td>
                                    <a href="User.aspx?ID=<?= $user['id'] ?>">
                                        <?= htmlspecialchars($user['username']) ?>
                                    </a>
                                    <br>
                                    <span>
                                        <?= htmlspecialchars($user['blurb']) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= $user['online'] !== 'offline' ? "Online" : "Offline" ?>
                                </td>

                                <td>
                                    <?php if ($user['online'] === 'ingame'): ?>
                                        In Game
                                    <?php else: ?>
                                        Last seen: <?= $user['last_seen'] ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="GridPager" style="font-size: 11px; text-align: left">
                                <td colspan="4">

                                <?php if ($totalPages > 1): ?>
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        
                                        <?php if ($i == $page): ?>
                                            <span><?= $i ?></span>
                                        <?php else: ?>
                                            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>">
                                                <?= $i ?>
                                            </a>
                                        <?php endif; ?>

                                    <?php endfor; ?>
                                <?php endif; ?>
                                <span style="color: black;">...</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php require_once "core/components/footer.php" ?>
</div>