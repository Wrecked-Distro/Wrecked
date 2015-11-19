<?php
// adminsales_items.php
// tools to manage items that are part of a sales order
// written by geoff maddock <wrecked@rhinoplex.org>
// revision 2-22-05

// include outside functions
include_once("db.php");

//initialize variables

// LOCAL FUNCTIONS

function confirmAll($sales_orderid)
{
 dbConnect();
 $sql = "SELECT * FROM sales_items WHERE sales_orderid = '$sales_orderid' ";

 $result = mysql_query($sql);

 if ($myrow = mysql_fetch_array($result))
 {
   do {
        $sales_itemid = $myrow["sales_itemid"];
        $itemid = $myrow["itemid"];
        $confirm = $myrow["confirm"];
        $quantity = $myrow["quantity"];

        if (confirm == 0)
        { // subtract item from live db

         $sql_remove = "UPDATE items SET quantity = quantity - '$quantity' WHERE itemid='$itemid'";
         $remove = mysql_query($sql_remove) or die("Unable to modify item quantity");
         echo "Updated quantity in catalog for itemid = $itemid<br>";
        };

    	  $sqlUpdate = "UPDATE sales_items SET confirm = 1 WHERE sales_itemid='$sales_itemid'";
    	  $resultUpdate = mysql_query($sqlUpdate) or die("Unable to confirm item");
    	  echo "Confirmed item, sales_itemid = $sales_itemid<p>";

	} while ($myrow = mysql_fetch_array($result));
 };
};

function getStatus($sales_itemid)
{
   dbConnect();

   $sql = "SELECT confirm FROM sales_items WHERE sales_itemid=$sales_itemid";
   $result = mysql_fetch_array(mysql_query($sql));

   $status=$result["confirm"];

   return $status;

}

// re-adds an item to the database when it has been deleted from an order

function readdItem($sales_itemid)
{
  dbConnect();

  $sql1 = sprintf("SELECT * FROM sales_items, items WHERE sales_itemid = %s AND sales_items.itemid = items.itemid", $sales_itemid);

  $result1 = mysql_fetch_array(mysql_query($sql1));

  $itemid = $result1["itemid"];
  $quantity = $result1["quantity"];
  $discount = $result1["discount"];

  echo "itemid = $itemid<p>";
  echo "quantity = $quantity<p>";

  $total = calcPrice($result1,$discount);


  $sql = "UPDATE items SET quantity = quantity + $quantity WHERE itemid=$itemid AND confirm=1";

  $result = mysql_query($sql);

}


// subtracts the cost of removed items from a sales order

function modifyCost($sales_orderid, $sales_itemid)
{
  dbConnect();

  $sql1= "SELECT * FROM sales_items, items WHERE sales_itemid=$sales_itemid AND sales_items.itemid= items.itemid";
  $result1 = mysql_fetch_array(mysql_query($sql1));

  $itemid = $result1["itemid"];
  $quantity = $result1["quantity"];
  $discount = $result1["discount"];
  $total = calcPrice($result1,$discount);

  echo "sales_orderid = $sales_orderid<p>";
  echo "sales_itemid = $sales_itemid<p>";
  echo "quantity = $quantity<p>";


  $sql = "UPDATE sales_orders SET order_cost = order_cost - $total WHERE sales_orderid=$sales_orderid";

  $result = mysql_query($sql);

}


// adds the cost of an item to a sales order

function addCost($sales_orderid, $sales_itemid)
{
  dbConnect();

  $sql1= "SELECT * FROM sales_items, items WHERE sales_itemid=$sales_itemid AND sales_items.itemid= items.itemid";
  $result1 = mysql_fetch_array(mysql_query($sql1));

  $itemid = $result1["itemid"];
  $quantity = $result1["quantity"];
  $discount = $result1["discount"];
  $total = calcPrice($result1,$discount);

  echo "sales_orderid = $sales_orderid<p>";
  echo "sales_itemid = $sales_itemid<p>";
  echo "quantity = $quantity<p>";

  $sql = "UPDATE sales_orders SET order_cost=order_cost+$total WHERE sales_orderid=$sales_orderid";

  $result = mysql_query($sql);

}

