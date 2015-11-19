<?php

// viewitem.php
// displays all items in catalog with options for a short or long listing, number of items
// written by geoff maddock - 2004

// to do:
//      - convert html into seperate view with template files
//     	- convert to single page view, with each page calculated by url commands
//		  - url request vars
//			- $module - directs you to the appropriate page
//			- $command - the function to use in the page 

// initialize local vars

// $module specifies the page to load - later convert this into class?
// $command specifies the specific command to load from that page

// HANDLE REQUEST
if ($_REQUEST['module']) 	{ $module = $_REQUEST['module']; } else { $module = "viewitem.php";};
if ($_REQUEST['command']) 	{ $command = $_REQUEST['command']; } else { $command = "ALL";};
if ($_REQUEST['number']) 	{ $number = $_REQUEST['number']; } else { $number = 20;};
if ($_REQUEST['lower']) 	{ $lower = $_REQUEST['lower']; } else { $lower = 0;};

$pagename = $_SERVER['PHP_SELF'];
$alpha = "ABCDEFGHIJKLMNOPQRSTUVWXYZ#";
$sell_all_after = 365;

// returns item select statement for wholesale vs regular customers based on usertype session var
if (!$_SESSION["usertype"]) {
  $usertype = 0;
} else {
  $usertype = $_SESSION["usertype"];
};

