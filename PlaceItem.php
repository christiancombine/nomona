<?php 
require_once "core/components/metadata.php";

$id = $_GET["ID"] ?? null;

if (empty($id)) {
    echo "<h2>Invalid id</h2>";
    exit;
}

$stmt = $db->prepare("SELECT * FROM games WHERE id = ?");
$stmt->execute([$id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt2 = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt2->execute([$game["creator_id"]]);
$creator = $stmt2->fetch(PDO::FETCH_ASSOC);
?>

<div id="Container">
    <?php require_once "core/components/header.php" ?>
    <div id="Body">
        <div id="ItemContainer" style="padding-left: 70px;">
            <div id="Item">
                <h2><?= $game["name"] ?></h2>
                <div id="Details">
                    <div id="Thumbnail_Place">
                        <a id="ctl00_cphRoblox_AssetThumbnailImage_Place" disabled="disabled" title="*_*Get married,Go on a Vacation and Adopt Children" onclick="return false" style="display:inline-block;"><img src="http://nomona.fit/thumbs/games/<?= $game["id"] ?>.png" border="0" alt="<?= $game["name"] ?>" width="420" height="230"></a>
                    </div>
                    <div id="Actions_Place">
			            <a id="ctl00_cphRoblox_FavoriteThisPlaceButton" disabled="disabled">Favorite</a>
			        </div>
                    <div id="ctl00_cphRoblox_PlayGames" class="PlayGames">
                        <span id="ctl00_cphRoblox_PlaceAccessIndicator_Public" style="display:inline;"><img id="ctl00_cphRoblox_PlaceAccessIndicator_iPublic" src="/images/public.png" alt="Public" border="0">&nbsp;Public</span>
                        <img id="ctl00_cphRoblox_CopyLockedIcon" src="/images/CopyLocked.png" alt="CopyLocked" border="0">
                        Copy Protection: CopyLocked
                        <br>
                        <br>
                        <div id="ctl00_cphRoblox_VisitButtons_VisitMPButton" style="display:inline">
                            <a href="/launch.php?gameId=<?= $game["id"] ?>" class="Button">Play Online</a>
                            <?php
                            if ($game["creator_id"] === $_USER["id"]) {
                                ?>
                                <a href="/My/Upload/Update.aspx?id=<?= $game["id"] ?>"class="Button">Update</a>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div id="Summary" style="padding-right: 0px; margin-right: 10px; margin-top: 10px; margin-bottom: 10px">
                    <h3>NOMONA Place</h3>
                    <div id="Creator" class="Creator">
                        <div class="Avatar">
                            <a id="ctl00_cphRoblox_AvatarImage" title="kitcat555" href="/User.aspx?ID=<?= $game["creator_id"] ?>" style="display:inline-block;cursor:pointer;"><img src="http://nomona.fit/thumbs/avatar/<?= $game["creator_id"] ?>.png" border="0" alt="kitcat555" width="120"></a>
                        </div>
                        Creator: <a id="ctl00_cphRoblox_CreatorHyperLink" href="/User.aspx?ID=<?= $game["creator_id"] ?>"><?= $creator["username"] ?></a>
                    </div>
                    <div id="LastUpdate">Updated: <?= $game["updated_at"] ?></div>
                    <div id="LastUpdate">Created: <?= $game["created_at"] ?></div>
                    <div id="ctl00_cphRoblox_VisitedPanel" class="Visited">Visited: <?= $game["visits"] ?> times</div>
                    <div id="ctl00_cphRoblox_DescriptionPanel">
					    <div id="DescriptionLabel">Description:</div>
					    <div id="Description"><?= $game["description"] ?></div>
                    <div id="ReportAbuse"><div id="ctl00_cphRoblox_AbuseReportButton1_AbuseReportPanel" class="ReportAbusePanel">
                        <span class="AbuseIcon"><a id="ctl00_cphRoblox_AbuseReportButton1_ReportAbuseIconHyperLink"><img src="/images/abuse.png" alt="Report Abuse" border="0"></a></span>
                        <span class="AbuseButton"><a id="ctl00_cphRoblox_AbuseReportButton1_ReportAbuseTextHyperLink">Report Abuse</a></span>
                    </div></div>
                </div>
            </div>
            <div style="clear: both;">
        </div>
        <div style="margin: 10px; width: 703px;">
            <div class="ajax__tab_xp" id="ctl00_cphRoblox_TabbedInfo">
                <div id="ctl00_cphRoblox_TabbedInfo_header">
                    <span id="__tab_ctl00_cphRoblox_TabbedInfo_GamesTab">
                        <h3>Servers</h3>
                    </span>
                </div>
                <div id="ctl00_cphRoblox_TabbedInfo_body">
                    <div id="ctl00_cphRoblox_TabbedInfo_GamesTab">
                        <div id="ctl00_cphRoblox_TabbedInfo_GamesTab_RunningGamesUpdatePanel">
                            <div class="RefreshRunningGames">
                                <a type="submit" name="ctl00$cphRoblox$TabbedInfo$GamesTab$RefreshRunningGamesButton" id="ctl00_cphRoblox_TabbedInfo_GamesTab_RefreshRunningGamesButton" class="Button" href="/PlaceItem.aspx?ID=<?= $game["id"] ?>">Refresh</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>