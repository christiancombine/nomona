<?php
header('Content-Type: application/json');

echo json_encode([
    "version" => "1.0.3",
    "url" => "http://nomona.fit/BootstrapperAPI/Install/Client.zip"
]);