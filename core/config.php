<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/services/RCCService.php";
require_once $_SERVER["DOCUMENT_ROOT"]. "/vendor/autoload.php";
require_once $_SERVER["DOCUMENT_ROOT"]. "/core/db.php";

class MySQLSessionHandler implements SessionHandlerInterface
{
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function open(string $path, string $name): bool {
        return true;
    }

    public function close(): bool {
        return true;
    }

    public function read(string $id): string|false {
        $stmt = $this->db->prepare("SELECT data FROM sessions WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $row["data"] : "";
    }

    public function write(string $id, string $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO sessions (id, data, last_access)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE
                data = VALUES(data),
                last_access = VALUES(last_access)
        ");

        return $stmt->execute([$id, $data, time()]);
    }

    public function destroy(string $id): bool {
        $stmt = $this->db->prepare("DELETE FROM sessions WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function gc(int $max_lifetime): int|false {
        $stmt = $this->db->prepare("DELETE FROM sessions WHERE last_access < ?");
        $stmt->execute([time() - $max_lifetime]);
        return $stmt->rowCount();
    }
}

$lifetime = 604800;

session_set_cookie_params($lifetime);
ini_set("session.gc_maxlifetime", $lifetime);

$handler = new MySQLSessionHandler($db);
session_set_save_handler($handler, true);

session_start();

$rcc = new RCCServiceSoap("45.248.37.221",64989);

$site = [
    "sitename" => "NOMONA",
    "sitedomain" => "nomona.fit"
];
$randomVids = [
"https://www.youtube.com/embed/d3okQfFA7Wc?si=fbGyFMQ97TvS5UbG", 
"https://www.youtube.com/embed/M7VSEZOQIlg?si=DxydVRTld8FRtrTN",
"https://www.youtube.com/embed/SYS0bEpSivA?si=oTjGQLDw1lHOvwis",
"https://www.youtube.com/embed/7OXx7_Rxx8k?si=cpsmccAVlivt5bug",
"https://www.youtube.com/embed/Nd0OFVeV-q4?si=04U3x5cM7kuO9gxs"
];

$RobloxColors = array(
    1,          //1
    208,        //2
    194,        //3
    199,        //4
    26,         //5
    21,         //6
    24,         //7
    226,        //8
    23,         //9
    107,        //10
    102,        //11
    11,         //12
    45,         //13
    135,        //14
    106,        //15
    105,        //16
    141,        //17
    28,         //18
    37,         //19
    119,        //20
    29,         //21
    151,        //22
    38,         //23
    192,        //24
    104,        //25
    9,          //26
    101,        //27
    5,          //28
    153,        //29
    217,        //30
    18,         //31
    125         //32
);

$RobloxColorsHtml = array(
    "#F2F3F2",  //1
    "#E5E4DE",  //2
    "#A3A2A4",  //3
    "#635F61",  //4
    "#1B2A34",  //5
    "#C4281B",  //6
    "#F5CD2F",  //7
    "#FDEA8C",  //8
    "#0D69AB",  //9
    "#008F9B",  //10
    "#6E99C9",  //11
    "#80BBDB",  //12
    "#B4D2E3",  //13
    "#74869C",  //14
    "#DA8540",  //15
    "#E29B3F",  //16
    "#27462C",  //17
    "#287F46",  //18
    "#4B974A",  //19
    "#A4BD46",  //20
    "#A1C48B",  //21
    "#789081",  //22
    "#A05F34",  //23
    "#694027",  //24
    "#6B327B",  //25
    "#E8BAC7",  //26
    "#DA8679",  //27
    "#D7C599",  //28
    "#957976",  //29
    "#7C5C45",  //30
    "#CC8E68",  //31
    "#EAB891"   //32
);

$cleanup = $db->prepare("
    DELETE FROM gameservers
    WHERE players = 0
    AND created_at < (NOW() - INTERVAL 20 SECOND)
");

$cleanup->execute();

if (!empty($_SESSION["user_id"])) {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION["user_id"]]);

    $_USER = $stmt->fetch(PDO::FETCH_ASSOC);

    $now = time();
    $lastClaim = $_USER["last_tix_claim"] ? strtotime($_USER["last_tix_claim"]) : 0;

    if ($now - $lastClaim >= 86400) {

        $reward = 25;

        $rewardStmt = $db->prepare("
            UPDATE users 
            SET tix = tix + :reward,
                last_tix_claim = NOW()
            WHERE id = :userid
        ");

        $rewardStmt->execute([
            "reward" => $reward,
            "userid" => $_USER["id"]
        ]);

        $_USER["tix"] += $reward;
    }

    $banStmt = $db->prepare("
        SELECT * FROM bans
        WHERE banneduser_id = ?
        AND expired = 0
        ORDER BY banned_at DESC
        LIMIT 1
    ");

    $banStmt->execute([$_SESSION["user_id"]]);
    $ban = $banStmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($ban)) {
        if ($ban["expires_at"] !== null && strtotime($ban["expires_at"]) <= time()) {

            $update = $db->prepare("
                UPDATE bans
                SET expired = 1
                WHERE id = ?
            ");

            $update->execute([$ban["id"]]);

        } else {
            if ($_SERVER["REQUEST_URI"] !== "/My/NotApproved.aspx") {
                header("Location: /My/NotApproved.aspx");
                exit;
            }

        }
    }
}