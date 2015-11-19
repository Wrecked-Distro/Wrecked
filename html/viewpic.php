<?php
// viewpic.php
// new view to consolidate pic view and short view of items
// written by geoff maddock / geoff.maddock@gmail.com
// updated july 20th 2005

// important functions in salesincludes
// function getDiscount($usertype, $arraycontainingitem) returns $discountID
// function calcQuantity($usertype, $arraycontainingitem) returns $displayquantity
// function calcPrice($arraycontainingitem, $discoundID) returns $currentprice


// print a short item listing for a new frontpage
function show_item_compact($itemid)
{
  GLOBAL $usertype;   
  GLOBAL $command;
  GLOBAL $module;

  dbConnect();

  $sql = "SELECT *, TO_DAYS(CURRENT_DATE) AS current_day, TO_DAYS(restocked) AS restocked_days FROM items WHERE itemid = $itemid";

  $result = mysql_query($sql);
  $myrow = mysql_fetch_array($result);

  $imagesql = "SELECT * FROM images WHERE itemid = $itemid";
  $imageresult = mysql_query($imagesql);
  $imagerow = mysql_fetch_array($imageresult);

  $flagurl = 1;

  if (!$imagerow["url"]) {
    $url = "images/noimage.gif"; $flagurl = 0;
  } else {
    $url = $imagerow["url"];
  };
  echo "<table class='item-grid' style='width:300px'>";
  echo "<tr class='release1'>";
  echo "<td>";

  if ($flagurl) {
    echo "<img src=\"".$url."\" alt='' style='width: 100px; height: 100px;'>";
  } else {
    if  (parseimage($myrow["itemid"])) {
      // if parsed, then it worked
   } else {
     echo "<img src=\"".$url."\" alt='' style='width: 100px; height: 100px;'>";
   };
  };

  echo "<b>".$myrow["artist"]."<br>\"".$myrow["title"]."\"<br>".$myrow["label"]." ".$myrow["catalog"]." ".$myrow["format"]."<br>";
  echo "<i>$".calcPrice($myrow,getDiscount($usertype,$myrow))."</i></b><br>";  
  $tracksql = "SELECT tracknumber, url FROM tracks WHERE itemid=$itemid";
  $trackresult = mysql_query($tracksql);

  if ($tracklist=mysql_fetch_array($trackresult))
  {
    echo "<b>Listen:</b> ";
    do
    {
     echo "[<a href=\"rammaker.php?url=".$tracklist["url"]."\">".$tracklist["tracknumber"]."</a>] ";
    } while ($tracklist = mysql_fetch_array($trackresult));
  } else {

    		//    displays audio
		echo "<div class='player' id='player-".$myrow['itemid']."'>";

		echo parseaudio($myrow["itemid"],'audio');
		echo "</div>"; 
  };
  echo "<p>";
  if (calcQuantity($usertype,$myrow)>0) {
    echo "(<a href=\"?module=additem.php&amp;command=ADD&amp;back=$module&amp;backcommand=$command&amp;itemid=".$myrow["itemid"]."&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode\">ADD TO CART</a>)";
  } else {
    echo "OUT OF STOCK";
  };
  echo "</td></tr>";
  echo "</table>"; 
};

 // print a short item listing for a new frontpage
 function show_item_short($itemid)
 {
   dbConnect();
   $sql = "SELECT * FROM items WHERE itemid=$itemid";
   $result = mysql_query($sql);
   $myrow = mysql_fetch_array($result);


   $imagesql = "SELECT * FROM images WHERE itemid=$itemid";
   $imageresult = mysql_query($imagesql);
   $imagerow = mysql_fetch_array($imageresult);

   $url = $imagerow["url"];
   echo "<table class='item-grid' style='width:500px; height: 100%;'>";

   echo "<tr class=release1>";
   echo "<td>".$myrow["artist"]."</td><td>".$myrow["title"]."</td><td>".$myrow["label"]." ".$myrow["catalog"]."</td>";
   echo "<td><b>".$myrow["format"]."</b></td>";
   echo "<td><b>$".$myrow["retail"]."</b></td>";  
   if ($myrow["quantity"] > 0) {
    echo "<td>(<a href=\"?module=additem.php&amp;command=ADD&amp;back=$module&amp;command=$command&amp;itemid=".$myrow["itemid"]."&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode\">ADD</a>)</td>";
   } else {
    echo "<td>OUT</td>";
   };

   echo "</tr>";
   echo "<tr class=release2>";
   echo "<td colspan=6> <img src=\"".$url."\" align='left' >";
   echo $myrow["description"]."<br>";

   $tracksql = "SELECT tracknumber, url FROM tracks WHERE itemid=$itemid";
   $trackresult = mysql_query($tracksql);

   if ($tracklist = mysql_fetch_array($trackresult)) {
    echo "<b>Listen:</b> ";
    do
    {
      echo "[<a href=\"rammaker.php?url=".$tracklist["url"]."\">".$tracklist["tracknumber"]."</a>] ";
    } while ($tracklist=mysql_fetch_array($trackresult));
   } else {
    parseaudio($itemid);
   };

   echo "</td>";
   echo "<tr>";
   echo "</table>"; 
};


