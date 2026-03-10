<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/core/config.php");

$db->query("
DELETE FROM gameservers
WHERE last_ping < (NOW() - INTERVAL 60 SECOND)
");

echo "cleaned";