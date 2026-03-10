<link rel="stylesheet" href="/CSS/AdminCSS.ashx">
<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/core/config.php";
require_once "Security.php";

if(isset($_POST["upload_hat"])){

    $assetsDir = $_SERVER["DOCUMENT_ROOT"]."/Admin/Assets/";
    $rbxmDir = $_SERVER["DOCUMENT_ROOT"]."/asset/assets/";

    $meshName = basename($_FILES["mesh"]["name"]);
    $pngName = basename($_FILES["texture"]["name"]);

    $meshPath = $assetsDir.$meshName;
    $pngPath = $assetsDir.$pngName;

    move_uploaded_file($_FILES["mesh"]["tmp_name"], $meshPath);
    move_uploaded_file($_FILES["texture"]["tmp_name"], $pngPath);

    $rbxmContent = file_get_contents($_FILES["rbxm"]["tmp_name"]);

    $meshURL = "http://nomona.fit/Admin/Assets/".$meshName;
    $textureURL = "http://nomona.fit/Admin/Assets/".$pngName;

    $rbxmContent = preg_replace(
        '/<Content name="MeshId"><url>.*?<\/url><\/Content>/',
        '<Content name="MeshId"><url>'.$meshURL.'</url></Content>',
        $rbxmContent
    );

    $rbxmContent = preg_replace(
        '/<Content name="TextureId"><url>.*?<\/url><\/Content>/',
        '<Content name="TextureId"><url>'.$textureURL.'</url></Content>',
        $rbxmContent
    );

    $id = 1;
    while(file_exists($rbxmDir.$id)){
        $id++;
    }

    file_put_contents($rbxmDir.$id, $rbxmContent);

    $stmt = $db->prepare("
    INSERT INTO catalog
    (asset_id, creator_id, name, description, price, currency, asset_type, is_for_sale, sold_times)
    VALUES
    (:asset_id, :creator_id, :name, :description, :price, :currency, :asset_type, :is_for_sale, 0)
    ");

    $stmt->execute([
        ":asset_id" => $id,
        ":creator_id" => $_SESSION["user_id"],
        ":name" => $_POST["hat_name"],
        ":description" => $_POST["description"],
        ":price" => $_POST["price"],
        ":currency" => $_POST["currency"],
        ":asset_type" => "Hat",
        ":is_for_sale" => 1
    ]);
    $lastInsert = $db->lastInsertId();

    $ch = curl_init("http://nomona.fit/Hat/Generate.ashx?hat=" . $lastInsert);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 200);
    curl_exec($ch);
    curl_close($ch);


    echo "Hat uploaded successfully. Asset ID: ".$id;
}
?>

<div id="AdminContainer">
    <?php require_once "Navbar.php"; ?>
    <br>
    <h2>Upload Hat</h2>
    <hr>
    <form method="POST" enctype="multipart/form-data">
        <label>Hat Name</label><br>
        <input type="text" name="hat_name" required>
        <br><br>

        <label>.rbxm File</label><br>
        <input type="file" name="rbxm" accept=".rbxm" required>
        <br><br>

        <label>.mesh File</label><br>
        <input type="file" name="mesh" accept=".mesh" required>
        <br><br>

        <label>.png Texture</label><br>
        <input type="file" name="texture" accept=".png" required>
        <br><br>

        <label>Currency</label><br>
        <select name="currency" required>
            <option value="robux">Robux</option>
            <option value="tix">Tix</option>
        </select>
        <br><br>

        <label>Price</label>
        <input type="number" name="price" value="0" required>
        <br><br>

        <label>Description</label>
        <textarea name="description" value="0" required></textarea>
        <br><br>

        <button type="submit" name="upload_hat" class="Button">Upload Hat</button>
    </form>
</div>