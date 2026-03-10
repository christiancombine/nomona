<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER["DOCUMENT_ROOT"]."/core/config.php";

function generateInviteKey($length = 15) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789%$@#';
    $key = '';
    
    for ($i = 0; $i < $length; $i++) {
        $key .= $characters[random_int(0, strlen($characters) - 1)];
    }

    return "NOMONA-" . $key;
}

function generateUniqueInvite($db) {
    do {
        $invite = generateInviteKey();

        $stmt = $db->prepare("SELECT id FROM invite_keys WHERE content = ?");
        $stmt->execute([$invite]);

        $exists = $stmt->fetch() !== false;

    } while ($exists);

    return $invite;
}

$invite = generateUniqueInvite($db);

$stmt = $db->prepare("INSERT INTO invite_keys (content) VALUES (?)");
$stmt->execute([$invite]);

echo $invite;