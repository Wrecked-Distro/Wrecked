<?php
// adminitemsoldgenre.php
// tool to display a list of items sold and in stock for each genre
// written by geoff maddock / cutups@rhinoplex.org
// updated july 21, 2005

// returns the number of items sold given a genre

function items_sold($category)
{
	$sql = "SELECT COUNT(sales_itemid) AS total, SUM(retail) AS sumtotal, SUM(sales_items.quantity) AS 
	sumquantity FROM items, sales_items, keywords WHERE keywords.keyword LIKE '%$category%' AND 
	items.itemid=sales_items.itemid AND items.itemid = keywords.itemid ";

	$result = mysql_fetch_array(mysql_query($sql));
	$total = $result["sumquantity"];
	$sumtotal = $result["sumtotal"];

	$sql = "SELECT COUNT(items.itemid) AS itemtotal, SUM(retail) AS sumitemtotal, SUM(quantity) AS 
	sumitemquantity FROM items, keywords WHERE keywords.keyword LIKE '%$category%' AND items.itemid = keywords.itemid"; 

	$result=mysql_fetch_array(mysql_query($sql));
	$itemtotal=$result["sumitemquantity"];
	$sumitemtotal=$result["sumitemtotal"];

	echo "<b>".$category."</b> (".$total." sold /  ".$itemtotal." in stock) <i>$".$sumtotal." sold value</i>"; 

	return $total;
}; 


function top_items_genre($category, $limit, $total)
{
	$desc = "DESC";
	$lower = 0;
	$number = 10;
	$totalitems = 0;

	$result = mysql_query("SELECT DATE_FORMAT(order_date,'%m/%d/%y') AS order_date_format, sales_items.itemid AS 
	itemid, sales_items.quantity AS quantity FROM sales_items, items, sales_orders, keywords  WHERE keywords.keyword LIKE 
	'$category' AND sales_items.itemid=items.itemid AND  sales_orders.sales_orderid=sales_items.sales_orderid AND items.itemid = 
	keywords.itemid ORDER BY order_date DESC LIMIT $limit");

     if ($myrow = mysql_fetch_array($result))
     {
       echo "<table>\n";

       echo "<tr class=\"title1\"><td colspan=7><b>Items</b></td>";
       if ($desc=="DESC") {
        echo "<td> <a href=\"$PHP_SELF?sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=&amp;nbsp;\">ASC</A>";
       } else { 
       	echo "<td> <a href=\"$PHP_SELF?sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=DESC\">DESC</a>";
       };

       echo "<td> <a href=\"$PHP_SELF?sort=$sort&amp;lower=0&amp;number=$count[0]$switch\"> Show All</a></td></tr>\n";
       echo "<tr class=\"title2\"> <td><a href=\"$phpself?module=$module&amp;sort=category&amp;lower=$lower&amp;number=$number&amp;desc=$desc";
       echo "\">Category</a></td> <td><a href=\"$phpself?module=$module&amp;sort=format&amp;lower=$lower&amp;number=$number&amp;desc=$desc";
       echo "\">Format</a></td> <td><a href=\"$phpself?module=$module&amp;sort=artist&amp;lower=$lower&amp;number=$number&amp;desc=$desc";
       echo "\">Artist</a></td> <td><a href=\"$phpself?module=$module&amp;sort=title&amp;lower=$lower&amp;number=$number&amp;desc=$desc";
       echo "\">Title</a></td>  <td><a href=\"$phpself?module=$module&amp;sort=label&amp;lower=$lower&amp;number=$number&amp;desc=$desc";
       echo "\">Label</a></td> <td><a href=\"$phpself?module=$module&amp;sort=condition&amp;lower=$lower&amp;number=$number&amp;desc=$desc";
       echo "\">Cond</a></td> <td><a href=\"$phpself?module=$module&amp;sort=restocked&amp;lower=$lower&amp;number=$number&amp;desc=$desc";
       echo "\">Stocked</a></td> <td><a href=\"$phpself?module=$module&amp;sort=quantity&amp;lower=$lower&amp;number=$number&amp;desc=$desc";
       echo "\">Sold</a></td> <td><a href=\"$phpself?module=$module&amp;sort=retail&amp;lower=$lower&amp;number=$number&amp;desc=$desc";
       echo "\">Cost</a></td> </tr>\n";

		// add an option so it will only count items sold in the past $scope days

       do
       {
		$currentitem=$myrow["itemid"];
		$sql2 = mysql_query("SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS restocked FROM items WHERE itemid='$currentitem'");
		$row = mysql_fetch_array($sql2);
		$sold = $myrow["quantity"];
		$totalitems = $totalitems + $sold;
		$totalsold = $totalsold + ($row["retail"]*$sold);

		printf("<tr> <td>%s</td><td>%s</td> <td>%s</td> <td><a href=\"%s?itemselect=%s\">%s</a>
		<td>%s %s</td> <td>%s</td>  <td>%s</td> <td>%s</td> <td>%s</td>",
		$row["category"], $row["format"], htmlentities($row["artist"]), $PHP_SELF, $row["itemid"], htmlentities($row["title"]),
		$row["label"], $row["catalog"],  $row["condition"], $row["restocked"], $myrow["order_date_format"],
		$row["retail"]);

       } while ($myrow=mysql_fetch_array($result));

		echo "<tr><td colspan=7></td><td>".$totalitems."</td><td>$".$totalsold."</td></tr>";
       	echo "</table>\n";
      };

};

?>

<b>SOLD ITEMS BY GENRE</b>

<p>

<?php
$grand = 0;

dbConnect();

$result = mysql_query("SELECT COUNT(keywordID) AS count, keyword FROM keywords GROUP BY keyword ORDER BY count DESC");

if ($myrow = mysql_fetch_array($result))
{
 do
 {
  $total = items_sold($myrow["keyword"]);
  $grand = $grand + $total;
  echo "<br>";
  top_items_genre($myrow["keyword"],20,$total);
 } while ($myrow=mysql_fetch_array($result));
};

echo "<p><b>Total Items Sold</b> ".$grand;
?>
