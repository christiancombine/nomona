<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/config.php";

$userId = (int)$_GET["userId"] ?? null;

if (empty($userId)) {
    header("Content-Type: text/plain");
    http_response_code(400);
    echo "Invalid userId";
    exit;
}

$stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $userId]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($user)) {
    header("Content-Type: text/plain");
    http_response_code(400);
    echo "Invalid user";
    exit;
}

$headcolor = $user["headcolor"];
$torsocolor = $user["torsocolor"];
$leftarmcolor = $user["leftarmcolor"];
$rightarmcolor = $user["rightarmcolor"];
$leftlegcolor = $user["leftlegcolor"];
$rightlegcolor = $user["rightlegcolor"];

header("Content-Type: text/xml");
echo <<<XML
<roblox xmlns:xmime="http://www.w3.org/2005/05/xmlmime" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://www.roblox.com/roblox.xsd" version="4">
    <External>null</External>
    <External>nil</External>
    <Item class="BodyColors" referent="RBX0">
        <Properties>
            <int name="HeadColor">$headcolor</int>
            <int name="LeftArmColor">$leftarmcolor</int>
            <int name="LeftLegColor">$leftlegcolor</int>
            <string name="Name">Body Colors</string>
            <int name="RightArmColor">$rightarmcolor</int>
            <int name="RightLegColor">$rightlegcolor</int>
            <int name="TorsoColor">$torsocolor</int>
            <bool name="archivable">true</bool>
        </Properties>
    </Item>
</roblox>
XML;
?>