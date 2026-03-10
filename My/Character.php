<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../core/components/metadata.php";

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) die("Not logged in.");

$wtype = $_GET['wtype'] ?? 'tshirt';

$stmt = $db->prepare("
    SELECT c.*, u.username
    FROM owned_items oi
    JOIN catalog c ON oi.asset_id = c.asset_id
    JOIN users u ON c.creator_id = u.id
    WHERE oi.user_id = ?
      AND c.asset_type = ?
      AND c.asset_id NOT IN (
            SELECT asset_id FROM wearing WHERE user_id = ?
      )
");
$stmt->execute([$user_id, $wtype, $user_id]);
$wardrobeItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare("
    SELECT c.*, u.username
    FROM wearing w
    JOIN catalog c ON w.asset_id = c.asset_id
    JOIN users u ON c.creator_id = u.id
    WHERE w.user_id = ?
");
$stmt->execute([$user_id]);
$wearingItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$BrickToHex = array_combine($RobloxColors, $RobloxColorsHtml);

$stmt = $db->prepare("
    SELECT headcolor, torsocolor,
           leftarmcolor, rightarmcolor,
           leftlegcolor, rightlegcolor
    FROM users
    WHERE id = ?
");
$stmt->execute([$user_id]);
$bodyColors = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bodyColors) {
    $bodyColors = [
        'headcolor'     => 1,
        'torsocolor'    => 26,
        'leftarmcolor'  => 1,
        'rightarmcolor' => 1,
        'leftlegcolor'  => 26,
        'rightlegcolor' => 26
    ];
}

function getHex($brickId, $lookup) {
    return $lookup[$brickId] ?? "#F2F3F2";
}
?>

<link rel="stylesheet" href="/CSS/WardrobeCSS.ashx" />

<div id="Container">
  <?php require_once "../core/components/header.php"; ?>

  <div id="Body">
    <div id="left" style="width: 69%; float: left">
      <!-- ============================= -->
      <!--         MY WARDROBE          -->
      <!-- ============================= -->

      <table cellspacing="0" width="100%" style="margin-bottom: 10px">
        <tbody>
          <tr>
            <th class="tablehead">My Wardrobe</th>
          </tr>

          <tr>
            <td
              class="tablebody"
              style="
                font-size: 12px;
                text-align: center;
                border-bottom: 1px solid black;
              "
            >
              <a href="?wtype=tshirt">T-Shirts</a> |
              <a href="?wtype=shirt">Shirts</a> |
              <a href="?wtype=pants">Pants</a> |
              <a href="?wtype=hat">Hats</a>
              <br />
              <a href="/Catalog.aspx">Shop</a> |
              <a href="/My/Upload/Default.aspx?type=0">Create</a>
            </td>
          </tr>

          <tr>
            <td class="tablebody">
              <div id="wardrobe" style="padding-left: 13px">
                <?php if (empty($wardrobeItems)): ?>
                <div style="padding: 20px; text-align: center">
                  You don't own any <?= htmlspecialchars($wtype) ?> items.
                </div>
                <?php endif; ?> <?php foreach ($wardrobeItems as $item): ?>
                <div
                  class="clothe"
                  style="
                    font-size: 10.85px;
                    display: inline-block;
                    margin: 5px;
                    vertical-align: top;
                  "
                >
                  <div class="imgc" style="cursor: pointer">
                    <a
                      href="/Item.aspx?ID=<?= $item['asset_id'] ?>"
                      style="
                        display: inline-block;
                        background-image: url(http://nomona.fit/thumbs/catalog/<?=$item["id"]?>.png);
                        background-size: 120px 120px;
                        height: 120px;
                        width: 120px;
                      "
                    >
                    </a>

                    <div class="fixed">
                      <a
                        href="/My/Functions/WearItem.php?id=<?= $item['asset_id'] ?>&wtype=<?= $item['asset_type'] ?>"
                      >
                        [ wear ]
                      </a>
                    </div>
                  </div>

                  <a class="name" href="/Item.aspx?ID=<?= $item['asset_id'] ?>">
                    <?= htmlspecialchars($item['name']) ?> </a
                  ><br />
                  <?php
                  $stmt2 = $db->prepare("
                  SELECT username FROM users WHERE id = ?
                  ");
                  $stmt2->execute([$item["creator_id"]]);
                  $user = $stmt2->fetch(PDO::FETCH_ASSOC);
                  ?>
                  Type: <?= ucfirst($item['asset_type']) ?><br />
                  Creator:
                  <a href="/User.aspx?ID=<?= $item['creator_id'] ?>">
                      <?= htmlspecialchars($item['username']) ?>
                  </a>
                </div>
                <?php endforeach; ?>

                <div style="clear: both"></div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="seperator"></div>

      <!-- ============================= -->
      <!--       CURRENTLY WEARING      -->
      <!-- ============================= -->

      <table cellspacing="0" width="100%" style="margin-bottom: 10px">
        <tbody>
          <tr>
            <th class="tablehead">Currently Wearing</th>
          </tr>
        </tbody>

        <tbody>
          <tr>
            <th class="tablebody">
              <?php if (empty($wearingItems)): ?>
              <div style="padding: 15px">Nothing equipped.</div>
              <?php endif; ?> <?php foreach ($wearingItems as $item): ?>
              <div
                class="clothe"
                style="
                  font-size: 10.85px;
                  display: inline-block;
                  margin: 5px;
                  vertical-align: top;
                "
              >
                <div class="imgc">
                  <a
                    href="/Item.aspx?ID=<?= $item['id'] ?>"
                    style="
                      display: inline-block;
                      background-image: url(http://nomona.fit/thumbs/catalog/<?=$item["id"]?>.png);
                      background-size: 120px 120px;
                      height: 120px;
                      width: 120px;
                    "
                  >
                  </a>

                  <div class="fixed">
                    <a
                      href="/My/Functions/RemoveItem.php?id=<?= $item['asset_id'] ?>&wtype=<?= $item['asset_type'] ?>"
                    >
                      [ remove ]
                    </a>
                  </div>
                </div>

                <a class="name" href="/Item.aspx?ID=<?= $item['asset_id'] ?>">
                  <?= htmlspecialchars($item['name']) ?> </a
                ><br />

                Type: <?= ucfirst($item['asset_type']) ?><br />
                Creator:
                <a href="/User.aspx?ID=<?= $item['creator_id'] ?>">
                    <?= htmlspecialchars($item['username']) ?>
                </a>
              </div>
              <?php endforeach; ?>
            </th>
          </tr>
        </tbody>
      </table>
    </div>

    <div id="right" style="width: 30%; float: right">
      <table cellspacing="0px" width="100%">
        <tbody>
          <tr>
            <th class="tablehead">My Character</th>
          </tr>
          <tr>
            <th class="tablebody">
              <img id="renderEngineIcon" src="/images/rendersettings.png" style="display: none; position: absolute; width: 45px; z-index: 99999; animation: spin 1s infinite linear; margin: 20px;">

              <img width="210" frameborder="0" style="padding-left: 5px;" class="margin" id="renderChar" src="http://nomona.fit/thumbs/avatar/<?= $_USER["id"] ?>.png">

              <img class="margin" id="uimg" src="">

              <form method="post" style="font-size: 11px;">
                Something wrong with your avatar? Click <a onclick="reload();">here</a> to fix the problem!
              </form>
            </th>
          </tr>
        </tbody>
      </table>
      <table cellspacing="0px" width="100%" style="margin-top: 10px;">
        <tbody>
        <tr>
          <th class="tablehead">Color Chooser</th>
        </tr>

        <tr>
          <th class="tablebody" style="font-size:11px;"><br>
            <button class="clickable"
                    id="head"
                    style="background-color:<?= getHex($bodyColors['headcolor'], $BrickToHex) ?>"
                    onclick="openColorPanel('head');"></button>

            <div class="seperator" style="height:5px;"></div>

            <button class="clickable2"
                    id="rightarm"
                    style="background-color:<?= getHex($bodyColors['rightarmcolor'], $BrickToHex) ?>"
                    onclick="openColorPanel('rightarm');"></button>

            <button class="clickable3"
                    id="torso"
                    style="background-color:<?= getHex($bodyColors['torsocolor'], $BrickToHex) ?>"
                    onclick="openColorPanel('torso');"></button>

            <button class="clickable2"
                    id="leftarm"
                    style="background-color:<?= getHex($bodyColors['leftarmcolor'], $BrickToHex) ?>"
                    onclick="openColorPanel('leftarm');"></button>

            <div class="seperator" style="height:5px;"></div>

            <button class="clickable2"
                    id="rightleg"
                    style="background-color:<?= getHex($bodyColors['rightlegcolor'], $BrickToHex) ?>"
                    onclick="openColorPanel('rightleg');"></button>

            <button class="clickable2"
                    id="leftleg"
                    style="background-color:<?= getHex($bodyColors['leftlegcolor'], $BrickToHex) ?>"
                    onclick="openColorPanel('leftleg');"></button>

            <br>
            Click <a href="#" disabled="disabled">here</a> to reset your character.<br>
          </th>
        </tr>
        </tbody>
      </table>
      <div id="colorPanel" class="popupControl" style="top: 435px; right: 165px; display: none; visibility: visible !important;">
      <table cellspacing="0" border="0" style="border-width:0px;border-collapse:collapse;">
        <tbody><tr>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('1')" style="display:inline-block;background-color:#F2F3F2;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('208')" style="display:inline-block;background-color:#E5E4DE;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('194')" style="display:inline-block;background-color:#A3A2A4;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('199')" style="display:inline-block;background-color:#635F61;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('26')" style="display:inline-block;background-color:#1B2A34;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('21')" style="display:inline-block;background-color:#C4281B;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('24')" style="display:inline-block;background-color:#F5CD2F;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('226')" style="display:inline-block;background-color:#FDEA8C;height:32px;width:32px;">
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('23')" style="display:inline-block;background-color:#0D69AB;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('107')" style="display:inline-block;background-color:#008F9B;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('102')" style="display:inline-block;background-color:#6E99C9;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('11')" style="display:inline-block;background-color:#80BBDB;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('45')" style="display:inline-block;background-color:#B4D2E3;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('135')" style="display:inline-block;background-color:#74869C;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('106')" style="display:inline-block;background-color:#DA8540;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('105')" style="display:inline-block;background-color:#E29B3F;height:32px;width:32px;">
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('141')" style="display:inline-block;background-color:#27462C;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('28')" style="display:inline-block;background-color:#287F46;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('37')" style="display:inline-block;background-color:#4B974A;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('119')" style="display:inline-block;background-color:#A4BD46;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('29')" style="display:inline-block;background-color:#A1C48B;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('210')" style="display:inline-block;background-color:#789081;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('38')" style="display:inline-block;background-color:#A05F34;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('192')" style="display:inline-block;background-color:#694027;height:32px;width:32px;">
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('104')" style="display:inline-block;background-color:#6B327B;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('9')" style="display:inline-block;background-color:#E8BAC7;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('101')" style="display:inline-block;background-color:#DA8679;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('5')" style="display:inline-block;background-color:#D7C599;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('153')" style="display:inline-block;background-color:#957976;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('217')" style="display:inline-block;background-color:#7C5C45;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('18')" style="display:inline-block;background-color:#CC8E68;height:32px;width:32px;">
            </div>
          </td>
          <td>
            <div class="ColorPickerItem" onclick="selBodyC('125')" style="display:inline-block;background-color:#EAB891;height:32px;width:32px;">
            </div>
          </td>
        </tr>
      </tbody></table>
    </div>
    </div>
    <script>
    let currentPart = null;
    const avatar  = document.getElementById("renderChar");

    function openColorPanel(part) {
        currentPart = part;
        document.getElementById("colorPanel").style.display = "block";
    }

    function renderAvatar(userId) {
      const spinner = document.getElementById("renderEngineIcon");

      spinner.style.display = "block";

      fetch("http://nomona.fit/Avatar/Generate.ashx?userId=" + userId)
      .then(res => res.text())
      .then(res => {
          console.log(res)
          spinner.style.display = "none";

          if (res === "OK") {
              avatar.src = "http://nomona.fit/thumbs/avatar/" + userId + ".png?cache=" + Date.now();
          }
      })
    }

    function reload() {
      window.location = "/My/Character.aspx";
    }

    function selBodyC(color) {
        if (!currentPart) return;

        fetch("/My/Functions/SetBodyColor.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "part=" + encodeURIComponent(currentPart) +
                  "&color=" + encodeURIComponent(color)
        })
        .then(res => res.text())
        .then(res => {
            if (res === "OK") {
                document.getElementById(currentPart).style.backgroundColor =
                    document.querySelector(`[onclick="selBodyC('${color}')"]`).style.backgroundColor;
                renderAvatar(<?= $_USER["id"] ?>);
                document.getElementById("colorPanel").style.display = "none";
            }
        });
        
        window.location = "/My/Character.aspx";
    }
    renderAvatar(<?= $_USER["id"] ?>);
    avatar.src = "http://nomona.fit/thumbs/avatar/<?= $_USER["id"] ?>.png"
    </script>
  </div>
</div>
