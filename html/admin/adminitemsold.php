<b>ITEM viewer</b>

<p>

<?php
if (!$_REQUEST['lower']) { $lower = 0; } else { $lower = $_REQUEST['lower']; };
if (!$_REQUEST['number']) { $number = 20; } else { $number = $_REQUEST['number']; };
if (!$_REQUEST['desc']) { $desc = 1; } else { $desc = $_REQUEST['desc']; };
if (!$_REQUEST['sort']) { $sort = "restocked"; } else { $sort = $_REQUEST['restocked']; };
if (!$_REQUEST['scope']) { $scope = 1000; } else { $scope = $_REQUEST['scope']; };
if (!$_REQUEST['itemselect']) { $itemselect = NULL; } else { $itemselect = $_REQUEST['itemselect']; };

$totalitems = 0;
$totalsold = 0;

dbConnect();

echo "<p>";
echo "<b>MOST POPULAR ITEMS SOLD :: sorted by $sort</b><br>";

// print the list if there is not editing

$result = mysql_query("SELECT COUNT(sales_itemid) FROM sales_items");
$count = mysql_fetch_array($result);

if ($lower<0) $lower=$count[0];
if ($lower>$count[0]) $lower=0;


if (!$itemselect) {
  $sql = "SELECT itemid, SUM(quantity) AS total FROM sales_items GROUP BY itemid ORDER BY total ".$sortArray[$desc]." LIMIT $lower, $number";
} else {
  $sql = "SELECT * FROM items WHERE itemid='$itemselect'";
};

echo "<div id='query'>Query: ".$sql."</div>";

$result = mysql_query($sql);

if ($myrow = mysql_fetch_array($result))
{
      echo "<table>\n";
     
       echo "<tr class=\"title1\"><td colspan=7><b>Items</b></td>";
       if ($desc=="DESC")	{
        echo "<td> <a href=\"$PHP_SELF?sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=&amp;nbsp;\">ASC</A>";
       } else {
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
        $currentitem = $myrow["itemid"];
        $sql2 = mysql_query("SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS restocked FROM items WHERE itemid='$currentitem'");
        $row = mysql_fetch_array($sql2);
        $sold = $myrow["total"];      
        $totalitems=$totalitems+$sold;
        $totalsold=$totalsold+($row["retail"]*$sold);

        sprintf("<tr> <td>%s</td><td>%s</td> <td>%s</td> <td><a href=\"%s?module=adminitemsold.php&amp;itemselect=%s\">%s</a>
        <td>%s %s</td> <td>%s</td>  <td>%s</td> <td>%s</td> <td>%s</td>", 
        $row["category"], $row["format"],$row["artist"], $PHP_SELF, $row["itemid"],$row["title"], 
        $row["label"], $row["catalog"],  $row["condition"], $row["restocked"], $sold, 
        $row["retail"]);
       } while ($myrow=mysql_fetch_array($result));

        echo "<tr><td colspan=7></td><td>".$totalitems."</td><td>$".$totalsold."</td></tr>";
        echo "</table>\n";
      };

?>

<table>
<tr><td>
<form action="<? echo $_SERVER['PHP_SELF'] ?>" method="post">
<input type="hidden" name="sort" value="<? echo $sort;?>">
<input type="submit" name="show" value="Show&amp;nbsp;:" class=button1>
<input type="text" name="number" value="<? echo $number; ?>">
rows beginning with number
<input type="text" name="lower" value="<? echo $lower; ?>">
in
<select name="desc">
<option value="1" <? if ($desc != 1) echo " SELECTED ";?> >ASCENDING
<option value="0" <? if ($desc == 0) echo " SELECTED ";?> >DESCENDING
</select>
order.
</form>
</td>
<td>
<form action="<? echo $_SERVER['PHP_SELF'] ?>" method="post">
<input type="hidden" name="number" value="<? echo $number; ?>">
<input type="hidden" name="lower" value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="< Previous <? echo $number;?>" class=button1>
</form>
</td>
<td>
<form action="<? echo $_SERVER['PHP_SELF'] ?>" method="post">
<input type="hidden" name="number" value="<? echo $number; ?>">
<input type="hidden" name="lower" value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Next <? echo $number;?> >" class=button1>
</form>
</td>
<td>
<form action="<? echo $_SERVER['PHP_SELF'] ?>" method="post">
<input type="hidden" name="number" value="<? echo $count[0]; ?>">
<input type="hidden" name="lower" value="0">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Show All <? echo $count[0];?>" class=button1>
</form>
</td>
</tr>
</table>