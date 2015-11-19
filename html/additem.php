<?php 
// include external functions

   include_once("access.php");
   include_once("header.php");
   include_once("saleincludes.php");

// important functions in salesincludes
// function getDiscount($usertype, $arraycontainingitem) returns $discountID
// function calcQuantity($usertype, $arraycontainingitem) returns $displayquantity
// function calcPrice($arraycontainingitem, $discoundID) returns $currentprice

function showOrder($username)
{

	//  GLOBAL $usertype;

	 // outputs all the content of the users shopping cart 

	 dbConnect();

	 $sql = "SELECT *, temp_orders.quantity AS temp_quantity, items.quantity AS quantity, DATE_FORMAT(items.restocked,'%m/%d/%y') AS date_format, TO_DAYS(CURRENT_DATE)-TO_DAYS(items.released) AS days_old, TO_DAYS(CURRENT_DATE) AS current_day, TO_DAYS(items.restocked) AS restocked_days FROM temp_orders, items, users WHERE  users.username='$username' AND temp_orders.userid=users.userid AND temp_orders.itemid=items.itemid ORDER BY  temp_orders.timestamp DESC";
	 $result = mysql_query($sql);

	 if ($myrow=mysql_fetch_array($result))
	 {
		echo
 "<table border=0 cellspacing=0 cellpadding=3>
 <tr><td colspan=8 class=title3><b>Current Shopping Cart Contents</b></tr>
 <tr class=title4>
 <td><b>Category</td>
 <td><b>Format</td>
 <td><b>Artist</td>
 <td><b>Title</td>
 <td><b>Label</td>
 <td><b>Cond</td>
 <td><b>#</td>
 <td><b>Cost</td></tr>";

 $total = 0;

  	do
   	{
	echo "<tr> <td>".$myrow["category"]."</td><td>".$myrow["format"]."</td><td>".$myrow["artist"]."</td> <td><a 
href=\"?module=viewitem.php&command=ALL&itemselect=".$myrow["itemid"]."\">".$myrow["title"]."</a></td> <td>".$myrow["label"]."</td> 
<td>".$myrow["condition"]."</td>";
 
	$usertype = $myrow["usertype"];
       
 	if (calcQuantity($usertype, $myrow) < 1)
 	{
		echo "<td>OUT</td>";
	}
 	else
 	{  
   		if ($myrow["temp_quantity"] > calcQuantity($usertype, $myrow))
  		{
			echo "<td>NOT ENOUGH (".$myrow["temp_quantity"].")</td>";
		}
		else
	   	{
			echo "<td>".$myrow["temp_quantity"]."</td>";
		};                      
	 };

	 $discountID = getDiscount($usertype, $myrow);
	 $retail = calcPrice($myrow, $discountID);

	 echo "<td>$".number_format($retail*$myrow["temp_quantity"],2,'.','')."</td>";

	 $total = $total+($retail*$myrow["temp_quantity"]);
	 $total = number_format($total,2,'.','');
	 echo "<td>(<a href=\"?module=login.php&command=DELETE&itemid=".$myrow["itemid"]."\">REMOVE</a>)</td></tr>";

	 } while ($myrow=mysql_fetch_array($result));

  		echo "<tr><td colspan=6></td><td><b>Total:<b></td><td>$".$total."</td></tr>";
  		echo "</table>";
	}
	else
	{
		echo "No Items in your Shopping Cart";
	};

};

//};
// end showOrder()

// *** START OF ACTUAL MAIN PAGE CODE ***

// includes 

// variables

//   if (!$_SESSION["usertype"]) {$usertype = 0;} else {$usertype = $_SESSION["usertype"];};


   dbConnect();

    // update current order basket with new item

     if ($itemid)
     {

	// get the userid for the current user

     $result = mysql_query("SELECT userid FROM users WHERE username='$username'");
     $temp = mysql_fetch_array($result);
     $userid = $temp[0];    

	$quantity = 0;

	// get info on current items in basket

     $check = "SELECT * FROM temp_orders WHERE temp_orders.userid='$userid' AND temp_orders.itemid='$itemid'";
     $checkresult = mysql_query($check);
     
     if ($myrow = mysql_fetch_array($checkresult))
	{
	     $quantity = $myrow["quantity"];
	};

     if ($quantity < 1)
      {
       $sql = "INSERT INTO temp_orders (temp_orderid, userid, itemid, quantity, timestamp) VALUES  (0, '$userid', '$itemid', 1 ,now())";
       $result = mysql_query($sql);
      }
      else
      {
       $sql = "UPDATE temp_orders SET quantity=quantity+1 WHERE itemid='$itemid'";
       $result = mysql_query($sql);
      };

     echo "<b><i>A new item was added to your shopping cart!</i></b><p>";
     showOrder($username);
     }
     else
     {
      showOrder($username);
     };

       
     // run SQL against the DB
	$back = $_REQUEST['back'];
	$backcommand = $_REQUEST['backcommand'];
	$keyword = $_REQUEST['keyword'];
	
      echo "<p><a href=\"?module=login.php&command=OUT\">Check Out Now</a> or go ";
      echo "<a href=\"?module=$back&command=$backcommand&sort=$sort&lower=$lower&number=$number&desc=$desc&mode=$mode&keyword=$keyword\">back to the previous page</a>";

?>
