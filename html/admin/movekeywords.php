<?php
include_once("db.php");

dbConnect();

// move all genre names into the keywords table
$sql = "SELECT itemid, category FROM items";
$result = mysql_query($sql);

if ($myrow = mysql_fetch_array($result))
{
  do
  {
    $itemID = $myrow["itemid"];
    $keyword = $myrow["category"];
    $sql2 = "INSERT INTO keywords (keywordID, itemID, keyword) VALUES (0, '$itemID','$keyword')";
    $result2 = mysql_query($sql2);

   } while ($myrow = mysql_fetch_array($result));
};
?>
