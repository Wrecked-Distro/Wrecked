<?php 

function getPendingSales($itemid)
{
	dbConnect();
	$sql = "SELECT * FROM sales_items, sales_orders WHERE sales_orders.sales_orderid=sales_items.sales_orderid AND sent=0 AND sales_items.itemid = '$itemid'";
	$result = mysql_query($sql);
	$temp = "";

	if ($myrow=mysql_fetch_array($result))
	{
		do
		{
			$temp=$temp.$myrow["sales_orderid"]." ";
		} while ($myrow=mysql_fetch_array($result));

	};

	return $temp;
}; 

?>

<!-- STARTS HERE -->

<b>ITEM viewer</b>

<p>

<?php
$sortArray = array(1=>"DESC",0=>"ASC");
$accountTypeArray = array(0=>"RETAIL ACCOUNT", 1=>"WHOLESALE LOGIN");

// get some request values
$module = isset($_REQUEST['module']) ? $_REQUEST['module'] : "adminitempending.php";
$command = isset($_REQUEST['command']) ? $_REQUEST['command'] : "ALL";
$state = isset($_REQUEST['state']) ? $_REQUEST['state'] : "OPEN";
$lower = isset($_REQUEST['lower']) ? $_REQUEST['lower'] : 0;
$number = isset($_REQUEST['number']) ? $_REQUEST['number'] : 20;
$desc = isset($_REQUEST['desc']) ? $_REQUEST['desc'] : 1;
$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : "restocked";
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 1;
$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : "itemid";
$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : "";
$scope = isset($_REQUEST['scope']) ? $_REQUEST['scope'] : 2000;
$itemid = isset($_REQUEST['itemid']) ? $_REQUEST['itemid'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

$totalitems = 0;
$totalsold = 0;

echo "<p>";
echo "<b>CURRENT ITEMS THAT MUST BE PULLED :: sorted by $sort</b> ";

echo "<a href=\"$PHPSELF?module=$module&amp;scope=10000\">ALL</a> | 
<a href=\"$PHPSELF?module=$module&amp;scope=365\">365</a> | 
<a href=\"$PHPSELF?module=$module&amp;scope=180\">180</a> | 
<a href=\"$PHPSELF?module=$module&amp;scope=90\">90</a> |
<a href=\"$PHPSELF?module=$module&amp;scope=30\">30</a> |
<a href=\"$PHPSELF?module=$module&amp;scope=7\">7</a>";
echo "<br>";

// print the list if there is not editing

$result=mysql_query("SELECT COUNT(sales_itemid) FROM sales_items");
$count=mysql_fetch_array($result);

if ($lower<0) $lower=$count[0];
if ($lower>$count[0]) $lower=0;

$sql = sprintf('SELECT itemid, SUM(quantity) AS total FROM sales_items, sales_orders WHERE sales_items.sales_orderid = sales_orders.sales_orderid 
AND TO_DAYS(CURRENT_DATE)<TO_DAYS(sales_orders.order_date) + %s AND sales_orders.sent = 0 GROUP BY itemid ORDER BY total %s LIMIT %s, %s',
$scope, $sortArray[$desc], $lower, $number);

echo "<div id='query'>Query: ".htmlentities($sql)."</div>";

$result = mysql_query($sql); 

if ($myrow = mysql_fetch_array($result))
{
	echo "<table>\n";

	echo "<tr class=\"title1\"><td colspan=7><b>Items</b></td>";

	if ($desc == "DESC") {
	 echo "<td> <a href=\"$PHP_SELF?sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=&amp;nbsp;\">ASC</A>";
	} else  {
	 echo "<td> <a href=\"$PHP_SELF?sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=DESC\">DESC</a>";
	};

	echo "<td> <a href=\"$PHP_SELF?sort=$sort&amp;lower=0&amp;number=$count[0]$switch\"> Show All</a></td></tr>\n";
	echo "<tr class=\"title2\">
	     <td><a href=\"$phpself?sort=category&amp;lower=$lower&amp;number=$number&amp;desc=$desc";
	echo "\">Category</a></td>
	     <td><a href=\"$phpself?sort=format&amp;lower=$lower&amp;number=$number&amp;desc=$desc";
	echo "\">Format</a></td>
	     <td><a href=\"$phpself?sort=artist&amp;lower=$lower&amp;number=$number&amp;desc=$desc";
	echo "\">Artist</a></td> 
	     <td><a href=\"$phpself?sort=title&amp;lower=$lower&amp;number=$number&amp;desc=$desc";
	echo "\">Title</a></td>
	     <td><a href=\"$phpself?sort=label&amp;lower=$lower&amp;number=$number&amp;desc=$desc";
	echo "\">Label</a></td>
	     <td><a href=\"$phpself?sort=condition&amp;lower=$lower&amp;number=$number&amp;desc=$desc";
	echo "\">Cond</a></td>
	     <td><a href=\"$phpself?sort=restocked&amp;lower=$lower&amp;number=$number&amp;desc=$desc";
	echo "\">Stocked</a></td>
	     <td><a href=\"$phpself?sort=quantity&amp;lower=$lower&amp;number=$number&amp;desc=$desc";
	echo "\">Sold</a></td>
	     <td><a href=\"$phpself?sort=retail&amp;lower=$lower&amp;number=$number&amp;desc=$desc";
	echo "\">Cost</a></td>

	     </tr>\n";

// add an option so it will only count items sold in the past $scope days
      
   do
   {
      $currentitem=$myrow["itemid"];
      $sql2 = mysql_query("SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS restocked FROM items WHERE itemid='$currentitem'");
      $row=mysql_fetch_array($sql2);
      $sold= $myrow["total"];      
      $totalitems=$totalitems+$sold;
      $totalsold=$totalsold+($row["retail"]*$sold);

        printf("<tr> <td>%s</td><td>%s</td> <td>%s</td> <td><a href=\"%s?itemselect=%s\">%s</a>
<td>%s %s</td> <td>%s</td>  <td>%s</td> <td>%s (%s)</td> <td>%s</td>", 
$row["category"], $row["format"],$row["artist"], $PHP_SELF, $row["itemid"],$row["title"], 
$row["label"], $row["catalog"],  $row["condition"], $row["restocked"], $sold,getPendingSales($row["itemid"]), 
$row["retail"]);


    } while ($myrow=mysql_fetch_array($result));

echo "<tr><td colspan=7></td><td>".$totalitems."</td><td>$".$totalsold."</td></tr>";
       echo "</table>\n";
      };

?>

<table>
<tr><td>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="sort" value="<? echo $sort;?>">
<input type="submit" name="show" value="Show&amp;nbsp;:" class=button1>
<input type="text" name="number"  value="<? echo $number; ?>">
rows beginning with number
<input type="text" name="lower"  value="<? echo $lower; ?>">
in
<select name="desc">
<option value="&amp;nbsp;" <? if ($desc!="DESC") echo " SELECTED ";?> >ASCENDING
<option value="DESC" <? if ($desc=="DESC") echo " SELECTED ";?> >DESCENDING
</select>
order.
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="number"  value="<? echo $number; ?>">
<input type="hidden" name="lower"  value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="< Previous <? echo $number;?>" class=button1>
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="number"  value="<? echo $number; ?>">
<input type="hidden" name="lower"  value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Next <? echo $number;?> >" class=button1>
</form>
</td>
<td>
<form action="<? echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="number"  value="<? echo $count[0]; ?>">
<input type="hidden" name="lower"  value="0">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="hidden" name="scope" value="<? echo $scope; ?>">
<input type="submit" name="show" value="Show All <? echo $count[0];?>" class=button1>
</form>
</td>
</tr>
</table>