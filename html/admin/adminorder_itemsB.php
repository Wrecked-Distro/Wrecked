<? include("header.php"); ?>

     <?


   if (!$lower) {$lower=0;};
   if (!$number) {$number=20;};
   if (!$desc) {$desc="DESC";};
   
   $db = mysql_connect("localhost","root","");

   mysql_select_db("wrecked",$db);

   if ($submit)
   {
    // here if no ID then adding, else we're editing
       
     if ($order_itemid)
     {
      $sql = "UPDATE order_items SET distro_orderid='$distro_orderid', itemid='$itemid', cost='$cost',
quantity='$quantity' WHERE order_itemid='$order_itemid'";
      echo "Update of ".$order_itemid."\n";
     }
     else
     {
  $sql = "INSERT INTO order_items
(order_itemid, distro_orderid, itemid, cost, quantity)
VALUES  (0,'$distro_orderid','$itemid','$cost','$quantity')";

      echo "inserting ".$order_itemid."\n";

     }

      $result = mysql_query($sql);

      echo "Record updated.<p>";

      echo "<a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=$desc\">more orders</a>";

     } elseif ($delete) {

       // delete a record

       $sql = "DELETE FROM order_items WHERE order_itemid='$order_itemid'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";
      echo "<a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";

     } else {


     $result = mysql_query("SELECT distributors.distroid, distributors.name, items.label, items.artist, items.title, 
items.itemid,
order_items.order_itemid, order_items.distro_orderid, order_items.itemid, order_items.cost, order_items.quantity,
distro_orders.distro_orderid, distro_orders.distroid, distro_orders.order_date  FROM  items, order_items, distro_orders,
distributors  WHERE  order_items.distro_orderid=distro_orders.distro_orderid  AND items.itemid=order_items.itemid AND
distributors.distroid=distro_orders.distroid AND order_items.itemid='$itemid'  LIMIT   
$lower, $number",$db);


     if ($myrow = mysql_fetch_array($result))
     {

       echo "<table border=0 cellspacing=0 cellpadding=3>\n";

       echo "<tr><td bgcolor=ffcc00 colspan=5><font color=ff0000><b>Current
Reviews</b></font></td></tr>\n";
       echo "<tr bgcolor=\"000066\">
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?sort=order_itemid&lower=$lower&number=$number&desc=$desc\">Order_ItemID</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?sort=distro_orderid&lower=$lower&number=$number&desc=$desc\">Distro Order</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?sort=itemid&lower=$lower&number=$number&desc=$desc\">Item</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?sort=cost&lower=$lower&number=$number&desc=$desc\">Cost</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?sort=quantity&lower=$lower&number=$number&desc=$desc\">Quantity</a></td>

             </tr>\n";

do
       {
        printf("<tr><td>%s</td> <td>%s %s %s</td> <td>%s %s - %s - %s</td><td>%s</td><td>%s</td> ",
        $myrow["order_itemid"], $myrow["distro_orderid"], $myrow["name"], $myrow["order_date"],
$myrow["itemid"],$myrow["artist"],$myrow["title"], $myrow["label"],$myrow["cost"],
$myrow["quantity"]);
      
        printf("<td><a
href=\"%s?order_itemid=%s&delete=yes&sort=$sort&lower=$lower&number=$number&desc=$desc\">(DELETE)</a></td><td><a
href=\"%s?order_itemid=%s&sort=$sort&lower=$lower&number=$number&desc=$desc\">(EDIT)</a></td></tr>",
        $PHP_SELF,$myrow["order_itemid"],$PHP_SELF,$myrow["order_itemid"]);

       } while ($myrow=mysql_fetch_array($result));
       echo "</table>\n";
      }
       
     echo "<p>";
      
     }

?>

     <form method="post" name="addform" action="<?php echo $PHP_SELF?>" >
       
     <?
     
     if ($order_itemid)
     { 
      
     // editing so select a record
       

     $sql = "SELECT * FROM order_items WHERE order_itemid='$order_itemid'";

     $result = mysql_query($sql);

     $myrow = mysql_fetch_array($result);

     $distro_orderid = $myrow["distro_orderid"];

     $itemid = $myrow["itemid"];

     $cost = $myrow["cost"];

     $quantity = $myrow["quantity"];

     // print the id for editing

     ?>

     <input type=hidden name="order_itemid" value="<?php echo $order_itemid; ?>">

     <?
     }

     ?>

     Fill in all fields to add a new track<br>     *'d fields are optional.<p>
     <table>


    <tr><td>
     <font color="000066">

     <a href="admindistro_orders.php">Distro Order</a></td>
     <td>
     <select name="distro_orderid" size="1">

     <?
      $sql = "SELECT distro_orderid, distro_orders.distroid, distributors.distroid, name, order_date FROM 
distro_orders,
distributors WHERE distro_orders.distroid=distributors.distroid";
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
     <font color="000066">
     
     <a href="adminitem.php">Item</a></td>
     <td>
     <select name="itemid" size="1">
       
     <?
      $sql = "SELECT itemid, artist, title, label, catalog, format FROM items";
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
     <font color="000066">
     Cost</td>
     <td>
     <input type="Text" name="cost" value="<? echo $myrow["cost"] ?>">
     </td>
     </tr>
     
     <tr><td>
     <font color="000066">
     Quantity</td>
     <td>
     <input type="Text" name="quantity" value="<? echo $myrow["quantity"] ?>">
     </td>
     </tr>
       
     <tr><td>
        <input type="hidden" name="sort" value="<? echo $sort ?>">
        <input type="hidden" name="lower" value="<? echo $lower ?>">
        <input type="hidden" name="number" value="<? echo $number ?>">
        <input type="hidden" name="desc" value="<? echo $desc ?>">
        <input type="Submit" name="submit" value="Enter information"></td></tr>
       
     </table>
     </form>
<?
echo "<P><a href=\"adminitem.php?sort=$sort&lower=$lower&number=$number&desc=$desc\">back to item admin</a>";     
  ?>   
     
</body>
     
</html>
