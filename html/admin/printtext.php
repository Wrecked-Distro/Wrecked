<?php
   dbConnect("db9372_distro");
   $value = 0;
   $alt = 0;
   $q_total = 0;
?>

<table>
<tr align=right>
<td><form action="index.php" method="post">
ENTER SEARCH KEYWORD(S)
<input type="hidden" name="module" size="3" value="<? echo $module;?>">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo $lower;?>">
<input type="hidden" name="mode" value="<? echo $mode;?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="text" name="keyword" size="12" value="<? echo $keyword; ?>" class="form1">
<select name="search" class="form1">
<option <? if ($search=="artist") echo "selected";?> name="search" value="artist" class="form1">artist
<option <? if ($search=="title") echo "selected";?> name="search" value="title">title
<option <? if ($search=="label") echo "selected";?> name="search" value="label">label
<option <? if ($search=="format") echo "selected";?> name="search" value="format">format
<option <? if ($search=="description") echo "selected";?> name="search" value="description">description
</select>
<input type="submit" name="show" value="Search" class="button1">
</form>
</td></tr>
</table>
<?php

    // print the list if there is not editing

   $query = "SELECT COUNT(itemid) FROM items WHERE quantity > 0";
   $result = mysql_query($query);
   $total = mysql_fetch_array($result);

   //echo $query;

   if ($lower<0) $lower=$total[0];
   if ($lower>$total[0]) $lower=0;

	// set sql query
	$query = "SELECT *, DATE_FORMAT(restocked,'%m/%d/%y') AS date_format, TO_DAYS(restocked) AS 
restocked_days, TO_DAYS(CURRENT_DATE) AS current_day  FROM items WHERE quantity > 0 AND $search LIKE '%$keyword%' ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower,$number";

//	echo $query;
	$result = mysql_query($query);

     if ($myrow = mysql_fetch_array($result))
     {      
       echo "<b>Items</b>";
       if ($desc == 1)
       {
	 echo " <a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=&nbsp;\">ASC</A>";
	}
       else       
       {
		echo " <a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=DESC\">DECENDING</a>";};

	       echo " <a href=\"$PHP_SELF?sort=$sort&lower=0&number=$total[0]$switch\"> Show All</a></font><p>\n";
       	echo "<a href=\"$phpself?module=$module&sort=itemid&lower=$lower&number=$number&desc=".(($desc == 0) ? 1 : 0);
	       echo "\">ItemID</a> <a href=\"$phpself?module=$module&sort=category&lower=$lower&number=$number&desc=".!$desc;
	       echo "\">Category</a> <a href=\"$phpself?module=$module&sort=format&lower=$lower&number=$number&desc=".!$desc;
	       echo "\">Format</a> <a href=\"$phpself?module=$module&sort=artist&lower=$lower&number=$number&desc=".!$desc."&mode=$mode";
       	echo "\">Artist</a> <a href=\"$phpself?module=$module&sort=title&lower=$lower&number=$number&desc=".!$desc;
       	echo "\">Title</a> <a href=\"$phpself?module=$module&sort=label&lower=$lower&number=$number&desc=".!$desc;
       	echo "\">Label</a> <a href=\"$phpself?module=$module&sort=condition&lower=$lower&number=$number&desc=".!$desc;
       	echo "\">Cond</a> <a href=\"$phpself?module=$module&sort=released&lower=$lower&number=$number&desc=".!$desc;
       	echo "\">Released</a> <a href=\"$phpself?module=$module&sort=quantity&lower=$lower&number=$number&desc=".!$desc;
       	echo "\">Quant</a> <a href=\"$phpself?module=$module&module=$module&sort=retail&lower=$lower&number=$number&desc=".!$desc;
       	echo "\">Cost</a><br>\n";
      
       do
       {
        printf("<font color=000000>%s - %s - %s %s - %s x %s<br>",$myrow["artist"], $myrow["title"], $myrow["label"], $myrow["catalog"], $myrow["format"],  $myrow['quantity']); 
        // get the running total
        $quantity = $myrow['quantity'];
        $q_total = $q_total + $quantity;
        $temp = $myrow["cost"] * $quantity;
        $value = $value + $temp;
        $alt = $alt + (2 * $quantity);
       } while ($myrow = mysql_fetch_array($result));

       echo "<p>";
       echo "<b>QUANTITY: </b> ".$q_total;
       echo "<b>GRAND TOTAL: </b> $".number_format($value,2);
       echo "<b>ALT TOTAL: </b> $".number_format($alt,2);
      };

?>

<table>
<tr><td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="module" value="<? echo $module;?>">
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
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="< Previous <? echo $number;?>">
</form>
</td>
<td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Next <? echo $number;?> >">
</form>
</td>
<td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="number" size="3" value="<? echo $total[0]; ?>">
<input type="hidden" name="lower" size="3" value="0">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Show All <? echo $total[0];?>">
</form>
</td>
</tr>
</table>

<?
echo "<p>";

if (!$itemselect)
{
  $itemquery = "SELECT itemid, artist, title, label, catalog, format, cost, retail, quantity, category, description FROM items WHERE quantity > 0 ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower, $number";
} else	{
		$itemquery = "SELECT * FROM items WHERE itemid='".$itemselect."'";
};        

$items = mysql_query($itemquery);
       
if ($myrow = mysql_fetch_array($items))
{
	 $countSQL = "SELECT COUNT(*) FROM items WHERE quantity > 0";
   $count = mysql_query($countSQL);
   $total = mysql_fetch_array($count);

	 do
		{ 
	 	echo $myrow["artist"]." - ".$myrow["title"]." - ".$myrow["label"]." ".$myrow["catalog"]." - ".$myrow["format"]." - $".$myrow["retail"].' x '.$myrow["quantity"];  
		echo "<br>";
		echo $myrow["description"]."<br>";

      $itemid=$myrow["itemid"];
      $tracksql = "SELECT tracknumber, url FROM tracks WHERE itemid=$itemid";
      $trackresult = mysql_query($tracksql);
     
      if ($tracklist=mysql_fetch_array($trackresult))
      {
      do
      {
       echo "[<a href=\"rammaker.php?url=".$tracklist["url"]."\">".$tracklist["tracknumber"]."</a>] ";
      } while ($tracklist=mysql_fetch_array($trackresult));
      };

		echo "<p>";
		} while ($myrow=mysql_fetch_array($items));
	};
  	



    ?>
     
     
     
</body>
     
</html>
