<?php
$host = "46.101.155.229:3306";
$port = 3306;
$username = "Zylovesyou";
$password = "Sunebam3496";
$database = "nomonadb";

try {
    $db = new PDO(
        "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4",
        $username,
        $password
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}