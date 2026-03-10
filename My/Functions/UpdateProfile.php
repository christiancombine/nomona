<?php
require_once "../../core/config.php";

$blurbContent = $_POST["BlurbContent"];
$theme = $_POST["Theme"];
$stmt = $db->prepare("UPDATE users SET blurb = ? WHERE id = ?");
$stmt->execute([$blurbContent, $_USER["id"]]);

$stmt2 = $db->prepare("UPDATE users SET theme = ? WHERE id = ?");
$stmt2->execute([$theme, $_USER["id"]]);

echo "OK";
header("Location: /My/Profile.aspx");
?>