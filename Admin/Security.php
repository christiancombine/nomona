<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/core/config.php";
if ($_USER["role"] !== "admin" || empty($_USER)) {
    http_response_code(400);
    exit;
}