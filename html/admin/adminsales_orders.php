<?php
// admin sales orders
// updated by geoff maddock june 27th 2005
// functions to update and manage wrecked sales orders

function getWeight($itemid)
{
 // returns the weight given an item

 dbConnect();

 $sql = "SELECT format.weight AS weight FROM items, format WHERE items.format=format.name AND items.itemid='$itemid'";
 $result=mysql_fetch_array(mysql_query($sql));

 $weight=$result["weight"];
 return $weight;
};

function cancelOrder($sales_orderid)
{
 // cancels the order w/o deleting
 // removes all approved items, and sets them to unapproved
 // sets cancelled flag to 1
 // adds a note of when the order was cancelled

 dbConnect();
 $sql = "SELECT * FROM sales_orders WHERE sales_orderid='$sales_orderid'";
 $myorder = mysql_fetch_array(mysql_query($sql));

 if ($myorder["cancelled"]!=1)
 {
 $sql = "SELECT * FROM sales_items WHERE sales_orderid='$sales_orderid'";
 $result = mysql_query($sql);
 if ($myrow=mysql_fetch_array($result))
 	{
		do
		{
		} while ($myrow=mysql_fetch_array($result));
	};
 }
 else
 { echo "Already Cancelled";};

};

function totalWeight($sales_orderid)
{
	// returns a sum of the weight of a sales order
	dbConnect();
	$sql = "SELECT itemid, quantity FROM sales_items WHERE sales_orderid='$sales_orderid'";
	$result = mysql_query($sql);
	$total=.5;

	if ($myrow=mysql_fetch_array($result))
	{
		do
		{
			$itemid=$myrow["itemid"];
			$total=$total+getWeight($itemid);
		} while ($myrow=mysql_fetch_array($result));
	};

	return $total;

};

// emails customer shipping information

function emailShip($sales_orderid)
{
  dbConnect();

  $sql="SELECT * FROM users, sales_orders WHERE sales_orderid='$sales_orderid' AND sales_orders.userid=users.userid";

  $result=mysql_fetch_array(mysql_query($sql));

  $email=$result["email"];
  $username=$result["username"];

 $order_cost = $result["order_cost"];
 $tax_cost = $result["tax_cost"];
 $shipping_cost = $result["shipping_cost"];

 $shipping_method = $result["shipping_method"];
 $billing_method = $result["billing_method"];

 $name = $result["ship_name"];
 $address = $result["ship_address"];
 $city = $result["ship_city"];
 $state = $result["ship_state"];
 $zip = $result["ship_zip"];
 $country = $result["ship_country"];

 $total=$order_cost+$tax_cost+$shipping_cost;
 $total = number_format($total,2,'.','');

  echo "<p>Sending email confirmation to: ".$email."<p>";

  $subject = "WRECKED Order ".$sales_orderid." Shipped";
  $body = $username." -\n\nWe have received your payment, and your order has been shipped.\n
 Order # ".$sales_orderid." - ".$username."\n";

  $from = "From: sales@wrecked-distro.com\r\n";
  $from .= "Reply-To: sales@wrecked-distro.com\r\n";
  $from .= "X-Mailer: PHP/".phpversion();

$sql = "SELECT *, sales_items.quantity AS quantity, items.quantity AS instock FROM sales_items, items WHERE
sales_items.itemid=items.itemid AND sales_items.sales_orderid='$sales_orderid' ORDER BY sales_items.sales_itemid DESC";
$result = mysql_query($sql);
if ($myrow=mysql_fetch_array($result))
{
 $body=$body."\nOrder Contents Category - Format - Artist - Title - Label - Condition - Quant - Cost\n";

  do {
   $body=$body." ".$myrow["category"]." - ".$myrow["format"]." - ".$myrow["artist"]." - ".$myrow["title"]." - ".$myrow["label"]." - ".$myrow["condition"]." - ".$myrow["quantity"]." - $".$myrow["retail"]*$myrow["quantity"]."\n";

  } while ($myrow=mysql_fetch_array($result));

  $body=$body."\nSubtotal: $".$order_cost."\nTax: $".$tax_cost."\nShipping: $".$shipping_cost."\nTotal: $".$total."\n\n";

} else {
	$body=$body."No Items in this Order!\n";
};

  $body=$body."Payment: Received. \n\n";

 // shipping methods
 // 1 - media rate
 // 2 - parcel post
 // 3 - priority
 // 4 - pick up/delivery
 // 5 - international surface
 // 6 - international airmail

switch ($shipping_method)
{
	case 1: $body=$body."Shipping Method: USPS Media Rate. Normally 5-10 days.\n\n";
	$body=$body."Shipping Address:\n\n".$name."\n".$address."\n".$city.", ".$state." ".$zip."\n".$country."\n\n";
	break;

	case 2: $body=$body."Shipping Method: USPS Parcel Post. Normally 3-7 days.\n\n";
	$body=$body."Shipping Address:\n\n".$name."\n".$address."\n".$city.", ".$state." ".$zip."\n".$country."\n\n";
	break;

	case 3: $body=$body."Shipping Method: USPS Priority. Normally 2-3 days.\n\n";
	$body=$body."Shipping Address:\n\n".$name."\n".$address."\n".$city.", ".$state." ".$zip."\n".$country."\n\n";
        break;

	case 4: $body=$body."Shipping Method: Pick Up/Delivery.\n\n";
	$body=$body."Shipping Address:\n\n".$name."\n".$address."\n".$city.", ".$state." ".$zip."\n".$country."\n\n";
	break;

	case 5: $body=$body."Shipping Method: International Surface Rate. Normally 2-6 weeks based on destination.\n\n";
	$body=$body."Shipping Address:\n\n".$name."\n".$address."\n".$city.", ".$state." ".$zip."\n".$country."\n\n";
	break;

	case 6: $body=$body."Shipping Method: International Airmail. Normally one week based on destination.\n\n";
	$body=$body."Shipping Address:\n\n".$name."\n".$address."\n".$city.", ".$state." ".$zip."\n".$country."\n\n";
	break;
};

$body=$body."Please contact us when your order arrives, or if the order hasn't arrived in the above timeframe.
If you have any questions reguarding your order, email sales@wrecked-distro.com\n
Thanks for your support! - \nWRECKED diy electronics distro\nhttp://wrecked-distro.com";

  mail($email, $subject, $body, $from);

}


// email Confirm: emails customer initial order information when order is placed