function show_new_columns($sort,$desc,$lower,$number)
{
  GLOBAL $sortArray;
  dbConnect();

  $result = mysql_query("SELECT * FROM items WHERE quantity > 0 ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower,$number");

  if ($myrow = mysql_fetch_array($result)) {
    echo "<table>";
    do {

	   echo "<tr><td>";
  	   show_item_compact($myrow["itemid"]);
	   echo "</td>";
     echo "<td>";
       $myrow = mysql_fetch_array($result);
       show_item_compact($myrow["itemid"]);
     echo "</td>";
     echo "<td>";

       $myrow = mysql_fetch_array($result);
  	   show_item_compact($myrow["itemid"]);
	   echo "</td>";
     echo "</tr>";      
    } while ($myrow = mysql_fetch_array($result)); 
   echo "</table>";
  };
};

// initialize variables

// $sortArray = array(0=>"DESC",1=>"ASC");
$accountTypeArray = array(0=>"RETAIL ACCOUNT",1=>"WHOLESALE LOGIN");

// connect to database
dbConnect();

// Header for specific page
if ($usertype) {
  echo "<b>".$accountTypeArray[$usertype]." <i>".$username."</i></b><br>";
};

echo "<span id='item-heading'>ALL ITEMS [PIC VIEW] :: sorted by $sort</span> ";

// print the list if there is not editing
$result = mysql_query("SELECT COUNT(itemid) FROM items");
$total  = mysql_fetch_array($result);

$page   = floor($lower / $number) + 1;
$pages  = 0;
$count  = $total[0];

if ($_REQUEST['module'])  { $module = $_REQUEST['module']; } else { $module = "viewpic.php";};
if ($_REQUEST['command'])   { $command = $_REQUEST['command']; } else { $command = "ALL";};
if ($_REQUEST['number'])  { $number = $_REQUEST['number']; } else { $number = 15;};
if ($_REQUEST['lower'])   { $command = $_REQUEST['lower']; } else { $lower = 0;};

if ($lower < 0) $lower=$total[0];
if ($lower > $total[0]) $lower=0;
	
$pagetotal = floor($count / $number)+1;

