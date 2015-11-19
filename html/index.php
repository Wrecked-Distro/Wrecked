<?php

// index.php
// new main page for wrecked, eliminating frames

// important functions in salesincludes
// function getDiscount($usertype, $arraycontainingitem) returns $discountID
// function calcQuantity($usertype, $arraycontainingitem) returns $displayquantity
// function calcPrice($arraycontainingitem, $discoundID) returns $currentprice

session_start();
 // start the session
header("Cache-control: private");

include('../config/app.config'); // application config

include ("header.php");
include ("db.php");


// functions to read audio and images from the folder field
include ("saleincludes.php");
include ("parseaudio.php");
include ("parseimage.php");
include ("search.php");
include ("listkeywords.php");

// log search
if ($_REQUEST['command'] == 'SEARCH') {
    
    //  logSearch($_SESSION['username'], $_REQUEST['search'], $_REQUEST['keyword']);
};

// initialize variables
$new_days = 2500;
$limit1 = 25;
$limit2 = 50;
$sortArray = array(1 => "DESC", 0 => "ASC");
$accountTypeArray = array(0 => "RETAIL ACCOUNT", 1 => "WHOLESALE LOGIN");

$module = isset($_REQUEST['module']) ? $_REQUEST['module'] : "viewitem.php";
$command = isset($_REQUEST['command']) ? $_REQUEST['command'] : "ALL";
$state = isset($_REQUEST['state']) ? $_REQUEST['state'] : "OPEN";
$lower = isset($_REQUEST['lower']) ? $_REQUEST['lower'] : 0;
$number = isset($_REQUEST['number']) ? $_REQUEST['number'] : 30;
$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : "released";
$desc = isset($_REQUEST['desc']) ? $_REQUEST['desc'] : 1;
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 1;
$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : "itemid";
$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : "";
$scope = isset($_REQUEST['scope']) ? $_REQUEST['scope'] : 30;
$itemid = isset($_REQUEST['itemid']) ? $_REQUEST['itemid'] : null;
$sales_orderid = isset($_REQUEST['sales_orderid']) ? $_REQUEST['sales_orderid'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

if ($lower == 0) {
    $page = 1;
} else {
    $page = floor($lower / $number) + 1;
};
$pages = 0;

// print the list if there is not editing

dbConnect();

// add item to request db if this is a restock request

if ($command == "request" AND isset($_SESSION['username'])) {
    dbConnect();
    
    $IP = getenv("REMOTE_ADDR");
    
    $sql_request = "INSERT INTO request (requestID, requestTime,requestUsername,requestIP,requestItem) VALUES (0 , NOW(),'" . $_SESSION['username'] . "','$IP','$itemid')";
    $result = mysql_query($sql_request) or die("Unable to add request.");
    echo "<i>A request to restock the record has been logged for *" . $_SESSION['username'] . "*.</i><p>";
};
?>

<body>
<div id="container">
  <div id="logo-top"><img src="../images/wreckedtop.gif" alt=""></div><div id="logo-middle-left"><img src="../images/wreckedbottomleft.gif" alt=""></div>
  
  <div id="logo-middle-right">
HOME
<a href="?module=viewnews.php" >News</a> |
<a href="?module=contact.php" >Contact</a> |

<a href="?module=events.php" >Events</a> |
<a href="?module=forums.php&amp;forum=1" >Forum</a> |
<a href="?module=links.php" >Links</a> |
<a href="?module=info.php" >About</a> |
<a href="?module=help.php">Help!</a>
<br>
BROWSE
<a href="?module=viewitem.php&amp;command=NEW" >New Stock</a> |
<a href="?module=viewitem.php&amp;command=ALL" >All Items</a> |
<a href="?module=viewitem.php&amp;command=KEYWORDS" >Keywords</a> |
<a href="?module=viewitem.php&amp;command=FORMAT&amp;search=format&amp;keyword=12" >Format</a> |
<a href="?module=viewitem.php&amp;command=ARTIST&amp;search=artist&amp;keyword=A" >Artist</a> |
<a href="?module=viewitem.php&amp;command=LABEL&amp;search=label&amp;keyword=A" >Label</a> |
<a href="?module=viewitem.php&amp;command=SALE" >Sale</a>

<br>
SPECIAL
<a href="?module=viewitem.php&amp;command=RANDOM">Random</a> |
<a href="?module=viewpic.php">Grid</a> |
<a href="?module=viewitem.php&amp;command=USED">User</a> |
<a href="?module=viewTopSellers.php">Popular</a> |
<a href="?module=viewimage.php">Images</a> |
<a href="?module=viewaudio.php">Audio</a>
<?php

// changes menu when logged in

if ($_SESSION["username"]) {
    echo "<br>";
    echo "ORDER ";
    echo "<font color=\"ff0000\">" . $_SESSION["username"] . "</font>";
    echo " <a href=\"?module=login.php&amp;command=CHECK\">CHECK CART</a>.";
    echo "<a href=\"?module=login.php&amp;command=ORDERS\">ORDERS</a>.";
    echo "<a href=\"?module=login.php&amp;command=OUT\">CHECK OUT</a>.";
    echo " <a href=\"?module=logout.php\">LOGOUT</a>.";
    echo "<br>";
} else {
    echo "<form name=\"login\" action='index.php' method='post' style='margin-bottom:1px;margin-top:1px;'>";
    echo "LOGIN ";
    echo "<b>Username</b>: <input name=\"username\" type=\"text\" size='12' class='form1'>";
    echo " <b>Password</b>: <input name=\"password\" type=\"password\" size='12' class='form1' pattern=\".{3,}\" title='Must contain a password of 3 or more characters.'>";
    echo " <input name=\"command\" type=\"hidden\" value=\"ALL\">";
    echo "<input name=\"module\" type=\"hidden\" value=\"login.php\">";
    echo "<input name=\"login\" type=\"submit\" value=\"Log in\" class=\"button1\">";
    echo "</form>";
};
?>

  <form action="index.php" method="post" style="margin:0;padding:0;">
  SEARCH CATALOG <input type="text" name="keyword" size="12" value="<?php echo $keyword; ?>" class="form1" >
  <? if (!$search) {$search="description";};?>
  <select name="search" class="form1">
  <option <? if ($search=="artist") {echo "selected";}; ?> value="artist" class="form1">artist
  <option <? if ($search=="title") {echo "selected";}; ?> value="title">title
  <option <? if ($search=="label") {echo "selected";}; ?> value="label">label
  <option <? if ($search=="description") {echo "selected";}; ?> value="description">description
  </select>
  <input type="hidden" name="module" value="viewitem.php">
  <input type="hidden" name="command" value="SEARCH">
  <input type="submit" name="submit" value="go" class="button1">
  </form>

  </div>

  <div id="content">
  <?php
 // logic to include correct module
include ($module);
?>
  </div>

</div>
</html>
