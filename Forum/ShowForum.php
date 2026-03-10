<?php 
require_once "../core/components/metadata.php"; 
$forumID = isset($_GET['ForumID']) ? intval($_GET['ForumID']) : 0;
$page = isset($_GET['Page']) ? intval($_GET['Page']) : 1;

$threadsPerPage = 20;
$offset = ($page - 1) * $threadsPerPage;

$stmt = $db->prepare("
SELECT 
    t.*,
    u.username AS author,
    COUNT(r.id) AS reply_count
FROM forum_threads t
LEFT JOIN forum_replies r ON r.thread_id = t.id
LEFT JOIN users u ON u.id = t.user_id
WHERE t.category_id = ?
GROUP BY t.id
ORDER BY t.pinned DESC, t.created_at DESC
LIMIT $threadsPerPage OFFSET $offset
");

$stmt->execute([$forumID]);
$threads = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="/CSS/ForumCSS.ashx">
<div id="Container">
    <?php require_once "../core/components/header.php"; ?>
    <div id="Body">
					
			<table width="100%" cellspacing="0" cellpadding="0" border="0">
				<tbody><tr>
					<td>
						</td>
				</tr>
				<tr valign="bottom">
					<td>
						<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
							<tbody><tr valign="top">
								<!-- left column -->
								<td>&nbsp; &nbsp; &nbsp;</td>
								<!-- center column -->
								<td id="ctl00_cphRoblox_CenterColumn" width="95%" class="CenterColumn">
									<br>
									<span id="ctl00_cphRoblox_Navigationmenu1">
<table width="100%" cellspacing="1" cellpadding="0">
	<tbody><tr>
		<td align="right" valign="middle" style="font-size: 12px;">
			<a id="ctl00_cphRoblox_Navigationmenu1_ctl00_HomeMenu" class="menuTextLink" href="/web/20080622175918/http://www.roblox.com/Forum/Default.aspx"><img src="/images/icon_mini_home.gif" border="0">Home &nbsp;</a>
			<a id="ctl00_cphRoblox_Navigationmenu1_ctl00_SearchMenu" class="menuTextLink" href="/web/20080622175918/http://www.roblox.com/Forum/Search/default.aspx"><img src="/images/icon_mini_search.gif" border="0">Search &nbsp;</a>
		</td>
	</tr>
</tbody></table>
</span>
									<span id="ctl00_cphRoblox_ThreadView1">
<table cellpadding="0" width="100%">
	<tbody><tr>
		<td colspan="2" align="left"><span id="ctl00_cphRoblox_ThreadView1_ctl00_Whereami1" name="Whereami1">
<table cellpadding="0" cellspacing="0" width="100%">
    <tbody><tr>
        <td valign="top" align="left" width="1px">
            <nobr>
                
            </nobr>
        </td>
        <td id="ctl00_cphRoblox_ThreadView1_ctl00_Whereami1_ctl00_ForumGroupMenu" class="popupMenuSink" valign="top" align="left" width="1px">
            <nobr>
                
                <a id="ctl00_cphRoblox_ThreadView1_ctl00_Whereami1_ctl00_LinkForumGroup" class="linkMenuSink" href="/web/20080622175918/http://www.roblox.com/Forum/ShowForumGroup.aspx?ForumGroupID=1">ROBLOX</a>
            </nobr>
        </td>

        <td id="ctl00_cphRoblox_ThreadView1_ctl00_Whereami1_ctl00_ForumMenu" class="popupMenuSink" valign="top" align="left" width="1px">
            <nobr>
                <span id="ctl00_cphRoblox_ThreadView1_ctl00_Whereami1_ctl00_ForumSeparator" class="normalTextSmallBold">&nbsp;&gt;</span>
                <a id="ctl00_cphRoblox_ThreadView1_ctl00_Whereami1_ctl00_LinkForum" class="linkMenuSink" href="/web/20080622175918/http://www.roblox.com/Forum/ShowForum.aspx?ForumID=13">General Discussion</a>
            </nobr>
        </td>

        <td id="ctl00_cphRoblox_ThreadView1_ctl00_Whereami1_ctl00_PostMenu" class="popupMenuSink" valign="top" align="left" width="1px">
            <nobr>
                
                
            </nobr>
        </td>

        <td valign="top" align="left" width="*">&nbsp;</td>
    </tr>
</tbody></table>

<span id="ctl00_cphRoblox_ThreadView1_ctl00_Whereami1_ctl00_MenuScript"></span></span></td>
	</tr>
	<tr>
		<td>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td valign="bottom" align="left"><a id="ctl00_cphRoblox_ThreadView1_ctl00_NewThreadLinkTop" href="/Forum/AddPost.aspx?ForumID=<?= $forumID ?>"><img id="ctl00_cphRoblox_ThreadView1_ctl00_NewThreadImageTop" src="/images/newtopic.gif" border="0"></a></td>
		<td align="right"><span class="normalTextSmallBold">Search 
      this forum: </span>
			<input name="ctl00$cphRoblox$ThreadView1$ctl00$Search" type="text" id="ctl00_cphRoblox_ThreadView1_ctl00_Search">
			<input type="submit" name="ctl00$cphRoblox$ThreadView1$ctl00$SearchButton" value=" Go " id="ctl00_cphRoblox_ThreadView1_ctl00_SearchButton"></td>
	</tr>
	<tr>
		<td valign="top" colspan="2"><table id="ctl00_cphRoblox_ThreadView1_ctl00_ThreadList" class="tableBorder" cellspacing="1" cellpadding="3" border="0" width="100%">
	<tbody style="font-size: 12px;"><tr>
		<th class="tableHeaderText" align="left" colspan="2" height="25">&nbsp;Thread&nbsp;</th><th class="tableHeaderText" align="center" nowrap="nowrap">&nbsp;Started By&nbsp;</th><th class="tableHeaderText" align="center">&nbsp;Replies&nbsp;</th><th class="tableHeaderText" align="center">&nbsp;Views&nbsp;</th><th class="tableHeaderText" align="center" nowrap="nowrap">&nbsp;Last Post&nbsp;</th>
	</tr><?php foreach ($threads as $thread): ?>

<?php
$views = $thread['views'];
$replies = $thread['reply_count'];
$isPinned = $thread['pinned'];
?>

<tr>

<td class="forumRow" align="center" valign="middle" width="25">

<?php if ($isPinned): ?>
<img title="Pinned post" src="/images/topic-popular.gif" border="0">
<?php else: ?>
<img title="Post (Not Read)" src="/images/topic_notread.gif" border="0">
<?php endif; ?>

</td>

<td class="forumRow" height="25">

<a class="linkSmallBold" href="/Forum/ShowPost.aspx?ThreadID=<?=$thread['id']?>">

<?=htmlspecialchars($thread['title'])?>

</a>

</td>

<td class="forumRowHighlight" align="left" width="100">
&nbsp;<a class="linkSmall" href="/User.aspx?ID=<?=$thread['user_id']?>">

<?=htmlspecialchars($thread['author'])?>

</a>
</td>

<td class="forumRowHighlight" align="center" width="50">
<span class="normalTextSmaller"><?=$replies?></span>
</td>

<td class="forumRowHighlight" align="center" width="50">
<span class="normalTextSmaller"><?=$views?></span>
</td>

<td class="forumRowHighlight" align="center" width="140" nowrap="nowrap">

<?php
$last = $db->prepare("
SELECT r.created_at, u.username
FROM forum_replies r
LEFT JOIN users u ON u.id = r.user_id
WHERE r.thread_id = ?
ORDER BY r.created_at DESC
LIMIT 1
");
$last->execute([$thread['id']]);
$lastPost = $last->fetch();
?>

<span class="normalTextSmaller">

<?php if($isPinned): ?>

<b>Pinned Post</b><br>by

<a class="linkSmall" href="/Forum/User/UserProfile.php?UserID=<?=$thread['user_id']?>">

<?=htmlspecialchars($thread['author'])?>

</a>

<?php else: ?>

<b><?=date("M d @ h:i A", strtotime($lastPost['created_at'] ?? $thread['created_at']))?></b><br>
by

<a class="linkSmall" href="/Forum/User/UserProfile.php">

<?=htmlspecialchars($lastPost['username'] ?? $thread['author'])?>

</a>

<?php endif; ?>

</span>

<a href="/Forum/ShowPost.aspx?ThreadID=<?=$thread['id']?>">

<img border="0" src="/images/icon_mini_topic.gif">

</a>

</td>

</tr>

<?php endforeach; ?>
	</tr><tr>
		<td class="forumHeaderBackgroundAlternate" colspan="6">&nbsp;</td>
</tr>
<?php
$totalThreads = $db->prepare("
SELECT COUNT(*) FROM forum_threads WHERE category_id = ?
");
$totalThreads->execute([$forumID]);

$totalThreads = $totalThreads->fetchColumn();
$totalPages = ceil($totalThreads / $threadsPerPage);
?>
</tbody></table><span id="ctl00_cphRoblox_ThreadView1_ctl00_Pager"><table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tbody style="font-size: 12px;"><tr>
<td>
<span class="normalTextSmallBold">
Page <?=$page?> of <?=$totalPages?>
</span>
</td>

<td align="right">

<span>
<span class="normalTextSmallBold">Goto to page: </span>

<?php
$maxPagesToShow = 3;
$start = max(1, $page);
$end = min($totalPages, $page + $maxPagesToShow - 1);

for ($i = $start; $i <= $end; $i++):
?>

<a 
id="ctl00_cphRoblox_ThreadView1_ctl00_Pager_Page<?=$i-1?>" 
class="normalTextSmallBold" 
href="?ForumID=<?=$forumID?>&Page=<?=$i?>">

<?=$i?>

</a>

<?php if ($i < $end): ?>
<span class="normalTextSmallBold">, </span>
<?php endif; ?>

<?php endfor; ?>

<?php if ($end < $totalPages): ?>
<span class="normalTextSmallBold"> ... </span>

<a 
id="ctl00_cphRoblox_ThreadView1_ctl00_Pager_Page<?=$totalPages-2?>" 
class="normalTextSmallBold" 
href="?ForumID=<?=$forumID?>&Page=<?=$totalPages-1?>">

<?=number_format($totalPages-1)?>

</a>

<span class="normalTextSmallBold">, </span>

<a 
id="ctl00_cphRoblox_ThreadView1_ctl00_Pager_Page<?=$totalPages-1?>" 
class="normalTextSmallBold" 
href="?ForumID=<?=$forumID?>&Page=<?=$totalPages?>">

<?=number_format($totalPages)?>

</a>
<?php endif; ?>

<?php if ($page < $totalPages): ?>

<span class="normalTextSmallBold">&nbsp;</span>

<a 
id="ctl00_cphRoblox_ThreadView1_ctl00_Pager_Next" 
class="normalTextSmallBold" 
href="?ForumID=<?=$forumID?>&Page=<?=$page+1?>">

Next

</a>

<?php endif; ?>

</span>

</td>
</tr>
</tbody></table></span></td>
	</tr>
	<tr>
		<td colspan="2">
			&nbsp;
		</td>
	</tr>
	<tr>
		<td align="left" valign="top" style="font-size: 12px;">
			<span id="ctl00_cphRoblox_ThreadView1_ctl00_Whereami2" name="Whereami2">
<table cellpadding="0" cellspacing="0" width="100%">
    <tbody><tr>
        <td valign="top" align="left" width="1px">
            <nobr>
                <a id="ctl00_cphRoblox_ThreadView1_ctl00_Whereami2_ctl00_LinkHome" class="linkMenuSink" href="/web/20080622175918/http://www.roblox.com/Forum/Default.aspx">ROBLOX Forum</a>
            </nobr>
        </td>
        <td id="ctl00_cphRoblox_ThreadView1_ctl00_Whereami2_ctl00_ForumGroupMenu" class="popupMenuSink" valign="top" align="left" width="1px">
            <nobr>
                <span id="ctl00_cphRoblox_ThreadView1_ctl00_Whereami2_ctl00_ForumGroupSeparator" class="normalTextSmallBold">&nbsp;&gt;</span>
                <a id="ctl00_cphRoblox_ThreadView1_ctl00_Whereami2_ctl00_LinkForumGroup" class="linkMenuSink" href="/web/20080622175918/http://www.roblox.com/Forum/ShowForumGroup.aspx?ForumGroupID=1">ROBLOX</a>
            </nobr>
        </td>

        <td id="ctl00_cphRoblox_ThreadView1_ctl00_Whereami2_ctl00_ForumMenu" class="popupMenuSink" valign="top" align="left" width="1px">
            <nobr>
                <span id="ctl00_cphRoblox_ThreadView1_ctl00_Whereami2_ctl00_ForumSeparator" class="normalTextSmallBold">&nbsp;&gt;</span>
                <a id="ctl00_cphRoblox_ThreadView1_ctl00_Whereami2_ctl00_LinkForum" class="linkMenuSink" href="/web/20080622175918/http://www.roblox.com/Forum/ShowForum.aspx?ForumID=13">General Discussion</a>
            </nobr>
        </td>

        <td id="ctl00_cphRoblox_ThreadView1_ctl00_Whereami2_ctl00_PostMenu" class="popupMenuSink" valign="top" align="left" width="1px">
            <nobr>
                
                
            </nobr>
        </td>

        <td valign="top" align="left" width="*">&nbsp;</td>
    </tr>
</tbody></table>

<span id="ctl00_cphRoblox_ThreadView1_ctl00_Whereami2_ctl00_MenuScript"></span></span>
			
		</td>
		<td align="right" style="font-size: 12px;">
			<span class="normalTextSmallBold">Display threads for: </span><select name="ctl00$cphRoblox$ThreadView1$ctl00$DisplayByDays" id="ctl00_cphRoblox_ThreadView1_ctl00_DisplayByDays">
	<option selected="selected" value="0">All Days</option>
	<option value="1">Today</option>
	<option value="3">Past 3 Days</option>
	<option value="7">Past Week</option>
	<option value="14">Past 2 Weeks</option>
	<option value="30">Past Month</option>
	<option value="90">Past 3 Months</option>
	<option value="180">Past 6 Months</option>
	<option value="360">Past Year</option>

</select>
			<br>
			<a id="ctl00_cphRoblox_ThreadView1_ctl00_MarkAllRead" class="linkSmallBold" href="javascript:__doPostBack('ctl00$cphRoblox$ThreadView1$ctl00$MarkAllRead','')">Mark all threads as read</a>
			<br>
			<span class="normalTextSmallBold">
				
			</span>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;
		</td>
	</tr>
</tbody></table>
</span>
								</td>

								<td class="CenterColumn">&nbsp;&nbsp;&nbsp;</td>
								<!-- right margin -->
								<td class="RightColumn">&nbsp;&nbsp;&nbsp;</td>
								
							</tr>
						</tbody></table>
					</td>
				</tr>
				</tbody></table>

				</div>
    <?php require_once "../core/components/footer.php"; ?>
</div>