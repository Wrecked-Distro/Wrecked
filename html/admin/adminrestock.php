<?php
include("header.php");
include("db.php");
?>
<html>
<head>
<title>item view</title>
</head>

<body>

<?php
   if (!$lower) {$lower=0;};
   if (!$number) {$number=20;};
   if (!$desc) {$desc="DESC";};
   if (!$sort) {$sort="itemid";};

   dbConnect();

     echo "<p>";
     echo "<font size=+1><b>OUT OF STOCK :: sorted by $sort</b></font><br>";

    // print the list if there is not editing

   $result=mysql_query("SELECT COUNT(itemid) FROM items WHERE quantity<1 AND
TO_DAYS(CURRENT_DATE)>TO_DAYS(released)");
   $total=mysql_fetch_array($result);

   if ($lower<0) $lower=$total[0];
   if ($lower>$total[0]) $lower=0;


        if (!$itemselect)
        {$result = mysql_query("SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS date_format FROM items WHERE quantity<1 AND
TO_DAYS(CURRENT_DATE)>TO_DAYS(released) AND condition='NEW' ORDER BY $sort $desc LIMIT $lower,
$number");}
        else
        {$result = mysql_query("SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS date_format FROM items WHERE
itemid='$itemselect'");};

     if ($myrow = mysql_fetch_array($result))
     {
       echo "<table border=0 cellspacing=0 cellpadding=3>\n";

       echo "<tr><td bgcolor=ffcc00 colspan=8><font color=ff0000><b>Items</b></td>";
       if ($desc=="DESC")
       	{ echo "<td bgcolor=ffcc00> <a
href=\"viewitem.php?sort=$sort&lower=$lower&number=$number&desc=&nbsp;\">ASC</A>";}
       else
       	{ echo "<td bgcolor=ffcc00> <a href=\"viewitem.php?sort=$sort&lower=$lower&number=$number&desc=DESC\">DECENDING</a>";};

       echo "<td bgcolor=ffcc00> <a href=\"viewitem.php?sort=$sort&lower=0&number=$total[0]$switch\"> Show
All</a></font></td></tr>\n";
       echo "<tr bgcolor=\"000066\">
             <td><font color=\"ffffff\"><a href=\"$phpself?sort=category&lower=$lower&number=$number&desc=$desc";
       echo "\">Category</a></td>
             <td><font color=\"ffffff\"><a href=\"$phpself?sort=format&lower=$lower&number=$number&desc=$desc";
       echo "\">Format</a></td>
             <td><font color=\"ffffff\"><a href=\"$phpself?sort=artist&lower=$lower&number=$number&desc=$desc";
       echo "\">Artist</a></td>
             <td><font color=\"ffffff\"><a href=\"$phpself?sort=title&lower=$lower&number=$number&desc=$desc";
       echo "\">Title</a></td>
             <td><font color=\"ffffff\"><a href=\"$phpself?sort=label&lower=$lower&number=$number&desc=$desc";
       echo "\">Label</a></td>
             <td><font color=\"ffffff\"><a href=\"$phpself?sort=condition&lower=$lower&number=$number&desc=$desc";
       echo "\">Cond</a></td>
             <td><font color=\"ffffff\"><a href=\"$phpself?sort=restocked&lower=$lower&number=$number&desc=$desc";
       echo "\">Stocked&nbsp;&nbsp;&nbsp;</a></td>
             <td><font color=\"ffffff\"><a href=\"$phpself?sort=quantity&lower=$lower&number=$number&desc=$desc";
       echo "\">#</a></td>
             <td><font color=\"ffffff\"><a href=\"$phpself?sort=retail&lower=$lower&number=$number&desc=$desc";
       echo "\">Cost</a></td>
             <td><font color=\"ffffff\"><a href=\"$phpself?sort=quantity&lower=$lower&number=$number&desc=$desc";
       echo "\">SoldCount</a></td>

             </tr>\n";

       do
       {
       $itemtemp = $myrow["itemid"];
       $result2 = mysql_query("SELECT COUNT(itemid) AS count FROM sales_items WHERE
sales_items.itemid='$itemtemp' GROUP BY itemid");
      $soldcount = mysql_fetch_array($result2);
      $count=$soldcount[0];

       printf("<tr> <td>%s</td><td>%s</td> <td>%s</td> <td><a href=\"%s?itemselect=%s\">%s</a>
<td>%s %s</td> <td>%s</td>  <td>%s</td> <td>%s</td> <td>%s</td> <td>%s</td>",
$myrow["category"], $myrow["format"],$myrow["artist"], $PHP_SELF, $myrow["itemid"],$myrow["title"],
$myrow["label"], $myrow["catalog"],  $myrow["condition"], $myrow["date_format"], $myrow["quantity"],
$myrow["retail"] ,$count);


       } while ($myrow=mysql_fetch_array($result));

       echo "</table>\n";
      };

?>

<table>
<tr><td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="sort" value="<? echo $sort;?>">
<input type="submit" name="show" value="Show&nbsp;:">
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
<input type="submit" name="show" value="< Previous <? echo $number;?>">
</form>
</td>
<td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Next <? echo $number;?> >">
</form>
</td>
<td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $total[0]; ?>">
<input type="hidden" name="lower" size="3" value="0">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Show All <? echo $total[0];?>">
</form>
</td>
</tr>
</table>



</body>

</html>
