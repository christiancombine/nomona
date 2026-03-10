<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "core/components/metadata.php";

$receiverId = $_GET["ID"];
$isFriendRequest = isset($_GET["fr"]) && $_GET["fr"] === "true";

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$receiverId]);
$receiver = $stmt->fetch(PDO::FETCH_ASSOC);

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $message = $_POST["message"];
    $subject = $_POST["subject"];

    $stmt = $db->prepare("
        INSERT INTO messages
        (messenger_id, sender_id, subject, content, is_friend_request)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $receiverId,
        $_USER["id"],
        $subject,
        $message,
        $isFriendRequest ? 1 : 0
    ]);
    header("Location: /User.aspx?ID=".$receiverId);
}
?>

<div id="Container">
    <?php require_once "core/components/header.php"; ?>
    <div id="Body">
        <h3>Send Friend Request</h3>
        <div id="MessageEditorContainer">
            <div id="MessageEditor">
                <table width="100%">
                    <tbody>
                        <tr valign="top">
                            <td style="width:12em; font-size: 12px">
                                <div id="From">
                                    <span class="Label">
                                    <span id="ctl00_cphRoblox_rbxMessageEditor_lblFrom">From:</span></span> <span class="Field">
                                    <br>
                                    <span id="ctl00_cphRoblox_rbxMessageEditor_lblAuthor"><?= $_USER["username"] ?></span></span>
                                </div>
                                <br>
                                <div id="To">
                                    <span class="Label">
                                    <span id="ctl00_cphRoblox_rbxMessageEditor_lblTo">Send To:</span></span> <span class="Field">
                                    <br>
                                    <span id="ctl00_cphRoblox_rbxMessageEditor_lblRecipient"><?= $receiver["username"] ?></span></span>
                                </div>
                            </td>
                            <td style="padding:0 24px 6px 12px">
                                <form method="POST" style="font-size: 12px;">
                                    <div id="Subject">
                                        <div class="Label"><label for="ctl00_cphRoblox_rbxMessageEditor_txtSubject" id="ctl00_cphRoblox_rbxMessageEditor_lblSubject">Subject:</label></div>
                                        <div class="Field"><input name="subject" type="text" id="ctl00_cphRoblox_rbxMessageEditor_txtSubject" class="TextBox" style="width:100%;"></div>
                                    </div>
                                    <div class="Body">
                                        <div class="Label">
                                            <label for="ctl00_cphRoblox_rbxMessageEditor_txtBody" id="ctl00_cphRoblox_rbxMessageEditor_lblBody">Message:</label>
                                        </div>
                                        <textarea name="message" rows="2" cols="20" id="ctl00_cphRoblox_rbxMessageEditor_txtBody" class="MultilineTextBox" style="width:100%;"></textarea>
                                        <br><br>
                                        <button class="Button"><?= $isFriendRequest ? "Send Friend Request" : "Send Message" ?></button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php require_once "core/components/footer.php"; ?>
</div>