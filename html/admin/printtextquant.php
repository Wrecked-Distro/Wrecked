<?php
   dbConnect("db9372_distro");

   if (!$lower)   { $lower  = 0;};
   if (!$number)  { $number = 20;};   
   if (!$desc)    { $desc   = "DESC";};   
   if (!$sort)    { $sort   = "itemid";};

   // print the list if there is not editing
   $result = mysql_query("SELECT COUNT(itemid) FROM items WHERE quantity>0");
   $total = mysql_fetch_array($result);

   if ($lower<0) $lower = $total[0];
   if ($lower>$total[0]) $lower=0;

   if (!$itemselect)  {
      $sql = "SELECT * FROM items WHERE quantity>0  ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower, $number";
      $result = mysql_query($sql);
    } else {
      $sql = "SELECT * FROM items WHERE itemid='$itemselect'";
      $result = mysql_query($sql);
    };

    echo $sql;
   if ($myrow = mysql_fetch_array($result))
   {    
     echo "<b>Items</b>";
     if ($desc=="DESC")	{
      echo " <a href=\"viewitem.php?sort=$sort&lower=$lower&number=$number&desc=&nbsp;\">ASC</A>";
    } else { 
      echo " <a href=\"viewitem.php?sort=$sort&lower=$lower&number=$number&desc=DESC\">DECENDING</a>";
    };

     echo " <a href=\"viewitem.php?module=$module&sort=$sort&lower=0&number=$total[0]$switch\"> Show All</a></font><p>\n";
     echo "<a href=\"$phpself?module=$module&sort=itemid&lower=$lower&number=$number&desc=".!$desc;
     echo "\">ItemID</a><a href=\"$phpself?module=$module&sort=category&lower=$lower&number=$number&desc=".!$desc;
     echo "\">Category</a> <a href=\"$phpself?module=$module&sort=format&lower=$lower&number=$number&desc=".!$desc;
     echo "\">Format</a><font color=\"ffffff\"><a href=\"$phpself?module=$module&sort=artist&lower=$lower&number=$number&desc=".!$desc;
     echo "\">Artist</a> <font color=\"ffffff\"><a href=\"$phpself?module=$module&sort=title&lower=$lower&number=$number&desc=".!$desc;
     echo "\">Title</a>  <font color=\"ffffff\"><a href=\"$phpself?module=$module&sort=label&lower=$lower&number=$number&desc=".!$desc;
     echo "\">Label</a>
           <font color=\"ffffff\"><a href=\"$phpself?module=$module&sort=condition&lower=$lower&number=$number&desc=".!$desc;
     echo "\">Cond</a>
           <font color=\"ffffff\"><a href=\"$phpself?module=$module&sort=released&lower=$lower&number=$number&desc=".!$desc;
     echo "\">Released</a>
           <font color=\"ffffff\"><a href=\"$phpself?module=$module&sort=quantity&lower=$lower&number=$number&desc=".!$desc;
     echo "\">Quant</a>
           <font color=\"ffffff\"><a href=\"$phpself?module=$module&sort=retail&lower=$lower&number=$number&desc=".!$desc;
     echo "\">Cost</a><br>\n";
      
       do
       {
        printf("<font color=000000>%s x %s - %s - %s %s - %s - $%s <br>",
$myrow["quantity"],$myrow["artist"], $myrow["title"], $myrow["label"], $myrow["catalog"],  
$myrow["format"], $myrow["retail"]);
    

       } while ($myrow=mysql_fetch_array($result));

       echo "<p>";
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

<?php
echo "<p>";

if (!$itemselect) {
  $itemquery = "SELECT itemid, artist, title, label, catalog, format, cost, retail, quantity, category, description FROM items WHERE quantity>0 ORDER BY $sort ".$sortArray[$desc]."  LIMIT $lower, $number";
} else {
  $itemquery = "SELECT * FROM items WHERE itemid='".$itemselect."'";
};        

echo $itemquery;
$items = mysql_query($itemquery);
        
if ($myrow = mysql_fetch_array($items))	{

  $count = mysql_query("SELECT COUNT(*) FROM items WHERE quantity > 0");
  $total = mysql_fetch_array($count);

  echo "<font size=+1>".$catrow["name"]."</font> (".$total[0].")<p>";

  do
  { 
    echo $myrow["artist"]." - ".$myrow["title"]." - ".$myrow["label"]." ".$myrow["catalog"]." - ".$myrow["format"]." - $".$myrow["retail"];  
    echo "<br>";
    echo $myrow["description"]."<br>";

    $itemid = $myrow["itemid"];
    $tracksql = "SELECT tracknumber, url FROM tracks WHERE itemid = $itemid";
    $trackresult = mysql_query($tracksql);

    if ($tracklist = mysql_fetch_array($trackresult))
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
