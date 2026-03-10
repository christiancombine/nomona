<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER["DOCUMENT_ROOT"] . "/core/components/metadata.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/config.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['ctl00$cphRoblox$UserName'] ?? '');
    $password = $_POST['ctl00$cphRoblox$Password'] ?? '';
    $confirm  = $_POST['ctl00$cphRoblox$TextBoxPasswordConfirm'] ?? '';
    $email    = trim($_POST['ctl00$cphRoblox$TextBoxEMail'] ?? '');
    $inviteKey = trim($_POST["InviteKey"]);

    if (!preg_match('/^[A-Za-z0-9]{3,20}$/', $username)) {
        $error = "Username must be 3-20 letters/numbers.";
    }
    elseif (strlen($password) < 4 || strlen($password) > 50) {
        $error = "Password must be 4-50 characters.";
    }
    elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    }
    elseif (empty($inviteKey)) {
        $error = "Invite key is required.";
    }

    $stmt = $db->prepare("SELECT id FROM invite_keys WHERE content = ? AND used = 0");
    $stmt->execute([$inviteKey]);
    $inviteData = $stmt->fetch();

    if (!$inviteData) {
        $error = "Invalid or already used invite key.";
    }

    if (empty($error)) {
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            $error = "Username already taken.";
        }
    }

    if (empty($error)) {

        function generateAuthToken($length = 10) {
            return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
        }

        $authToken = generateAuthToken(10);

        $db->prepare("
            INSERT INTO users (username, password, email, role, created_at, blurb, authToken)
            VALUES (?, ?, ?, 'Member', NOW(), ?, ?)
        ")->execute([$username, $password, $email, "", $authToken]);

        $userId = $db->lastInsertId();

        $db->prepare("
            UPDATE invite_keys 
            SET used = 1, used_by = ?, used_at = NOW() 
            WHERE content = ?
        ")->execute([$userId, $inviteKey]);

        $_SESSION['user_id'] = $userId;

        $ch = curl_init("http://nomona.fit/Avatar/Generate.ashx?userId=" . $userId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 200);
        curl_exec($ch);
        curl_close($ch);

        header("Location: /Games.aspx");
        exit;
    }
}
?>

<div id="Container">
  <?php require_once "../core/components/header.php" ?>
  <div id="Body">
    <form id="Registration" method="post" action="/Login/New.aspx">
      <div id="ctl00_cphRoblox_upAccountRegistration">
        <h2>Sign Up and Play</h2>
        <h3>Step 1 of 2: Create Account</h3>
        <?php
        if (!empty($error)) {
          ?>
          <h3 style="color: red;"><?= $error ?></h3>
          <?php
        }
        ?>
        <div id="EnterAgeGroup">
          <fieldset title="Provide your age-group">
            <legend>Provide your age-group</legend>
            <div class="Suggestion">
              This will help us to customize your experience. Users under 13
              years will only be shown pre-approved images.
            </div>
            <div class="AgeGroupRow">
              <span id="ctl00_cphRoblox_rblAgeGroup"
                ><input
                  id="ctl00_cphRoblox_rblAgeGroup_0"
                  type="radio"
                  name="ctl00$cphRoblox$rblAgeGroup"
                  value="1"
                  checked="checked"
                  tabindex="5"
                /><label for="ctl00_cphRoblox_rblAgeGroup_0"
                  >Under 13 years</label
                ><br /><input
                  id="ctl00_cphRoblox_rblAgeGroup_1"
                  type="radio"
                  name="ctl00$cphRoblox$rblAgeGroup"
                  value="2"
                  onclick="
                    javascript: setTimeout(
                      '__doPostBack(\'ctl00$cphRoblox$rblAgeGroup$1\',\'\')',
                      0,
                    );
                  "
                  tabindex="5"
                /><label for="ctl00_cphRoblox_rblAgeGroup_1"
                  >13 years or older</label
                ></span
              >
            </div>
          </fieldset>
        </div>
        <div id="EnterUsername">
          <fieldset title="Choose a name for your <?= $site["sitename"] ?> character">
            <legend>Choose a name for your <?= $site["sitename"] ?> character</legend>
            <div class="Suggestion">
              Use 3-20 alphanumeric characters: A-Z, a-z, 0-9, no spaces
            </div>
            <div class="Validators">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="UsernameRow">
              <label
                for="ctl00_cphRoblox_UserName"
                id="ctl00_cphRoblox_UserNameLabel"
                class="Label"
                >Character Name:</label
              >&nbsp;<input
                name="ctl00$cphRoblox$UserName"
                type="text"
                id="ctl00_cphRoblox_UserName"
                tabindex="1"
                class="TextBox"
              />
            </div>
          </fieldset>
        </div>
        <div id="EnterPassword">
          <fieldset title="Choose your <?= $site["sitename"] ?> password">
            <legend>Choose your <?= $site["sitename"] ?> password</legend>
            <div class="Suggestion">4-10 characters, no spaces</div>
            <div class="Validators">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="PasswordRow">
              <label
                for="ctl00_cphRoblox_Password"
                id="ctl00_cphRoblox_LabelPassword"
                class="Label"
                >Password:</label
              >&nbsp;<input
                name="ctl00$cphRoblox$Password"
                type="password"
                id="ctl00_cphRoblox_Password"
                tabindex="2"
                class="TextBox"
              />
            </div>
            <div class="ConfirmPasswordRow">
              <label
                for="ctl00_cphRoblox_TextBoxPasswordConfirm"
                id="ctl00_cphRoblox_LabelPasswordConfirm"
                class="Label"
                >Confirm Password:</label
              >&nbsp;<input
                name="ctl00$cphRoblox$TextBoxPasswordConfirm"
                type="password"
                id="ctl00_cphRoblox_TextBoxPasswordConfirm"
                tabindex="3"
                class="TextBox"
              />
            </div>
          </fieldset>
        </div>
        <div id="EnterChatMode">
          <fieldset title="Choose your chat mode">
            <legend>Choose your chat mode</legend>
            <div class="Suggestion">
              All in-game chat is subject to profanity filtering and moderation.
              For enhanced chat safety, choose SuperSafe Chat; only chat from
              pre-approved menus will be shown to you.
            </div>
            <div class="ChatModeRow">
              <span id="ctl00_cphRoblox_rblChatMode"
                ><input
                  id="ctl00_cphRoblox_rblChatMode_0"
                  type="radio"
                  name="ctl00$cphRoblox$rblChatMode"
                  value="false"
                  checked="checked"
                  tabindex="6"
                /><label for="ctl00_cphRoblox_rblChatMode_0">Safe Chat</label
                ><br /><input
                  id="ctl00_cphRoblox_rblChatMode_1"
                  type="radio"
                  name="ctl00$cphRoblox$rblChatMode"
                  value="true"
                  tabindex="6"
                /><label for="ctl00_cphRoblox_rblChatMode_1"
                  >SuperSafe Chat</label
                ></span
              >
            </div>
          </fieldset>
        </div>
        <div id="EnterEmail">
          <fieldset title="Provide your parent's email address">
            <legend>Provide your parent's email address</legend>
            <div class="Suggestion">
              This will allow you to recover a lost password
            </div>
            <div class="Validators">
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="EmailRow">
              <label
                for="ctl00_cphRoblox_TextBoxEMail"
                id="ctl00_cphRoblox_LabelEmail"
                class="Label"
                >Your Parent's Email:</label
              >&nbsp;<input
                name="ctl00$cphRoblox$TextBoxEMail"
                type="text"
                id="ctl00_cphRoblox_TextBoxEMail"
                tabindex="4"
                class="TextBox"
              />
            </div>
          </fieldset>
        </div>
        <div id="EnterEmail">
          <fieldset title="Provide your invite key for NOMONA">
            <legend>Provide your invite key for NOMONA</legend>
            <div class="Suggestion">
              This will allow you to create an account. It's needed.
            </div>
            <div class="Validators">
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="EmailRow">
              <label
                for="ctl00_cphRoblox_TextBoxInvite"
                id="ctl00_cphRoblox_LabelInvite"
                class="Label"
                >Your Invite Key:</label
              >&nbsp;<input
                name="InviteKey"
                type="text"
                id="ctl00_cphRoblox_TextBoxInvite"
                tabindex="4"
                class="TextBox"
              />
            </div>
          </fieldset>
        </div>
        <div class="Confirm">
          <input
            type="submit"
            name="ctl00$cphRoblox$ButtonCreateAccount"
            value="Register"
            onclick="
              javascript: WebForm_DoPostBackWithOptions(
                new WebForm_PostBackOptions(
                  'ctl00$cphRoblox$ButtonCreateAccount',
                  '',
                  true,
                  '',
                  '',
                  false,
                  false,
                ),
              );
            "
            id="ctl00_cphRoblox_ButtonCreateAccount"
            tabindex="5"
            class="BigButton"
          />
        </div>
      </div>
    </form>
    <div id="Sidebars">
      <div id="AlreadyRegistered">
        <h3>Already Registered?</h3>
        <p>
          If you just need to login, go to the
          <a
            id="ctl00_cphRoblox_HyperLinkLogin"
            href="Default.aspx?ReturnUrl=%2f"
            >Login</a
          >
          page.
        </p>
        <p>
          If you have already registered but you still need to download the game
          installer, go directly to
          <a
            id="ctl00_cphRoblox_HyperLinkDownload"
            href="/web/20070804083927/http://roblox.com/Install/Default.aspx?ReturnUrl=%2f"
            >download</a
          >.
        </p>
      </div>
      <div id="TermsAndConditions">
        <h3>Terms &amp; Conditions</h3>
        <p>
          Registration does not provide any guarantees of service. See our
          <a
            id="ctl00_cphRoblox_HyperLinkToS"
            href="/web/20070804083927/http://roblox.com/Info/TermsOfService.aspx?layout=null"
            target="_blank"
            >Terms of Service</a
          >
          and
          <a
            id="ctl00_cphRoblox_HyperLinkEULA"
            href="/web/20070804083927/http://roblox.com/Info/EULA.htm"
            target="_blank"
            >Licensing Agreement</a
          >
          for details.
        </p>
        <p>
          <?= $site["sitename"] ?> will not share your email address with 3rd parties. See our
          <a
            id="ctl00_cphRoblox_HyperLinkPrivacy"
            href="/web/20070804083927/http://roblox.com/Info/Privacy.aspx?layout=null"
            target="_blank"
            >Privacy Policy</a
          >
          for details.
        </p>
      </div>
    </div>
    <div id="ctl00_cphRoblox_ie6_peekaboo" style="clear: both"></div>
  </div>
  <?php require_once "../core/components/footer.php" ?>
</div>
