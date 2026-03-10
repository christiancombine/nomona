<?php 
require_once "../core/components/metadata.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['Username'] ?? '');
    $password = $_POST['Password'] ?? '';

    $stmt = $db->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && $password == $user["password"]) {

        $_SESSION['user_id'] = $user['id'];

        header("Location: /Games.aspx");
        exit;

    } else {
        $error = "Invalid username or password";
    }
}
?>

<div id="Container">
  <?php require_once "../core/components/header.php" ?>
  <div id="Body">
    <script type="text/javascript">
      function signUp() {
        window.location = "/Login/New.aspx";
      }
    </script>
    <div
      id="FrameLogin"
      style="
        margin: 150px auto 150px auto;
        width: 500px;
        border: black thin solid;
        padding: 22px;
      "
    >
      <div id="PaneNewUser">
        <h3>New User?</h3>
        <p>You need an account to play <?= $site["sitename"] ?>.</p>
        <p>
          If you aren't a <?= $site["sitename"] ?> member then
          <a id="ctl00_cphRoblox_HyperLink1" href="New.aspx">register</a>. It's
          easy and we do <em>not</em> share your personal information with
          anybody.
        </p>
      </div>
      <form id="PaneLogin" action="/Login/Default.aspx" method="post">
        <h3>Log In</h3>
        <?php
        if (!empty($error)) {
          ?>
          <h4 style="color: red;"><?= $error ?></h4>
          <?php
        }
        ?>
        <div class="AspNet-Login">
          <div class="AspNet-Login-UserPanel">
            <label
              for="ctl00_cphRoblox_lRobloxLogin_UserName"
              class="TextboxLabel"
              ><em>U</em>ser Name:</label
            >
            <input
              type="text"
              id="ctl00_cphRoblox_lRobloxLogin_UserName"
              name="Username"
              value=""
              accesskey="u"
            />&nbsp;
          </div>
          <div class="AspNet-Login-PasswordPanel">
            <label
              for="ctl00_cphRoblox_lRobloxLogin_Password"
              class="TextboxLabel"
              ><em>P</em>assword:</label
            >
            <input
              type="password"
              id="ctl00_cphRoblox_lRobloxLogin_Password"
              name="Password"
              value=""
              accesskey="p"
            />&nbsp;
          </div>
          <div class="AspNet-Login-SubmitPanel">
            <button
              type="submit"
              id="ctl00_cphRoblox_lRobloxLogin_LoginButton"
              name="ctl00$cphRoblox$lRobloxLogin$LoginButton"
              >
              Login</button>
          </div>
          <div class="AspNet-Login-PasswordRecoveryPanel">
            <a href="ResetPasswordRequest.aspx" title="Password recovery"
              >Forgot your password?</a
            >
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php require_once "../core/components/footer.php" ?>
</div>