echo " | <a href=\"?module=$module&amp;command=ALL&amp;number=15&amp;lower=$lower&amp;desc=$desc&amp;sort=$sort&amp;mode=$mode\"";
if ($number==15) {echo " class=\"select2\"";};
echo ">Limit 15 </a> | ";
echo "<a href=\"?module=$module&amp;command=ALL&amp;number=30&amp;lower=$lower&amp;desc=$desc&amp;sort=$sort&amp;mode=$mode\"";
if ($number==30) {echo " class=\"select2\"";};
echo ">Limit 30 </a> | ";
echo "<a href=\"?module=$module&amp;command=ALL&amp;number=$total[0]&amp;lower=0&amp;desc=$desc&amp;sort=$sort&amp;mode=$mode\"";
if (($number!=15) AND ($number!=30)) {echo " class=\"select2\"";};
echo ">Show All</a>";
echo " | <a href=\"?module=$module&amp;command=ALL&amp;number=$number&amp;lower=".($lower-$number)."&amp;desc=$desc&amp;sort=$sort\">PREV </a>";
echo " | <a href=\"?module=$module&amp;command=ALL&amp;umber&amp;lower=".($lower+$number)."&amp;desc=$desc&amp;sort=$sort\">NEXT </a>";
echo " | Page $page of $pagetotal";

echo "<br>";

show_new_columns($sort,$desc,$lower,$number);

echo "<table>
<tr><td>
<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">
<input type=\"hidden\" name=\"sort\" value=\"".$sort."\">
<input type=\"hidden\" name=\"module\" value=\"".$module."\">
<input type=\"hidden\" name=\"command\" value=\"".$command."\">
<input type=\"submit\" name=\"show\" value=\"Show&amp;nbsp;:\" class='button'>
<input type=\"text\" name=\"number\" size=\"3\" value=\"".$number."\">
rows beginning with number
<input type=\"text\" name=\"lower\" size=\"3\" value=\"".$lower."\" >
in <select name=\"desc\" class=form1>
<option value=\"&amp;nbsp;\" ";

if ($desc != 0) echo " SELECTED ";

echo " class=form1>ASCENDING <option value=\"DESC\"";

if ($desc ==0) echo " SELECTED ";

echo " >DESCENDING </select>
order.
</form>
</td>
<td>

<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">
<input type=\"hidden\" name=\"number\" value=\"".$number."\">
<input type=\"hidden\" name=\"lower\" value=\"".($lower-$number)."\">
<input type=\"hidden\" name=\"desc\" value=\"".$desc."\">
<input type=\"hidden\" name=\"sort\" value=\"".$sort."\">
<input type=\"hidden\" name=\"mode\" value=\"".$mode."\">
<input type=\"hidden\" name=\"module\" value=\"".$module."\">
<input type=\"hidden\" name=\"command\" value=\"".$command."\">
<input type=\"submit\" name=\"show\" value=\"< Previous ".$number."\" class='button'>

</form>
</td>
<td>
<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">
<input type=\"hidden\" name=\"number\" value=\"".$number."\">
<input type=\"hidden\" name=\"lower\"  value=\"".($lower+$number)."\">
<input type=\"hidden\" name=\"desc\" value=\"".$desc."\">
<input type=\"hidden\" name=\"sort\" value=\"".$sort."\">
<input type=\"hidden\" name=\"module\" value=\"".$module."\">
<input type=\"hidden\" name=\"command\" value=\"".$command."\">
<input type=\"submit\" name=\"show\" value=\" Next ".$number." >\" class='button'>
</form>
</td>

<td>
<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">>
<input type=\"hidden\" name=\"number\"  value=\"".$total[0]."\" >
<input type=\"hidden\" name=\"lower\"  value=\"0\">
<input type=\"hidden\" name=\"desc\" value=\"".$desc."\">
<input type=\"hidden\" name=\"sort\" value=\"".$sort."\">
<input type=\"hidden\" name=\"module\" value=\"".$module."\">
<input type=\"hidden\" name=\"command\" value=\"".$command."\">
<input type=\"submit\" name=\"show\" value=\"Show All ".$total[0]."\" class='button'>
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
        audioWidth: 280,
        playlistposition: 'bottom',
        features: ['playlistfeature', 'prevtrack', 'playpause', 'nexttrack', 'loop', 'shuffle', 'playlist', 'current', 'progress', 'duration', 'volume'],
    });
});
</script>