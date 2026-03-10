<?php
if (!isset($_GET["id"])) {
        http_response_code(400);
        echo "Missing asset id";
        return;
    }

$id = (int)$_GET["id"];

$url = "https://assetdelivery.roblox.com/v1/asset/?id=" . $id;
$assetPath = __DIR__ . "/assets/" . $id;

if (file_exists($assetPath)) {
    $asset = file_get_contents($assetPath);

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_buffer($finfo, $asset);
    finfo_close($finfo);

    header("Content-Length: " . strlen($asset));
    if (substr($asset, 0, 8) === "\x89PNG\r\n\x1a\n") {
        header("Content-Type: image/png");
    } else {
        header("Content-Type: text/xml");
    }

    echo $asset;
    return;
}

$cookieValue = "
_|WARNING:-DO-NOT-SHARE-THIS.--Sharing-this-will-allow-someone-to-log-in-as-you-and-to-steal-your-ROBUX-and-items.|_CAEaAhADIhwKBGR1aWQSFDEyMjM5MzI5MzMyMDYwNDM4OTU3KAM.v2vJStaP2d_6mZsKdTLsCfkZqn0x5HItdXp3-8w5QB0p-vtnXsg8-IgsGf7AuB2cTnmx2kME-jQm_Y0qMFkFsgSFDKBMKXh9FkUrqr5-C8Ho9hSpvPO0PEgidQpbjprB5yclZORSoaRDuYjUKbF0UlvPkFrojiFeFc-eQbddyeYql6kXYvP4NQsuMJBoHYNxq0cf2zN7Hbqb3OFqTwrJ1k3hmYwV7azwMlz3J3Byu8u1HIQFQl-xk53rMYhXqARP4Y8nNEkX2dCMckJD48kCDmTILziWKODXO4cYLxXv5zsbdfeAOa6Co_uJi1YbcR1be_NKW8x3xRVKCS4ASEbqlZIbRXmAg3C9mSVtla6G57VuZqUmGLcBq6Cqku_zzOsjTT4iOcCNhGdfaJC3FyqcphGI3tYnl_Djb1Jzm6wUmHPM9aEuY_eJ499iPhStwC5n6PH9NJeCqt-UEJ4VACUR_8lsQEEdeBa1j1ebPFHjvMkO2Rvnor_LOK53ElrAM5KQFGrrBJ6j0TYm6-f0tbJLws9zmY_sy7RJdg9Y5j4kH8xx_KKp3Pf-TdbYCYEOx6rP_I2XUVEFgNp1qihwgIDO2oUtVzW1ClvPyXyXiwIqH3eRvYhJZqP554O5d_LQMOiorgX9L4wLFaNq-lRvaQpRRWidqCRyi4NSG38oZ356UGeuk8k18TzRBcNiygG8XTUTmoMQ_qZgvFRX65jlWlt9TEXQxMDxI5ALNSpkyNmkIqiFYunWJWFvKo3NumgjySaPK6ck_ipK-3aPMHtf9aIQB9etaJA
";

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HEADER => true,
    CURLOPT_COOKIE => ".ROBLOSECURITY=" . $cookieValue,
    CURLOPT_HTTPHEADER => [
        'User-Agent: Mozilla/5.0'
    ]
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
    return;
}

$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$body = substr($response, $headerSize);

file_put_contents($assetPath, $body);

$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
if ($contentType) {
    header("Content-Type: " . $contentType);
}

echo $body;

curl_close($ch);
?>