// set sql statement based on command 
if ($command == "NEW") {

  $searchType = "NEW IN STOCK";

  $sql_string = "SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS date_format,  TO_DAYS(CURRENT_DATE)-TO_DAYS(released) AS days_old, TO_DAYS(CURRENT_DATE) AS current_day, TO_DAYS(restocked) AS restocked_days
  FROM items WHERE quantity > 0 AND `condition`='NEW' AND (TO_DAYS(CURRENT_DATE)-$new_days) < TO_DAYS(released) ORDER BY $sort  ".$sortArray[$desc];

  $sql_count = "SELECT COUNT(itemid) FROM items WHERE quantity > 0 AND `condition`='NEW' AND (TO_DAYS(CURRENT_DATE) - $new_days) < TO_DAYS(released) AND $search LIKE '%$keyword%' ";

} else if ($command == "SEARCH") {

  $searchType = "SEARCH :: '$keyword' in $search";

  $sql_string = "SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS date_format, TO_DAYS(restocked) AS
  restocked_days, TO_DAYS(CURRENT_DATE) AS current_day  FROM items WHERE $search LIKE '%$keyword%' AND
  TO_DAYS(CURRENT_DATE)>=TO_DAYS(restocked) ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower, $number";

  $sql_count = "SELECT COUNT(itemid) FROM items WHERE $search LIKE '%$keyword%' ";

  logSearch($username, $search, $keyword);
} else if ($command == "KEYWORDS") {

	if (!$keyword) {
   $keyword = "hardcore"; 
  };
	$searchType = "KEYWORD :: $keyword";
	$sql_string = "SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS date_format, TO_DAYS(CURRENT_DATE) AS current_day, TO_DAYS(restocked) AS restocked_days FROM items, keywords WHERE keywords.keyword = '$keyword' AND items.itemid = keywords.itemID AND TO_DAYS(CURRENT_DATE) >= TO_DAYS(restocked) ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower, $number";
  $sql_count = "SELECT COUNT(*) FROM items, keywords WHERE items.itemid = keywords.itemID AND keyword = '$keyword' ";

	$catlist = mysql_query("SELECT COUNT(keywordID) AS count, keyword FROM keywords GROUP BY keyword ORDER BY count DESC");

	if ($catrow = mysql_fetch_array($catlist)) {
    $menu .= "<div id='keywords'>"; 
		do {
       	$catname = $catrow["keyword"];
       	$menu .= "<a href=\"?module=$module&amp;command=$command&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;keyword=".urlencode($catrow["keyword"])."\"";
		    if ($category == $catrow["keyword"]) {
          $menu .= " class='select2' ";
        };
		    $menu .= ">".$catrow["keyword"]."</a> (".$catrow["count"].") | ";
       	} while ($catrow=mysql_fetch_array($catlist));
		    $menu .= "</div>";
     	};
    echo "<p>";
   } else if ($command == "RANDOM") {
	  $searchType = "RANDOM :: $keyword";
	  $sql_string = "SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS date_format, TO_DAYS(CURRENT_DATE) AS current_day, TO_DAYS(restocked) AS restocked_days  FROM items WHERE quantity>0 ORDER BY RAND() LIMIT 1";
    $sql_count = "SELECT COUNT(itemid) FROM items WHERE $search LIKE '%$keyword%' LIMIT 1 ";
   } else if ($command == "FORMAT") {	
    $searchType = "FORMAT :: $keyword";
    $search = "format";
    $sql_string = "SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS date_format, TO_DAYS(CURRENT_DATE) AS  current_day, TO_DAYS(restocked) AS restocked_days FROM items WHERE format = '$keyword'  AND 
    TO_DAYS(CURRENT_DATE) >= TO_DAYS(restocked) ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower,$number";
      	$sql_count = "SELECT COUNT(itemid) FROM items WHERE $search LIKE '%$keyword%' ";
    //echo $sql_count;
    $menu =  "<b>VINYL</b> >> ";
    	$menu .= "<a href=\"?module=$module&amp;command=$command&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode&amp;keyword=7\"";
    if ($keyword=="7") {$menu.= " class='select2' ";};
    $menu .= ">7\"</a> | ";
    	$menu .= "<a href=\"?module=$module&amp;command=$command&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode&amp;keyword=10\"";
    if ($keyword=="10") {$menu .= " class=select2 ";};
    $menu .= ">10\"</a> | ";
    $menu .= "<a href=\"?module=$module&amp;command=$command&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode&amp;keyword=12\"";
    if ($keyword=="12") {$menu .= " class=select2 ";};
    $menu .= ">12\"</a>| ";
    $menu .= "<a href=\"?module=$module&amp;command=$command&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode&amp;keyword=LP\"";
    if ($keyword=="LP") {$menu .= " class=select2 ";};
    $menu .= ">LP</a> |";
    	$menu .= "<a href=\"?module=$module&amp;command=$command&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode&amp;keyword=2LP\"";
    if ($keyword=="2LP") {$menu .= " class=select2 ";};
    $menu .= ">2LP</a> | ";
    	$menu .= "<a href=\"?module=$module&amp;command=$command&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode&amp;keyword=3LP\"";
    if ($keyword=="3LP") {$menu .= " class=select2 ";};
    $menu .= ">3LP</a> | ";
    $menu .= "<a href=\"?module=$module&amp;command=$command&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode&amp;keyword=4LP\"";
    if ($keyword=="4LP") {$menu .= " class=select2 ";};
    $menu .= ">4LP</a> | ";
    	$menu .= "<a href=\"?module=$module&amp;command=$command&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode&amp;keyword=boxset\"";
    if ($keyword=="boxset") {$menu .= " class=select2 ";};
    $menu .= ">Boxset</a> <br><b>CD</b> >> ";
    $menu .= "<a href=\"?module=$module&amp;command=$command&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode&amp;keyword=CD\"";
    if ($keyword=="CD") {$menu .= " class=select2 ";};
    $menu .= ">CD</a> | ";
    	$menu .= "<a href=\"?module=$module&amp;command=$command&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode&amp;keyword=2CD\"";
    if ($keyword=="2CD") {$menu .= " class=select2 ";};
    $menu .= ">2CD</a> | ";
    	$menu .= "<a href=\"?module=$module&amp;command=$command&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode&amp;keyword=DVD\"";
    if ($keyword=="DVD") {$menu .= " class=select2 ";};
    $menu .= ">DVD</a><br><b>OTHER</b> >> ";
    	$menu .= "<a href=\"?module=$module&amp;command=$command&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode&amp;keyword=tape\"";
    if ($keyword=="tape") {$menu .= " class=select2 ";};
    $menu .= ">Tape</a> | ";
    	$menu .= "<a href=\"?module=$module&amp;command=$command&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode&amp;keyword=t-shirt\"";
    if ($keyword=="t-shirt") {$menu .= " class=select2 ";};
    $menu .= ">T-Shirt</a> | ";
    	$menu .= "<a href=\"?module=$module&amp;command=$command&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode&amp;keyword=book\"";
    if ($keyword=="book") {$menu .= " class=select2 ";};
    $menu .= ">Book</a> | ";
    	$menu .= "<a href=\"?module=$module&amp;command=$command&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode&amp;keyword=zine\"";
    if ($keyword=="zine") {$menu .= " class=select2 ";};
    $menu .= ">Zine</a> | ";	
    $menu .= "<a href=\"?module=$module&amp;command=$command&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;keyword=CDzine\"";	
    if ($keyword=="CDzine") {$menu .= " class=select2 ";};
    $menu .= ">CD+Zine</a> ";

   } else if ($command == "ARTIST") {
    
    $searchType = "ARTIST :: $keyword";
    
    $sql_string = "SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS date_format, TO_DAYS(CURRENT_DATE) AS
    current_day, TO_DAYS(restocked) AS restocked_days FROM items WHERE upper($search) LIKE '$keyword%' AND 
    TO_DAYS(CURRENT_DATE) >= TO_DAYS(restocked) ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower,$number";
    
    $sql_count = "SELECT COUNT(itemid) FROM items WHERE $search LIKE '$keyword%' ";

  	for ($i=0;$i<strlen($alpha);$i++) {
  	 $menu .= "<a href=\"?module=$module&amp;command=$command&amp;search=$search&amp;keyword=".urlencode($alpha[$i])."&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\"";
  		if ($label == $alpha[$i]) { 
        $menu .= " class=select2 ";
      };
  		$menu .= ">".$alpha[$i]."</a> | ";
  	};
   } else if ($command == "LABEL") {
    
    $searchType = "LABEL :: $keyword";

    $sql_string = "SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS date_format, TO_DAYS(CURRENT_DATE) AS
    current_day, TO_DAYS(restocked) AS restocked_days FROM items WHERE upper($search) LIKE '$keyword%' AND 
    TO_DAYS(CURRENT_DATE) >= TO_DAYS(restocked) ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower,$number";
    
    $sql_count = "SELECT COUNT(itemid) FROM items WHERE $search LIKE '$keyword%' ";

    for ($i=0;$i<strlen($alpha);$i++)
    {
       	$menu .= "<a 
    href=\"?module=$module&amp;command=$command&amp;search=$search&amp;keyword=".$alpha[$i]."&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\"";
    if ($label == $alpha[$i]) { $menu .= " class=select2 ";};
    $menu .= ">".$alpha[$i]."</a> | ";
    };
   } else if ($command == "TOPSALES")
   {
    $searchType = "TOP ITEMS SOLD";
    $sql_string = "SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS date_format, TO_DAYS(CURRENT_DATE) AS
    current_day, TO_DAYS(restocked) AS restocked_days FROM items WHERE upper($search) LIKE '$keyword%' AND 
    TO_DAYS(CURRENT_DATE) >= TO_DAYS(restocked) ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower,$number";
    $sql_count = "SELECT COUNT(itemid) FROM items WHERE $search LIKE '$keyword%' ";

    for ($i=0;$i<strlen($alpha);$i++)
    {
     	$menu .= "<a 
    href=\"?module=$module&amp;command=$command&amp;search=$search&amp;keyword=".$alpha[$i]."&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\"";
    	if ($label == $alpha[$i]) { $menu .= " class=select2 ";};
    	$menu .= ">".$alpha[$i]."</a> | ";
    };
   } else if ($command == "USED")
   {
    $searchType = "USED";
    $sql_string = "SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS date_format, TO_DAYS(CURRENT_DATE) AS
    current_day, TO_DAYS(restocked) AS restocked_days FROM items WHERE upper($search) LIKE '$keyword%' AND `quantity` > 0 AND 
    `condition` != 'NEW' ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower,$number";
    $sql_count = "SELECT COUNT(itemid) FROM items WHERE $search LIKE '%$keyword%' AND `quantity` > 0 AND `condition` != 'NEW' ";

   } else if ($command == "SALE")
   {
    $searchType = "SALE ITEMS :: $keyword";
    $sql_string = "SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS date_format, TO_DAYS(CURRENT_DATE) AS
    current_day, TO_DAYS(restocked) AS restocked_days FROM items WHERE upper($search) LIKE '$keyword%' AND 
    (TO_DAYS(restocked)+$sell_all_after) < TO_DAYS(CURRENT_DATE) AND quantity > 0 ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower, $number";

    $sql_count = "SELECT COUNT(itemid) FROM items WHERE $search LIKE '$keyword%' AND (TO_DAYS(restocked)+$sell_all_after) < TO_DAYS(CURRENT_DATE) AND quantity > 0";

   } else if ($command == "COMING")
   {
    $searchType = "COMING SOON";
    $sql_string = "SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS date_format, TO_DAYS(CURRENT_DATE) AS
    current_day, TO_DAYS(restocked) AS restocked_days FROM items WHERE upper($search) LIKE '$keyword%' AND 
    TO_DAYS(restocked) > TO_DAYS(CURRENT_DATE) ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower,$number";
    $sql_count = "SELECT COUNT(itemid) FROM items WHERE $search LIKE '$keyword%' AND TO_DAYS(restocked) > TO_DAYS(CURRENT_DATE)";

   } else {
	  $searchType = "ALL ITEMS";
    //$number = 4000;

    $sql_string = "SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS date_format, TO_DAYS(restocked) AS restocked_days, TO_DAYS(CURRENT_DATE) AS current_day  FROM items WHERE $search LIKE '%$keyword%' AND TO_DAYS(CURRENT_DATE)>=TO_DAYS(restocked) AND quantity > 0 ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower, $number";
    $sql_count = "SELECT COUNT(itemid) FROM items WHERE $search LIKE '%$keyword%'";
   };

   // connect to database
   dbConnect();

   echo "<div id='item-container'>";

   // Header for specific page
   echo "<nav id='heading'>";
   if ($usertype) { 
    echo "<b>WHOLESALER LOGIN <i>".$username."</i></b><br>";
 }; 

	echo "<span id='search-header'>$searchType :: sorted by $sort</span> ";

	// toggle display of item descriptions
	// print the list if there is not editing
	$result = mysql_query($sql_count);
	$total = mysql_fetch_array($result);
	$count = $total[0];

	if ($command == "NEW") { $number = $count; };
	$page = $number ? floor($lower/$number) + 1 : 1;
	$pages = 0;

	if ($lower<0) {$lower = $count-$number;}
	if ($lower>$count) {$lower=0;}

	echo " | <a href='index.php?module=$module&amp;command=$command&amp;number=10&amp;lower=$lower&amp;desc=$desc&amp;sort=$sort&amp;keyword=$keyword&amp;search=$search'";
	if ($number==10) {echo " class=\"select2\"";}; 
	echo ">Limit 10 </a> | ";
	echo "<a href=\"?module=$module&amp;command=$command&amp;number=20&amp;lower=$lower&amp;desc=$desc&amp;sort=$sort&amp;keyword=$keyword&amp;search=$search\"";
	if ($number==20) {echo " class=\"select2\"";};
	echo ">Limit 20 </a> | ";
	echo "<a href=\"?module=$module&amp;command=$command&amp;number=$total[0]&amp;lower=0&amp;desc=$desc&amp;sort=$sort&amp;keyword=$keyword&amp;search=$search\"";
	if (($number != 10) AND ($number != 20)) {
	echo " class=\"select2\"";
	};
	echo ">Show&nbsp;All</a>";
	echo " | <a href=\"?module=$module&amp;command=$command&amp;number=$number&amp;lower=".($lower-$number)."&amp;desc=$desc&amp;sort=$sort&amp;keyword=$keyword&amp;search=$search\">PREV </a>";
	echo " | <a href=\"?module=$module&amp;command=$command&amp;number=$number&amp;lower=".($lower+$number)."&amp;desc=$desc&amp;sort=$sort&amp;keyword=$keyword&amp;search=$search\">NEXT </a>";
	echo " | Page $page";
	echo "</nav>";

	echo $menu;

    if (!$itemselect) {
      $result = mysql_query($sql_string);
    } else {
      $result = mysql_query("SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS date_format,TO_DAYS(restocked) AS restocked_days, TO_DAYS(CURRENT_DATE) AS current_day  FROM items WHERE itemid='$itemselect'");
    };

     if ($myrow = mysql_fetch_array($result))
     {
       echo "<table class='item-list'>\n";
       echo "<tr class='item-heading'>
             <th><a href=\"?module=$module&amp;command=$command&amp;keyword=$keyword&amp;search=$search&amp;sort=format&amp;lower=$lower&amp;number=$number&amp;desc=".!$desc."&amp;mode=$mode";
       echo "\">Format</a></th>
             <th><a href=\"?module=$module&amp;command=$command&amp;keyword=$keyword&amp;search=$search&amp;sort=artist&amp;lower=$lower&amp;number=$number&amp;desc=".!$desc."&amp;mode=$mode";
       echo "\">Artist</a></th> 
             <th><a href=\"?module=$module&amp;command=$command&amp;keyword=$keyword&amp;search=$search&amp;sort=title&amp;lower=$lower&amp;number=$number&amp;desc=".!$desc."&amp;mode=$mode";
       echo "\">Title</a></th>
             <th><a href=\"?module=$module&amp;command=$command&amp;keyword=$keyword&amp;search=$search&amp;sort=label&amp;lower=$lower&amp;number=$number&amp;desc=".!$desc."&amp;mode=$mode";
       echo "\">Label</a></th>
             <th><a href=\"?module=$module&amp;command=$command&amp;keyword=$keyword&amp;search=$search&amp;sort=condition&amp;lower=$lower&amp;number=$number&amp;desc=".!$desc."&amp;mode=$mode";
       echo "\">Cond</a></th>
             <th><a href=\"?module=$module&amp;command=$command&amp;keyword=$keyword&amp;search=$search&amp;sort=restocked&amp;lower=$lower&amp;number=$number&amp;desc=".!$desc."&amp;mode=$mode";
       echo "\">Stocked</a></th>
             <th><a href=\"?module=$module&amp;command=$command&amp;keyword=$keyword&amp;search=$search&amp;sort=quantity&amp;lower=$lower&amp;number=$number&amp;desc=".!$desc."&amp;mode=$mode";
       echo "\">#</a></th>
             <th><a href=\"?module=$module&amp;command=$command&amp;keyword=$keyword&amp;search=$search&amp;sort=retail&amp;lower=$lower&amp;number=$number&amp;desc=".!$desc."&amp;mode=$mode";
       echo "\">Price</a></th>  
             <th>&nbsp;</th>    
             </tr>\n";
       do
       {
      	// if wholesale, modify quantities
      	echo "<tbody class='item'>";
        printf("<tr class='item-title'>
	              <td>%s</td>
	              <td><a href=\"?module=$module&amp;command=ARTIST&amp;search=artist&amp;keyword=%s\">%s</a></td>
	              <td><a href='index.php?module=$module&amp;command=ALL&amp;search=title&amp;keyword=%s'>%s</a></td>
	              <td><a href='?module=$module&amp;command=LABEL&amp;search=label&amp;keyword=%s'>%s</a> %s</td> 
	              <td>%s</td>
	              <td>%s</td>
	              <td>%s</td> 
	              <td>$%s</td>", 
              $myrow["format"],urlencode($myrow["artist"]),$myrow["artist"],urlencode($myrow["title"]),$myrow["title"], 
              urlencode($myrow["label"]),urlencode($myrow["label"]), $myrow["catalog"],  $myrow["condition"], $myrow["date_format"], 
              calcQuantity($usertype,$myrow),calcPrice($myrow,getDiscount($usertype,$myrow)));

		if (calcQuantity($usertype,$myrow) > 0 AND $myrow["restocked_days"]<=$myrow["current_day"])
		{
			echo "<td>[<a title='Add this item to your shopping cart.' href=\"?module=additem.php&amp;command=ADD&amp;back=$module&amp;backcommand=$command&amp;itemid=".$myrow["itemid"]."&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode\">ADD</a>]</td>"; 
		} else 
		{
			echo "<td>OUT<br><a href=\"?module=$module&amp;command=request&amp;itemid=".$myrow["itemid"]."&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode\" style=\"font-size:6pt;\">RESTOCK ME</a></td>";
		};
		echo "</tr>";

		echo "<tr class='item-description'>";
		echo "<td colspan='8'>";

		$itemselect = $myrow["itemid"];

		$image = mysql_query("SELECT * FROM images WHERE itemid='$itemselect'");

		if ($imagerow = mysql_fetch_array($image))
		{
		  do {
		      echo "<img alt='' class='cover' src='".$imagerow["url"]."'>";
		   } while ($imagerow = mysql_fetch_array($image));
		} else
		{
		  parseimage($myrow["itemid"]);
		};

		echo "<div class='item-description'>";
		echo htmlentities($myrow["description"])."<br>";
		echo "</div>";

		//  shows keyword tags
		echo "<br>";
		echo "<b>Keywords:</b> ";
		listkeywords($myrow["itemid"]);
		echo "<br>";
		//    displays audio
		echo "<div class='player' id='player-".$myrow['itemid']."'>";

		echo parseaudio($myrow["itemid"],'audio');
		echo "</div>"; 
		echo "<p>";
		echo "</td>";
		echo "<td></td>";
		echo "</tr>";
		echo "</tbody>";

 } while ($myrow=mysql_fetch_array($result));

echo "</table>\n";

echo "<nav id='nav-footer'>";
echo "<table>
<tr><td>
<form action='".$_SERVER['PHP_SELF']."' method='post'>
<input type=\"hidden\" name=\"module\" value=\"".$module."\">
<input type=\"hidden\" name=\"command\" value=\"".$command."\">
<input type=\"hidden\" name=\"sort\" value=\"".$sort."\">
<input type=\"hidden\" name=\"mode\" value=\"".$mode."\">
<input type=\"submit\" name=\"show\" value=\"Show:\" class='button'>
<input type=\"text\" name=\"number\" value=\"".$number."\" size='5'>
rows beginning with number
<input type=\"text\" name=\"lower\" value=\"".$lower."\" size='10'>
in <select name=\"desc\">
<option value=\"".$sortArray[$desc]."\" SELECTED ";
echo ">".$sortArray[$desc];
echo "<option value=\"".$sortArray[!$desc]."\"";

echo " >".$sortArray[!$desc]."</select> order.
</form>
</td>

<td>
<form action='".$_SERVER['PHP_SELF']."' method='post'>
<input type=\"hidden\" name=\"number\" value=\"".$number."\">
<input type=\"hidden\" name=\"lower\" value=\"".($lower-$number)."\">
<input type=\"hidden\" name=\"desc\" value=\"".$desc."\">
<input type=\"hidden\" name=\"sort\" value=\"".$sort."\">
<input type=\"hidden\" name=\"search\" value=\"".$search."\">
<input type=\"hidden\" name=\"keyword\" value=\"".$keyword."\">
<input type=\"hidden\" name=\"module\" value=\"".$module."\">
<input type=\"hidden\" name=\"command\" value=\"".$command."\">
<input type=\"submit\" name=\"show\" value=\"< Previous ".$number."\" class='button'>
</form>
</td>

<td>
<form action='".$_SERVER['PHP_SELF']."' method='post'>
<input type=\"hidden\" name=\"number\" value=\"".$number."\">
<input type=\"hidden\" name=\"lower\" value=\"".($lower+$number)."\">
<input type=\"hidden\" name=\"desc\" value=\"".$desc."\">
<input type=\"hidden\" name=\"sort\" value=\"".$sort."\">
<input type=\"hidden\" name=\"search\" value=\"".$search."\">
<input type=\"hidden\" name=\"keyword\" value=\"".$keyword."\">
<input type=\"hidden\" name=\"module\" value=\"".$module."\">
<input type=\"hidden\" name=\"command\" value=\"".$command."\">
<input type=\"submit\" name=\"show\" value=\" Next ".$number." >\" class=\"button\">
</form>
</td>

<td>
<form action='".$_SERVER['PHP_SELF']."' method='post'>
<input type=\"hidden\" name=\"number\" value=\"".$total[0]."\" >
<input type=\"hidden\" name=\"lower\" value=\"0\">
<input type=\"hidden\" name=\"desc\" value=\"".$desc."\">
<input type=\"hidden\" name=\"sort\" value=\"".$sort."\">
<input type=\"hidden\" name=\"module\" value=\"".$module."\">
<input type=\"hidden\" name=\"search\" value=\"".$search."\">
<input type=\"hidden\" name=\"keyword\" value=\"".$keyword."\">
<input type=\"hidden\" name=\"command\" value=\"".$command."\">
<input type=\"submit\" name=\"show\" value=\"Show All ".$total[0]."\" class='button'>
</form>
</td>
</tr>

</table>";

} else {

echo "No results in the past ".$new_days." days.";
};

?>     

</nav>
</div>     

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