<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/config.php";

header("Content-Type: text/css");

$cssFoldername = "default";
$cssFilename = "Forum";

if (!empty($_USER) && !empty($_USER["theme"])) {
    $allowedThemes = ["default", "roblox2"];

    if (in_array($_USER["theme"], $allowedThemes)) {
        $cssFoldername = $_USER["theme"];
    }
}

$cssFile = $_SERVER["DOCUMENT_ROOT"] . "/CSS/themes/$cssFoldername/$cssFilename.css";

if (!file_exists($cssFile)) {
    $cssFile = $_SERVER["DOCUMENT_ROOT"] . "/CSS/themes/default/$cssFilename.css";
}

readfile($cssFile);
?>