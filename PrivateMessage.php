<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "core/components/metadata.php";

$messageId = $_GET["MessageID"] ?? null;

if (empty($messageId)) {
    header("Location: /My/Inbox.aspx");
    exit;
}

$stmt = $db->prepare("
    SELECT m.*, u.username 
    FROM messages m
    LEFT JOIN users u ON u.id = m.sender_id
    WHERE m.id = ?
");

$stmt->execute([$messageId]);
$message = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$message) {
    header("Location: /My/Inbox.aspx");
    exit;
}

$db->prepare("UPDATE messages SET is_read = 1 WHERE id = ?")->execute([$messageId]);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["accept_friend"])) {

    $senderId = (int)$_POST["sender_id"];
    $currentUserId = $_USER["id"];
    
    $stmt = $db->prepare("
        INSERT INTO friends (user1_id, user2_id)
        VALUES (?, ?)
    ");
    $stmt->execute([$currentUserId, $senderId]);

    $stmt = $db->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$messageId]);

    header("Location: /User.aspx");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reply_message"])) {
    print_r($_POST);
    $receiverId = (int)$_POST["receiver_id"];
    $currentUserId = $_USER["id"];
    $subject = "Re: " . $message["subject"];
    $content = trim($_POST["reply_content"]);

    if ($message["messenger_id"] == $currentUserId) {
        header("Location: /My/Inbox.aspx");
        exit;
    }

    $stmt = $db->prepare("
        INSERT INTO messages (messenger_id, sender_id, subject, content, is_read)
        VALUES (?, ?, ?, ?, 0)
    ");

    $stmt->execute([$currentUserId, $receiverId, $subject, $content]);

    header("Location: /My/Inbox.aspx");
    exit;
}

$isFriendRequest = $message["is_friend_request"];
?>

<div id="Container">
    <?php require_once "core/components/header.php" ?>
    <div id="Body">
        <h3><?= htmlspecialchars($message["subject"]) ?></h3>
        <div id="MessageEditorContainer">
            <table width="100%">
                <tbody>
                    <tr valign="top">
                        <td style="width:12em; font-size: 12px">
                            <div id="From">
                                <span class="Label">
                                <span id="ctl00_cphRoblox_rbxMessageEditor_lblFrom">From:</span></span> <span class="Field">
                                <br>
                                <span id="ctl00_cphRoblox_rbxMessageEditor_lblAuthor">
                                <?= htmlspecialchars($message["username"] ?? "System") ?>
                                </span>
                            </div>
                            <td style="padding:0 24px 6px 12px; font-size: 12px">
                                <div class="Body">
                                    <div class="Label">
                                        <label for="ctl00_cphRoblox_rbxMessageEditor_txtBody" id="ctl00_cphRoblox_rbxMessageEditor_lblBody">Message:</label>
                                    </div>
                                    <p name="message" rows="2" cols="20" id="ctl00_cphRoblox_rbxMessageEditor_txtBody" class="MultilineTextBox" style="width:100%;"><?= nl2br(htmlspecialchars($message["content"])) ?></p>
                                    <br><br>
                                    <?php if ($isFriendRequest): ?>
                                    <form method="POST">
                                        <input type="hidden" name="accept_friend" value="1">
                                        <input type="hidden" name="sender_id" value="<?= $message['sender_id'] ?>">
                                        <button class="Button">Accept Friend Request</button>
                                    </form>
                                    <?php elseif ($message["messenger_id"] != $_USER["id"]): ?>
                                    <form method="POST">
                                        <input type="hidden" name="reply_message" value="1">
                                        <input type="hidden" name="receiver_id" value="<?= $message['sender_id'] ?>">

                                        <textarea name="reply_content" class="MultilineTextBox" style="width:100%; height:80px;" placeholder="Write your reply..."></textarea>
                                        <br><br>

                                        <button class="Button">Send Reply</button>
                                    </form>

                                    <?php else: ?>

                                    <p><i>You must wait for the other user to reply.</i></p>

                                    <?php endif; ?>
                                </div>
                            </td>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php require_once "core/components/footer.php" ?>
</div>