function emailConfirm($sales_orderid)
{

  dbConnect();

  $sql="SELECT * FROM users, sales_orders WHERE sales_orderid='$sales_orderid' AND sales_orders.userid=users.userid";
  $result=mysql_fetch_array(mysql_query($sql));

  $email=$result["email"];
  $username=$result["username"];

 $order_cost=$result["order_cost"];
 $tax_cost=$result["tax_cost"];
 $shipping_cost=$result["shipping_cost"];

 $shipping_method=$result["shipping_method"];
 $billing_method=$result["billing_method"];

 $name=$result["ship_name"];
 $address=$result["ship_address"];
 $city=$result["ship_city"];
 $state=$result["ship_state"];
 $zip=$result["ship_zip"];
 $country=$result["ship_country"];

 $total=$order_cost+$tax_cost+$shipping_cost;
 $total=number_format($total,2,'.','');

  echo "<p>Sending email confirmation to: ".$email."<p>";

  $subject = "WRECKED Order ".$sales_orderid." Confirmation";
  $body = $username." -\n\nYour order with WRECKED has been confirmed.\nReview the order info below for payment instructions.\n
Order # ".$sales_orderid." - ".$username."\n";

  $from = "From: sales@wrecked-distro.com\r\n";
  $from .= "CC: sales@wrecked-distro.com\r\n";
  $from .= "Reply-To: sales@wrecked-distro.com\r\n";
  $from .= "X-Mailer: PHP/".phpversion();

$sql = "SELECT *, sales_items.quantity AS quantity, items.quantity AS instock FROM sales_items, items WHERE
sales_items.itemid=items.itemid AND sales_items.sales_orderid='$sales_orderid' ORDER BY sales_items.sales_itemid DESC";
$result = mysql_query($sql);
   if ($myrow=mysql_fetch_array($result))
{
 $body=$body."\nOrder Contents
Category - Format - Artist - Title - Label - Condition - Quant - Cost\n";

  do
   {
   $body=$body." ".$myrow["category"]." - ".$myrow["format"]." - ".$myrow["artist"]." - ".$myrow["title"]." -";
   $body=$body." ".$myrow["label"]." - ".$myrow["condition"]." - ".$myrow["quantity"]." -";
   $body=$body." $".discountitem($myrow["itemid"],$myrow["discountid"])*$myrow["quantity"]."\n";

  } while ($myrow=mysql_fetch_array($result));

  $body=$body."\nSubtotal: $".$order_cost."\nTax: $".$tax_cost."\nShipping: $".$shipping_cost."\nTotal: $".$total."\n\n";

}
else
{$body=$body."No Items in this Order!\n";};

 // billing methods
 // 1 - cash
 // 3 - paypal
 // 4 - check
 // 5 - money order


switch ($billing_method)
{
	case 1: $body=$body."Payment: Cash.  Get cash to me. \n\nWRECKED\n50 Pasadena Street\nPittsburgh PA 15211\nUSA\n\n";
		break;
	case 3: $body=$body."Payment: Paypal. Follow the link below to pay. https://www.paypal.com/xclick/business=sales%40wrecked-distro.com&amp;item_name=".$username."+order+".$sales_orderid."&amp;item_number=1&amp;amount=%24".$total."\n\n";
		break;
	case 4: $body=$body."Payment: Check.  Make payable to Geoff Maddock and mail to: \n\nWRECKED\n50 Pasadena Street\nPittsburgh PA 15211\nUSA\n\n";
		break;
	case 5: $body=$body."Payment: Money Order.  Make payable to Geoff Maddock and mail to: \n\nWRECKED\n50 Pasadena Street\nPittsburgh PA 15211\nUSA\n\n";
		break;
};

 // shipping methods
 // 1 - media rate
 // 2 - parcel post
 // 3 - priority
 // 4 - pick up/delivery
 // 5 - international surface
 // 6 - international airmail

switch ($shipping_method)
{
	case 1: $body=$body."Shipping Method: USPS Media Rate.\n\n";
	$body=$body."Shipping Address:\n\n".$name."\n".$address."\n".$city.", ".$state." ".$zip."\n".$country."\n\n";
	break;

	case 2: $body=$body."Shipping Method: USPS Parcel Post.\n\n";
	$body=$body."Shipping Address:\n\n".$name."\n".$address."\n".$city.", ".$state." ".$zip."\n".$country."\n\n";
	break;

	case 3: $body=$body."Shipping Method: USPS Priority.\n\n";
	$body=$body."Shipping Address:\n\n".$name."\n".$address."\n".$city.", ".$state." ".$zip."\n".$country."\n\n";

	case 4: $body=$body."Shipping Method: Pick Up/Delivery.\n\n";
	$body=$body."Shipping Address:\n\n".$name."\n".$address."\n".$city.", ".$state." ".$zip."\n".$country."\n\n";
	break;

	case 5: $body=$body."Shipping Method: International Surface Rate.\n\n";
	$body=$body."Shipping Address:\n\n".$name."\n".$address."\n".$city.", ".$state." ".$zip."\n".$country."\n\n";
	break;

	case 6: $body=$body."Shipping Method: International Airmail.\n\n";
	$body=$body."Shipping Address:\n\n".$name."\n".$address."\n".$city.", ".$state." ".$zip."\n".$country."\n\n";
	break;
};

$body=$body."Your order will be packed and shipped on the first available day after payment.
When we ship, we'll send a followup email to you, and as well as update your account on the web.
If you have any questions reguarding your order, email sales@wrecked-distro.com\n
Thanks for your support! - \nWRECKED diy electronics distro\nhttp://wrecked-distro.com";

  mail($email, $subject, $body, $from);

}

// returns the amount of tax given a username (to determine state) and a dollar total

function getTax($username,$total)
{
  dbConnect();
  $sql="SELECT state FROM users WHERE username='$username'";
  $result=mysql_fetch_array(mysql_query($sql));
  $state=$result["state"];

 if (strtoupper($state)=="PA")
 { return ($total*.07);}
 else
 { return 0;};
}

// gets returns confirmation status of a sales order

function itemConfirm($sales_orderid)
{
  dbConnect();

  $sql="SELECT confirm FROM sales_items WHERE sales_orderid='$sales_orderid'";
  $itemConfirm=1;

  $result= mysql_query($sql);

  if ($myrow=mysql_fetch_array($result))
  {
   do
   {
    if ($myrow["confirm"]==0) {$itemConfirm=0;}
   } while ($myrow=mysql_fetch_array($result));
  };

  return $itemConfirm;
}

// returns confirmation status given a sales orderid

