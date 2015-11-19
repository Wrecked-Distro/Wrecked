<?php
include("header.php");
include("db.php");

	echo "<html>";
	echo "<head>";
	echo "<title>request view</title>";
	echo "</head>";

	echo "<body>";

	echo "<b>ITEM viewer</b>";

	echo "<p>";

   if (!$lower) {$lower=0;};
   if (!$number) {$number=20;};
   if (!$desc) {$desc="DESC";};
   if (!$sort) {$sort="requestTime";};
   if (!$scope) {$scope=10000;};
   $totalitems=0;
   $totalsold=0;

   dbConnect();

        echo "<p>";
     echo "<font size=+1><b>MOST POPULAR RESTOCK REQUESTS BY DATE:: sorted by $sort</b> ";

echo "<a href=\"$PHPSELF?scope=10000\">ALL</a> |
<a href=\"$PHPSELF?scope=365\">365</a> |
<a href=\"$PHPSELF?scope=180\">180</a> |
<a href=\"$PHPSELF?scope=90\">90</a> |
<a href=\"$PHPSELF?scope=30\">30</a> |
<a href=\"$PHPSELF?scope=7\">7</a>";
echo "</font><br>";

    // print the list if there is not editing

   $result=mysql_query("SELECT COUNT(requestID) FROM request");
   $count=mysql_fetch_array($result);

   if ($lower<0) $lower=$count[0];
   if ($lower>$count[0]) $lower=0;


        $result = mysql_query("SELECT requestItem, requestUsername  FROM request WHERE
TO_DAYS(CURRENT_DATE)<TO_DAYS(request.requestTime)+'$scope' AND requestUsername != '' ORDER BY requestTime $desc LIMIT
$lower, $number");

     if ($myrow = mysql_fetch_array($result))
     {

       echo "<table border=0 cellspacing=0 cellpadding=3>\n";

       echo "<tr class=\"title1\"><td colspan=7><b>Items</b></td>";
       if ($desc=="DESC")
       	{ echo "<td> <a
href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=&nbsp;\">ASC</A>";}
       else
       	{ echo "<td> <a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=DESC\">DESC</a>";};

       echo "<td> <a href=\"$PHP_SELF?sort=$sort&lower=0&number=$count[0]$switch\"> Show
All</a></td></tr>\n";
       echo "<tr class=\"title2\">
             <td><a href=\"$phpself?sort=category&lower=$lower&number=$number&desc=$desc";
       echo "\">Username</a></td>
             <td><a href=\"$phpself?sort=format&lower=$lower&number=$number&desc=$desc";
       echo "\">Category</a></td>
             <td><a href=\"$phpself?sort=format&lower=$lower&number=$number&desc=$desc";
       echo "\">Format</a></td>
             <td><a href=\"$phpself?sort=artist&lower=$lower&number=$number&desc=$desc";
       echo "\">Artist</a></td>
             <td><a href=\"$phpself?sort=title&lower=$lower&number=$number&desc=$desc";
       echo "\">Title</a></td>
             <td><a href=\"$phpself?sort=label&lower=$lower&number=$number&desc=$desc";
       echo "\">Label</a></td>
             <td><a href=\"$phpself?sort=condition&lower=$lower&number=$number&desc=$desc";
       echo "\">Cond</a></td>
             <td><a href=\"$phpself?sort=restocked&lower=$lower&number=$number&desc=$desc";
       echo "\">Request</a></td>
             <td><a href=\"$phpself?sort=quantity&lower=$lower&number=$number&desc=$desc";
       echo "\">Sold</a></td>
             <td><a href=\"$phpself?sort=retail&lower=$lower&number=$number&desc=$desc";
       echo "\">Cost</a></td>

             </tr>\n";

// add an option so it will only count items sold in the past $scope days

       do
       {
      $currentitem=$myrow["requestItem"];
      $sql2 = mysql_query("SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS restocked FROM items WHERE
itemid='$currentitem'");
      $row=mysql_fetch_array($sql2);
      $sold= $myrow["total"];
      $totalitems=$totalitems+$sold;
      $totalsold=$totalsold+($row["retail"]*$sold);

        printf("<tr> <td>%s</td><td>%s</td><td>%s</td> <td>%s</td> <td><a href=\"%s?itemselect=%s\">%s</a>
<td>%s %s</td> <td>%s</td>  <td>%s</td> <td>%s</td> <td>%s</td>",
$myrow["requestUsername"],$row["category"], $row["format"],$row["artist"], $PHP_SELF, $row["itemid"],$row["title"],
$row["label"], $row["catalog"],  $row["condition"], $row["restocked"], $sold,
$row["retail"]);


       } while ($myrow=mysql_fetch_array($result));

echo "<tr><td colspan=7></td><td>".$totalitems."</td><td>$".$totalsold."</td></tr>";
       echo "</table>\n";
      };

?>

<table>
<tr><td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="sort" value="<? echo $sort;?>">
<input type="submit" name="show" value="Show&nbsp;:" class=button1>
<input type="text" name="number" size="3" value="<? echo $number; ?>">
rows beginning with number
<input type="text" name="lower" size="3" value="<? echo $lower; ?>">
in
<select name="desc">
<option value="&nbsp;" <? if ($desc!="DESC") echo " SELECTED ";?> >ASCENDING
<option value="DESC" <? if ($desc=="DESC") echo " SELECTED ";?> >DESCENDING
</select>
order.
</form>
</td>
<td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="< Previous <? echo $number;?>" class=button1>
</form>
</td>
<td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Next <? echo $number;?> >" class=button1>
</form>
</td>
<td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $count[0]; ?>">
<input type="hidden" name="lower" size="3" value="0">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="hidden" name="scope" value="<? echo $scope; ?>">
<input type="submit" name="show" value="Show All <? echo $count[0];?>" class=button1>
</form>
</td>
</tr>
</table>





</body>

</html>
