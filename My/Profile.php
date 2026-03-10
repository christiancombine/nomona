<?php
require_once "../core/components/metadata.php";

if (empty($_USER)) {
    header("Location: /Login/Default.aspx");
}
?>

<div id="Container">
    <?php require_once "../core/components/header.php"; ?>
    <style>
    #EditProfileContainer {
        background-color: #eeeeee;
        border: 1px solid #000;
        color: #555;
        margin: 0 auto;
        width: 620px;
    }
    fieldset {
        font-size: 1.2em;
        margin: 15px 0 0 0;
    }
    </style>
    <div id="Body">
        <div id="EditProfileContainer">
            <h2>Update Profile</h2>
            <form method="post" action="/My/Functions/UpdateProfile.php">
                <div id="Theme">
                    <fieldset title="Update your theme" style="font-size: 12px;">
                        <legend>Update your theme</legend>
                        <div class="Suggestion">Update your <?= $site["sitename"] ?> so your site can look unique!</div>
                        <select name="Theme" required>
                            <option value="default" <?= $_USER["theme"] == "default" ? "selected" : "" ?>>Default</option>
                            <option value="roblox2" <?= $_USER["theme"] == "roblox2" ? "selected" : "" ?>>Slopblox</option>
                        </select>
                    </fieldset>
                </div>
                <div id="Blurb">
                    <fieldset title="Update your personal blurb" style="font-size: 12px;">
                        <legend>Update your personal blurb</legend>
                        <div class="Suggestion">Describe yourself here (max. 1000 characters). Make sure not to provide any details that can be used to identify you outside <?= $site["sitename"] ?>.</div>
                        <div class="Suggestion" style="color: red;"></div>
                        <div class="BlurbRow" style="text-align: center; width: 100%">
                            <textarea class="TextBox" style="text-align: left; width: 100%" name="BlurbContent"><?= $_USER["blurb"] ?></textarea>
                        </div>
                        <div class="BlurbSubmit" style="text-align: center;">
                            <button class="Button" type="submit">Update Profile</button>
                        </div>
                    </fieldset>
                </div>
            </form>
        </div>
    </div>
    <?php require_once "../core/components/footer.php"; ?>
</div>