// returns the most recently added item on a sales order

function recentItem($sales_orderid)
{
 dbConnect();
 $sql = "SELECT sales_itemid FROM sales_items WHERE sales_orderid='$sales_orderid' ORDER BY sales_itemid DESC LIMIT 1";
 $result = mysql_fetch_array(mysql_query($sql));

 $mostrecentItem = $result["sales_itemid"];

 return $mostrecentItem;
}

    $state = isset($_REQUEST['add']) ? $_REQUEST['add'] : "OPEN";

    dbConnect();

    // if confirmall flag, process all items

    if ($_REQUEST['confirmall'])
    {
      confirmAll($_REQUEST['sales_orderid']);
    };


   if ($_REQUEST['submit'])
   {
    // here if no ID then adding, else we're editing

     if ($_REQUEST['sales_itemid'])
     {

    	$sales_itemid = $_REQUEST['sales_itemid'];
    	$sales_orderid = $_REQUEST['sales_orderid'];

	     // current status is unconfirmed and a confirmation was requested

      if ((getStatus($sales_itemid) == 0) && ($_REQUEST['confirm'] == 1))

      {
		      // subtract item from live db
      		$sql = "UPDATE items SET quantity = quantity - ".$_REQUEST['quantity']." WHERE itemid='".$_REQUEST['itemid']."'";
      		$remove = mysql_query($sql);

      		echo "REMOVED ITEM<p>";

      };

      if ((getStatus($sales_itemid)==1) && ($confirm==0))
      {
        // re-add item to live db

      	$sql = "UPDATE items SET quantity = quantity + ".$_REQUEST['quantity']." WHERE itemid='".$_REQUEST['itemid']."'";
		    $readd = mysql_query($sql);

		    echo "ADDED ITEM<p>";
      };

      $sql = "UPDATE sales_items SET sales_orderid='".$_REQUEST['sales_orderid']."', itemid = '".$_REQUEST['itemid']."', quantity='".$_REQUEST['quantity']."', confirm='".$_REQUEST['confirm']."', discount='".$_REQUEST['discount']."' WHERE sales_itemid='".$_REQUEST['sales_itemid']."'";

      echo "Update of ".$order_itemid."\n sales_orderid=".$sales_orderid." \n";

     } else  {
      $sql = "INSERT INTO sales_items (sales_itemid, sales_orderid, itemid, quantity, confirm, discount)
      VALUES  (0,'".$_REQUEST['sales_orderid']."','".$_REQUEST['itemid']."','".$_REQUEST['quantity']."','".$_REQUEST['confirm']."','".$_REQUEST['discount']."')";

      $add = 1;
     }

      $result = mysql_query($sql);

      echo "Record updated.<p>";

      if ($add == 1)
      {
      	$mostrecentItem = recentItem($_REQUEST['sales_orderid']);

      	addCost($_REQUEST['sales_orderid'], $mostrecentItem);

      	echo "Inserting ".$mostrecentItem." into order ".$sales_orderid." <br>";
      };

      echo "<a href=\"".$_SERVER['PHP_SELF']."?sales_orderid=".$_REQUEST['sales_orderid']."&sort=$sort&lower=$lower&number=$number&desc=$desc\">more items</a>";

     } elseif ($_REQUEST['delete']) {

       // delete a record

       $getinfosql = "SELECT * FROM sales_items WHERE sales_itemid = ".$_REQUEST['sales_itemid'];

       $getinforesult = mysql_fetch_array(mysql_query($getinfosql));

       $sales_orderid = $getinforesult["sales_orderid"];

       echo "<p>modify cost</p>";

       modifyCost($sales_orderid, $_REQUEST['sales_itemid']);

    	 readdItem($sales_itemid);

       $sql = "DELETE FROM sales_items WHERE sales_itemid='".$_REQUEST['sales_itemid']."'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";


      echo "<a href=\"".$_SERVER['PHP_SELF']."?sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";

     } else {

     $sql = sprintf("SELECT items.label, items.artist, items.title, items.catalog, items.itemid,
items.retail, items.cost, items.format, sales_items.sales_itemid, sales_items.sales_orderid, sales_items.itemid,
sales_items.quantity, sales_items.confirm, sales_items.discount
FROM  items, sales_items
WHERE items.itemid=sales_items.itemid
AND sales_items.sales_orderid = '%s'", $_REQUEST['sales_orderid']);

     $result = mysql_query($sql);

     //echo "<b>SQL</b> ".$sql;

     if ($myrow = mysql_fetch_array($result))
     {

       echo "<table border=0 cellspacing=0 cellpadding=3>\n";

       echo "<tr><td class=\"title1\" colspan=7><b>Current
Items in Order</b></td></tr>\n";
       echo "<tr class=\"title2\">
             <td><a href=\"".$_SERVER['PHP_SELF']."?sort=order_itemid&lower=$lower&number=$number&desc=$desc\">Sales_ItemID</a></td>
             <td><a href=\"".$_SERVER['PHP_SELF']."?sort=distro_orderid&lower=$lower&number=$number&desc=$desc\">Sales OrderID</a></td>
             <td><a href=\"".$_SERVER['PHP_SELF']."?sort=itemid&lower=$lower&number=$number&desc=$desc\">Item</a></td>
             <td><a href=\"".$_SERVER['PHP_SELF']."?sort=quantity&lower=$lower&number=$number&desc=$desc\">Quantity</a></td>
             <td><a href=\"".$_SERVER['PHP_SELF']."?sort=cost&lower=$lower&number=$number&desc=$desc\">Cost</a></td>
             <td><a href=\"".$_SERVER['PHP_SELF']."?sort=cost&lower=$lower&number=$number&desc=$desc\">Confirm</a></td>
             <td><a href=\"".$_SERVER['PHP_SELF']."?sort=cost&lower=$lower&number=$number&desc=$desc\">Discount</a></td>
             </tr>\n";

do {
	   $discount = $myrow["discount"];
     printf("<tr><td>%s</td> <td>%s</td> <td>%s %s - %s - %s %s</td><td>%s</td><td>%s</td>",
     $myrow["sales_itemid"], $myrow["sales_orderid"], $myrow["itemid"], $myrow["artist"],$myrow["title"],
$myrow["label"],$myrow["catalog"],$myrow["quantity"], calcPrice($myrow,$discount));
        if ($myrow["confirm"]==0)
	{ echo "<td>no</td>";}
	else
	{ echo "<td>yes</td>";};

	echo "<td>".$discount."</td>";
        printf("<td><a href=\"%s?sales_itemid=%s&delete=yes&sort=$sort&lower=$lower&number=$number&desc=$desc\">(DELETE)</a></td><td><a
href=\"%s?sales_itemid=%s&sales_orderid=%s&sort=$sort&lower=$lower&number=$number&desc=$desc\">(EDIT)</a></td></tr>",
        $_SERVER['PHP_SELF'],$myrow["sales_itemid"],$_SERVER['PHP_SELF'],$myrow["sales_itemid"],$myrow["sales_orderid"]);

       } while ($myrow=mysql_fetch_array($result));
       echo "</table>\n";
      }

     echo "<p>";

     }

?>
<b>Order Items from Pending Sales - Admin</b>

     <form method="post" name="addform" action="<?php echo $_SERVER['PHP_SELF']?>" >

     <?

     if ($_REQUEST['sales_orderid'])
     {
     echo "<a href=\"".$_SERVER['PHP_SELF']."?module=$module&sales_orderid=".$_REQUEST['sales_orderid']."&confirmall=1&sort=$sort&lower=$lower&number=$number&desc=$desc\">confirm all items</a><p>";
     };

     if ($_REQUEST['sales_itemid'])
     {

     // editing so select a record


     $sql = "SELECT * FROM sales_items WHERE sales_itemid='".$_REQUEST['sales_itemid']."'";

     $result = mysql_query($sql);

     $myrow = mysql_fetch_array($result);

     $sales_orderid = $myrow["sales_orderid"];

     $itemid = $myrow["itemid"];

     $quantity = $myrow["quantity"];

     $confirm = $myrow["confirm"];

     $discount = $myrow["discount"];

     // print the id for editing

     ?>

     <input type=hidden name="sales_itemid" value="<?php echo $_REQUEST['sales_itemid']; ?>">

     <?
     }

     ?>

     <table>
    <tr><td><a href="adminsales_orders.php">Sales Order</a></td>
     <td>
     <select name="sales_orderid" size="1">
     <?
      $sql = "SELECT * FROM sales_orders, users WHERE sales_orders.userid = users.userid AND sales_orderid='".$_REQUEST['sales_orderid']."'";
      $result = mysql_query($sql);

      if ($orderlist = mysql_fetch_array($result))
      {
      	do
      	{
       	echo "<option value=\"".$orderlist["sales_orderid"]."\" ";
       	if ($orderlist["sales_orderid"] == $myrow["sales_orderid"])
        	{
			echo "selected";
		};
       	echo ">".$orderlist["sales_orderid"]." ".$orderlist["username"]." ".$orderlist["order_date"];
      	} while ($orderlist=mysql_fetch_array($result));
      };
     ?>
     </select>
     </td></tr>


    <tr><td><a href="adminitem.php">Item</a></td>
     <td>
     <select name="itemid" size="1">

     <?
      $sql = "SELECT itemid, artist, title, label, catalog, format, quantity FROM items ORDER BY artist";
      $result = mysql_query($sql);

      if ($itemlist=mysql_fetch_array($result))
      {
      	do
      	{
       	echo "<option value=\"".$itemlist["itemid"]."\" ";
       	if ($itemlist["itemid"] == $itemid)
        	{
			echo "selected";
		};
       	echo ">".$itemlist["artist"]." - ".$itemlist["title"]." - ".$itemlist["label"]." ".$itemlist["catalog"]." - ".$itemlist["format"]."(".$itemlist["quantity"].")";
      } while ($itemlist=mysql_fetch_array($result));
      };
     ?>

     </select>
     </td></tr>

     <tr><td>
     <font class=\"text3\">
     Quantity</td>
     <td>
     <input type="Text" name="quantity" value="<? echo $myrow["quantity"] ?>">
     </td>
     </tr>

     <tr><td>
     <font class=\"text3\">
     Confirm *</td>

     <td>
     <select name="confirm" size="1">
     <option value="0" <? if ($confirm=="0") echo "selected"; ?>>No
     <option value="1" <? if ($confirm=="1") echo "selected"; ?>>Yes
     </select>
     </td></tr>

    <tr><td>
     <font class=\"text3\">

     Discount</a></td>
     <td>
     <select name="discount" size="1">
     <?
      $sql = "SELECT * FROM discount";
      $result = mysql_query($sql);

      if ($discountlist = mysql_fetch_array($result))
      {
      	do
      	{
       	echo "<option value=\"".$discountlist["discountID"]."\" ";
       	if ($discountlist["discountID"] == $myrow["discount"])
        	{
			echo "selected";
		};
       	echo ">".$discountlist["discountID"]." ".$discountlist["discountNAME"];
      	} while ($discountlist = mysql_fetch_array($result));
      };
     ?>
     </select>
     </td></tr>


     <tr><td>
	 <input type="hidden" name="module" value="<? echo $module ?>">
        <input type="hidden" name="sales_orderid" value="<? echo $_REQUEST['sales_orderid']; ?>">
        <input type="hidden" name="sort" value="<? echo $sort ?>">
        <input type="hidden" name="lower" value="<? echo $lower ?>">
        <input type="hidden" name="number" value="<? echo $number ?>">
        <input type="hidden" name="desc" value="<? echo $desc ?>">
        <input type="Submit" name="submit" value="Enter information" class="button1"></td></tr>

     </table>
     </form>
<?
echo "<P><a href=\"?module=adminsales_items.php&sort=$sort&lower=$lower&number=$number&desc=$desc\">back to sales item admin</a><P>";
echo "<P><a href=\"?module=adminsales_orders_pending.php&sort=$sort&lower=$lower&number=$number&desc=$desc\">back to sales orders admin</a><P>";
  ?>

</body>

</html>