function saleConfirm($sales_orderid)
{
  dbConnect();

  $sql="SELECT confirm FROM sales_orders WHERE sales_orderid='$sales_orderid'";
  $result=mysql_fetch_array(mysql_query($sql));
  $saleConfirm=$result["confirm"];

  return $saleConfirm;
}

// returns an order's sent status 0=unsent 1=sent

function getSent($sales_orderid)
{

   dbConnect();

  $sql="SELECT sent FROM sales_orders WHERE sales_orderid='$sales_orderid'";
  $result=mysql_fetch_array(mysql_query($sql));
  $sent=$result["sent"];

  return $sent;
}

   $sortArray = array(0=>"DESC",1=>"ASC");
   $sales_orderid = isset($_REQUEST['sales_orderid']) ? $_REQUEST['sales_orderid'] : null;
   $shippingmethod = isset($_REQUEST['shippingmethod']) ? $_REQUEST['shippingmethod'] : null;
   $sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : "sales_orderid";
   $command = isset($_REQUEST['command']) ? $_REQUEST['command'] : "view";

   $search = isset($_REQUEST['search']) ? $_REQUEST['search'] : "sales_orderid";
   $keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : "";
   $lower = isset($_REQUEST['lower']) ? $_REQUEST['lower'] : 0;
   $number = isset($_REQUEST['number']) ? $_REQUEST['number'] : 30;
   $desc = isset($_REQUEST['desc']) ? $_REQUEST['desc'] : 0;
   $mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 1;
   $sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : "order_date";
   //$sort = "order_date";

?>

<b>Sales Orders from Customer admin</b>

<p>

<table>
<tr>
<td><form action="index.php"  method="post">
ENTER SEARCH KEYWORD(S)
<input type="hidden" name="module"  value="<?php echo $module; ?>">
<input type="hidden" name="number"  value="<?php echo $number; ?>">
<input type="hidden" name="lower"  value="<? echo $lower;?>">
<input type="hidden" name="mode" value="<? echo $mode;?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="">
<input type="text" name="keyword" size="12" value="<?php echo $keyword; ?>" class="form1">
<select name="search" class="form1">
<option <? if ($search=="all") echo "selected";?> value="" class="form1">all
<option <? if ($search=="username") echo "selected";?> value="username" class="form1">username
<option <? if ($search=="sales_orderid") echo "selected";?> value="sales_orderid">salesorder
<option <? if ($search=="confirm") echo "selected";?>  value="confirm">confirm
<option <? if ($search=="paid") echo "selected";?>  value="paid">paid
<option <? if ($search=="sent") echo "selected";?>  value="sent">sent
<option <? if ($search=="label") echo "selected";?> value="label">label
<option <? if ($search=="state") echo "selected";?>  value="state">state
</select>
<input type="submit" name="show" value="Search" class="button1">
</form>
</td></tr>
</table>

