<?php

require_once "include/bittorrent.inc.php";

dbconn();

loggedinorreturn();

stdhead($CURUSER["username"] . "'s private page");

$referrer = $_SERVER['HTTP_REFERER'];
$UA = $_SERVER['HTTP_USER_AGENT'];

if (isset($_GET["edited"])) {
    print("<p id=toast class=success>Your profile has been updated.</p>\n");
} elseif (strpos($referrer, 'login') !== false) {
    print("<p id=toast class=success><span class=title>Welcome back, " . htmlspecialchars($CURUSER["username"]) . "!</span><br>We've missed you!\n");
    if (strpos($UA, 'MYOB') == false)
        print("<span id=uawarn><b>Warning!</b>\nYour browser's user agent is leaking!<br>" . $UA . "</span>");
    print("</p>\n");
}

?>
<form method=post action=takeprofedit.php>
<table id=myaccount>
<tr><th colspan="2">Profile for: <?php echo $CURUSER["username"] ?>&nbsp;&nbsp;<a href=mytorrents.php>View or edit your torrents</a></th></tr>
<?php

$res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) FROM torrents WHERE owner=" . $CURUSER["id"]);
$row = mysqli_fetch_array($res);
tr("Uploaded torrents", $row[0]);

$res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT COUNT(*) FROM comments WHERE user=" . $CURUSER["id"]);
$row = mysqli_fetch_array($res);
tr("Comments posted", $row[0]);

tr("New password", "<input type=password name=chpassword size=40 required />", 1);
tr("Confirm password", "<input type=password name=passagain size=40 required />", 1);

?>
<tr id=dostuff><td colspan=2><input type=submit value="Update Password" /></td></tr>
</table>
</form>
<?php
stdfoot();
?>
