<?php

function showHistory($username,$sales_orderid)
{
	// shows a list of previous orders

	echo "<font size=+1><b>PAST ORDERS</b></font><p>";

	dbConnect();

	$sql = "SELECT *, DATE_FORMAT(order_date,'%M %D, %Y') AS order_date, DATE_FORMAT(sent_date,'%M %D, %Y') AS sent_date FROM
sales_orders, users WHERE users.username='$username' AND sales_orders.userid=users.userid ORDER BY sales_orders.order_date
DESC";
	$result = mysql_query($sql);

	if ($myrow=mysql_fetch_array($result))
	{
		echo "<table border=0 cellspacing=0 cellpadding=3><tr><td bgcolor=ffcc00 colspan=5><font color=ff0000><b>Past Order Information</b></tr><tr bgcolor=000066>
<td><font color=00cccc><b>ID</td>
<td><font color=00cccc><b>Order Date</td>
<td><font color=00cccc><b>Send Date</td>
<td><font color=00cccc><b>Total</td>
<td><font color=00cccc><b>Status</td></tr>";

		do
		{
			echo "<tr>
 <td>".$myrow["sales_orderid"]."</td>
 <td>".$myrow["order_date"]."</td>
 <td>".$myrow["sent_date"]."</td>
<td>$".($myrow["order_cost"]+$myrow["tax_cost"]+$myrow["shipping_cost"])."</td>";

			if ($myrow["paid"])
			{
				if ($myrow["sent"])
				{ echo "<td>Complete</td>"; }
				else
				{ echo "<td>Paid</td>";};
			}
			else
			{
				if ($myrow["confirm"])
				{ echo "<td>Confirmed</td>";}
				else
				{echo "<td>Ordered</td>";};
			};

			echo "<td>(<a href=\"login.php?code=PAST&sales_orderid=".$myrow["sales_orderid"]."\">DETAILS</a>)</td></tr>";
		} while ($myrow=mysql_fetch_array($result));

		echo "</table>";

		echo "<P>";

		if ($sales_orderid)
		{ printOrder($username, $sales_orderid);};
	}
	else
	{
		echo "No Past Orders.";
	};
}


