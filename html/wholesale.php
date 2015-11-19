<?php
// wholesale.php 
// script for printing wholesale catalog info
//
// prints all items that are available to wholesalers
//
 
include("db.php");

$minimum_stock = 4;
$sell_all_after = 365;

// returns the wholesaler price for an item given the format
// may also want another function that adds based on who the
// customer is, how many are in stock, how long i've had it in  

function WholesaleDiscount ($format)
{
  $value=0;
  switch ($format)
	{
	case "7": $value=.5; break;
	case "10": $value=.5; break;
	case "12": $value=1; break;
	case "LP": $value=1; break;
	case "2LP": $value=2; break;
	case "Boxset": $value=3; break;
	case "CD": $value=.5; break;
	case "2CD": $value=1; break;
	case "DVD": $value=2; break;
	case "Zine": $value=.5; break;
	case "Book": $value=1; break;
	case "T-shirt": $value=1; break;
	case "tape": $value=.5; break;
	case "3LP": $value=1.5; break;
	case "4LP": $value=2; break;
	case "CDzine": $value=1; break;
	case "2x10": $value=1; break;
	case "CDR": $value=.5; break;
	case "3inCD": $value=.5; break;
	case "2x3in": $value=1; break;
	case "2x7": $value=1; break;
	};
  return $value;
};

?>

<html>
<head>
<title>item view</title>
</head>

<body>

<b>WHOLESALE Viewer</b>
<br>Minimum order of $100.<br>
Postage not included<br>
<p>

<?php
  if (!$lower) {$lower=0;};
  if (!$number) {$number=2000;};   
  if (!$desc) {$desc="";};   
  if ($_REQUEST['sort']) {
    $sort = $_REQUEST['sort'];
  } else {
    $sort = "artist";
  };

  dbConnect();

  // print the list if there is not editing
  $result = mysql_query("SELECT COUNT(itemid) FROM items WHERE quantity>$minimum_stock OR (quantity>0 AND ((TO_DAYS(CURRENT_DATE)-$sell_all_after) > TO_DAYS(restocked))) ORDER BY $sort $desc LIMIT $lower, $number");
  $total = mysql_fetch_array($result);

  if ($lower<0) $lower=$total[0];
  if ($lower>$total[0]) $lower=0;
  $count = $total[0];

  if (!$itemselect) {
    $result = mysql_query("SELECT * FROM items WHERE quantity>$minimum_stock OR (quantity>0 AND ((TO_DAYS(CURRENT_DATE)-$sell_all_after) > TO_DAYS(restocked))) ORDER BY $sort $desc LIMIT $lower,$number");
  } else {
    $result = mysql_query("SELECT * FROM items WHERE itemid = '$itemselect'");
  };

  if ($myrow = mysql_fetch_array($result)) {
    echo "<b>Items</b>";
    if ($desc=="DESC")	{
      echo " <a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=&nbsp;\">ASC</A>";
    } else 	{
      echo " <a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=DESC\">DECENDING</a>";
    };

    echo "<a href=\"$PHP_SELF?sort=$sort&lower=0&number=$count$switch\"> Show All</a></font><p>\n";
    echo "<a href=\"$phpself?sort=itemid&lower=$lower&number=$number&desc=$desc";
    echo "\">ItemID</a> <a href=\"$phpself?sort=category&lower=$lower&number=$number&desc=$desc";
    echo "\">Category</a> <a href=\"$phpself?sort=format&lower=$lower&number=$number&desc=$desc";
    echo "\">Format</a> <font color=\"ffffff\"><a href=\"$phpself?sort=artist&lower=$lower&number=$number&desc=$desc";
    echo "\">Artist</a> <font color=\"ffffff\"><a href=\"$phpself?sort=title&lower=$lower&number=$number&desc=$desc";
    echo "\">Title</a> <font color=\"ffffff\"><a href=\"$phpself?sort=label&lower=$lower&number=$number&desc=$desc";
    echo "\">Label</a> <font color=\"ffffff\"><a href=\"$phpself?sort=condition&lower=$lower&number=$number&desc=$desc";
    echo "\">Cond</a> <font color=\"ffffff\"><a href=\"$phpself?sort=released&lower=$lower&number=$number&desc=$desc";
    echo "\">Released</a> <font color=\"ffffff\"><a href=\"$phpself?sort=quantity&lower=$lower&number=$number&desc=$desc";
    echo "\">Quant</a> <font color=\"ffffff\"><a href=\"$phpself?sort=retail&lower=$lower&number=$number&desc=$desc";
    echo "\">Wholesale</a>

    <br>\n";

    do {

      $cost = $myrow["cost"] + WholesaleDiscount($myrow["format"]);
      if ($myrow["quantity"]>1) {$quantity=$myrow["quantity"]-1;} else {$quantity=$myrow["quantity"];};
      if ($quantity==1) {$cost=$cost+.5;};  

      printf("<font color=000000>%s x %s - %s - %s %s - %s - $%s <br>", $quantity,$myrow["artist"], $myrow["title"], $myrow["label"], $myrow["catalog"],$myrow["format"], $cost);

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
  $itemquery = "SELECT itemid, artist, title, label, catalog, format, cost, retail, quantity, category, description FROM items WHERE quantity>$minimum_stock OR (quantity>0 AND ((TO_DAYS(CURRENT_DATE)-$sell_all_after) > TO_DAYS(restocked))) ORDER BY $sort $desc LIMIT $lower, $number";
} else {
  $itemquery = "SELECT * FROM items WHERE itemid='".$itemselect."'";
};        

$items = mysql_query($itemquery);

if ($myrow = mysql_fetch_array($items))
{
  $count = mysql_query("SELECT COUNT(*) FROM items WHERE quantity>$minimum_stock OR (quantity>0 AND ((TO_DAYS(CURRENT_DATE)-$sell_all_after) > TO_DAYS(restocked)))");
  $total = mysql_fetch_array($count);

  echo "<font size=+1>".$catrow["name"]."</font> (".$total[0].")<p>";
	do { 
    if ($myrow["quantity"]>1) {$quantity=$myrow["quantity"]-1;} else {$quantity=$myrow["quantity"];};
    if ($quantity==1) {$cost=$myrow["cost"]+1;} else {$cost=$myrow["cost"]+.5;};  

    echo $myrow["artist"]." - ".$myrow["title"]." - ".$myrow["label"]." ".$myrow["catalog"]." -  ".$myrow["format"]." - $".$cost;  
    echo "<br>";
    echo $myrow["description"]."<br>";
    echo "<p>";
  } while ($myrow=mysql_fetch_array($items));
};

?>
</body>
</html>
