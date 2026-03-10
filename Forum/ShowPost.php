<?php require_once "../core/components/metadata.php"; ?>

<?php

$threadID = isset($_GET['ThreadID']) ? intval($_GET['ThreadID']) : 0;

/* THREAD */
$threadStmt = $db->prepare("
SELECT 
    t.*,
    u.username,
    u.created_at,

    (
        (SELECT COUNT(*) FROM forum_threads WHERE user_id = u.id) +
        (SELECT COUNT(*) FROM forum_replies WHERE user_id = u.id)
    ) AS post_count

FROM forum_threads t
LEFT JOIN users u ON u.id = t.user_id
WHERE t.id = ?
");
$threadStmt->execute([$threadID]);
$thread = $threadStmt->fetch();

/* REPLIES */
$replyStmt = $db->prepare("
SELECT 
    r.*,
    u.username,
    u.created_at,

    (
        (SELECT COUNT(*) FROM forum_threads WHERE user_id = u.id) +
        (SELECT COUNT(*) FROM forum_replies WHERE user_id = u.id)
    ) AS post_count

FROM forum_replies r
LEFT JOIN users u ON u.id = r.user_id
WHERE r.thread_id = ?
ORDER BY r.created_at ASC
");
$replyStmt->execute([$threadID]);
$replies = $replyStmt->fetchAll();

function getUserBadges($db, $userId) {
    $stmt = $db->prepare("SELECT badge FROM badges WHERE owned_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getTopPosterBadge($postCount) {
    if ($postCount >= 500) {
        return "users_top25.gif";
    }

    if ($postCount >= 250) {
        return "users_top50.gif";
    }

    if ($postCount >= 100) {
        return "users_top100.gif";
    }

    return null;
}
?>

<link rel="stylesheet" href="/CSS/ForumCSS.ashx">
<div id="Container">
<?php require_once "../core/components/header.php"; ?>

<div id="Body">

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody>
<tr>
<td></td>
</tr>

<tr valign="bottom">
<td>

<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
<tbody>
<tr valign="top">

<td>&nbsp; &nbsp; &nbsp;</td>

<td id="ctl00_cphRoblox_CenterColumn" width="95%" class="CenterColumn">

<br>

<!-- NAV -->

<span id="ctl00_cphRoblox_Navigationmenu1">
<table width="100%" cellspacing="1" cellpadding="0">
<tbody>
<tr>
<td align="right" valign="middle" style="font-size: 12px;">

<a id="ctl00_cphRoblox_Navigationmenu1_ctl00_HomeMenu" class="menuTextLink" href="/Forum/Default.aspx">
<img src="/images/icon_mini_home.gif" border="0">Home &nbsp;
</a>

<a id="ctl00_cphRoblox_Navigationmenu1_ctl00_SearchMenu" class="menuTextLink" href="/Forum/Search.aspx">
<img src="/images/icon_mini_search.gif" border="0">Search &nbsp;
</a>

</td>
</tr>
</tbody>
</table>
</span>

<span id="ctl00_cphRoblox_PostView1">

<table cellpadding="0" width="100%">

<tbody>

<tr>
<td colspan="2">

<table id="ctl00_cphRoblox_PostView1_ctl00_PostList" class="tableBorder" cellspacing="1" cellpadding="0" border="0" width="100%" style="font-size: 12px;">

<tbody style="font-size: 12px;">

<tr>
<td class="forumHeaderBackgroundAlternate" colspan="2" height="20">
<table width="100%">
<tr>
<td></td>
<td align="right"></td>
</tr>
</table>
</td>
</tr>

<tr>
<th class="tableHeaderText" align="left" height="25" width="100">&nbsp;Author</th>
<th class="tableHeaderText" align="left" width="85%">
&nbsp;Thread: <?=htmlspecialchars($thread['title'])?>
</th>
</tr>

<!-- THREAD START -->

<tr style="font-size: 12px;">

<td class="forumRow" valign="top" width="150" nowrap="nowrap">

<table border="0">

<tr>
<td>
&nbsp;

<a class="normalTextSmallBold" href="/User.aspx?ID=<?=$thread['user_id']?>" style="font-size: 12px;">

<?=htmlspecialchars($thread['username'])?>

</a>
<br>
</td>
</tr>

<tr>
<td>
<img src="http://nomona.fit/thumbs/avatar/<?= $thread["user_id"] ?>.png" width="100" border="0">
</td>
</tr>

<?php
$badges = getUserBadges($db, $thread['user_id']);
$topBadge = getTopPosterBadge($thread['post_count']);

if ($topBadge) {
    echo '<tr><td><img src="/images/'.$topBadge.'" border="0"></td></tr>';
}

foreach ($badges as $badge) {
    if ($badge === "Forum Moderator") {
        echo '<tr><td><img src="/images/users_moderator.gif" border="0"></td></tr>';
    }
}
?>

<tr>
<td>
<span class="normalTextSmaller" style="font-size: 12px;">
<b>Joined:</b> <?=date("d M Y", strtotime($thread['created_at']))?>
</span>
</td>
</tr>

<tr>
<td>
<span class="normalTextSmaller" style="font-size: 12px;">
<b>Total Posts: </b><?=$thread['post_count']?>
</span>
</td>
</tr>

</table>

</td>

<td class="forumRow" valign="top">

<table cellspacing="0" cellpadding="3" border="0" width="100%">

<tr>
<td class="forumRowHighlight">

<span class="normalTextSmallBold" style="font-size: 12px;">

<?=htmlspecialchars($thread['title'])?>

</span>

<br>

<span class="normalTextSmaller" style="font-size: 12px;">

Posted: <?=date("d M Y h:i A", strtotime($thread['created_at']))?>

</span>

</td>
</tr>

<tr>
<td colspan="2">
<span class="normalTextSmall" style="font-size: 12px;">

<?=nl2br(htmlspecialchars($thread['body']))?>

</span>
</td>
</tr>
<tr>
    <td colspan="2"><a href="/Forum/Reply.aspx?ReplyID=<?= $thread['id'] ?>"><img border="0" src="/images/newpost.gif"></a></td>
</tr>

</table>

</td>

</tr>

<!-- REPLIES -->

<?php
$alt = true;
foreach($replies as $reply):
$rowClass = $alt ? "forumAlternate" : "forumRow";
$alt = !$alt;
?>

<tr>

<td class="<?=$rowClass?>" valign="top" width="150" nowrap="nowrap">

<table border="0">

<tr>
<td>
&nbsp;

<a class="normalTextSmallBold" href="/User.aspx?ID=<?=$reply['user_id']?>" style="font-size: 12px;">

<?=htmlspecialchars($reply['username'])?>

</a>
<br>
</td>
</tr>

<tr>
<td>
<img src="http://nomona.fit/thumbs/avatar/<?= $reply["user_id"] ?>.png" width="100" border="0">
</td>
</tr>

<tr>
<td>
<span class="normalTextSmaller" style="font-size: 12px;">
<b>Joined:</b> <?=date("d M Y", strtotime($reply['created_at']))?>
</span>
</td>
</tr>

<?php
$badges = getUserBadges($db, $thread['user_id']);
$topBadge = getTopPosterBadge($thread['post_count']);

if ($topBadge) {
    echo '<tr><td><img src="/images/'.$topBadge.'" border="0"></td></tr>';
}

foreach ($badges as $badge) {
    if ($badge === "Forum Moderator") {
        echo '<tr><td><img src="/images/users_moderator.gif" border="0"></td></tr>';
    }
}
?>

<tr>
<td>
<span class="normalTextSmaller" style="font-size: 12px;">
<b>Total Posts: </b><?=$reply['post_count']?>
</span>
</td>
</tr>

</table>

</td>

<td class="<?=$rowClass?>" valign="top">

<table cellspacing="0" cellpadding="3" border="0" width="100%">

<tr>
<td class="forumRowHighlight">

<span class="normalTextSmallBold" style="font-size: 12px;">
Re: <?=htmlspecialchars($thread['title'])?>
</span>

<br>

<span class="normalTextSmaller" style="font-size: 12px;">

Posted: <?=date("d M Y h:i A", strtotime($reply['created_at']))?>

</span>

</td>
</tr>

<tr>
<td colspan="2">
<span class="normalTextSmall" style="font-size: 12px;">

<?=nl2br(htmlspecialchars($reply['body']))?>

</span>
</td>
</tr>

<tr>
    <td colspan="2"><a href="/Forum/Reply.aspx?ReplyID=<?= $reply['id'] ?>"><img border="0" src="/images/newpost.gif"></a></td>
</tr>

</table>

</td>

</tr>

<?php endforeach; ?>

</tbody>
</table>

</td>
</tr>

</tbody>
</table>

</span>

</td>

<td class="CenterColumn">&nbsp;&nbsp;&nbsp;</td>

<td class="RightColumn">&nbsp;&nbsp;&nbsp;</td>

</tr>
</tbody>
</table>

</td>
</tr>
</tbody>
</table>

</div>

<?php require_once "../core/components/footer.php"; ?>

</div>