function printOrder($username, $sales_orderid)
{

// shows all currently pending and past orders

echo "<font size=+1><b>ORDER DETAIL</b></font><p>";

dbConnect();


// get sales orders information

$sqlsale = "SELECT *, DATE_FORMAT(order_date,'%M %D, %Y') AS order_format, DATE_FORMAT(paid_date,'%M %D, %Y') AS paid_date,
DATE_FORMAT(sent_date,'%M %D, %Y') AS sent_date FROM sales_orders WHERE sales_orderid=$sales_orderid ORDER BY sales_orderid
DESC";

$saleresult = mysql_query($sqlsale);

if ($salerow=mysql_fetch_array($saleresult))
{
do
   {

    if ($salerow["sent"]) {$tablecolor="ffbb00";} else {$tablecolor="ffee00";};

    echo "<table bgcolor=$tablecolor width=500><tr><td>";

  $total = $salerow["order_cost"]+$salerow["shipping_cost"]+$salerow["tax_cost"];
    echo "<font size=+1><b>Order #:</b> ".$salerow["sales_orderid"]." <br>";
    echo "<b>Date:</b> ".$salerow["order_format"]."<br>";
    echo "<b>Order Status:</b> ";

    if ($salerow["confirm"])
    {
     if ($salerow["paid"])
     {
      if ($salerow["sent"])
       { echo "Complete! Package Sent on ".$salerow["sent_date"]."<br><b>What You Do:</b> Let me know when it arrives!
Enjoy.<br>";}
       else
       { echo "Payment received on ".$salerow["paid_date"].". Pending shipping/pickup/delivery.<br>";
         if ($salerow["shipping_method"]==4) {echo "<b>What You Do:</b> Meet for pickup/wait for delivery.<br>";}
          else {echo "<b>What You Do:</b> Wait for shipping confirmation.<br>";};
       };
     }
     else
       { echo "Order confirmed.  Pending payment.<br>";
         if ($salerow["billing_method"]==1) {echo "<b>What You Do:</b> Get me the cash. Total <b>$".$total."</b><p>
Send to:<br>WRECKED<br>3826 East Street<br>Pittsburgh PA 15214<br>USA<p>";};
         if ($salerow["billing_method"]==3)
        {echo "<b>What You Do:</b> Paypal <b>$".$total."</b> to wrecked@rhinoplex.org. ";
echo "<br>
Click the paypal icon to pay now.
<form name=\"_xclick\" target=\"paypal\" action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">
<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">
<input type=\"hidden\" name=\"business\" value=\"wrecked@rhinoplex.org\">
<input type=\"hidden\" name=\"item_name\" value=\"Order #".$salerow["sales_orderid"]." - ".$username."\">
<input type=\"hidden\" name=\"item_number\" value=\"1\">
<input type=\"hidden\" name=\"amount\" value=\"".$total."\">
<input type=\"image\" src=\"../images/x-click-but02.gif\" border=\"0\" name=\"submit\"
alt=\"Make payments with PayPal - it's fast, free and secure!\">

<input type=\"hidden\" name=\"add\" value=\"1\"> </form><br>";
 };
         if ($salerow["billing_method"]==4) {echo "<b>What You Do:</b> Make check for $".$total." payable to Geoff
Maddock.<p>Send to:<br>WRECKED<br> 3826 East Street<br>Pittsburgh PA 15214<br>USA<br>";};
         if ($salerow["billing_method"]==5) {echo "<b>What You Do:</b> Make money order for $".$total." payable to Geoff
Maddock.<p>Send to:<br>WRECKED<br> 3826 East Street<br>Pittsburgh PA 15214<br>USA<br>";};

        };
    }
    else
    { echo "Order submitted. Pending confirmation.<br><b>What You Do:</b> Wait for confirmation email, or check this page
for confirmation of order.<br>";};

    echo "</font><P>";
    showSoldItems($salerow["sales_orderid"]);


echo "<b>Billing Method</b><br> ";

      $bill=$salerow["billing_method"];

      $billsql = "SELECT * FROM billing_method WHERE billing_methodid='$bill'";
      $billresult = mysql_fetch_array(mysql_query($billsql));
      $billing_method = $billresult["name"];

echo $billing_method."<br>";

echo "<p><b>Shipping Method</b><br> ";

      $ship=$salerow["shipping_method"];

      $shipsql = "SELECT * FROM shipping WHERE shippingid='$ship'";
      $shipresult = mysql_fetch_array(mysql_query($shipsql));
      $shipping_method = $shipresult["type"];

echo $shipping_method."<p>";

echo "<b>Shipping Address</b><br>";
echo $salerow["ship_name"]."<br>";
echo $salerow["ship_address"]."<br>";
echo $salerow["ship_city"].", ".$salerow["ship_state"]." ".$salerow["ship_zip"]."<br>";
echo $salerow["ship_country"]."<br>";

  echo "<p><b>Note</b><br>";
  if ($salerow["note"])
  {echo $salerow["note"]."<p>";}
  else
  {echo "No special instructions<p>";};
  echo "<b>Sub Total:</b> $".$salerow["order_cost"]."<br>";
  echo "<b>Tax (PA Residents):</b> $".$salerow["tax_cost"]."<br>";
  echo "<b>Shipping:</b> $".$salerow["shipping_cost"]."<br>";
  echo "<b>Grand Total:</b> $".($salerow["order_cost"]+$salerow["shipping_cost"]+$salerow["tax_cost"]);
  echo "</td></tr></table>";
  echo "<P>---<P>";
   } while ($salerow=mysql_fetch_array($saleresult));
}
else
{ echo "No past or pending orders.";};

}



function showAddress($username)
{
// shows the mailing address for the user

dbConnect();

$sqluser = "SELECT * FROM users WHERE username='$username'";
$userinfo = mysql_fetch_array(mysql_query($sqluser));
$userfirst = $userinfo["first_name"];
$userlast = $userinfo["last_name"];
$useraddress = $userinfo["address"];
$usercity = $userinfo["city"];
$userstate = $userinfo["state"];
$userzip = $userinfo["zip"];
$usercountry = $userinfo["country"];

echo "<b>Shipping Address</b><br>";
echo $userfirst." ".$userlast."<br>";
echo $useraddress."<br>";
echo $usercity.", ".$userstate." ".$userzip."<br>";
echo $usercountry."<br>";
}


function showOrders($username)
{

// shows all currently pending orders

echo "<font size=+1><b>ORDER STATUS</b></font><p>";

dbConnect();

// first, get the userid for the order

$sqluser = "SELECT * FROM users WHERE username='$username'";
$userinfo = mysql_fetch_array(mysql_query($sqluser));
$userid = $userinfo["userid"];

// show all sales orders

$sqlsale = "SELECT *, DATE_FORMAT(order_date,'%M %D, %Y') AS order_format, DATE_FORMAT(paid_date,'%M %D, %Y') AS paid_date,
DATE_FORMAT(sent_date,'%M %D, %Y') AS sent_date FROM sales_orders WHERE userid='$userid' AND
(TO_DAYS(current_date)-TO_DAYS(sent_date)) < 14 ORDER BY sales_orderid DESC";
$saleresult = mysql_query($sqlsale);

if ($salerow=mysql_fetch_array($saleresult))
{
do
   {

    if ($salerow["sent"]) {$tablecolor="ffbb00";} else {$tablecolor="ffee00";};

    echo "<table bgcolor=$tablecolor width=500><tr><td>";

    $total = $salerow["order_cost"]+$salerow["shipping_cost"]+$salerow["tax_cost"];
    echo "<font size=+1><b>Order #:</b> ".$salerow["sales_orderid"]." <br>";
    echo "<b>Date:</b> ".$salerow["order_format"]."<br>";
    echo "<b>Order Status:</b> ";

    if ($salerow["confirm"])
    {
     if ($salerow["paid"])
     {
      if ($salerow["sent"])
       { echo "Complete! Package Sent on ".$salerow["sent_date"]."<br><b>What You Do:</b> Let me know when it arrives! Enjoy.<br>";}
       else
       { echo "Payment received on ".$salerow["paid_date"].". Pending shipping/pickup/delivery.<br>";
         if ($salerow["shipping_method"]==4) {echo "<b>What You Do:</b> Meet for pickup/wait for delivery.<br>";}
	  else {echo "<b>What You Do:</b> Wait for shipping confirmation.<br>";};
       };
     }
     else
       { echo "Order confirmed.  Pending payment.<br>";
         if ($salerow["billing_method"]==1) {echo "<b>What You Do:</b> Get me the cash. Total <b>$".$total."</b><p>
Send to:<br>WRECKED<br>3826 East Street<br>Pittsburgh PA 15214<br>USA<p>";};
         if ($salerow["billing_method"]==3)
	{echo "<b>What You Do:</b> Paypal <b>$".$total."</b> to wrecked@rhinoplex.org. ";
echo "<br>
Click the paypal icon to pay now.
<form name=\"_xclick\" target=\"paypal\" action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">
<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">
<input type=\"hidden\" name=\"business\" value=\"wrecked@rhinoplex.org\">
<input type=\"hidden\" name=\"item_name\" value=\"Order #".$salerow["sales_orderid"]." - ".$username."\">
<input type=\"hidden\" name=\"item_number\" value=\"1\">
<input type=\"hidden\" name=\"amount\" value=\"".$total."\">
<input type=\"image\" src=\"../images/x-click-but02.gif\" border=\"0\" name=\"submit\"
alt=\"Make payments with PayPal - it's fast, free and secure!\">

<input type=\"hidden\" name=\"add\" value=\"1\"> </form><br>";
 };
         if ($salerow["billing_method"]==4) {echo "<b>What You Do:</b> Make check for $".$total." payable to Geoff
Maddock.<p>Send to:<br>WRECKED<br> 3826 East Street<br>Pittsburgh PA 15214<br>USA<br>";};
         if ($salerow["billing_method"]==5) {echo "<b>What You Do:</b> Make money order for $".$total." payable to Geoff
Maddock.<p>Send to:<br>WRECKED<br> 3826 East Street<br>Pittsburgh PA 15214<br>USA<br>";};

	};
    }
    else
    { echo "Order submitted. Pending confirmation.<br><b>What You Do:</b> Wait for confirmation email, or check this page
for confirmation of order within 24-48 hours.<br>";};

    echo "</font><P>";
    showSoldItems($salerow["sales_orderid"]);

echo "<b>Billing Method</b><br> ";

      $bill=$salerow["billing_method"];

      $billsql = "SELECT * FROM billing_method WHERE billing_methodid='$bill'";
      $billresult = mysql_fetch_array(mysql_query($billsql));
      $billing_method = $billresult["name"];

echo $billing_method."<br>";

echo "<p><b>Shipping Method</b><br> ";

      $ship=$salerow["shipping_method"];

      $shipsql = "SELECT * FROM shipping WHERE shippingid='$ship'";
      $shipresult = mysql_fetch_array(mysql_query($shipsql));
      $shipping_method = $shipresult["type"];

echo $shipping_method."<p>";

echo "<b>Shipping Address</b><br>";
echo $salerow["ship_name"]."<br>";
echo $salerow["ship_address"]."<br>";
echo $salerow["ship_city"].", ".$salerow["ship_state"]." ".$salerow["ship_zip"]."<br>";
echo $salerow["ship_country"]."<br>";


  echo "<p><b>Note</b><br>";
  if ($salerow["note"])
  {echo $salerow["note"]."<p>";}
  else
  {echo "No special instructions<p>";};
  echo "<b>Sub Total:</b> $".$salerow["order_cost"]."<br>";
  echo "<b>Tax (PA Residents):</b> $".$salerow["tax_cost"]."<br>";
  echo "<b>Shipping:</b> $".$salerow["shipping_cost"]."<br>";
  echo "<b>Grand Total:</b> $".($salerow["order_cost"]+$salerow["shipping_cost"]+$salerow["tax_cost"]);
  echo "</td></tr></table>";
  echo "<P>---<P>";
   } while ($salerow=mysql_fetch_array($saleresult));
}
else
{ echo "No past or pending orders.";};

}

function showSoldItems($sales_orderid)
{
// show a table containing all the items in an order to be processed

dbConnect();

$sql = "SELECT *, sales_items.quantity AS quantity, items.quantity AS instock FROM sales_items, items WHERE
sales_items.itemid=items.itemid AND sales_items.sales_orderid='$sales_orderid' ORDER BY sales_items.sales_itemid DESC";
$result = mysql_query($sql);
   if ($myrow=mysql_fetch_array($result))
{
  echo
"<table border=0 cellspacing=0 cellpadding=3><tr><td bgcolor=ffcc00 colspan=8>
<font color=ff0000><b>Order Contents</b></tr>
<tr bgcolor=000066>
<td><font color=00cccc><b>Category</td>
<td><font color=00cccc><b>Format</td>
<td><font color=00cccc><b>Artist</td>
<td><font color=00cccc><b>Title</td>
<td><font color=00cccc><b>Label</td>
<td><font color=00cccc><b>Cond</td>
<td><font color=00cccc><b>#</td>
<td><font color=00cccc><b>Cost</td></tr>";

 $total=0;

  do
   {
    echo
"<tr>
 <td>".$myrow["category"]."</td>
 <td>".$myrow["format"]."</td>
 <td>".$myrow["artist"]."</td>
 <td><a href=\"viewitem.php?itemselect=".$myrow["itemid"]."\">".$myrow["title"]."</a></td>
 <td>".$myrow["label"]."</td>
 <td>".$myrow["condition"]."</td>
 <td>".$myrow["quantity"]."</td>";

 echo "<td>$".$myrow["retail"]*$myrow["quantity"]."</td>";

 $total=$total+($myrow["retail"]*$myrow["quantity"]);

  echo "</tr>";
  } while ($myrow=mysql_fetch_array($result));

  echo "<td colspan=5></td><td><b>Total:<b></td><td>$".$total."</td></tr>";
  echo "</table>";
}
else
{echo "No Items in this Order!";};

}


function processOrder($username, $shipping, $billing, $total, $tax, $note)
{

dbConnect();

// first, get the userid for the user processing the order

$sqluser = "SELECT * FROM users WHERE username='$username'";
$userinfo = mysql_fetch_array(mysql_query($sqluser));
$userid = $userinfo["userid"];
$shipname = $userinfo["first_name"]." ".$userinfo["last_name"];
$shipaddress = $userinfo["address"];
$shipcity = $userinfo["city"];
$shipstate = $userinfo["state"];
$shipzip = $userinfo["zip"];
$shipcountry = $userinfo["country"];


// add the sales order to the database

$sqlcreatesale = "INSERT INTO sales_orders (sales_orderid, userid, order_date, confirm, paid_date, paid, sent_date, sent,
order_cost, tax_cost, shipping_cost, shipping_method, billing_method, note, ship_name, ship_address, ship_city, ship_state,
ship_zip, ship_country) VALUES  (0, '$userid', CURRENT_DATE, 0, CURRENT_DATE, 0, CURRENT_DATE, 0, '$total', '$tax',
0,'$shipping', '$billing', '$note', '$shipname','$shipaddress','$shipcity','$shipstate','$shipzip','$shipcountry')";
$createsale=mysql_query($sqlcreatesale);

// retreive the sales id of the new sale

$sqlsale = "SELECT MAX(sales_orderid) as sales_orderid FROM sales_orders WHERE userid='$userid'";
$saleinfo = mysql_fetch_array(mysql_query($sqlsale));
$saleid = $saleinfo["sales_orderid"];

// add the sales items to the sales items database for confirmation

$sqlitems = "SELECT *, temp_orders.quantity AS quantity, items.quantity AS instock, temp_orders.itemid AS itemid FROM
temp_orders, items WHERE userid='$userid' AND temp_orders.itemid=items.itemid ORDER BY temp_orders.timestamp DESC";
$itemresult = mysql_query($sqlitems);

if ($itemrow=mysql_fetch_array($itemresult))
{
 do
 {
  $itemid=$itemrow["itemid"];
  if ($itemrow["quantity"]>$itemrow["instock"]) {$quantity=$itemrow["instock"];} else {$quantity=$itemrow["quantity"];};
  $additem = mysql_query("INSERT INTO sales_items (sales_itemid, sales_orderid, itemid, quantity, confirm) VALUES (0,
'$saleid', '$itemid','$quantity',0)");
 } while ($itemrow=mysql_fetch_array($itemresult));
}
else
{
echo "No items in order";
};

// remove the items from temp_orders

$tempitems = "DELETE FROM temp_orders WHERE userid='$userid'";
$tempresult = mysql_query($tempitems);


// tell the user that its been accepted or rejected, and display the order?

echo "<P><b><i>Your Order has been accepted!  Once we confirm that your order is in stock,<br>
            you'll receive an email with instructions on what to do next.  Expect it within 24-48 hours.</i></b><P>";

showOrders($username);

$to = "wrecked@rhinoplex.org";
$subject = $username." - New Order #".$saleid;
$body = "WRECKED - \n\nNew order submitted for user [".$username."].\n\nOrder #".$saleid."\n\nLog in and confirm this order ASAP.\n
Your friendly automated ordering system.\n\n-YFAOR";

  $from = "From: wrecked@rhinoplex.org\r\n";
  $from .= "Reply-To: wrecked@rhinoplex.org\r\n";
  $from .= "X-Mailer: PHP/".phpversion();

mail($to,$subject,$body,$from);


}


function checkOut($username)
{

echo "<font size=+1><b>CHECK OUT</b></font><p>";

// get userid for user who is checking out

dbConnect();
$sqluser = "SELECT * FROM users WHERE username='$username'";
$userinfo = mysql_fetch_array(mysql_query($sqluser));

// select items from temp orders, as well as item info from items

$sql = "SELECT *, temp_orders.quantity AS quantity, items.quantity AS instock FROM temp_orders, items, users WHERE
users.username='$username' AND temp_orders.userid=users.userid AND temp_orders.itemid=items.itemid ORDER BY
temp_orders.timestamp DESC";

// display results

$result = mysql_query($sql);
   if ($myrow=mysql_fetch_array($result))
{
  echo
"<table border=0 cellspacing=0 cellpadding=3><tr><td bgcolor=ffcc00 colspan=8>
<font color=ff0000><b>Current Shopping Cart Contents</b></tr>
<tr bgcolor=000066>
<td><font color=00cccc><b>Category</td>
<td><font color=00cccc><b>Format</td>
<td><font color=00cccc><b>Artist</td>
<td><font color=00cccc><b>Title</td>
<td><font color=00cccc><b>Label</td>
<td><font color=00cccc><b>Cond</td>
<td><font color=00cccc><b>#</td>
<td><font color=00cccc><b>Cost</td></tr>";

 $total=0;

  do
   {
    echo "<tr>
 <td>".$myrow["category"]."</td>
 <td>".$myrow["format"]."</td>
 <td>".$myrow["artist"]."</td>
 <td><a href=\"viewitem.php?itemselect=".$myrow["itemid"]."\">".$myrow["title"]."</a></td>
 <td>".$myrow["label"]."</td>
 <td>".$myrow["condition"]."</td>";


// show how many are in stock, depending on how many are ordered as opposed to the number actually in

 if ($myrow["instock"]==0)
 {echo "<td><font color=ff0000>OUT</font></td><td>$0</td>";}
 else
 {
   if ($myrow["quantity"]>$myrow["instock"])
   {echo "<td><font color=ff0000>".$myrow["instock"]."</font></td>
 <td>$".$myrow["retail"]*$myrow["instock"]."</td>";
 $total=$total+($myrow["retail"]*$myrow["instock"]);
   }
   else
   { echo "<td>".$myrow["quantity"]."</td> <td>$".$myrow["retail"]*$myrow["quantity"]."</td>";
 $total=$total+($myrow["retail"]*$myrow["quantity"]);
 };
 };
   echo "</tr>";
   } while ($myrow=mysql_fetch_array($result));

  echo "<tr><td>
        </td><td colspan=5></td><td><b>Total:<b></td><td>$".$total."</td></tr>";
  echo "</table>";
}
else
{echo "No Items in your Shopping Cart";};

echo "<form action=\"login.php\"><P>";

echo "<b>Billing Method</b><br>
      <select style=\"font-name: Arial; font-size: 12px;\" name=\"billing_method\" size=\"1\">";

      $sql = "SELECT * FROM billing_method WHERE access=1";
      $result = mysql_query($sql);

      if ($bill=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$bill["billing_methodid"]."\" ";
       if ($bill["billing_methodid"]==$userinfo["billing_method"])
        {echo "SELECTED ";};
       echo ">".$bill["name"];
      } while ($bill=mysql_fetch_array($result));
      };
echo "</select><P>";

echo "<b>Shipping Method</b><br>
      <select style=\"font-name: Arial; font-size: 12px;\" name=\"shipping_method\" size=\"1\">";

      $sql = "SELECT * FROM shipping";
      $result = mysql_query($sql);

      if ($ship=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$ship["shippingid"]."\" ";
       if ($ship["shippingid"]==$userinfo["shipping"])
        {echo "SELECTED ";};
       echo ">".$ship["type"];
      } while ($ship=mysql_fetch_array($result));
      };
echo "</select><p>";

// echo "<B>Delivery Confirmation ($0.50)</a></b> <INPUT TYPE=\"checkbox\" NAME=\"delivery_confirm\" VALUE=\"1\"><P>";
// echo "<B>Insurance ($1.30 up to $50, $2.20 up to $100, $1/$100 more)</a></b> <INPUT TYPE=\"checkbox\"
// NAME=\"delivery_confirm\" VALUE=\"1\"><P>";

  echo "<b>Note</b><br><textarea name=\"note\" cols=\"40\" rows=\"5\" style=\"font-name: Arial; font-size:
12px;\"></textarea><p>";

  echo "<b>Address</b><br>";
  echo $userinfo["first_name"]." ".$userinfo["last_name"]."<br>";
  echo $userinfo["address"]."<br>";
  echo $userinfo["city"].", ".$userinfo["state"]." ".$userinfo["zip"]."<br>";
  echo $userinfo["country"]."<br>";
  echo "(<a href=\"account.php\">Update Info</a>)<P> ";

  $tax=getTax($userinfo["state"],$total);
  $shipping=0;

  echo "<b>Sub Total:</b> $".$total."<br>";
  echo "<b>Tax (PA Residents):</b> $".$tax."<br>";
  echo "<b>Shipping:</b> $".$shipping." (pending) <br>";
  echo "<b>Grand Total:</b> $".($total+$shipping+$tax)." (pending) <p>";
  echo "<input type=\"hidden\" name=\"tax\" value=\"".$tax."\">";
  echo "<input type=\"hidden\" name=\"total\" value=\"".$total."\">";
  echo "<input type=\"hidden\" name=\"code\" value=\"PROCESS\">";
  echo "<input style=\"font-name: Arial; font-size: 14px;\" type=\"submit\" name=\"submit\"
value=\"PROCESS\"></form>";

}


function getTax($state,$total)
{
 if ($state=="PA")
 { return ($total*.07);}
 else
 { return 0;};
}


function showUser($username)
{

  echo "<font size=+1><b>MY INFO</b></font><p>";

  dbConnect();

  $sql = "SELECT *, DATE_FORMAT(start_date,'%b %D, %Y') AS date_format FROM users WHERE username='$username'";
  $userinfo = mysql_fetch_array(mysql_query($sql));

  echo "<b>Username:</b> ".$userinfo["username"]."<br>";
  echo "<b>Name:</b> ".$userinfo["first_name"]." ".$userinfo["last_name"]."<br>";
  echo "<b>Email:</b> ".$userinfo["email"]."<br>";
  echo "<b>Phone:</b> ".$userinfo["phone"]."<br>";
  echo "<p>";

  echo "<b>Address</b><br>";
  echo $userinfo["first_name"]." ".$userinfo["last_name"]."<br>";
  echo $userinfo["address"]."<br>";
  echo $userinfo["city"].", ".$userinfo["state"]." ".$userinfo["zip"]."<br>";
  echo $userinfo["country"]."<p>";

  $sql = "SELECT * FROM billing_method WHERE billing_methodid='".$userinfo["billing_method"]."'";
  $billinginfo = mysql_fetch_array(mysql_query($sql));

  if (!$billinginfo["name"])
  { $billing_method="None";}
  else
  { $billing_method=$billinginfo["name"];};

  echo "<b>Default Billing Method:</b> ".$billing_method."<p>";
  echo "<b>Started on ".$userinfo["date_format"];
  echo "<p>(<a href=\"account.php\">Update Info</a>) ";
}


function showCart($username)
{

  echo "<font size=+1><b>CHECK CART</b></font><p>";

dbConnect();

$sql = "SELECT *, temp_orders.quantity AS quantity, items.quantity AS instock FROM temp_orders, items, users WHERE
users.username='$username' AND temp_orders.userid=users.userid AND temp_orders.itemid=items.itemid ORDER BY
temp_orders.timestamp DESC";
$result = mysql_query($sql);
   if ($myrow=mysql_fetch_array($result))
{
  echo
"<table border=0 cellspacing=0 cellpadding=3><tr><td bgcolor=ffcc00 colspan=8>
<font color=ff0000><b>Current Shopping Cart Contents</b></tr>
<tr bgcolor=000066>
<td><font color=00cccc><b>Category</td>
<td><font color=00cccc><b>Format</td>
<td><font color=00cccc><b>Artist</td>
<td><font color=00cccc><b>Title</td>
<td><font color=00cccc><b>Label</td>
<td><font color=00cccc><b>Cond</td>
<td><font color=00cccc><b>#</td>
<td><font color=00cccc><b>Cost</td></tr>";

 $total=0;

  do
   {
    echo
"<tr>
 <td>".$myrow["category"]."</td>
 <td>".$myrow["format"]."</td>
 <td>".$myrow["artist"]."</td>
 <td><a href=\"viewitem.php?itemselect=".$myrow["itemid"]."\">".$myrow["title"]."</a></td>
 <td>".$myrow["label"]."</td>
 <td>".$myrow["condition"]."</td>";

 if ($myrow["instock"]==0)
 {echo "<td><font color=ff0000>OUT</font></td>";}
 else
 {
   if ($myrow["quantity"]>$myrow["instock"])
   {echo "<td><font color=ff0000>NOT ENOUGH</font></td>";}
   else
   { echo "<td>".$myrow["quantity"]."</td>";};
 };

 echo "
 <td>$".$myrow["retail"]*$myrow["quantity"]."</td>";
 $total=$total+($myrow["retail"]*$myrow["quantity"]);
   echo "<td>(<a href=\"login.php?code=DELETE&itemid=".$myrow["itemid"]."\">REMOVE</a>)</td></tr>";
    } while ($myrow=mysql_fetch_array($result));

  echo "<tr><td>
	<form action=\"login.php\">
       	<input style=\"font-name: Arial; font-size: 14px;\" type=\"hidden\" name=\"code\" value=\"OUT\">
      	<input style=\"font-name: Arial; font-size: 14px;\" type=\"submit\" name=\"submit\" value=\"Submit Order\"></form>
	</td><td colspan=5></td><td><b>Total:<b></td><td>$".$total."</td></tr>";
  echo "</table>";
}
else
{echo "No Items in your Shopping Cart";};

}

function deleteItem($username,$itemid)
{
 dbConnect();

//removes one of the items from an order, or deletes the item entirely

 $check = "SELECT * FROM temp_orders, users WHERE users.username='$username' AND temp_orders.userid=users.userid AND
temp_orders.itemid='$itemid'";
 $checkresult = mysql_query($check);

 $checkrow=mysql_fetch_array($checkresult);
 $quantity=$checkrow["quantity"];
 $userid=$checkrow["userid"];

 if ($quantity>1)
 {
  $sql = "UPDATE temp_orders SET quantity=quantity-1 WHERE userid='$userid' AND itemid='$itemid'";
  $result = mysql_query($sql);
  echo "<i>selections UPDATED</i><P>";
 }
 else
 {
  $sql = "DELETE FROM temp_orders WHERE userid='$userid' AND itemid='$itemid'";
  $result = mysql_query($sql);
  echo "<i>selection REMOVED from your cart</i><P>";
 };

}



function time_format($timestamp)
{
    $hour = substr($timestamp,8,2);
    $minute = substr($timestamp,10,2);
    $second = substr($timestamp,12,2);
    $month = substr($timestamp,4,2);
    $day = substr($timestamp,6,2);
    $year = substr($timestamp,0,4);
    $mktime = mktime($hour, $minute, $second, $month, $day, $year);
    $formated = date("F j, Y, g:i a",$mktime);
    return $formated;
}


//
// if the user is logged in
//


   include("access.php");
   include("header.php");

   echo "<font size=+1><b>".$username."</b></font> is ";
   echo "<B>Logged in</B>&nbsp;&nbsp;&nbsp;";

   echo "<br>(<a href=\"".$PHP_SELF."?code=INFO\">MyInfo</a>) ";
   echo "(<a href=\"".$PHP_SELF."?code=CHECK\">Check Cart</a>) ";
   echo "(<a href=\"".$PHP_SELF."?code=ORDERS\">Current Orders</a>) ";
   echo "(<a href=\"".$PHP_SELF."?code=PAST\">Past Orders</a>) ";
   echo "(<a href=\"logout2.php\">Log out</a>)";

   dbConnect();

   echo "<p>";

   switch ($code)
   {

   case 'DELETE':
			// removes an item from the shopping cart
			deleteItem($username,$itemid);
			showCart($username);
			break;
   case 'CHECK':
			// shows contents of current shopping cart
			showCart($username);
			break;
   case 'OUT':
			// starts check out for current shopping cart
			checkOut($username);
			break;
   case 'PROCESS':
			// processes cart & selections to a final sale
			processOrder($username,$shipping_method, $billing_method, $total, $tax, $note);
			break;
   case 'ORDERS':
			// shows current and completed orders
			showOrders($username);
			break;
   case 'PAST':
			// shows past completed orders
			showHistory($username,$sales_orderid);
			break;

   case 'INFO':
			// displays current user information
			showUser($username);
			break;

   };



?>
