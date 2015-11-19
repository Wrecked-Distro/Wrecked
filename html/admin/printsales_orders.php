<? 
//include("header.php"); 
//include("db.php");
//include("saleincludes.php");

$database = "db9372_distro";

function printOrder($sales_orderid)
{

  dbConnect("db9372_distro");

  $sql = "SELECT * FROM users, sales_orders WHERE sales_orderid='$sales_orderid' AND sales_orders.userid=users.userid";
  $result = mysql_fetch_array(mysql_query($sql));

  $email = $result["email"];
  $username = $result["username"];
  $note = $result["note"];  

 $order_cost = $result["order_cost"];
 $tax_cost = $result["tax_cost"];
 $shipping_cost = $result["shipping_cost"];

 $shipping_method=$result["shipping_method"];
 $billing_method=$result["billing_method"];

 $order=$result["order_date"]; 
 $paid=$result["paid_date"]; 
 $sent=$result["sent_date"]; 

 $note=$result["note"]; 

 $name = $result["ship_name"];
 $address = $result["ship_address"];
 $city = $result["ship_city"];
 $state = $result["ship_state"];
 $zip = $result["ship_zip"];
 $country = $result["ship_country"];

 $total = ($order_cost+$tax_cost + $shipping_cost);

 echo "<table><tr><td>";
 echo "<font size=+2>".$name."<br>".$address."<br>".$city.", ".$state." ".$zip."<br>".$country."<p>";
 echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>";
 echo "<font size=+1>WRECKED<br>3826 East Street<br>Pittsburgh PA 15214<br>USA";
 echo "</td></tr></table><p>"; 

  echo "<font size=+1>WRECKED Order ".$sales_orderid." Information</font><br>";
  echo "Username <b>".$username."</b><p>";

  echo "Order Date ".$order."<br>";

  if ($result["paid"]) {  echo "Paid ".$paid."<br>";} else {echo "Not paid<br>";};
  if ($result["sent"]) { echo "Sent ".$sent."<p>";} else {echo "Not sent<P>";};

$sql = "SELECT *, sales_items.quantity AS quantity, items.quantity AS instock FROM sales_items, items WHERE
sales_items.itemid=items.itemid AND sales_items.sales_orderid='$sales_orderid' ORDER BY sales_items.sales_itemid DESC";

$result = mysql_query($sql);

   if ($myrow = mysql_fetch_array($result))
{
 echo "Order Contents<br>
Category - Format - Artist - Title - Label - Condition - Quant - 
Cost<br>";
 
  do
   {
   echo $myrow["category"]." - ".$myrow["format"]." - ".$myrow["artist"]." - ".$myrow["title"]." - 
".$myrow["label"]." ".$myrow["catalog"]." - ".$myrow["condition"]." - 
".$myrow["quantity"]." - 
$".number_format(discountItem($myrow["itemid"],$myrow["discount"])*$myrow["quantity"],2,'.','')."<br>";
 
  } while ($myrow = mysql_fetch_array($result));

  echo "<p>Subtotal: $".$order_cost."<br>Tax: $".$tax_cost."\nShipping: $".$shipping_cost."<br>Total: $".$total."<p>";

}
else
{$body=$body."No Items in this Order!\n";};


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
	case 1: echo "Shipping Method: USPS Media Rate. Normally 5-10 days.<p>";
	echo "Shipping Address:<br>".$name."<br>".$address."<br>".$city.", ".$state." ".$zip."<br>".$country."<p>";
	break;

	case 2: echo "Shipping Method: USPS Parcel Post. Normally 3-7 days.<p>";
	echo "Shipping Address:<br>".$name."<br>".$address."<br>".$city.", ".$state." ".$zip."<br>".$country."<p>";
	break;

	case 3: echo "Shipping Method: USPS Priority. Normally 2-3 days.<p>";
	echo "Shipping Address:<br>".$name."<br>".$address."<br>".$city.", ".$state." ".$zip."\n".$country."<p>";
        break;

	case 4: echo "Shipping Method: Pick Up/Delivery.<p>";
	echo "Shipping Address:<br>".$name."<br>".$address."<br>".$city.", ".$state." ".$zip."\n".$country."<p>";
	break;

	case 5: echo "Shipping Method: International Surface Rate. Normally 2-6 weeks based on destination.<p>";
	echo "Shipping Address:<br>".$name."<br>".$address."<br>".$city.", ".$state." ".$zip."\n".$country."<p>";
	break;

	case 6: echo "Shipping Method: International Airmail. Normally one week based on destination.<p>";
	echo "Shipping Address:<br>".$name."<br>".$address."<br>".$city.", ".$state." ".$zip."<br>".$country."<p>";
	break;
};

echo "<b>Additional Notes</b><br>".$note."<p>";

echo "Please contact us when your order arrives, or if the order hasn't arrived in the above timeframe.<br>
If you have any questions reguarding your order, email sales@wrecked-distro.com\n
Thanks for your support! - <br>WRECKED diy electronics distro<br>http://wrecked-distro.com";


}



function getTax($username,$total)
{

  dbConnect("db9372_distro");

  $sql="SELECT state FROM users WHERE username='$username'";
  $result=mysql_fetch_array(mysql_query($sql,$db));
  $state=$result["state"];

 if ($state=="PA")
 { return ($total*.07);}
 else
 { return 0;};
}

if ($_REQUEST['sales_order'])
{
 printOrder($_REQUEST['sales_order']);
};


?>

