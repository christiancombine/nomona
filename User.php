<?php
require_once "core/components/metadata.php";

$userId = $_GET["ID"] ?? null;
$isPrivateProfile = false;

$userProfileData = null;

if (empty($userId)) {
    $isPrivateProfile = true;

    if (empty($_USER)) {
        header("Location: /Login/New.aspx");
    } else {
        $userProfileData = $_USER;
    }
} else {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $userProfileData = $stmt->fetch(PDO::FETCH_ASSOC);
}

$stmt = $db->prepare("SELECT * FROM games WHERE creator_id = ?");
$stmt->execute([$userProfileData["id"]]);
$userPlaces = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT * FROM badges WHERE owned_id = ?");
$stmt->execute([$userProfileData["id"]]);
$userBadges = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare("
SELECT users.id, users.username
FROM friends
JOIN users ON users.id =
    CASE
        WHEN friends.user1_id = ? THEN friends.user2_id
        ELSE friends.user1_id
    END
WHERE friends.user1_id = ? OR friends.user2_id = ?
LIMIT 6
");

$stmt->execute([
    $userProfileData["id"],
    $userProfileData["id"],
    $userProfileData["id"]
]);

$userFriends = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare("
SELECT * FROM owned_items
WHERE user_id = ?
ORDER BY obtained_at DESC
");

$stmt->execute([$userProfileData["id"]]);
$userAssets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$assetType = $_GET["asset"] ?? "tshirt";

$filteredAssets = array_filter($userAssets, function($asset) use ($assetType) {
    return strtolower($asset["asset_type"]) === strtolower($assetType);
});
?>

<div id="Container">
    <?php require_once "core/components/header.php"; ?>
    <div id="Body">
        <div id="UserContainer">
            <div id="LeftBank">
                <div id="ProfilePane">
                    <table width="100%" bgcolor="lightsteelblue" cellpadding="6" cellspacing="0">
                        <tbody>
                            <tr style="text-align: center;">
                                <td>
                                    <?php
                                    if (!$isPrivateProfile) {
                                        ?>
                                        <span id="ctl00_cphRoblox_rbxUserPane_lUserName" class="Title"><?= $userProfileData["username"] ?></span><br>         
                                    <span id="ctl00_cphRoblox_rbxUserPane_lUserOnlineStatus" class="<?= $userProfileData['online'] !== 'offline' ? 'UserOnlineMessage' : 'UserOfflineMessage' ?>"><?= $userProfileData['online'] !== 'offline' ? '[ Online ]' : '[ Offline ]' ?></span>
                                        <?php
                                    } else {
                                        ?>
                                        <span id="ctl00_cphRoblox_rbxUserPane_lUserName" class="Title">Hi, <?= $userProfileData["username"] ?>!</span><br>         
                                        <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr style="text-align: center; font-size: 11px">
                                <td>
                                    <span id="ctl00_cphRoblox_rbxUserPane_lUserRobloxURL"><?= $userProfileData["username"] ?>'s <?= $site["sitename"] ?>:</span>
                                    <br>
                                    <a id="ctl00_cphRoblox_rbxUserPane_hlUserRobloxURL" href="/User.aspx?ID=<?= $userProfileData["id"] ?>">http://www.nomona.fit/User.aspx?ID=<?= $userProfileData["id"] ?></a>
                                    <br>
                                    <br>
                                    <div style="left: 0px; float: left; position: relative; top: 0px">
                                        <a id="ctl00_cphRoblox_rbxUserPane_Image1" disabled="disabled" title="matt24772" onclick="return false" style="display:inline-block;"><img src="http://nomona.fit/thumbs/avatar/<?= $userProfileData["id"] ?>.png" border="0" alt="<?= $userProfileData["username"] ?>" width="245"></a><br>
                                        <div id="ctl00_cphRoblox_rbxUserPane_AbuseReportButton1_AbuseReportPanel" class="ReportAbusePanel">
                                            <span class="AbuseIcon"><a id="ctl00_cphRoblox_rbxUserPane_AbuseReportButton1_ReportAbuseIconHyperLink"><img src="/images/abuse.png" alt="Report Abuse" border="0"></a></span>
                                            <span class="AbuseButton"><a id="ctl00_cphRoblox_rbxUserPane_AbuseReportButton1_ReportAbuseTextHyperLink">Report Abuse</a></span>
                                        </div>
                                    </div>
                                    <p></p>
                                    <p></p>
                                    <?php
                                    if ($isPrivateProfile) {
                                        ?>
                                        <a href="/My/Inbox.aspx">Inbox</a>
                                        <br><br>
                                        <a href="/My/Character.aspx">Change Character</a>
                                        <br><br>
                                        <a href="/User.aspx?ID=<?= $userProfileData["id"] ?>">View Public Profile</a>
                                        <br><br>
                                        <a href="/My/Profile.aspx">Edit Profile</a>
                                        <?php
                                    }
                                    ?>
                                    <?php if(!$isPrivateProfile && $_USER["id"] !== $userProfileData["id"]): ?>
                                    <br><br>
                                    <a href="/SendMessage.aspx?ID=<?= $userProfileData["id"] ?>&fr=true">Add Friend</a>
                                    <br><br>
                                    <a href="/SendMessage.aspx?ID=<?= $userProfileData["id"] ?>">Send Message</a>
                                    <?php endif; ?>
                                    <p><span id="ctl00_cphRoblox_rbxUserPane_rbxPublicUser_lBlurb"><?= $userProfileData["blurb"] ?></span></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="UserBadgesPane">
                    <div id="UserBadges">
                        <h4><a id="ctl00_cphRoblox_rbxUserBadgesPane_hlHeader">Badges</a></h4>
                        <table id="ctl00_cphRoblox_rbxUserBadgesPane_dlBadges" cellspacing="0" align="Center" border="0">
                        <tbody style="font-size: 11px;"><tr>
                            <td>
                                <?php
                                if (!empty($userBadges)) {
                                    foreach ($userBadges as $badge) {
                                        ?>
                                        <div class="Badge">
                                            <?php
                                            if ($badge["badge"] === "Administrator") {
                                                ?>
                                                <div class="BadgeImage"><a id="ctl00_cphRoblox_rbxUserBadgesPane_dlBadges_ctl00_hlHeader"><img id="ctl00_cphRoblox_rbxUserBadgesPane_dlBadges_ctl00_iBadge" src="/images/Badges/Administrator.png" alt="This badge is given to any player who is a NOMONA Administrator" height="75" border="0"></a></div>
                                                <?php
                                            } elseif ($badge["badge"] === "partner <3") {
                                                ?>
                                                <div class="BadgeImage"><a id="ctl00_cphRoblox_rbxUserBadgesPane_dlBadges_ctl00_hlHeader"><img id="ctl00_cphRoblox_rbxUserBadgesPane_dlBadges_ctl00_iBadge" src="/images/Badges/trans.png" alt="love you <3" height="75" border="0"></a></div>
                                                <?php
                                            }
                                            ?>
                                            <div class="BadgeLabel"><a id="ctl00_cphRoblox_rbxUserBadgesPane_dlBadges_ctl00_HyperLink1"><?= $badge["badge"] ?></a></div>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <div class="Badge">
                                        <div class="BadgeLabel"><a id="ctl00_cphRoblox_rbxUserBadgesPane_dlBadges_ctl00_HyperLink1">This user has no <?= $site["sitename"] ?> badges.</a></div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody></table>
                    </div>
                </div>
                <div id="UserStatisticsPane" style="background-color: #eee; height: 130px">
                    <div id="UserStatistics">
                        <h4>Statistics</h4>
                        <div class="Statistic">
                            <div class="Label"><acronym title="The number of this user's friends.">Friends</acronym>:</div>
                            <div class="Value"><span id="ctl00_cphRoblox_rbxUserStatisticsPane_lFriendsStatistics">289 (59 last week)</span></div>
                        </div>
                        
                        <div class="Statistic">
                            <div class="Label"><acronym title="The number of posts this user has made to the ROBLOX forum.">Forum Posts</acronym>:</div>
                            <div class="Value"><span id="ctl00_cphRoblox_rbxUserStatisticsPane_lForumPostsStatistics">8</span></div>
                        </div>
                        <div class="Statistic">
                            <div class="Label"><acronym title="The number of times this user's profile has been viewed.">Profile Views</acronym>:</div>
                            <div class="Value"><span id="ctl00_cphRoblox_rbxUserStatisticsPane_lProfileViewsStatistics">3,162 (830 last week)</span></div>
                        </div>
                        <div class="Statistic">
                            <div class="Label"><acronym title="The number of times this user's place has been visited.">Place Visits</acronym>:</div>
                            <div class="Value"><span id="ctl00_cphRoblox_rbxUserStatisticsPane_lPlaceVisitsStatistics">7,475 (1,599 last week)</span></div>
                        </div>
                        <div class="Statistic">
                            <div class="Label"><acronym title="The number of times this user's character has destroyed another user's character in-game.">Knockouts</acronym>:</div>
                            <div class="Value"><span id="ctl00_cphRoblox_rbxUserStatisticsPane_lKillsStatistics"><?= $userProfileData["knockouts"] ?></span></div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div id="RightBank">
                <div id="UserPlacesPane">
                    <div id="UserPlaces">
                        <h4>Showcase</h4>
                        <div id="ctl00_cphRoblox_rbxUserPlacesPane_ShowcasePlacesAccordion">
                            <?php foreach ($userPlaces as $place): ?>
                            <div class="AccordionHeader" onclick="showGame('game<?= $place['id'] ?>')">
                                <?= htmlspecialchars($place["name"]) ?>
                            </div>

                            <div id="game<?= $place['id'] ?>" style="display: none;">
                                <div class="Place">

                                    <div class="PlayStatus">
                                        <span style="display:inline;">
                                            <img src="/images/public.png" alt="Public" border="0">&nbsp;Public
                                        </span>
                                    </div>

                                    <div class="PlayOptions">
                                        <div style="display:inline">
                                            <button class="Button"
                                                onclick="window.location = '/PlaceItem.aspx?ID=<?= $place['id'] ?>'; return false;">
                                                Visit
                                            </button>
                                        </div>
                                    </div>

                                    <div class="Statistics">
                                        Visited <?= number_format($place["visits"]) ?> times
                                    </div>

                                    <div class="Thumbnail">
                                        <img src="/thumbs/games/<?= $place['id'] ?>.png"
                                            alt="<?= htmlspecialchars($place["name"]) ?>" width="420">
                                    </div>

                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($userPlaces)): ?>
                                <p>This user has no places.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div id="FriendsPane">
                    <div id="Friends">
                        <h4><?= $userProfileData["username"] ?>'s Friends <a href="/Friends.aspx?UserID=<?= $userProfileData["id"] ?>">See all <?= count($userFriends) ?></a></h4>
                        <table id="ctl00_cphRoblox_rbxFriendsPane_dlFriends" cellspacing="0" align="Center" border="0">
                            <tbody>
                                <?php foreach (array_slice($userFriends, 0, 6) as $index => $friend): ?>
                                <?php if ($index % 3 == 0): ?>
                                <tr>
                                <?php endif; ?>

                                <td>
                                    <div class="Friend">
                                        <div class="Avatar">
                                            <a title="<?= htmlspecialchars($friend["username"]) ?>"
                                            href="/User.aspx?ID=<?= $friend["id"] ?>"
                                            style="display:inline-block;cursor:pointer;">

                                                <img src="http://nomona.fit/thumbs/avatar/<?= $friend["id"] ?>.png"
                                                    width="100"
                                                    border="0"
                                                    alt="<?= htmlspecialchars($friend["username"]) ?>">
                                            </a>
                                        </div>
                                        <div class="Summary" style="font-size: 12px;">
                                            <span class="Name">
                                                <a href="User.aspx?ID=<?= $friend["id"] ?>">
                                                    <?= htmlspecialchars($friend["username"]) ?>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <?php if ($index % 3 == 2): ?>
                                </tr>
                                <?php endif; ?>

                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="FavoritesPane">
                    <div></div>
                </div>
            </div>
            <div id="UserAssetsPane">
                <div id="ctl00_cphRoblox_rbxUserAssetsPane_upUserAssetsPane">
                    <div id="UserAssets">
                        <h4>Stuff</h4>
                        <div id="AssetsMenu">
                            <div class="AssetsMenuItem">
                                <a class="AssetsMenuButton" href="?ID=<?= $userProfileData["id"] ?>&asset=tshirt">T-Shirts</a>
                            </div>

                            <div class="AssetsMenuItem">
                                <a class="AssetsMenuButton" href="?ID=<?= $userProfileData["id"] ?>&asset=shirt">Shirts</a>
                            </div>

                            <div class="AssetsMenuItem">
                                <a class="AssetsMenuButton" href="?ID=<?= $userProfileData["id"] ?>&asset=pants">Pants</a>
                            </div>

                            <div class="AssetsMenuItem">
                                <a class="AssetsMenuButton" href="?ID=<?= $userProfileData["id"] ?>&asset=hat">Hats</a>
                            </div>

                            <div class="AssetsMenuItem">
                                <a class="AssetsMenuButton" href="?ID=<?= $userProfileData["id"] ?>&asset=decal">Decals</a>
                            </div>
                        </div>
                        <div id="AssetsContent">
                            <table cellspacing="0" border="0" style="font-size: 12px;">
                                <tbody>
                                    <?php
                                    $count = 0;

                                    if(!empty($filteredAssets)):

                                    foreach ($filteredAssets as $asset):

                                    $stmt3 = $db->prepare("SELECT * FROM catalog WHERE asset_id = ?");
                                    $stmt3->execute([$asset["asset_id"]]);

                                    $assetInfo = $stmt3->fetch(PDO::FETCH_ASSOC);

                                    $stmt4 = $db->prepare("SELECT * FROM users WHERE id = ?");
                                    $stmt4->execute([$assetInfo["creator_id"]]);

                                    $assetCreator = $stmt4->fetch(PDO::FETCH_ASSOC);

                                    if ($count % 5 == 0) {
                                        echo "<tr>";
                                    }
                                    ?>

                                    <td class="Asset" valign="top">
                                    <div style="padding:5px">

                                    <div class="AssetThumbnail">
                                    <a href="/Item.aspx?ID=<?= $assetInfo["id"] ?>">
                                    <img src="http://nomona.fit/thumbs/catalog/<?= $assetInfo["id"] ?>.png" border="0" style="width: 100px">
                                    </a>
                                    </div>

                                    <div class="AssetDetails">
                                    <div class="AssetName">
                                    <a href="/Item.aspx?ID=<?= $asset["asset_id"] ?>">
                                    <?= $assetInfo["name"] ?>
                                    </a>
                                    </div>

                                    <div class="AssetCreator">
                                    <span class="Label">Creator:</span>
                                    <span class="Detail">
                                    <a href="/User.aspx?ID=<?= $assetInfo["creator_id"] ?>">
                                    <?= htmlspecialchars($assetCreator["username"]) ?>
                                    </a>
                                    </span>
                                    </div>
                                    </div>

                                    </div>
                                    </td>

                                    <?php
                                    $count++;

                                    if ($count % 5 == 0) {
                                        echo "</tr>";
                                    }

                                    endforeach;

                                    if ($count % 5 != 0) {
                                        echo "</tr>";
                                    }
                                    ?>

                                    <?php endif; ?>

                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
        async function showGame(id) {
            let gameInstance = document.getElementById(id);
            let display = window.getComputedStyle(gameInstance).display;

            if (display === "none") {
                gameInstance.style.display = "block";
            } else {
                gameInstance.style.display = "none";
            }
        }
        </script>
    </div>
</div>