<?php

   dbConnect();

   $result = mysql_query("SELECT COUNT(sales_orderid) as total FROM sales_orders");
   $total = mysql_fetch_array($result);

   $ordercount = $total["total"];

   if ($lower < 0) {$lower = $total[0];};
   if ($lower > $ordercount) { $lower=0;};

   //$number = $ordercount;

   if ($_REQUEST['mail'])
   {
	echo "SalesOrder = ".$_GET["sales_orderid"];
	emailConfirm($_GET["sales_orderid"]);
  	echo "<a href=\"javascript:history.back()\">back to main</a>";

   };

   if ($_REQUEST['submit'])
   {
    // here if no ID then adding, else we're editing

    $tax_submit = getTax($username,$order_cost);

     if ($_REQUEST['sales_orderid'])
     {

     	if ((getSent($sales_orderid) == 0) && ($sent == 1))
      	{
		emailShip($sales_orderid);
	};

     	if ((saleConfirm($_REQUEST['sales_orderid']) == 0) && ($_REQUEST['confirm'] == 1))
      	{
		// changing from unconfirmed to confirmed, see if all items are confirmed

       	echo "Changing from unconfirmed to confirmed.<p>";

		if (itemConfirm($_REQUEST['sales_orderid']))
		{
			// if items are confirmed, make change, and email customer, remove items from db

			echo "All items are confirmed.<P>";

			$sql = "UPDATE sales_orders SET userid='".$_REQUEST['userid']."', order_date='".$_REQUEST['oyear'].$_REQUEST['omonth'].$_REQUEST['oday']."', confirm='".$_REQUEST['confirm']."',
paid_date='".$_REQUEST['pyear'].$_REQUEST['pmonth'].$_REQUEST['pday']."', paid='".$_REQUEST['paid']."', sent_date='".$_REQUEST['syear'].$_REQUEST['smonth'].$_REQUEST['sday']."', sent='".$_REQUEST['sent']."',
order_cost='".$_REQUEST['order_cost']."', tax_cost='".$_REQUEST['tax_cost']."', shipping_cost='".$_REQUEST['shipping_cost']."', shipping_method='".$_REQUEST['shipping_method']."',
billing_method='".$_REQUEST['billing_method']."',note='".$_REQUEST['note']."', ship_name='".$_REQUEST['ship_name']."', ship_address='".$_REQUEST['ship_address']."', ship_city='".$_REQUEST['ship_city']."',
ship_state='".$_REQUEST['ship_state']."', ship_zip='".$_REQUEST['ship_zip']."', ship_country='".$_REQUEST['ship_country']."', cancel='".$_REQUEST['cancel']."' WHERE sales_orderid='".$_REQUEST['sales_orderid']."'";

			emailConfirm($_REQUEST['sales_orderid']);

			echo "Update of *".$_REQUEST['sales_orderid']."\n";
		}
	else
	{
         echo "All items are not confirmed - please confirm before confirming sale.<p>";
	 $confirm = 0;
			$sql = "UPDATE sales_orders SET userid='".$_REQUEST['userid']."', order_date='".$_REQUEST['oyear'].$_REQUEST['omonth'].$_REQUEST['oday']."', confirm='".$confirm."',
paid_date='".$_REQUEST['pyear'].$_REQUEST['pmonth'].$_REQUEST['pday']."', paid='".$_REQUEST['paid']."', sent_date='".$_REQUEST['syear'].$_REQUEST['smonth'].$_REQUEST['sday']."', sent='".$_REQUEST['sent']."',
order_cost='".$_REQUEST['order_cost']."', tax_cost='".$_REQUEST['tax_cost']."', shipping_cost='".$_REQUEST['shipping_cost']."', shipping_method='".$_REQUEST['shipping_method']."',
billing_method='".$_REQUEST['billing_method']."',note='".$_REQUEST['note']."', ship_name='".$_REQUEST['ship_name']."', ship_address='".$_REQUEST['ship_address']."', ship_city='".$_REQUEST['ship_city']."',
ship_state='".$_REQUEST['ship_state']."', ship_zip='".$_REQUEST['ship_zip']."', ship_country='".$_REQUEST['ship_country']."', cancel='".$_REQUEST['cancel']."' WHERE sales_orderid='".$_REQUEST['sales_orderid']."'";
      echo "Update of ".$distroid."\n";
	};

      }
      else
      {
      if ((saleConfirm($sales_orderid)==1) && ($confirm==0))
      { // changing from confirmed to unconfirmed, add items back to db

        echo "Changing from confirmed to unconfirmed.<p>";

	//additems($salesorderid);
	};
			$sql = "UPDATE sales_orders SET userid='".$_REQUEST['userid']."', order_date='".$_REQUEST['oyear'].$_REQUEST['omonth'].$_REQUEST['oday']."', confirm='".$_REQUEST['confirm']."',
paid_date='".$_REQUEST['pyear'].$_REQUEST['pmonth'].$_REQUEST['pday']."', paid='".$_REQUEST['paid']."', sent_date='".$_REQUEST['syear'].$_REQUEST['smonth'].$_REQUEST['sday']."', sent='".$_REQUEST['sent']."',
order_cost='".$_REQUEST['order_cost']."', tax_cost='".$_REQUEST['tax_cost']."', shipping_cost='".$_REQUEST['shipping_cost']."', shipping_method='".$_REQUEST['shipping_method']."',
billing_method='".$_REQUEST['billing_method']."',note='".$_REQUEST['note']."', ship_name='".$_REQUEST['ship_name']."', ship_address='".$_REQUEST['ship_address']."', ship_city='".$_REQUEST['ship_city']."',
ship_state='".$_REQUEST['ship_state']."', ship_zip='".$_REQUEST['ship_zip']."', ship_country='".$_REQUEST['ship_country']."', cancel='".$_REQUEST['cancel']."' WHERE sales_orderid='".$_REQUEST['sales_orderid']."'";
      echo "Update of ".$distroid."\n";

      };

     }
     else
     {
  $sql = "INSERT INTO sales_orders (sales_orderid, userid, order_date, confirm, paid_date, paid, sent_date, sent,
order_cost, tax_cost, shipping_cost, shipping_method, billing_method, note, ship_name, ship_address, ship_city,
ship_state, ship_zip, ship_country) VALUES (0,'".$_REQUEST['userid']."','".$_REQUEST['oyear'].$_REQUEST['omonth'].$_REQUEST['oday']."','".$_REQUEST['confirm']."','".$_REQUEST['pyear'].$_REQUEST['pmonth'].$_REQUEST['pday']."','".$_REQUEST['paid']."',
'".$_REQUEST['syear'].$_REQUEST['smonth'].$_REQUEST['sday']."','".$_REQUEST['sent']."','".$_REQUEST['order_cost']."',
'$tax_submit','".$_REQUEST['$shipping_cost']."','".$_REQUEST['shipping_method']."','".$_REQUEST['billing_method']."','".$_REQUEST['note']."','".$_REQUEST['ship_name']."',
'".$_REQUEST['ship_address']."','".$_REQUEST['ship_city']."','".$_REQUEST['ship_state']."','".$_REQUEST['ship_zip']."','".$_REQUEST['ship_country']."')";

      echo "inserting ".$sales_orderid."\n";

     };
     // run SQL against the DB

      $result = mysql_query($sql);

      echo "Record updated.<p>";

      echo "<a href=\"$PHP_SELF?sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">back</a>";

     } elseif ($_REQUEST['delete']) {

       // delete a record

 	if ($_REQUEST['confirm'])
	{
	  echo "Are you sure you want to delete salesorder = ".$sales_orderid."?";
	  echo " (<a href=\"$PHP_SELF?sales_orderid=$sales_orderid&amp;delete=yes&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;keyword=$keyword&amp;search=$search\">YES</a>
/ <a href=\"javascript:history.back()\">NO</a>)";
	} else
	{
         $sql = "DELETE FROM sales_orders WHERE sales_orderid='$sales_orderid'";
         $result = mysql_query($sql);

         $sql = "DELETE FROM sales_items WHERE sales_orderid='$sales_orderid'";
         $result = mysql_query($sql);

         echo "$sales_orderid Record deleted!<p>";

         echo "<a href=\"$PHP_SELF?sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">back</a>";
        };
     } else {

      // this part happens if we don't press submit

     if (!$_REQUEST['sales_orderid']) {
    // print the list if there is not editing
     if ($search == '') {
     	$search = 'username';
     };

     $sql = "SELECT *, DATE_FORMAT(order_date,'%m/%d/%y') AS formatted_order_date,
DATE_FORMAT(paid_date,'%m/%d/%y') AS paid_date, DATE_FORMAT(sent_date,'%m/%d/%y') AS sent_date, sales_orders.note AS note
FROM sales_orders, users, shipping WHERE  sales_orders.userid=users.userid AND sales_orders.shipping_method=shipping.shippingid AND
$search LIKE \"%$keyword%\" ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower, $number";
    echo "<div id='query'>Query: ".$sql."</div>";
     $result = mysql_query($sql);
     if ($myrow = mysql_fetch_array($result))
     {
       echo "<table>\n";

       echo "<tr><td class=\"title1\" colspan='18'><b>Current Sales Orders</b></td></tr>\n";
       echo "<tr class=\"title2\">
             <td>
<a href=\"$PHP_SELF?module=$module&amp;sort=sales_orderid&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">SalesID</a></td>
             <td>
<a href=\"$PHP_SELF?module=$module&amp;sort=username&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Username</a></td>
             <td>
<a href=\"$PHP_SELF?module=$module&amp;sort=order_date&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Order Date</a></td>
             <td>
<a href=\"$PHP_SELF?module=$module&amp;sort=confirmed&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Conf</a></td>
             <td>
<a href=\"$PHP_SELF?module=$module&amp;sort=paid_date&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Paid Date </a></td>
             <td>
<a href=\"$PHP_SELF?module=$module&amp;sort=paid&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Paid?</a></td>
             <td>
<a href=\"$PHP_SELF?module=$module&amp;sort=sent_date&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Sent Date </a></td>
             <td>
<a href=\"$PHP_SELF?module=$module&amp;sort=sent&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Sent?</a></td>
             <td>
<a href=\"$PHP_SELF?module=$module&amp;sort=order_cost&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Order Cost</a></td>
             <td>
<a href=\"$PHP_SELF?module=$module&amp;sort=order_cost&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Tax</a></td>
             <td>
<a href=\"$PHP_SELF?module=$module&amp;sort=shipping_cost&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Ship</a></td>
             <td>
<a href=\"$PHP_SELF?module=$module&amp;sort=shipping_method&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Method</a></td>
             <td>
<a href=\"$PHP_SELF?module=$module&amp;sort=order_cost&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Total</a></td>
             <td>
<a href=\"$PHP_SELF?module=$module&amp;sort=order_cost&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Weight</a></td>
		<td colspan='4'></td>
             </tr>\n";

       do
       {

        if ($myrow["paid"]) {$divcolor="text1";} else {$divcolor="text2";};
        printf("<tr >\n<td> <div class=\"$divcolor\">%s</div></td>\n <td><div class=\"$divcolor\"><a
href=\"adminusers.php?userid=%s\">%s</a></div></td>\n
<td><div class=$divcolor>%s</div></td>\n",$myrow["sales_orderid"], $myrow["userid"],$myrow["username"],
$myrow["formatted_order_date"]);

        if ($myrow["confirm"]==1) {echo "<td><div class=$divcolor>Yes</div></td>\n";} else {echo "<td><div
class=$divcolor>No</div></td>\n";};

        if ($myrow["paid"]==1) {echo "<td><div class=$divcolor>".$myrow["paid_date"]."</div></td>\n
		<td><div class=$divcolor>Yes</div></td>\n";} else {echo "<td></td>\n<td><div
class=$divcolor>No</div></td>\n";};

        if ($myrow["sent"]==1) {echo "<td><div class=\"$divcolor\">".$myrow["sent_date"]."</div></td>\n
		<td><div class=\"$divcolor\">Yes</div></td>\n";} else {echo "<td></td>\n<td><div
class=$divcolor>No</div></td>\n";};

        printf("<td><div class=\"$divcolor\">$%s</div></td>\n <td><div class=\"$divcolor\">$%s</div></td> <td><div
class=\"$divcolor\">$%s</div></td>",
$myrow["order_cost"], $myrow["tax_cost"], $myrow["shipping_cost"]);

        echo "<td><div class=\"$divcolor\">".$myrow["type"]."
".$myrow["state"]." ".$myrow["zip"]."</div></td>";

	 $total=$myrow["order_cost"]+$myrow["shipping_cost"]+$myrow["tax_cost"];
        $total=number_format($total,2,'.','');
 	printf("<td><div class=\"$divcolor\">$%s</div></td>",$total);

	printf("<td><div class=\"$divcolor\">%s</div></td>",totalWeight($myrow["sales_orderid"]));


        printf("<td><a href=\"?module=$module&amp;sales_orderid=%s&amp;delete=yes&amp;confirm=yes&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">(DEL)</a></td>
<td><a href=\"?module=$module&amp;sales_orderid=%s&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">(EDIT)</a></td>
<td><a href=\"?module=adminsales_items.php&amp;sales_orderid=%s&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">(ADD)</a></td>
<td><a href=\"?module=printsales_orders.php&amp;sales_order=%s\">(PRINT)</a></td></tr>",
$myrow["sales_orderid"],$myrow["sales_orderid"],$myrow["sales_orderid"],$myrow["sales_orderid"]);

       } while ($myrow=mysql_fetch_array($result));
       echo "</table>\n";
      }

     echo "<p>";

     }

    ?>

<table>
<tr><td>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="sort" value="<? echo $sort;?>">
<input type="hidden" name="keyword" value="<? echo $keyword;?>">
<input type="hidden" name="search" value="<? echo $sort;?>">
<input type="submit" name="show" value="Show&nbsp;:" class="button1">
<input type="text" name="number"  value="<? echo $number; ?>" class="form1">
rows beginning with number
<input type="text" name="lower"  value="<? echo $lower; ?>" class="form1">
in
<select name="desc">
<option value="0" <? if ($desc != "1") echo " SELECTED ";?> class="form1" >ASCENDING
<option value="1" <? if ($desc == "1") echo " SELECTED ";?> class="form1">DESCENDING
</select>
order.
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="number"  value="<? echo $number; ?>">
<input type="hidden" name="lower"  value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="submit" name="show" value="Previous <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="number"  value="<? echo $number; ?>">
<input type="hidden" name="lower"  value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="submit" name="show" value="Next <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="number"  value="<? echo $ordercount; ?>">
<input type="hidden" name="lower"  value="0">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="hidden" name="module" value="<? echo $module;?>">

<input type="submit" name="show" value="Show All" class="button1">
</form>
</td>
</tr>
</table>
     <p>

     <a href="<?php echo $_SERVER['PHP_SELF'];?>">ADD A sales order</a>

     <p>

     <form method="post" name="addform" action="<?php echo $_SERVER['PHP_SELF'];?>" >

     <?

     if ($_REQUEST['sales_orderid'])
     {

     // editing so select a record

     $sql = "SELECT *, sales_orders.note AS note, sales_orders.billing_method AS billing_method,
sales_orders.shipping_method AS shipping_methog FROM sales_orders, users WHERE
sales_orders.sales_orderid='$sales_orderid' AND sales_orders.userid=users.userid";

     $result = mysql_query($sql);

     $myrow = mysql_fetch_array($result);

     $sales_orderid = $myrow["sales_orderid"];

     $userid = $myrow["userid"];

     $confirm = $myrow["confirm"];

     $paid_date = $myrow["paid_date"];

     $paid = $myrow["paid"];

     $sent_date = $myrow["sent_date"];

     $sent = $myrow["sent"];

     $order_cost = $myrow["order_cost"];

     $tax_cost = $myrow["tax_cost"];

     $shipping_cost = $myrow["shipping_cost"];

     $shipping_method = $myrow["shipping_method"];

     $note = $myrow["note"];

     $shipping_name = $myrow["ship_name"];

     $shipping_address = $myrow["ship_address"];

     $shipping_city = $myrow["ship_city"];

     $shipping_state = $myrow["ship_state"];

     $shipping_zip = $myrow["ship_zip"];

     $shipping_country = $myrow["ship_country"];


     // print the id for editing

     ?>

     <input type=hidden name="sales_orderid" value="<?php echo $sales_orderid ?>">

     <?
     }

     ?>

     Fill in all fields to add a new sales order<br>     *'d fields are optional.<p>

     <? echo "<div>Sales Order ".$sales_orderid."</div><p>";?>


     <table>


     <tr><td>
     <a href="adminusers.php">User</a></td>
     <td>
     <select name="userid" size="1">

     <?
      $sql = "SELECT userid, username FROM users ORDER BY username";
      $result = mysql_query($sql);

      if ($userlist=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$userlist["userid"]."\" ";
       if ($userlist["userid"]==$myrow["userid"])
        {echo "selected";};
       echo ">".$userlist["username"];
      } while ($userlist=mysql_fetch_array($result));
      };
     ?>
     </select>

     </td></tr>

     <tr><td>Order Date *</td>
        <?
        $omonth=date("m",strtotime($myrow["order_date"]));
        $oday=date("d",strtotime($myrow["order_date"]));
        $oyear=date("Y",strtotime($myrow["order_date"]));
        ?>


     <td>
     <select name="omonth" size="1">
     <option value="01" <? if ($omonth=="01") echo "selected"; ?>>Jan
     <option value="02" <? if ($omonth=="02") echo "selected"; ?>>Feb
     <option value="03" <? if ($omonth=="03") echo "selected"; ?>>Mar
     <option value="04" <? if ($omonth=="04") echo "selected"; ?>>Apr
     <option value="05" <? if ($omonth=="05") echo "selected"; ?>>May
     <option value="06" <? if ($omonth=="06") echo "selected"; ?>>Jun
     <option value="07" <? if ($omonth=="07") echo "selected"; ?>>Jul
     <option value="08" <? if ($omonth=="08") echo "selected"; ?>>Aug
     <option value="09" <? if ($omonth=="09") echo "selected"; ?>>Sep
     <option value="10" <? if ($omonth=="10") echo "selected"; ?>>Oct
     <option value="11" <? if ($omonth=="11") echo "selected"; ?>>Nov
     <option value="12" <? if ($omonth=="12") echo "selected"; ?>>Dec
     </select>

    <select name="oday" size="1">
     <option value="01" <? if ($oday=="01") echo "selected"; ?>>01
     <option value="02" <? if ($oday=="02") echo "selected"; ?>>02
     <option value="03" <? if ($oday=="03") echo "selected"; ?>>03
     <option value="04" <? if ($oday=="04") echo "selected"; ?>>04
     <option value="05" <? if ($oday=="05") echo "selected"; ?>>05
     <option value="06" <? if ($oday=="06") echo "selected"; ?>>06
     <option value="07" <? if ($oday=="07") echo "selected"; ?>>07
     <option value="08" <? if ($oday=="08") echo "selected"; ?>>08
     <option value="09" <? if ($oday=="09") echo "selected"; ?>>09
     <option value="10" <? if ($oday=="10") echo "selected"; ?>>10
     <option value="11" <? if ($oday=="11") echo "selected"; ?>>11
     <option value="12" <? if ($oday=="12") echo "selected"; ?>>12
     <option value="13" <? if ($oday=="13") echo "selected"; ?>>13
     <option value="14" <? if ($oday=="14") echo "selected"; ?>>14
     <option value="15" <? if ($oday=="15") echo "selected"; ?>>15
     <option value="16" <? if ($oday=="16") echo "selected"; ?>>16
     <option value="17" <? if ($oday=="17") echo "selected"; ?>>17
     <option value="18" <? if ($oday=="18") echo "selected"; ?>>18
     <option value="19" <? if ($oday=="19") echo "selected"; ?>>19
     <option value="20" <? if ($oday=="20") echo "selected"; ?>>20
     <option value="21" <? if ($oday=="21") echo "selected"; ?>>21
     <option value="22" <? if ($oday=="22") echo "selected"; ?>>22
     <option value="23" <? if ($oday=="23") echo "selected"; ?>>23
     <option value="24" <? if ($oday=="24") echo "selected"; ?>>24
     <option value="25" <? if ($oday=="25") echo "selected"; ?>>25
     <option value="26" <? if ($oday=="26") echo "selected"; ?>>26
     <option value="27" <? if ($oday=="27") echo "selected"; ?>>27
     <option value="28" <? if ($oday=="28") echo "selected"; ?>>28
     <option value="29" <? if ($oday=="29") echo "selected"; ?>>29
     <option value="30" <? if ($oday=="30") echo "selected"; ?>>30
     <option value="31" <? if ($oday=="31") echo "selected"; ?>>31
     </select>


     <select name="oyear" size="1">
     <? for ($i=1990;$i<=date("Y",time());$i++)
     { echo "<option ";
       if ($oyear==$i) echo "selected";
       echo ">".$i;
     };
     ?>
     </select>
     </td></tr>

     <tr><td>

     Confirm *</td>

     <td>
     <select name="confirm" size="1">
     <option value="0" <? if ($confirm=="0") echo "selected"; ?>>No
     <option value="1" <? if ($confirm=="1") echo "selected"; ?>>Yes
     </select>
     </td></tr>


     <tr><td>

     Paid Date
     <input type="checkbox" value="1" name="paid" <? if ($paid==1) echo "
CHECKED"; ?> >
     </td>
        <?
        $pmonth=date("m",strtotime($myrow["paid_date"]));
        $pday=date("d",strtotime($myrow["paid_date"]));
        $pyear=date("Y",strtotime($myrow["paid_date"]));
        ?>


     <td>
     <select name="pmonth" size="1">
     <option value="01" <? if ($pmonth=="01") echo "selected"; ?>>Jan
     <option value="02" <? if ($pmonth=="02") echo "selected"; ?>>Feb
     <option value="03" <? if ($pmonth=="03") echo "selected"; ?>>Mar
     <option value="04" <? if ($pmonth=="04") echo "selected"; ?>>Apr
     <option value="05" <? if ($pmonth=="05") echo "selected"; ?>>May
     <option value="06" <? if ($pmonth=="06") echo "selected"; ?>>Jun
     <option value="07" <? if ($pmonth=="07") echo "selected"; ?>>Jul
     <option value="08" <? if ($pmonth=="08") echo "selected"; ?>>Aug
     <option value="09" <? if ($pmonth=="09") echo "selected"; ?>>Sep
     <option value="10" <? if ($pmonth=="10") echo "selected"; ?>>Oct
     <option value="11" <? if ($pmonth=="11") echo "selected"; ?>>Nov
     <option value="12" <? if ($pmonth=="12") echo "selected"; ?>>Dec
     </select>

    <select name="pday" size="1">
     <option value="01" <? if ($pday=="01") echo "selected"; ?>>01
     <option value="02" <? if ($pday=="02") echo "selected"; ?>>02
     <option value="03" <? if ($pday=="03") echo "selected"; ?>>03
     <option value="04" <? if ($pday=="04") echo "selected"; ?>>04
     <option value="05" <? if ($pday=="05") echo "selected"; ?>>05
     <option value="06" <? if ($pday=="06") echo "selected"; ?>>06
     <option value="07" <? if ($pday=="07") echo "selected"; ?>>07
     <option value="08" <? if ($pday=="08") echo "selected"; ?>>08
     <option value="09" <? if ($pday=="09") echo "selected"; ?>>09
     <option value="10" <? if ($pday=="10") echo "selected"; ?>>10
     <option value="11" <? if ($pday=="11") echo "selected"; ?>>11
     <option value="12" <? if ($pday=="12") echo "selected"; ?>>12
     <option value="13" <? if ($pday=="13") echo "selected"; ?>>13
     <option value="14" <? if ($pday=="14") echo "selected"; ?>>14
     <option value="15" <? if ($pday=="15") echo "selected"; ?>>15
     <option value="16" <? if ($pday=="16") echo "selected"; ?>>16
     <option value="17" <? if ($pday=="17") echo "selected"; ?>>17
     <option value="18" <? if ($pday=="18") echo "selected"; ?>>18
     <option value="19" <? if ($pday=="19") echo "selected"; ?>>19
     <option value="20" <? if ($pday=="20") echo "selected"; ?>>20
     <option value="21" <? if ($pday=="21") echo "selected"; ?>>21
     <option value="22" <? if ($pday=="22") echo "selected"; ?>>22
     <option value="23" <? if ($pday=="23") echo "selected"; ?>>23
     <option value="24" <? if ($pday=="24") echo "selected"; ?>>24
     <option value="25" <? if ($pday=="25") echo "selected"; ?>>25
     <option value="26" <? if ($pday=="26") echo "selected"; ?>>26
     <option value="27" <? if ($pday=="27") echo "selected"; ?>>27
     <option value="28" <? if ($pday=="28") echo "selected"; ?>>28
     <option value="29" <? if ($pday=="29") echo "selected"; ?>>29
     <option value="30" <? if ($pday=="30") echo "selected"; ?>>30
     <option value="31" <? if ($pday=="31") echo "selected"; ?>>31
     </select>


     <select name="pyear" size="1">
     <? for ($i=1990;$i<=date("Y",time());$i++)
     { echo "<option ";
       if ($pyear==$i) echo "selected";
       echo ">".$i;
     };
     ?>
     </select>
     </td></tr>

     <tr><td>

     Sent Date
    <input type="checkbox" value="1" name="sent" <? if ($sent==1) echo "CHECKED"; ?>>
</td>
        <?
        $smonth=date("m",strtotime($myrow["sent_date"]));
        $sday=date("d",strtotime($myrow["sent_date"]));
        $syear=date("Y",strtotime($myrow["sent_date"]));
        ?>


     <td>
     <select name="smonth" size="1">
     <option value="01" <? if ($smonth=="01") echo "selected"; ?>>Jan
     <option value="02" <? if ($smonth=="02") echo "selected"; ?>>Feb
     <option value="03" <? if ($smonth=="03") echo "selected"; ?>>Mar
     <option value="04" <? if ($smonth=="04") echo "selected"; ?>>Apr
     <option value="05" <? if ($smonth=="05") echo "selected"; ?>>May
     <option value="06" <? if ($smonth=="06") echo "selected"; ?>>Jun
     <option value="07" <? if ($smonth=="07") echo "selected"; ?>>Jul
     <option value="08" <? if ($smonth=="08") echo "selected"; ?>>Aug
     <option value="09" <? if ($smonth=="09") echo "selected"; ?>>Sep
     <option value="10" <? if ($smonth=="10") echo "selected"; ?>>Oct
     <option value="11" <? if ($smonth=="11") echo "selected"; ?>>Nov
     <option value="12" <? if ($smonth=="12") echo "selected"; ?>>Dec
     </select>

    <select name="sday" size="1">
     <option value="-" <? if ($sday=="-") echo "selected"; ?>>-
     <option value="01" <? if ($sday=="01") echo "selected"; ?>>01
     <option value="02" <? if ($sday=="02") echo "selected"; ?>>02
     <option value="03" <? if ($sday=="03") echo "selected"; ?>>03
     <option value="04" <? if ($sday=="04") echo "selected"; ?>>04
     <option value="05" <? if ($sday=="05") echo "selected"; ?>>05
     <option value="06" <? if ($sday=="06") echo "selected"; ?>>06
     <option value="07" <? if ($sday=="07") echo "selected"; ?>>07
     <option value="08" <? if ($sday=="08") echo "selected"; ?>>08
     <option value="09" <? if ($sday=="09") echo "selected"; ?>>09
     <option value="10" <? if ($sday=="10") echo "selected"; ?>>10
     <option value="11" <? if ($sday=="11") echo "selected"; ?>>11
     <option value="12" <? if ($sday=="12") echo "selected"; ?>>12
     <option value="13" <? if ($sday=="13") echo "selected"; ?>>13
     <option value="14" <? if ($sday=="14") echo "selected"; ?>>14
     <option value="15" <? if ($sday=="15") echo "selected"; ?>>15
     <option value="16" <? if ($sday=="16") echo "selected"; ?>>16
     <option value="17" <? if ($sday=="17") echo "selected"; ?>>17
     <option value="18" <? if ($sday=="18") echo "selected"; ?>>18
     <option value="19" <? if ($sday=="19") echo "selected"; ?>>19
     <option value="20" <? if ($sday=="20") echo "selected"; ?>>20
     <option value="21" <? if ($sday=="21") echo "selected"; ?>>21
     <option value="22" <? if ($sday=="22") echo "selected"; ?>>22
     <option value="23" <? if ($sday=="23") echo "selected"; ?>>23
     <option value="24" <? if ($sday=="24") echo "selected"; ?>>24
     <option value="25" <? if ($sday=="25") echo "selected"; ?>>25
     <option value="26" <? if ($sday=="26") echo "selected"; ?>>26
     <option value="27" <? if ($sday=="27") echo "selected"; ?>>27
     <option value="28" <? if ($sday=="28") echo "selected"; ?>>28
     <option value="29" <? if ($sday=="29") echo "selected"; ?>>29
     <option value="30" <? if ($sday=="30") echo "selected"; ?>>30
     <option value="31" <? if ($sday=="31") echo "selected"; ?>>31
     </select>


     <select name="syear" size="1">
     <? for ($i=1990;$i<=date("Y",time());$i++)
     { echo "<option ";
       if ($syear==$i) echo "selected";
       echo ">".$i;
     };
     ?>
     </select>
     </td></tr>

     <tr><td>

     Order Cost
     </td><td><input type="Text" name="order_cost" value="<? echo $myrow["order_cost"] ?>"></td>
     </tr>

     <tr><td>

     Tax Cost
     </td><td><input type="Text" name="tax_cost" value="<? echo $myrow["tax_cost"] ?>"></td>
     </tr>

     <tr><td>

     Shipping Cost
     </td><td><input type="Text" name="shipping_cost" value="<? echo $myrow["shipping_cost"] ?>"></td>
     </tr>

     <tr><td><a href="adminshipping.php">Shipping Method</a></td>
     <td>
     <select name="shipping_method" size="1">

     <?
      $sql = "SELECT shippingid, type FROM shipping";
      $result = mysql_query($sql);

      if ($shiplist=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$shiplist["shippingid"]."\" ";
       if ($shiplist["shippingid"]==$myrow["shipping_method"])
        {echo "selected";};
       echo ">".$shiplist["type"];
      } while ($shiplist=mysql_fetch_array($result));
      };
     ?>
     </select>
     </td></tr>

     <tr><td>
<?
echo "Billing Method</td><td>
      <select name=\"billing_method\" size=\"1\">";

      $sql = "SELECT * FROM billing_method WHERE access=1";
      $result = mysql_query($sql);

      if ($bill=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$bill["billing_methodid"]."\" ";
       if ($bill["billing_methodid"]==$myrow["billing_method"])
        {echo "SELECTED ";};
       echo ">".$bill["name"];
      } while ($bill=mysql_fetch_array($result));
      };
echo "</select>";
?>
     </td></tr>


     <tr><td>

     Note</td>
     <td>
     <textarea name="note" rows="7" cols="40"><? echo $myrow["note"] ?></textarea>
     </td>
     </tr>

     <tr><td>

     Shipping Name
     </td>
     <td>
     <input type="Text" name="ship_name" value="<? echo $myrow["ship_name"] ?>">
     </td>
     </tr>

     <tr><td>

     Shipping Address </td>
     <td>
     <textarea name="ship_address" rows="3" cols="40"><? echo $myrow["ship_address"] ?></textarea>
     </td>
     </tr>

     <tr><td>

     Shipping City
     </td>
     <td>
     <input type="Text" name="ship_city" value="<? echo $myrow["ship_city"] ?>">
     </td>
     </tr>


     <tr><td>

     Shipping State
     </td>
     <td>
     <input type="Text" name="ship_state" value="<? echo $myrow["ship_state"] ?>">
     </td>
     </tr>


     <tr><td>

     Shipping Zip
     </td>
     <td>
     <input type="Text" name="ship_zip" size=10 value="<? echo $myrow["ship_zip"] ?>">
     </td>
     </tr>


     <tr><td>

     Shipping Country
     </td>
     <td>
     <input type="Text" name="ship_country" value="<? echo $myrow["ship_country"] ?>">
     </td>
     </tr>


     <tr><td>
	<input type="hidden" name="module" value="<? echo $module;?>">
        <input type="hidden" name="sort" value="<? echo $sort ?>">
        <input type="hidden" name="lower" value="<? echo $lower ?>">
        <input type="hidden" name="number" value="<? echo $number ?>">
        <input type="hidden" name="desc" value="<? echo $desc ?>">
        <input type="Submit" name="submit" value="Enter information" class="button1"></td>
        <td></td></tr>

     </table>
     </form>

<?
     // print all items included in order, and show form to add a new item.


     $result = mysql_query("SELECT items.label, items.artist, items.title, items.itemid, items.catalog,
items.retail, items.cost, items.format, sales_items.sales_itemid, sales_items.sales_orderid, sales_items.itemid,
sales_items.quantity,
sales_items.confirm, sales_items.discount  FROM  items, sales_items WHERE  items.itemid=sales_items.itemid  AND
sales_items.sales_orderid='$sales_orderid' ");


     if ($myrow = mysql_fetch_array($result))
     {

       echo "<table border=0 cellspacing=0 cellpadding=3>\n";

       echo "<tr><td class=\"title1\" colspan=6><b>Order Contents</b></td></tr>\n";
       echo "<tr class=\"title2\">
             <td>
<a href=\"$PHP_SELF?sort=order_itemid&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Sales_ItemID</a></td>
             <td>
<a href=\"$PHP_SELF?sort=distro_orderid&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Sales OrderID</a></td>
             <td>
<a href=\"$PHP_SELF?sort=itemid&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Item</a></td>
             <td>
<a href=\"$PHP_SELF?sort=quantity&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Quantity</a></td>
             <td>
<a href=\"$PHP_SELF?sort=cost&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Cost</a></td>
             <td>
<a href=\"$PHP_SELF?sort=cost&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Confirm</a></td>

             </tr>\n";

do
       {
	$discount = $myrow["discount"];
	$quantity = $myrow["quantity"];
        $price = (calcPrice($myrow,$discount) * $quantity);
        $price = number_format($price,2,'.','');
        printf("<tr><td>%s</td> <td>%s</td> <td>%s %s - %s - %s %s</td><td>%s</td><td>$%s</td> ",
        $myrow["sales_itemid"], $myrow["sales_orderid"], $myrow["itemid"], $myrow["artist"],$myrow["title"],
$myrow["label"],$myrow["catalog"],$myrow["quantity"], $price);
        if ($myrow["confirm"]==0)

        { echo "<td>no</td>";}
        else
        { echo "<td>yes</td>";};

        printf("<td><a
href=\"%s?sales_itemid=%s&amp;delete=yes&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">(DELETE)</a></td><td><a
href=\"%s?sales_itemid=%s&amp;sales_orderid=%s&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">(EDIT)</a></td></tr>",
       "adminsales_items.php",$myrow["sales_itemid"],"adminsales_items.php",$myrow["sales_itemid"],$myrow["sales_orderid"]);

       } while ($myrow=mysql_fetch_array($result));
       echo "</table>\n";
      };


     }

?>
<P>
