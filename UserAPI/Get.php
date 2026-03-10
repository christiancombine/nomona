<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/config.php";

$id = $_GET["ID"] ?? null;

header("Content-Type: text/plain");

if (!ctype_digit($id)) {
    http_response_code(400);
    echo "InvalidUser";
    exit;
}

$stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(404);
    echo "UserNotFound";
    exit;
}

echo "ID=" . $user["id"] . "\n";
echo "Username=" . $user["username"] . "\n";
echo "Blurb=" . $user["blurb"] . "\n";
echo "JoinDate=" . $user["created_at"] . "\n";
echo "MemberType=" . $user["role"] . "\n";