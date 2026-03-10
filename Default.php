<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "core/components/metadata.php";

$stmt = $db->prepare("
    SELECT * FROM games WHERE is_cool = 1
");

$stmt->execute();
$coolGames = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="Container">
  <?php require_once "core/components/header.php" ?>
  <div id="Body">
    <div id="SplashContainer">
      <div id="SignInPane">
        <div id="LoginViewContainer">
          <form id="LoginView" method="post" action="/Login/Default.aspx">
            <?php if (!empty($_USER)): ?>
              <h5>Logged In</h5>
            <?php else: ?>
              <h5>Member Login</h5>
            <?php endif; ?>

            <div class="AspNet-Login">
              <div class="AspNet-Login">
                <?php if (!empty($_USER)): ?>
                  <img src="http://nomona.fit/thumbs/avatar/<?= $_USER["id"] ?>.png" style="display:inline-block;width:145px;margin-top:15px;" border="0" id="img" alt="<?= $_USER["username"] ?>">
                <?php else: ?>
                  <div class="AspNet-Login-UserPanel">
                    <label
                      for="ctl00_cphRoblox_rbxLoginView_lvLoginView_lSignIn_UserName"
                      id="ctl00_cphRoblox_rbxLoginView_lvLoginView_lSignIn_UserNameLabel"
                      class="Label"
                      >Character Name</label
                    >
                    <input
                      name="Username"
                      type="text"
                      id="ctl00_cphRoblox_rbxLoginView_lvLoginView_lSignIn_UserName"
                      tabindex="1"
                      class="Text"
                    />
                  </div>
                  <div class="AspNet-Login-PasswordPanel">
                    <label
                      for="ctl00_cphRoblox_rbxLoginView_lvLoginView_lSignIn_Password"
                      id="ctl00_cphRoblox_rbxLoginView_lvLoginView_lSignIn_PasswordLabel"
                      class="Label"
                      >Password</label
                    >
                    <input
                      name="Password"
                      type="password"
                      id="ctl00_cphRoblox_rbxLoginView_lvLoginView_lSignIn_Password"
                      tabindex="2"
                      class="Text"
                    />
                  </div>
                  <!--div class="AspNet-Login-RememberMePanel"-->

                  <!--/div-->
                  <div class="AspNet-Login-SubmitPanel">
                    <button
                      id="ctl00_cphRoblox_rbxLoginView_lvLoginView_lSignIn_Login"
                      tabindex="4"
                      class="Button"
                      type="submit"
                    >Login</button>
                  </div>
                  <div class="AspNet-Login-PasswordRecoveryPanel">
                    <a
                      id="ctl00_cphRoblox_rbxLoginView_lvLoginView_lSignIn_hlPasswordRecovery"
                      tabindex="5"
                      href="Login/ResetPasswordRequest.aspx"
                      >Forgot your password?</a
                    >
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </form>
          <div id="Figure">
            <a
              id="ctl00_cphRoblox_LoginView1_ImageFigure"
              disabled="disabled"
              title="Figure"
              onclick="return false;"
              style="display: inline-block"
              ><img
                src="/images/NewFrontPageGuy.png"
                border="0"
                alt="Figure"

            /></a>
          </div>
        </div>
      </div>
      <div id="RobloxAtAGlance">
        <h2><?= $header ?></h2>
        <h3><?= $paragraph ?></h3>
        <ul id="ThingsToDo">
          <li id="Point1">
            <h3><?= $build1 ?></h3>
            <div>
              <?= $build1d ?>
            </div>
          </li>
          <li id="Point2">
            <h3><?= $build2 ?></h3>
            <div>
              <?= $build2d ?>
            </div>
          </li>
          <li id="Point3">
            <h3><?= $build3 ?></h3>
            <div>
              <?= $build3d ?>
            </div>
          </li>
        </ul>
        <div id="Showcase">
          <iframe
            width="400"
            height="326"
            src="<?= $randomVids[array_rand($randomVids)] ?>"
            title="YouTube video player"
            frameborder="0"
            allow="
              accelerometer;
              autoplay;
              clipboard-write;
              encrypted-media;
              gyroscope;
              picture-in-picture;
              web-share;
            "
            referrerpolicy="strict-origin-when-cross-origin"
            allowfullscreen
          ></iframe>
          <div id="Install" style="margin-right: 138px">
            <div id="CompatibilityNote">Works with your<br />Windows PC!</div>
            <div id="DownloadAndPlay">
              <a
                id="ctl00_cphRoblox_RobloxAtAGlanceLoginView_RobloxAtAGlance_Anonymous_hlDownloadAndPlay"
                href="Login/New.aspx?ReturnUrl=%2fGames.aspx"
                ><img
                  src="/images/DownloadAndPlay.png"
                  alt="FREE - Download and Play!"
                  border="0"
              /></a>
            </div>
          </div>
          <div id="ForParents">
            <a
              id="ctl00_cphRoblox_RobloxAtAGlanceLoginView_RobloxAtAGlance_Anonymous_hlKidSafe"
              title="ROBLOX is kid-safe!"
              href="Parents.aspx"
              style="display: inline-block"
              ><img
                title="ROBLOX is kid-safe!"
                src="/images/COPPASeal-125x125.jpg"
                border="0"
            /></a>
          </div>
        </div>
      </div>
      <div id="UserPlacesPane">
        <div id="UserPlaces_Content">
          <table
            id="ctl00_cphRoblox_CoolPlaces_CoolPlacesDataList"
            cellspacing="0"
            border="0"
            width="100%"
          >
            <tbody>
              <tr>
                <?php foreach($coolGames as $game): ?>
                <td class="UserPlace">
                  <a href="/PlaceItem.aspx?ID=<?= $game["id"] ?>">
                    <img 
                      width="130"
                      src="http://nomona.fit/thumbs/games/<?= $game["id"] ?>.png"
                      alt="<?= htmlspecialchars($game["name"]) ?>"
                    >
                  </a>
                </td>
                <?php endforeach; ?>
              </tr>
            </tbody>
          </table>
        </div>
        <div id="UserPlaces_Header">
          <h3>Cool Places</h3>
          <p>Check out some of our Social Links!</p>
        </div>
        <div
          id="ctl00_cphRoblox_CoolPlaces_ie6_peekaboo"
          style="clear: both"
        ></div>
      </div>
    </div>
  </div>
  <?php require_once "core/components/footer.php" ?>
</div>
