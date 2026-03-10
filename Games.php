<?php
require_once "core/components/metadata.php";

$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;

$games_per_page = 15;
$offset = ($page - 1) * $games_per_page;

$mode = $_GET['m'] ?? "TopFavorites";
$time = $_GET['t'] ?? "AllTime";

$order = "visits DESC";
$where = "";

if ($mode === "RecentlyUpdated") {
    $order = "updated_at DESC";
}

if ($mode === "TopFavorites") {
    $order = "visits DESC";
}

if ($time === "PastDay") {
    $where = "WHERE created_at >= NOW() - INTERVAL 1 DAY";
}

elseif ($time === "PastWeek") {
    $where = "WHERE created_at >= NOW() - INTERVAL 7 DAY";
}

elseif ($time === "Now") {
    $where = "WHERE created_at >= NOW() - INTERVAL 1 HOUR";
}

elseif ($time === "AllTime") {
    $where = "";
}

$total_games = $db->query("SELECT COUNT(*) FROM games")->fetchColumn();
$total_pages = ceil($total_games / $games_per_page);


$sql = "SELECT * FROM games $where ORDER BY $order LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
$stmt->bindValue(':limit', $games_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($games) === 0 && $time !== "AllTime") {

    $stmt = $db->prepare("
        SELECT * FROM games
        ORDER BY $order
        LIMIT :limit OFFSET :offset
    ");

    $stmt->bindValue(':limit', $games_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div id="Container">
    <?php require_once "core/components/header.php"; ?>
    <div id="Body">
        <div id="GamesContainer">
            <div id="ctl00_cphRoblox_rbxGames_GamesContainerPanel">
                <div class="DisplayFilters">
                    <h2>Games&nbsp;<a id="ctl00_cphRoblox_rbxGames_hlNewsFeed" href="/Games.aspx?feed=rss"><img src="/images/feed-icon-14x14.png" alt="RSS" border="0"></a></h2>
                    <div id="BrowseMode">
                        <h4>Browse</h4>
                        <ul>
                            <li><img id="ctl00_cphRoblox_rbxGames_MostPopularBullet" class="GamesBullet" src="/images/games_bullet.png" alt="Bullet" border="0"><a id="ctl00_cphRoblox_rbxGames_hlMostPopular" href="Games.aspx?m=MostPopular&amp;t=Now"><b>Most Popular</b></a></li>
                            <li><a id="ctl00_cphRoblox_rbxGames_hlTopFavorites" href="Games.aspx?m=TopFavorites&amp;t=AllTime">Top Favorites</a></li>
                            <li><a id="ctl00_cphRoblox_rbxGames_hlRecentlyUpdated" href="Games.aspx?m=RecentlyUpdated">Recently Updated</a></li>
                            <li><a id="ctl00_cphRoblox_rbxGames_hlFeatured" href="User.aspx?id=1">Featured Games</a></li>
                        </ul>
                    </div>
                    <div id="ctl00_cphRoblox_rbxGames_pTimespan">
                        <div id="Timespan">
                            <h4>Time</h4>
                            <ul>
                                <li><img id="ctl00_cphRoblox_rbxGames_TimespanNowBullet" class="GamesBullet" src="/images/games_bullet.png" alt="Bullet" border="0"><a id="ctl00_cphRoblox_rbxGames_hlTimespanNow" href="Games.aspx?m=MostPopular&amp;t=Now"><b>Now</b></a></li>
                                <li><a id="ctl00_cphRoblox_rbxGames_hlTimespanPastDay" href="Games.aspx?m=MostPopular&amp;t=PastDay">Past Day</a></li>
                                <li><a id="ctl00_cphRoblox_rbxGames_hlTimespanPastWeek" href="Games.aspx?m=MostPopular&amp;t=PastWeek">Past Week</a></li>
                                <li><a id="ctl00_cphRoblox_rbxGames_hlTimespanAllTime" href="Games.aspx?m=MostPopular&amp;t=AllTime">All-time</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div id="Games">
                    <span id="ctl00_cphRoblox_rbxGames_lGamesDisplaySet" class="GamesDisplaySet">Most Popular (Now)</span>
                    <div class="HeaderPager">
                        <span>Page <?= $page ?> of <?= $total_pages ?>:</span>
                        <?php if ($page > 1): ?>
                            <a href="?m=MostPopular&t=Now&p=<?= $page-1 ?>">
                            <span class="NavigationIndicators">&lt;&lt;</span> Prev
                            </a>
                        <?php endif; ?>
                        <?php if ($page < $total_pages): ?>
                            <a href="?m=MostPopular&t=Now&p=<?= $page+1 ?>">
                            Next <span class="NavigationIndicators">&gt;&gt;</span>
                            </a>
                        <?php endif; ?>
                    </div>
                    <table id="ctl00_cphRoblox_rbxGames_dlGames" cellspacing="0" align="Center" border="0" width="550">
                        <tbody style="font-size: 12px;">

                        <?php
                        $count = 0;

                        foreach ($games as $game):

                            $stmt2 = $db->prepare("SELECT * FROM users WHERE id = ?");
                            $stmt2->execute([$game["creator_id"]]);

                            $creator = $stmt2->fetch(PDO::FETCH_ASSOC);
                            $stmt = $db->prepare("
                            SELECT SUM(players) AS total_players
                            FROM gameservers
                            WHERE game_id = ?
                            ");

                            $stmt->execute([$game["id"]]);
                            $result = $stmt->fetch();
                            $playersOnline = $result["total_players"] ?? 0;

                        if ($count % 3 == 0):
                        ?>
                        <tr>
                        <?php endif; ?>

                        <td class="Game" valign="top">
                            <div style="padding-bottom:5px">
                                <div class="GameThumbnail">
                                    <a title="<?= htmlspecialchars($game['name']) ?>" href="/PlaceItem.aspx?ID=<?= $game['id'] ?>" style="display:inline-block;cursor:pointer;">
                                        <img src="http://nomona.fit/thumbs/games/<?= $game['id'] ?>.png" border="0" alt="<?= htmlspecialchars($game['name']) ?>" width="160" height="100">
                                    </a>
                                </div>

                                <div class="GameDetails">
                                    <div class="GameName">
                                        <a href="/PlaceItem.aspx?ID=<?= $game['id'] ?>">
                                            <?= htmlspecialchars($game['name']) ?>
                                        </a>
                                    </div>

                                    <div class="GameLastUpdate">
                                        <span class="Label">Updated:</span>
                                        <span class="Detail"><?= date("M j, Y", strtotime($game['updated_at'])) ?></span>
                                    </div>

                                    <div class="GameCreator">
                                        <span class="Label">Creator:</span>
                                        <span class="Detail">
                                            <a href="User.aspx?ID=<?= $game['creator_id'] ?>">
                                                <?= $creator["username"] ?>
                                            </a>
                                        </span>
                                    </div>

                                    <div class="GamePlays">
                                        <span class="Label">Played:</span>
                                        <span class="Detail"><?= number_format($game['visits']) ?> times</span>
                                    </div>

                                    <div>
                                        <div class="GameCurrentPlayers">
                                            <span class="DetailHighlighted"><?= $playersOnline ?> players online</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <?php
                        $count++;

                        if ($count % 3 == 0):
                        ?>
                        </tr>
                        <?php endif; ?>

                        <?php endforeach; ?>

                        <?php
                        if ($count % 3 != 0) {
                            echo "</tr>";
                        }
                        ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>