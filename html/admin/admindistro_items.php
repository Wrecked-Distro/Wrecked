<?php 


function modifyCost($sales_orderid, $sales_itemid, $quantity)
{
  dbConnect();

  echo "sales_orderid = $sales_orderid<p>";
  echo "sales_itemid = $sales_itemid<p>";
  echo "quantity = $quantity<p>";

  $sql = "SELECT retail FROM items WHERE itemid=$sales_itemid";
  $result = mysql_fetch_array(mysql_query($sql));

  $retail=$result["retail"];

  $total=$retail*$quantity;

  $sql = "UPDATE sales_orders SET order_cost=order_cost-$total WHERE sales_orderid=$sales_orderid";
  $result = mysql_query($sql);



}

include("header.php");
include("db.php");

   if (!$lower) {$lower=0;};
   if (!$number) {$number=20;};
   if (!$desc) {$desc="DESC";};

   dbConnect();

   if ($submit)
   {
    // here if no ID then adding, else we're editing

     if ($distro_itemid)
     {

      $sql = "UPDATE distro_items SET distro_orderid='$distro_orderid', itemid='$itemid', cost='$cost',quantity='$quantity'  WHERE
distro_itemid='$distro_itemid'";
      echo "Update of ".$distro_itemid."\n distro_orderid=".$distro_orderid." \n";
     }
     else
     {
  $sql = "INSERT INTO distro_items (distro_itemid, distro_orderid, itemid, cost, quantity)
VALUES  (0,'$distro_orderid','$itemid','$cost','$quantity')";

      echo "inserting into ".$distro_orderid."\n";

     }

      $result = mysql_query($sql);

      echo "Record updated.<p>";

      echo "<a
href=\"$PHP_SELF?distro_orderid=$distro_orderid&sort=$sort&lower=$lower&number=$number&desc=$desc\">more
items</a>";

     } elseif ($delete) {

       // delete a record

       $sql = "DELETE FROM distro_items WHERE distro_itemid='$distro_itemid'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";


      echo "<a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";

     } else {


     $result = mysql_query("SELECT items.label, items.artist, items.title, items.catalog, items.itemid,
items.retail, distro_items.distro_itemid, distro_items.distro_orderid, distro_items.itemid, distro_items.cost,
distro_items.quantity  FROM  items, distro_items WHERE  items.itemid=distro_items.itemid  AND
distro_items.distro_orderid='$distro_orderid'");


     if ($myrow = mysql_fetch_array($result))
     {

       echo "<table border=0 cellspacing=0 cellpadding=3>\n";

       echo "<tr><td class=\"title1\" colspan=6><b>Current Distro Items in Order</b></td></tr>\n";
       echo "<tr class=\"title2\">
             <td>
<a href=\"$PHP_SELF?sort=order_itemid&lower=$lower&number=$number&desc=$desc\">Distro_ItemID</a></td>
             <td>
<a href=\"$PHP_SELF?sort=distro_orderid&lower=$lower&number=$number&desc=$desc\">Distro OrderID</a></td>
             <td>
<a href=\"$PHP_SELF?sort=itemid&lower=$lower&number=$number&desc=$desc\">Item</a></td>
             <td>
<a href=\"$PHP_SELF?sort=quantity&lower=$lower&number=$number&desc=$desc\">Quantity</a></td>
             <td>
<a href=\"$PHP_SELF?sort=cost&lower=$lower&number=$number&desc=$desc\">Unit Cost</a></td>
             <td>
<a href=\"$PHP_SELF?sort=cost&lower=$lower&number=$number&desc=$desc\">Total Cost</a></td>

             </tr>\n";

do
       {
        printf("<tr><td>%s</td> <td>%s</td> <td>%s %s - %s - %s %s</td><td>%s</td><td>$%s</td><td>$%s</td>",
        $myrow["distro_itemid"], $myrow["distro_orderid"], $myrow["itemid"], $myrow["artist"],$myrow["title"],
$myrow["label"],$myrow["catalog"],$myrow["quantity"], $myrow["cost"],$myrow["cost"]*$myrow["quantity"]);

        printf("<td><a
href=\"%s?distro_itemid=%s&delete=yes&sort=$sort&lower=$lower&number=$number&desc=$desc\">(DELETE)</a></td><td><a
href=\"%s?distro_itemid=%s&distro_orderid=%s&sort=$sort&lower=$lower&number=$number&desc=$desc\">(EDIT)</a></td></tr>",
        $PHP_SELF,$myrow["distro_itemid"],$PHP_SELF,$myrow["distro_itemid"],$myrow["distro_orderid"]);

       } while ($myrow=mysql_fetch_array($result));
       echo "</table>\n";
      }

     echo "<p>";

     }

?>

     <form method="post" name="addform" action="<?php echo $PHP_SELF?>" >

     <?

     if ($distro_itemid)
     {

     // editing so select a record


     $sql = "SELECT * FROM distro_items WHERE distro_itemid='$distro_itemid'";

     $result = mysql_query($sql);

     $myrow = mysql_fetch_array($result);

     $distro_orderid = $myrow["distro_orderid"];

     $itemid = $myrow["itemid"];

     $cost = $myrow["cost"];

     $quantity = $myrow["quantity"];

     // print the id for editing

     ?>

     <input type=hidden name="distro_itemid" value="<?php echo $distro_itemid; ?>">

     <?
     }

     ?>

     Fill in all fields to add a new item to a distro order <br>     *'d fields are optional.<p>
     <table>


    <tr><td>
     <font class=\"text3\">

     <a href="admindistro_orders.php">Distro Order</a></td>
     <td>
     <select name="distro_orderid" size="1">
     <?
      $sql = "SELECT * FROM distro_orders, distributors WHERE distro_orders.distroid=distributors.distroid AND
distro_orderid='$distro_orderid'";
      $result = mysql_query($sql);

      if ($orderlist=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$orderlist["distro_orderid"]."\" ";
       if ($orderlist["distro_orderid"]==$myrow["distro_orderid"])
        {echo "selected";};
       echo ">".$orderlist["distro_orderid"]." ".$orderlist["name"]." ".$orderlist["order_date"];
      } while ($orderlist=mysql_fetch_array($result));
      };
     ?>
     </select>
     </td></tr>


    <tr><td>
     <font class=\"text3\">

     <a href="adminitem.php">Item</a></td>
     <td>
     <select name="itemid" size="1">

     <?
      $sql = "SELECT itemid, artist, title, label, catalog, format FROM items ORDER BY artist";
      $result = mysql_query($sql);

      if ($itemlist=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$itemlist["itemid"]."\" ";
       if ($itemlist["itemid"]==$itemid)
        {echo "selected";};
       echo ">".$itemlist["artist"]." - ".$itemlist["title"]." - ".$itemlist["label"]." ".$itemlist["catalog"]." -
".$itemlist["format"];
      } while ($itemlist=mysql_fetch_array($result));
      };
     ?>

     </select>
     </td></tr>

     <tr><td>
     <font class=\"text3\">
     Cost</td>
     <td>
     <input type="Text" name="cost" value="<? echo $myrow["cost"] ?>">
     </td>
     </tr>

     <tr><td>
     <font class=\"text3\">
     Quantity</td>
     <td>
     <input type="Text" name="quantity" value="<? echo $myrow["quantity"] ?>">
     </td>
     </tr>

     <tr><td>

        <input type="hidden" name="distro_orderid" value="<? echo $distro_orderid ?>">
        <input type="hidden" name="sort" value="<? echo $sort ?>">
        <input type="hidden" name="lower" value="<? echo $lower ?>">
        <input type="hidden" name="number" value="<? echo $number ?>">
        <input type="hidden" name="desc" value="<? echo $desc ?>">
        <input type="Submit" name="submit" value="Enter information" class="button1"></td></tr>

     </table>
     </form>
<?
echo "<P><a href=\"admindistro_items.php?sort=$sort&lower=$lower&number=$number&desc=$desc\">back to distro item admin</a><P>";
echo "<P><a href=\"admindistro_orders.php?sort=$sort&lower=$lower&number=$number&desc=$desc\">back to distro orders admin</a><P>";
  ?>

</body>

</html>
