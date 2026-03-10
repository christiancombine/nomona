<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/core/components/metadata.php";

$type = $_GET["type"] ?? "0";

$isGameUpload = ($type == "1");
$isDecalUpload = ($type == "2");
$isShirtUpload = ($type == "3");
$isPantsUpload = ($type == "4");
?>
<link rel="stylesheet" href="/CSS/UploadCSS.ashx">

<div id="Container">
    <?php require_once $_SERVER["DOCUMENT_ROOT"]."/core/components/header.php"; ?>
    <div id="Body">
        <h1 style="font-family: Verdana, Geneva, Tahoma, sans-serif;">
        <?php
        if ($isGameUpload) {
            echo "Game Upload";
        } elseif ($isDecalUpload) {
            echo "Decal Upload";
        } elseif ($isShirtUpload) {
            echo "Shirt Builder";
        } elseif ($isPantsUpload) {
            echo "Pants Builder";
        } else {
            echo "T-Shirt Builder";
        }
        ?>
        </h1>
        <big>
            <table cellspacing="0px" width="100%" style="font-size: 12px;">
                <tbody>
                    <tr><th class="tablehead">Instructions</th></tr>
                    <tr><th class="tablebody">
                            <?php if ($isGameUpload): ?>
                            <p>Upload a Roblox place file (.rbxl) to create a new game on <?= $site["sitename"] ?>.</p>
                            <ol>
                                <li>Click the "Browse" button.</li>
                                <li>Select your <b>.rbxl</b> game file.</li>
                                <li>Enter a name and description.</li>
                                <li>Click <b>Upload Game</b>.</li>
                            </ol>
                            <p>Your game will appear on its own page and players will be able to join it.</p>
                            <?php elseif ($isDecalUpload): ?>
                            <p>Upload an image to create a decal on <?= $site["sitename"] ?>.</p>
                            <ol>
                            <li>Click the "Browse" button.</li>
                            <li>Select your image.</li>
                            <li>Enter a name and description.</li>
                            <li>Click <b>Upload Decal</b>.</li>
                            </ol>
                            <p>The decal will be added to your inventory.</p>
                            <?php elseif ($isShirtUpload): ?>
                            <p>Upload a shirt template image to create a shirt on <?= $site["sitename"] ?>.</p>
                            <ol>
                            <li>Click the "Browse" button.</li>
                            <li>Select your shirt template image.</li>
                            <li>Enter a name and description.</li>
                            <li>Click <b>Create Shirt</b>.</li>
                            </ol>
                            <p>The shirt will be added to your inventory.</p>
                            <?php elseif ($isPantsUpload): ?>
                            <p>Upload a pants template image to create pants on <?= $site["sitename"] ?>.</p>
                            <ol>
                            <li>Click the "Browse" button.</li>
                            <li>Select your pants template.</li>
                            <li>Enter a name and description.</li>
                            <li>Click <b>Create Pants</b>.</li>
                            </ol>
                            <p>The pants will be added to your inventory.</p>
                            <?php else: ?>
                            <p>On <?= $site["sitename"] ?>, a T-Shirt is a transparent torso adornment with a decal applied to the front surface.</p>
                            <ol>
                                <li>Click the "Browse" button below.</li>
                                <li>Select the image you want to use.</li>
                                <li>Click "Create T-Shirt".</li>
                            </ol>
                            <p>The shirt will be added to your inventory.</p>
                            <?php endif; ?>
                    </th></tr>
                </tbody>
            </table>
            <br>
            <table cellspacing="0px" width="100%" style="font-size: 12px;">
                <tbody>
                    <tr><th class="tablehead"><?php
                    if ($isGameUpload) {
                        echo "Game Upload";
                    } elseif ($isDecalUpload) {
                        echo "Decal Upload";
                    } elseif ($isShirtUpload) {
                        echo "Shirt Builder";
                    } elseif ($isPantsUpload) {
                        echo "Pants Builder";
                    } else {
                        echo "T-Shirt Builder";
                    }
                    ?></th></tr>
                    <tr>
                        <th class="tablebody">
                        <form style="text-align: center;"
                        action="<?=
                        $isGameUpload ? "/BaseAPI/UploadGame.php" :
                        ($isDecalUpload ? "/BaseAPI/UploadDecal.php" :
                        ($isShirtUpload ? "/BaseAPI/UploadShirt.php" :
                        ($isPantsUpload ? "/BaseAPI/UploadPants.php" :
                        "/BaseAPI/UploadTShirt.php")))
                        ?>"
                        method="post" enctype="multipart/form-data">
                            <div class="formrow" id="livePreviewWrap" style="display:none;">
                                <div class="previewbox">
                                <img id="livePreviewImg" style="width:150px;height:150px;">
                                </div>
                            </div>
                            <form method="post" enctype="multipart/form-data" style="padding:25px;">
                                <div class="formrow">
                                    <div>Name</div>
                                    <input type="text" name="name" maxlength="50" value="">
                                </div>
                                <div class="formrow">
                                    <div>Description</div>
                                    <textarea name="description" rows="4"></textarea>
                                </div>
                                <?php if (!$isGameUpload): ?>
                                <div class="formrow">
                                    <div>Currency</div>
                                    <select name="buywith">
                                        <option value="robux" selected>Robux</option>
                                        <option value="tix">Tix</option>
                                    </select>
                                </div>

                                <div class="formrow">
                                    <div>Price</div>
                                    <input type="number" name="price" min="0" max="1000000" value="0">
                                </div>

                                <?php endif; ?>
                                <div class="formrow">
                                    <div><?= $isGameUpload ? "RBXL File" : "PNG File" ?></div>
                                    <input type="file" name="file" id="fileToUpload"
                                    accept="<?=
                                    $isGameUpload ? ".rbxl" :
                                    ($isShirtUpload ? "image/png,image/jpeg,image/jpg" :
                                    ($isPantsUpload ? "image/png,image/jpeg,image/jpg" :
                                    ($isDecalUpload ? "image/png,image/jpeg,image/jpg" : "image/png")))
                                    
                                    ?>">
                                </div>
                                <br>
                                <input type="submit" name="submit"
                                value="<?=
                                $isGameUpload ? "Upload Game" :
                                ($isDecalUpload ? "Upload Decal" :
                                ($isPantsUpload ? "Create Pants" :
                                ($isShirtUpload ? "Create Shirt" : "Create T-Shirt")))
                                ?>">
                            </form>
                        </center>
                        </th>
                    </tr>
                </tbody>
            </table>
        </big>
    </div>
    <?php require_once $_SERVER["DOCUMENT_ROOT"]."/core/components/footer.php"; ?>
</div>