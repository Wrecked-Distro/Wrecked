<?php 
// viewaudio.php
// displayes only items that have audio files associated
// written by geoff maddock / cutups@rhinoplex.org
// update july 18th 2005
// update nov 30 2006 


// important functions in salesincludes
// function getDiscount($usertype, $arraycontainingitem) returns $discountID
// function calcQuantity($usertype, $arraycontainingitem) returns $displayquantity
// function calcPrice($arraycontainingitem, $discoundID) returns $currentprice

include_once("saleincludes.php");
include_once("parseaudio.php");
include_once("parseimage.php");


// connect to database
dbConnect();

// if item restock is requested, add to the database

// HANDLE REQUEST
if ($_REQUEST['module']) 	{ $module = $_REQUEST['module']; } else { $module = "viewaudio.php";};
if ($_REQUEST['command']) 	{ $command = $_REQUEST['command']; } else { $command = "ALL";};
if ($_REQUEST['number']) 	{ $number = $_REQUEST['number']; } else { $number = 20;};
if ($_REQUEST['lower']) 	{ $lower = $_REQUEST['lower']; } else { $lower = 0;};

if ($command == "request")
{
  dbConnect();
  $IP = getenv("REMOTE_ADDR");

  $sql_request = "INSERT INTO request (requestID, requestTime,requestUsername,requestIP,requestItem) VALUES (0 , NOW(),'$username','$IP','$itemid')";

  $result = mysql_query($sql_request) or die("Unable to add request.");
  echo "<i>A request to restock the record has been logged $usernameRequest.</i><p>";
};


// Header for specific page

if (!$_SESSION["usertype"]) {$usertype = 0;} else {$usertype = $_SESSION["usertype"];};

if ($usertype) {echo "<b>WHOLESALER LOGIN <i>".$username."</i></b><br>";};

echo "<span id='search-header'>ITEMS WITH AUDIO :: sorted by $sort</span> ";

// toggle display of item descriptions
echo "<a onclick=\"showAll()\" href=\"#\">DETAILS</a>";

// print the list if there is not editing
$result = mysql_query("SELECT COUNT(DISTINCT itemid) FROM tracks");
$total = mysql_fetch_array($result);

$page = floor($lower/$number)+1;
$pages = 0;

if ($lower<0) $lower=$total[0];
if ($lower>$total[0]) $lower=0;

echo " | <a href=\"?module=$module&amp;command=$command&amp;number=10&amp;lower=$lower&amp;desc=$desc&amp;sort=$sort&amp;mode=$mode\"";
if ($number==10) { echo " class=\"select2\""; };
echo ">Limit 10 </a> | ";
echo "<a href=\"?module=$module&amp;command=$command&amp;number=20&amp;lower=$lower&amp;desc=$desc&amp;sort=$sort&amp;mode=$mode\"";
if ($number==20) { echo " class=\"select2\""; };
echo ">Limit 20 </a> | ";
echo "<a href=\"?module=$module&amp;command=$command&amp;number=$total[0]&amp;lower=0&amp;desc=$desc&amp;sort=$sort&amp;mode=$mode\"";
if (($number!=10) AND ($number!=20)) {echo " class=\"select2\"";};
echo ">Show All</a>";
echo " | <a href=\"?module=$module&amp;command=$command&amp;number=$number&amp;lower=".($lower-$number)."&amp;desc=$desc&amp;sort=$sort&amp;mode=$mode\">PREV 
</a>"; echo " | <a href=\"?module=$module&amp;command=$command&amp;number=$number&amp;lower=".($lower+$number)."&amp;desc=$desc&amp;sort=$sort&amp;mode=$mode\">NEXT 
</a>"; echo " | Page $page";

echo "<br>";

// select all items from the database that have been in stock
 
if (!$itemselect)  {
  $sql_string ="SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS date_format,  
  TO_DAYS(restocked) AS restocked_days, TO_DAYS(CURRENT_DATE) AS current_day FROM items WHERE 
  TO_DAYS(CURRENT_DATE)>=TO_DAYS(restocked) ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower, 1000000";
} else {
  $sql_string = "SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS 
  date_format,TO_DAYS(CURRENT_DATE)-TO_DAYS(released) AS days_old, TO_DAYS(CURRENT_DATE) AS current_day, TO_DAYS(restocked) AS 
  restocked_days  FROM items WHERE itemid='$itemselect' LIMIT $lower, $number";
};


$result = mysql_query($sql_string);

// pull all items that have audio folders into the new array

if ($myrow = mysql_fetch_array($result))
{
  $index = 0;
  do 
  {
    if (hasAudio($myrow["itemid"]))
    {
      $newarray[$index] = $myrow;
      $index++;
    };
  } while ($myrow = mysql_fetch_array($result) AND ($index<$number));
};

echo "<table class='item-list'>\n";
echo "<tr class='item-heading'>";
echo "<td><a href=\"?module=$module&amp;command=$command&amp;sort=category&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode\">Category</a></td>";
echo "<td><a href=\"?module=$module&amp;command=$command&amp;sort=format&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode\">Format</a></td>";
echo "<td><a href=\"?module=$module&amp;command=$command&amp;sort=artist&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode\">Artist</a></td>";
echo "<td><a href=\"?module=$module&amp;command=$command&amp;sort=title&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode\">Title</a></td>";
echo "<td><a href=\"?module=$module&amp;command=$command&amp;sort=label&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode\">Label</a></td>";
echo "<td><a href=\"?module=$module&amp;command=$command&amp;sort=condition&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode\">Cond</a></td>";
echo "<td><a href=\"?module=$module&amp;command=$command&amp;sort=retail&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode\">Price</a></td>";
echo "<td>&nbsp;</td>";
echo "</tr>\n";

