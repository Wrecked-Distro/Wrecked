<?php
// viewnewdate.php
// tool for viewing the most popular items given a timespam
// written by gmaddock / cutups@rhinoplex.org
// updated july 20th 2005


// important functions in salesincludes
// function getDiscount($usertype, $arraycontainingitem) returns $discountID
// function calcQuantity($usertype, $arraycontainingitem) returns $displayquantity
// function calcPrice($arraycontainingitem, $discoundID) returns $currentprice

$module            = "viewTopSellers.php";
$sortArray         = array(0=>"DESC",1=>"ASC");
$accountTypeArray  = array(0=>"RETAIL ACCOUNT",1=>"WHOLESALE LOGIN");
// HANDLE REQUEST
if ($_REQUEST['module']) 	{ $module = $_REQUEST['module']; } else { $module = "viewitem.php";};
if ($_REQUEST['command']) 	{ $command = $_REQUEST['command']; } else { $command = "ALL";};
if ($_REQUEST['number']) 	{ $number = $_REQUEST['number']; } else { $number = 100;};
if ($_REQUEST['lower']) 	{ $lower = $_REQUEST['lower']; } else { $lower = 0;};
if ($_REQUEST['scope']) 	{ $scope = $_REQUEST['scope']; } else { $scope = 10000;};

$totalitems        = 0;
$totalsold         = 0;

if ($usertype) {echo "<b>".$accountTypeArray[$usertype]." <i>".$username."</i></b><br>";};
echo "<nav id='heading'><span id='search-header'>MOST POPULAR ITEMS SOLD IN THE PAST :: ";

echo "<a href=\"?module=$module&amp;command=$command&amp;scope=10000\"";
if ($scope==10000) {echo " class='select2' ";};
echo ">ALL</a> | <a href=\"?module=$module&amp;command=$command&amp;scope=365\"";
if ($scope==365) {echo " class='select2' ";};
echo ">Year</a> | <a href=\"?module=$module&amp;command=$command&amp;scope=180\"";
if ($scope==180) {echo " class='select2' ";};
echo ">6 Months</a> | <a href=\"?module=$module&amp;command=$command&amp;scope=90\"";
if ($scope==90) {echo " class='select2' ";};
echo ">3 Month</a> | <a href=\"?module=$module&amp;command=$command&amp;scope=30\"";
if ($scope==30) {echo " class='select2' ";};
echo ">Month</a> | <a href=\"?module=$module&amp;command=$command&amp;scope=7\"";
if ($scope==7) {echo " class='select2' ";};
echo ">Week</a>";
echo "</span>";
echo "</nav>";

// print the list if there is not editing
$result = mysql_query("SELECT COUNT(sales_itemid) FROM sales_items, sales_orders WHERE TO_DAYS(CURRENT_DATE) < TO_DAYS(sales_orders.order_date)+'$scope' AND sales_items.sales_orderid = sales_orders.sales_orderid");
$count = mysql_fetch_array($result);

if ($lower < 0) $lower = $count[0];
if ($lower > $count[0]) $lower = 0;

