<?php
require_once "../core/components/metadata.php";

if (empty($_USER)) {
    header("Location: /Login/Default.aspx");
}

$stmt = $db->prepare("
SELECT m.*, u.username
FROM messages m
JOIN users u ON u.id = m.sender_id
WHERE m.messenger_id = ?
ORDER BY m.messaged_at DESC
");

$stmt->execute([$_USER["id"]]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="Container">
    <?php require_once "../core/components/header.php"; ?>
    <div id="Body">
        <div id="InboxContainer">
            <h2>Inbox</h2>
            <div id="Inbox">
                <div>
                    <table cellspacing="0" cellpadding="3" border="0" id="ctl00_cphRoblox_InboxGridView" style="width:898px;border-collapse:collapse;font-size:12px">
                        <tr class="InboxHeader">
                            <th align="left" scope="col">
							    <input id="ctl00_cphRoblox_InboxGridView_ctl01_SelectAllCheckBox" type="checkbox" name="ctl00$cphRoblox$InboxGridView$ctl01$SelectAllCheckBox" onclick="javascript:setTimeout('__doPostBack(\'ctl00$cphRoblox$InboxGridView$ctl01$SelectAllCheckBox\',\'\')', 0)">
						    </th>
                            <th align="left" scope="col"><a href="javascript:__doPostBack('ctl00$cphRoblox$InboxGridView','Sort$m.[Subject]')">Subject</a></th>
                            <th align="left" scope="col"><a href="javascript:__doPostBack('ctl00$cphRoblox$InboxGridView','Sort$u.[userName]')">From</a></th>
                            <th align="left" scope="col"><a href="javascript:__doPostBack('ctl00$cphRoblox$InboxGridView','Sort$m.[Created]')">Date</a></th>
                            <?php if(!empty($messages)): ?>
                            <?php foreach($messages as $msg): ?>
                            <tr class="InboxRow">

                                <td>
                                    <span style="display:inline-block;width:25px;">
                                    <input type="checkbox" name="delete[]" value="<?= $msg["id"] ?>">
                                    </span>
                                </td>

                                <td align="left">
                                    <a href="/PrivateMessage.aspx?MessageID=<?= $msg["id"] ?>" style="display:inline-block;width:325px;">
                                    <?= htmlspecialchars(substr($msg["subject"],0,40)) ?>
                                    </a>
                                </td>

                                <td align="left">
                                    <a title="Visit <?= htmlspecialchars($msg["username"]) ?>'s Home Page"
                                    href="../User.aspx?ID=<?= $msg["sender_id"] ?>"
                                    style="display:inline-block;width:175px;">
                                    <?= htmlspecialchars($msg["username"]) ?>
                                    </a>
                                </td>

                                <td align="left">
                                    <?= date("n/j/Y g:i:s A", strtotime($msg["messaged_at"])) ?>
                                </td>

                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>

                            <tr class="InboxRow">
                            <td colspan="4">You have no messages.</td>
                            </tr>

                            <?php endif; ?>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php require_once "../core/components/footer.php"; ?>
</div>