foreach ($newarray as $newrow) {
  // create a list of audio links
  printf("<tr class=\"item-title\" id='%s'> 
  <td>%s</td>
  <td>%s</td>
  <td><a href=\"?module=viewitem.php&amp;command=ARTIST&amp;keyword=%s&amp;search=artist\">%s</a></td>
  <td><a href=\"?module=viewitem.php&amp;command=ALL&amp;search=title&amp;keyword=%s\">%s</a></td>
  <td><a href=\"?module=viewitem.php&amp;command=LABEL&amp;keyword=%s&amp;search=label\">%s</a> %s</td>
  <td>%s</td> 
  <td>$%s</td>", $newrow["itemid"], $newrow["category"], $newrow["format"], urlencode($newrow["artist"]),  htmlentities($newrow["artist"]),  urlencode($newrow["title"]), htmlentities($newrow["title"]), 
  urlencode($newrow["label"]),  htmlentities($newrow["label"]), $newrow["catalog"],  $newrow["condition"], calcPrice($newrow, getDiscount($usertype,$newrow)));

  if (calcQuantity($usertype,$newrow) > 0 AND $newrow["restocked_days"] <= $newrow["current_day"]) {
  	echo "<td>[<a href=\"?module=additem.php&amp;command=ADD&amp;back=$module&amp;itemid=".$newrow["itemid"]."&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode\">ADD</a>]</td>"; 
  } else {
  	echo "<td>OUT<br><a href=\"viewitem.php?command=request&amp;itemid=".$newrow["itemid"]."&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode\" style=\"font-size:6pt;\">RESTOCK ME</a></td>";
  };
  echo "</tr>";
  echo "<tr>";
  echo "<td colspan='8'>";
  echo "<div class='player' id='player-".$newrow['itemid']."'>";
  echo parseaudio($newrow["itemid"],'audio');
  echo "</div>"; 
  echo "</tr>";
};
echo "</table>\n";
echo "<table>
<tr><td>
<form action='".$_SERVER['PHP_SELF']."' method='post'>
<input type='hidden' name='module' value='".$module."'>
<input type=\"hidden\" name=\"sort\" value=\"".$sort."\">
<input type=\"hidden\" name=\"mode\" value=\"".$mode."\">
<input type='submit' name='show' value='Show:' class='button1'>
<input type=\"text\" name=\"number\"  value=\"".$number."\" class='form1'>
rows beginning with number
<input type=\"text\" name=\"lower\"  value=\"".$lower."\" class='form1'>
in <select name=\"desc\" >
<option value=\"&amp;nbsp;\" ";

if ($desc!="DESC") echo " SELECTED ";

echo " class=form1>ASCENDING <option value=\"DESC\"";

if ($desc=="DESC") echo " SELECTED ";

echo " >DESCENDING </select>
order.
</form>
</td>
<td>


<form action='".$_SERVER['PHP_SELF']."' method='post'>
<input type='hidden' name='module' value='".$module."'>
<input type=\"hidden\" name=\"number\" value=\"".$number."\">
<input type=\"hidden\" name=\"lower\"  value=\"".($lower-$number)."\">
<input type=\"hidden\" name=\"desc\" value=\"".$desc."\">
<input type=\"hidden\" name=\"sort\" value=\"".$sort."\">
<input type=\"hidden\" name=\"mode\" value=\"".$mode."\">
<input type=\"submit\" name=\"show\" value=\"< Previous ".$number."\" class='button1'>

</form>
</td>
<td>
<form action='".$_SERVER['PHP_SELF']."' method='post'>
<input type='hidden' name='module' value='".$module."'>
<input type=\"hidden\" name=\"number\"  value=\"".$number."\">
<input type=\"hidden\" name=\"lower\"  value=\"".($lower+$number)."\">
<input type=\"hidden\" name=\"desc\" value=\"".$desc."\">
<input type=\"hidden\" name=\"sort\" value=\"".$sort."\">
<input type=\"hidden\" name=\"mode\" value=\"".$mode."\">
<input type=\"submit\" name=\"show\" value=\" Next ".$number." >\" class='button1'>
</form>
</td>

<td>
<form action='".$_SERVER['PHP_SELF']."' method='post'>
<input type='hidden' name='module' value='".$module."'>
<input type=\"hidden\" name=\"number\"  value=\"".$total[0]."\" >
<input type=\"hidden\" name=\"lower\"  value=\"0\">
<input type=\"hidden\" name=\"desc\" value=\"".$desc."\">
<input type=\"hidden\" name=\"sort\" value=\"".$sort."\">
<input type=\"hidden\" name=\"mode\" value=\"".$mode."\">
<input type=\"submit\" name=\"show\" value=\"Show All ".$total[0]."\" class='button1'>
</form>
</td>
</tr>
</table>";
?>
<script type="text/javascript">
$(function(){
    $('audio').mediaelementplayer({
        loop: true,
        shuffle: true,
        playlist: false,
        audioHeight: 30,
        audioWidth: 500,
        playlistposition: 'bottom',
        features: ['playlistfeature', 'prevtrack', 'playpause', 'nexttrack', 'loop', 'shuffle', 'playlist', 'current', 'progress', 'duration', 'volume'],
    });
});
</script>