$sql = "SELECT itemid, COUNT(itemid) AS count, SUM(quantity) AS total FROM sales_items, sales_orders WHERE sales_items.sales_orderid = sales_orders.sales_orderid AND TO_DAYS(CURRENT_DATE) < TO_DAYS(sales_orders.order_date)+'$scope' GROUP BY itemid ORDER BY total DESC LIMIT $lower, $number";
$result = mysql_query($sql); 

    if ($myrow = mysql_fetch_array($result))
     {
       echo "<table class='item-list'>\n";
     
       echo "<tr class='item-heading'>";
       echo "<th><a href=\"?module=$module&amp;command=$command&amp;sort=quantity&amp;lower=$lower&amp;number=$number&amp;desc=".!$desc;
       echo "\">Rank</a></th>
             <th><a href=\"?module=$module&amp;command=$command&amp;sort=category&amp;lower=$lower&amp;number=$number&amp;desc=".!$desc;
       echo "\">Category</a></th>
             <th><a href=\"?module=$module&amp;command=$command&amp;sort=format&amp;lower=$lower&amp;number=$number&amp;desc=".!$desc;
       echo "\">Format</a></th>
             <th><a href=\"?module=$module&amp;command=$command&amp;sort=artist&amp;lower=$lower&amp;number=$number&amp;desc=".!$desc;
       echo "\">Artist</a></th> 
             <th><a href=\"?module=$module&amp;command=$command&amp;sort=title&amp;lower=$lower&amp;number=$number&amp;desc=".!$desc;
       echo "\">Title</a></th>
             <th><a href=\"?module=$module&amp;command=$command&amp;sort=label&amp;lower=$lower&amp;number=$number&amp;desc=".!$desc;
       echo "\">Label</a></th>
             <th><a href=\"?module=$module&amp;command=$command&amp;sort=condition&amp;lower=$lower&amp;number=$number&amp;desc=".!$desc;
       echo "\">Cond</a></th>
             <th><a href=\"?module=$module&amp;command=$command&amp;sort=restocked&amp;lower=$lower&amp;number=$number&amp;desc=".!$desc;
       echo "\">Stocked</a></th>
             <th><a href=\"?module=$module&amp;command=$command&amp;sort=retail&amp;lower=$lower&amp;number=$number&amp;desc=".!$desc;
       echo "\">Price</a></th>
       <th>#</th>
       <th>&nbsp;</th>
             </tr>\n";

      // add an option so it will only count items sold in the past $scope days
      $temp = 1 + $lower;
       do
       {
        $currentitem = $myrow["itemid"];
        $sql2 = mysql_query("SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS restocked, TO_DAYS(CURRENT_DATE) AS current_day, TO_DAYS(restocked) AS restocked_days  FROM items WHERE itemid='$currentitem'");
        $row = mysql_fetch_array($sql2);
        $sold = $myrow["total"];      
        $totalitems = $totalitems + $sold;
        $totalsold = $totalsold + ($row["retail"]*$sold);

        printf("<tr class='item-title'>
         	<td>%s</td>
         	<td>%s</td>
         	<td>%s</td> 
        	<td>%s</td> 
        	<td><a href=\"?module=viewitem.php&amp;command=ALL&amp;itemselect=%s\">%s</a>
        	<td>%s %s</td>
        	<td>%s</td>
        	<td>%s</td>
        	<td>$%s</td>
        	<td>%s</td>", $temp, $row["category"], $row["format"],$row["artist"], $row["itemid"],$row["title"], 
        $row["label"], $row["catalog"],  $row["condition"], $row["restocked"], calcPrice($row,getDiscount($usertype,$row)), $myrow["count"]);
        $temp++;

        if (calcQuantity($usertype,$row) > 0) { 
	       echo "<td>(<a href=\"?module=additem.php&amp;command=ADD&amp;back=$module&amp;backcommand=ALL&amp;itemid=".$myrow["itemid"]."&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode\">ADD</a>)</td>"; 
        } else {
          echo "<td>OUT</td>";
        };

       } while ($myrow=mysql_fetch_array($result));
      echo "</tr>";
      echo "</table>\n";
    };
?>
<table>
<tr><td>
<form action="<? echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="sort" value="<? echo $sort;?>">
<input type="hidden" name="scope" value="<? echo $scope;?>">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="command" value="<? echo $command;?>">
<input type="submit" name="show" value="Show&amp;nbsp;:" class=button1>
<input type="text" name="number"  value="<? echo $number; ?>" class=form1>
rows beginning with number
<input type="text" name="lower"  value="<? echo $lower; ?>" class=form1>
in
<select name="desc" class=form1>
<option value="&amp;nbsp;" <? if ($desc!="DESC") echo " SELECTED ";?> >ASCENDING
<option value="DESC" <? if ($desc=="DESC") echo " SELECTED ";?> >DESCENDING
</select>
order.
</form>
</td>
<td>
<form action="<? echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="number" value="<? echo $number; ?>">
<input type="hidden" name="lower" value="<? echo ($lower-$number);?>">
<input type="hidden" name="scope" value="<? echo $scope;?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="command" value="<? echo $command;?>">
<input type="submit" name="show" value="< Previous <? echo $number;?>" class=button1>
</form>
</td>
<td>
<form action="<? echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="number" value="<? echo $number; ?>">
<input type="hidden" name="lower" value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="hidden" name="scope" value="<? echo $scope;?>">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="command" value="<? echo $command;?>">
<input type="submit" name="show" value="Next <? echo $number;?> >" class=button1>
</form>
</td>
<td>
<form action="<? echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="number" value="<? echo $count[0]; ?>">
<input type="hidden" name="lower" value="0">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="hidden" name="scope" value="<? echo $scope;?>">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="command" value="<? echo $command;?>">
<input type="submit" name="show" value="Show All <? echo $count[0];?>" class=button1>
</form>
</td>
</tr>
</table>