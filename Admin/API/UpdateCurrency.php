<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/core/config.php";

$userId = $_POST["userId"] ?? null;
$currencyType = $_POST["currencytype"] ?? null;
$additionMethod = $_POST["additionmethod"] ?? null;
$amount = intval($_POST["amount"] ?? 0);

$allowedCurrencies = ["robux", "tix"];

if (!$userId || !is_numeric($userId)) {
    die("Invalid user ID");
}

if (!in_array($currencyType, $allowedCurrencies)) {
    die("Invalid currency type");
}

if (!in_array($additionMethod, ["set", "update"])) {
    die("Invalid addition method");
}

try {

    if ($additionMethod === "set") {
        $stmt = $db->prepare("
            UPDATE users 
            SET $currencyType = :amount 
            WHERE id = :userid
        ");
        $stmt->bindParam(":amount", $amount, PDO::PARAM_INT);
    } else {
        $stmt = $db->prepare("
            UPDATE users 
            SET $currencyType = $currencyType + :amount 
            WHERE id = :userid
        ");
        $stmt->bindParam(":amount", $amount, PDO::PARAM_INT);
    }

    $stmt->bindParam(":userid", $userId, PDO::PARAM_INT);

    $stmt->execute();

    header("Location: /Admin/UserList.aspx");
    exit;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>