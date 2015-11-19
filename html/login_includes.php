<?php
dbConnect();
// shows a list of all previously ordered items for a user
function showItemHistory($username) {
    echo "<font size=+1><b>PAST ITEMS ORDERED</b></font><P>";

    $sql = "SELECT *,DATE_FORMAT(order_date,'%M %D, %Y') AS order_date, TO_DAYS(CURRENT_DATE)-TO_DAYS(released) AS  days_old, TO_DAYS(CURRENT_DATE) AS current_day, TO_DAYS(restocked) AS  restocked_days
    FROM sales_orders, users, sales_items, items
    WHERE users.username='$username' AND sales_orders.userid=users.userid AND sales_items.sales_orderid = sales_orders.sales_orderid AND sales_items.itemid = items.itemid
    ORDER BY sales_orders.order_date DESC";
    $result = mysql_query($sql);

    if ($myrow = mysql_fetch_array($result)) {
        echo "<table border=0 cellspacing=0 cellpadding=3><tr class=title3><td colspan=10>
  <b>Order Contents</b></tr>
  <tr class=title4>
  <td><b>OrderID</td>
  <td><b>Date</td>
  <td><b>Category</td>
  <td><b>Format</td>
  <td><b>Artist</td>
  <td><b>Title</td>
  <td><b>Label</td>
  <td><b>Cond</td>
  <td><b>#</td>
  <td><b>Cost</td></tr>";

        $total = 0;
        do {

            $itemid = $myrow["itemid"];
            $sql = "SELECT * FROM items WHERE itemid='$itemid'";
            $result2 = mysql_query($sql);
            $item = mysql_fetch_array($result2);
            echo "<tr class=dotbot>
  <td class=dotbot><a href=\"?module=$module&command=PAST&sales_orderid=" . $myrow["sales_orderid"] . "\">" . $myrow["sales_orderid"] . "</td>
  <td class=dotbot>" . $myrow["order_date"] . "</td>
  <td class=dotbot>" . $item["category"] . "</td>
  <td class=dotbot>" . $item["format"] . "</td>
  <td class=dotbot>" . $item["artist"] . "</td>
  <td class=dotbot><a href=\"?module=viewitem.php&command=ALL&itemselect=" . $item["itemid"] . "\">" . $item["title"] . "</a></td>
  <td class=dotbot>" . $item["label"] . " " . $item["catalog"] . "</td>
  <td class=dotbot>" . $item["condition"] . "</td>
  <td class=dotbot>" . $myrow["quantity"] . "</td>";
            $discountID = $myrow["discount"];
            $itemtotal = calcPrice($item, $discountID);

            echo "<td class=dotbot>$" . $itemtotal . "</td>";

            $total = $total + $itemtotal;

            echo "</tr>";
        } while ($myrow = mysql_fetch_array($result));
    };
};

// shows a list of previous orders

function showHistory($username, $sales_orderid) {
    GLOBAL $usertype;
    GLOBAL $module;
    GLOBAL $command;
    echo "<font size=+1><b>PAST ORDERS</b></font><p>";

    $sql = "SELECT *, DATE_FORMAT(order_date,'%M %D, %Y') AS order_date, DATE_FORMAT(sent_date,'%M %D, %Y') AS sent_date FROM
sales_orders, users WHERE users.username='$username' AND sales_orders.userid=users.userid ORDER BY sales_orders.order_date
DESC";
    $result = mysql_query($sql);
    if ($myrow = mysql_fetch_array($result)) {
        echo "<table border=0 cellspacing=0 cellpadding=3 class='dotborder'><tr><td class=title3 colspan=5>
<b>Past Order Information</b></tr>
<tr class=title4>
<td><b>ID</td>
<td><b>Order Date</td>
<td><b>Send Date</td>
<td><b>Total</td>
<td><b>Status</td></tr>";

        do {
            $total = $myrow["order_cost"] + $myrow["tax_cost"] + $myrow["shipping_cost"];
            $total = number_format($total, 2, '.', '');
            if ($myrow["sales_orderid"] == $sales_orderid) {
                echo "<tr bgcolor=000000>";
            } else {
                echo "<tr>";
            };
            echo "
 <td>" . $myrow["sales_orderid"] . "</td>
 <td>" . $myrow["order_date"] . "</td>
 <td>" . $myrow["sent_date"] . "</td>
<td>$" . $total . "</td>";

            if ($myrow["paid"]) {
                if ($myrow["sent"]) {
                    echo "<td>Complete</td>";
                } else {
                    echo "<td>Paid</td>";
                };
            } else {
                if ($myrow["confirm"]) {
                    echo "<td>Confirmed</td>";
                } else {
                    echo "<td>Ordered</td>";
                };
            };

            echo "<td>(<a href=\"?module=$module&command=PAST&sales_orderid=" . $myrow["sales_orderid"] . "\">DETAILS</a>)</td></tr>";
        } while ($myrow = mysql_fetch_array($result));

        echo "</table>";
        echo "<P>";

        if ($sales_orderid) {
            printOrder($username, $sales_orderid);
        };
    } else {
        echo "No Past Orders.";
    };
}

// shows all currently pending and past orders

function printOrder($username, $sales_orderid) {
    echo "<font size=+1><b>ORDER DETAIL</b></font><p>";

    // get sales orders information

    $sqlsale = "SELECT *, DATE_FORMAT(order_date,'%M %D, %Y') AS order_format, DATE_FORMAT(paid_date,'%M %D, %Y') AS paid_date,
  DATE_FORMAT(sent_date,'%M %D, %Y') AS sent_date FROM sales_orders WHERE sales_orderid=$sales_orderid ORDER BY sales_orderid
  DESC";

    $saleresult = mysql_query($sqlsale);

    if ($salerow = mysql_fetch_array($saleresult)) {
        do {
            if ($salerow["sent"]) {
                $tablecolor = "release2";
            } else {
                $tablecolor = "release1";
            };

            echo "<table width=600 class=dotbot><tr ";

            echo "><td>";

            $total = $salerow["order_cost"] + $salerow["shipping_cost"] + $salerow["tax_cost"];
            $total = number_format($total, 2, '.', '');
            echo "<b>WRECKED Order " . $salerow["sales_orderid"] . " Information</b> <br>";
            echo "<b>Date:</b> " . $salerow["order_format"] . "<br>";
            echo "<b>Status:</b> ";

            if ($salerow["confirm"]) {
                if ($salerow["paid"]) {
                    if ($salerow["sent"]) {
                        echo "Complete! Package Sent on " . $salerow["sent_date"] . "<br><b>What You Do:</b> Let me know when it arrives! Enjoy.<br>";
                    } else {
                        echo "Payment received on " . $salerow["paid_date"] . ". Pending shipping/pickup/delivery.<br>";
                        if ($salerow["shipping_method"] == 4) {
                            echo "<b>What You Do:</b> Meet for pickup/wait for delivery.<br>";
                        } else {
                            echo "<b>What You Do:</b> Wait for shipping confirmation.<br>";
                        };
                    };
                } else {
                    echo "Order confirmed.  Pending payment.<br>";
                    if ($salerow["billing_method"] == 1) {
                        echo "<b>What You Do:</b> Get me the cash. Total <b>$" . $total . "</b><p> Send to:<br>WRECKED<br>50 Pasadena Street<br>Pittsburgh PA 15211<br>USA<p>";
                    };
                    if ($salerow["billing_method"] == 3) {
                        echo "<b>What You Do:</b> Paypal <b>$" . $total . "</b> to sales@wrecked-distro.com. ";
                        echo "<br>
Click the paypal icon to pay now.
<form name=\"_xclick\" target=\"paypal\" action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">
<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">
<input type=\"hidden\" name=\"business\" value=\"sales@wrecked-distro.com\">
<input type=\"hidden\" name=\"item_name\" value=\"Order #" . $salerow["sales_orderid"] . " - " . $username . "\">
<input type=\"hidden\" name=\"item_number\" value=\"1\">
<input type=\"hidden\" name=\"amount\" value=\"" . $total . "\">
<input type=\"image\" src=\"http://www.paypal.com/images/x-click-but01.gif\" border=\"0\" name=\"submit\"
alt=\"Make payments with PayPal - it's fast, free and secure!\">

<input type=\"hidden\" name=\"add\" value=\"1\"> </form><br>";
                    };
                    if ($salerow["billing_method"] == 4) {
                        echo "<b>What You Do:</b> Make check for $" . $total . " payable to Geoff
Maddock.<p>Send to:<br>WRECKED<br> 50 Pasadena Street<br>Pittsburgh PA 15211<br>USA<br>";
                    };
                    if ($salerow["billing_method"] == 5) {
                        echo "<b>What You Do:</b> Make money order for $" . $total . " payable to Geoff
Maddock.<p>Send to:<br>WRECKED<br> 50 Pasadena Street<br>Pittsburgh PA 15211<br>USA<br>";
                    };
                };
            } else {
                echo "Order submitted. Pending confirmation.<br><b>What You Do:</b><br>Wait for confirmation email, or check
this page for confirmation of order.<br>";
            };

            echo "</font><P>";
            showSoldItems($salerow["sales_orderid"]);
            echo "<b>Billing Method</b><br> ";
            $bill = $salerow["billing_method"];

            $billsql = "SELECT * FROM billing_method WHERE billing_methodid='$bill'";
            $billresult = mysql_fetch_array(mysql_query($billsql));
            $billing_method = $billresult["name"];

            echo $billing_method . "<br>";

            echo "<p><b>Shipping Method</b><br> ";

            $ship = $salerow["shipping_method"];

            $shipsql = "SELECT * FROM shipping WHERE shippingid='$ship'";
            $shipresult = mysql_fetch_array(mysql_query($shipsql));
            $shipping_method = $shipresult["type"];

            echo $shipping_method . "<p>";

            echo "<b>Shipping Address</b><br>";
            echo $salerow["ship_name"] . "<br>";
            echo $salerow["ship_address"] . "<br>";
            echo $salerow["ship_city"] . ", " . $salerow["ship_state"] . " " . $salerow["ship_zip"] . "<br>";
            echo $salerow["ship_country"] . "<br>";

            echo "<p><b>Note</b><br>";
            if ($salerow["note"]) {
                echo $salerow["note"] . "<p>";
            } else {
                echo "No special instructions<p>";
            };
            echo "<b>Sub Total:</b> $" . $salerow["order_cost"] . "<br>";
            echo "<b>Tax (PA Residents):</b> $" . $salerow["tax_cost"] . "<br>";
            echo "<b>Shipping:</b> $" . $salerow["shipping_cost"] . "<br>";
            $grandtotal = $salerow["order_cost"] + $salerow["shipping_cost"] + $salerow["tax_cost"];
            $grandtotal = number_format($grandtotal, 2, '.', '');
            echo "<b>Grand Total:</b> $" . $grandtotal;
            echo "</td></tr></table>";
            echo "<P>---<P>";
        } while ($salerow = mysql_fetch_array($saleresult));
    } else {
        echo "No past or pending orders.";
    };
}

function showAddress($username) {

    // shows the mailing address for the user

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
    echo $userfirst . " " . $userlast . "<br>";
    echo $useraddress . "<br>";
    echo $usercity . ", " . $userstate . " " . $userzip . "<br>";
    echo $usercountry . "<br>";
}

function showOrders($username) {

    // shows all currently pending orders
    echo "<font size=+1><b>ORDER STATUS</b></font><p>";

    // first, get the userid for the order

    $sqluser = "SELECT * FROM users WHERE username='$username'";
    $userinfo = mysql_fetch_array(mysql_query($sqluser));
    $userid = $userinfo["userid"];

    // show all sales orders

    $sqlsale = "SELECT *, DATE_FORMAT(order_date,'%M %D, %Y') AS order_format, DATE_FORMAT(paid_date,'%M %D, %Y') AS
  paid_date, DATE_FORMAT(sent_date,'%M %D, %Y') AS sent_date FROM sales_orders WHERE userid='$userid' AND (sent=0 OR
  (TO_DAYS(current_date)-TO_DAYS(sent_date)) < 14) ORDER BY sales_orderid DESC";
    $saleresult = mysql_query($sqlsale);

    if ($salerow = mysql_fetch_array($saleresult)) {
        do {

            if ($salerow["sent"]) {
                $tablecolor = "release2";
            } else {
                $tablecolor = "release1";
            };

            echo "<table class=dotbot width=500><tr bgcolor=000000><td>";

            $total = $salerow["order_cost"] + $salerow["shipping_cost"] + $salerow["tax_cost"];
            $total = number_format($total, 2, '.', '');
            echo "<b>WRECKED Order " . $salerow["sales_orderid"] . " Information</b><br>";
            echo "<b>Date:</b> " . $salerow["order_format"] . "<br>";
            echo "<b>Status:</b> ";

            if ($salerow["confirm"]) {
                if ($salerow["paid"]) {
                    if ($salerow["sent"]) {
                        echo "Complete! Package Sent on " . $salerow["sent_date"] . "<br><b>What You Do:</b> Let me know when it arrives! Enjoy.<br>";
                    } else {
                        echo "Payment received on " . $salerow["paid_date"] . ". Pending shipping/pickup/delivery.<br>";
                        if ($salerow["shipping_method"] == 4) {
                            echo "<b>What You Do:</b> Meet for pickup/wait for delivery.<br>";
                        } else {
                            echo "<b>What You Do:</b> Wait for shipping confirmation.<br>";
                        };
                    };
                } else {
                    echo "Order confirmed.  Pending payment.<br>";
                    if ($salerow["billing_method"] == 1) {
                        echo "<b>What You Do:</b> Get me the cash. Total <b>$" . $total . "</b><p>
  Send to:<br>WRECKED<br>50 Pasadena Street<br>Pittsburgh PA 15211<br>USA<p>";
                    };
                    if ($salerow["billing_method"] == 3) {
                        echo "<b>What You Do:</b> Paypal <b>$" . $total . "</b> to geoff.maddock@gmail.com. ";
                        echo "<br>
  Click the paypal icon to pay now.
  <form name=\"_xclick\" target=\"paypal\" action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">
  <input type=\"hidden\" name=\"cmd\" value=\"_xclick\">
  <input type=\"hidden\" name=\"business\" value=\"sales@wrecked-distro.com\">
  <input type=\"hidden\" name=\"item_name\" value=\"Order #" . $salerow["sales_orderid"] . " - " . $username . "\">
  <input type=\"hidden\" name=\"item_number\" value=\"1\">
  <input type=\"hidden\" name=\"amount\" value=\"" . $total . "\">
  <input type=\"image\" src=\"http://www.paypal.com/images/x-click-but01.gif\" border=\"0\" name=\"submit\"
  alt=\"Make payments with PayPal - it's fast, free and secure!\">

  <input type=\"hidden\" name=\"add\" value=\"1\"> </form><br>";
                    };
                    if ($salerow["billing_method"] == 4) {
                        echo "<b>What You Do:</b> Make check for $" . $total . " payable to Geoff
  Maddock.<p>Send to:<br>WRECKED<br> 50 Pasadena Street<br>Pittsburgh PA 15211<br>USA<br>";
                    };
                    if ($salerow["billing_method"] == 5) {
                        echo "<b>What You Do:</b> Make money order for $" . $total . " payable to Geoff
  Maddock.<p>Send to:<br>WRECKED<br> 50 Pasadena Street<br>Pittsburgh PA 15211<br>USA<br>";
                    };
                };
            } else {
                echo "Order submitted. Pending confirmation.<br><b>What You Do:</b><br>The contents of your order will be  confirmed in stock within the next 24-48 hours. Wait for our confirmation email, or check this page   for your total and payment instructions.<br>";
            };

            echo "</font><P>";
            showSoldItems($salerow["sales_orderid"]);

            echo "<b>Billing Method</b><br> ";

            $bill = $salerow["billing_method"];

            $billsql = "SELECT * FROM billing_method WHERE billing_methodid='$bill'";
            $billresult = mysql_fetch_array(mysql_query($billsql));
            $billing_method = $billresult["name"];

            echo $billing_method . "<br>";

            echo "<p><b>Shipping Method</b><br> ";

            $ship = $salerow["shipping_method"];

            $shipsql = "SELECT * FROM shipping WHERE shippingid='$ship'";
            $shipresult = mysql_fetch_array(mysql_query($shipsql));
            $shipping_method = $shipresult["type"];

            echo $shipping_method . "<p>";

            echo "<b>Shipping Address</b><br>";
            echo $salerow["ship_name"] . "<br>";
            echo $salerow["ship_address"] . "<br>";
            echo $salerow["ship_city"] . ", " . $salerow["ship_state"] . " " . $salerow["ship_zip"] . "<br>";
            echo $salerow["ship_country"] . "<br>";
            echo "<p><b>Note</b><br>";
            if ($salerow["note"]) {
                echo $salerow["note"] . "<p>";
            } else {
                echo "No special instructions<p>";
            };
            echo "<b>Sub Total:</b> $" . $salerow["order_cost"] . "<br>";
            echo "<b>Tax (PA Residents):</b> $" . $salerow["tax_cost"] . "<br>";
            echo "<b>Shipping:</b> $" . $salerow["shipping_cost"] . "<br>";
            $grandtotal = $salerow["order_cost"] + $salerow["shipping_cost"] + $salerow["tax_cost"];
            $grandtotal = number_format($grandtotal, 2, '.', '');
            echo "<b>Grand Total:</b> $" . $grandtotal;
            echo "</td></tr></table>";
            echo "<P>---<P>";
        } while ($salerow = mysql_fetch_array($saleresult));
    } else {
        echo "No past or pending orders.";
    };
}

function showSoldItems($sales_orderid) {

    // show a table containing all the items in an order to be processed

    GLOBAL $usertype;

    dbConnect();

    $sql = "SELECT *, sales_items.quantity AS orderQuantity, items.quantity AS quantity,
TO_DAYS(CURRENT_DATE)-TO_DAYS(released)
AS days_old, TO_DAYS(CURRENT_DATE) AS current_day, TO_DAYS(restocked) AS
restocked_days FROM sales_items, items WHERE
sales_items.itemid=items.itemid AND sales_items.sales_orderid='$sales_orderid' ORDER BY sales_items.sales_itemid DESC";
    $result = mysql_query($sql);
    if ($myrow = mysql_fetch_array($result)) {
        echo "<table border=0 cellspacing=0 cellpadding=3 class='dotbot'><tr class=title3><td colspan=8>
<b>Order Contents</b></tr>
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

        do {
            echo "<tr class=dotbot>
 <td>" . $myrow["category"] . "</td>
 <td>" . $myrow["format"] . "</td>
 <td>" . $myrow["artist"] . "</td>
 <td><a href=\"?module=viewitem.php&command=ALL&search=itemid&keyword=" . $myrow["itemid"] . "\">" . $myrow["title"] . "</a></td>
 <td>" . $myrow["label"] . " " . $myrow["catalog"] . "</td>
 <td>" . $myrow["condition"] . "</td>
 <td>" . $myrow["orderQuantity"] . "</td>";

            $discount = $myrow["discount"];
            $orderQuantity = $myrow["orderQuantity"];
            $itemtotal = calcPrice($myrow, $discount) * $orderQuantity;
            $itemtotal = number_format($itemtotal, 2, '.', '');
            echo "<td>$" . $itemtotal . "</td>";

            $total = $total + $itemtotal;

            echo "</tr>";
        } while ($myrow = mysql_fetch_array($result));

        $total = number_format($total, 2, '.', '');
        echo "<tr ><td colspan=6 ></td><td><b> SubTotal:<b></td><td>$" . $total . "</td></tr>";
        echo "</table>";
    } else {
        echo "No Items in this Order!";
    };
}

function processOrder($username, $shipping, $billing, $total, $tax, $note) {

    dbConnect();

    // first, get the userid for the user processing the order

    $sqluser = "SELECT * FROM users WHERE username='$username'";
    $userinfo = mysql_fetch_array(mysql_query($sqluser));
    $userid = $userinfo["userid"];
    $usertype = $userinfo["usertype"];

    $shipname = $userinfo["first_name"] . " " . $userinfo["last_name"];
    $shipaddress = $userinfo["address"];
    $shipcity = $userinfo["city"];
    $shipstate = $userinfo["state"];
    $shipzip = $userinfo["zip"];
    $shipcountry = $userinfo["country"];
    $email = $userinfo["email"];

    // add the sales order to the database

    $sqlcreatesale = "INSERT INTO sales_orders (sales_orderid, userid, order_date, confirm, paid_date, paid, sent_date, sent,
order_cost, tax_cost, shipping_cost, shipping_method, billing_method, note, ship_name, ship_address, ship_city, ship_state,
ship_zip, ship_country) VALUES  (0, '$userid', CURRENT_DATE, 0, CURRENT_DATE, 0, CURRENT_DATE, 0, '$total', '$tax',
0,'$shipping', '$billing', '$note', '$shipname','$shipaddress','$shipcity','$shipstate','$shipzip','$shipcountry')";
    $createsale = mysql_query($sqlcreatesale);

    // retreive the sales id of the new sale

    $sqlsale = "SELECT MAX(sales_orderid) as sales_orderid FROM sales_orders WHERE userid='$userid'";
    $saleinfo = mysql_fetch_array(mysql_query($sqlsale));
    $saleid = $saleinfo["sales_orderid"];

    // add the sales items to the sales items database for confirmation

    $sqlitems = "SELECT *, temp_orders.quantity AS orderQuantity, items.quantity AS quantity, temp_orders.itemid AS itemid,
TO_DAYS(CURRENT_DATE)-TO_DAYS(released) AS days_old, TO_DAYS(CURRENT_DATE) AS current_day, TO_DAYS(restocked) AS
restocked_days FROM temp_orders, items WHERE userid='$userid' AND temp_orders.itemid=items.itemid ORDER BY
temp_orders.timestamp DESC";

    $itemresult = mysql_query($sqlitems);

    if ($itemrow = mysql_fetch_array($itemresult)) {
        do {
            $itemid = $itemrow["itemid"];
            $discount = getDiscount($usertype, $itemrow);
            if ($itemrow["orderQuantity"] > $itemrow["quantity"]) {
                $quantity = $itemrow["quantity"];
            } else {
                $quantity = $itemrow["orderQuantity"];
            };
            $additem = mysql_query("INSERT INTO sales_items (sales_itemid, sales_orderid, itemid, quantity, confirm, discount) VALUES (0,
'$saleid', '$itemid','$quantity',0,'$discount')");
        } while ($itemrow = mysql_fetch_array($itemresult));
    } else {
        echo "No items in order";
    };

    // remove the items from temp_orders

    $tempitems = "DELETE FROM temp_orders WHERE userid='$userid'";
    $tempresult = mysql_query($tempitems);

    // tell the user that its been accepted or rejected, and display the order?

    echo "<P><b><i>Your Order has been accepted!  Once we confirm that your order is in stock,<br>
            you'll receive an email with instructions on what to do next.  Expect it within 24-48 hours.</i></b><P>";

    showOrders($username);

    // email data to me - may want to convert to a new function

    $to = "sales@wrecked-distro.com";
    $subject = $username . " - New Order #" . $saleid;

    //$body = "WRECKED - \n\n<br><br>New order submitted for user [".$username."].<br><br>\n\nOrder #".$saleid."\n\n<br><br>Log in and confirm this order ASAP.\n<br>";

    $body = "WRECKED - \n\nNew order submitted for user [" . $username . "].\n\nOrder #" . $saleid . "\n\nLog in and confirm this order
ASAP.\n";

    // $body=$body.showOrders($username);
    $body = $body . "Your friendly automated ordering system.\n\n-YFAOR";

    $from = "From: sales@wrecked-distro.com\r\n";
    $from.= "Reply-To: sales@wrecked-distro.com\r\n";
    $from.= "X-Mailer: PHP/" . phpversion();

    // $email = "sales@wrecked-distro.com";
    $from = "sales@wrecked-distro.com";
    $namefrom = "wrecked";
    $nameto = $username;
    $message = $body;

    mail($to, $subject, $message, $from);
}

// function that handles the check-out process
// - needs revision for new price calculation

function checkOut($username) {

    GLOBAL $usertype;
    GLOBAL $module;

    echo "<font size=+1><b>CHECK OUT</b></font><p>";

    // get userid for user who is checking out

    $sqluser = "SELECT * FROM users WHERE username='$username'";
    $userinfo = mysql_fetch_array(mysql_query($sqluser));

    // select items from temp orders, as well as item info from items

    $sql = "SELECT *, temp_orders.quantity AS orderQuantity, items.quantity AS quantity,
TO_DAYS(CURRENT_DATE)-TO_DAYS(released) AS days_old, TO_DAYS(CURRENT_DATE) AS current_day, TO_DAYS(restocked) AS
restocked_days FROM
temp_orders, items, users
WHERE  users.username='$username' AND temp_orders.userid=users.userid AND temp_orders.itemid=items.itemid ORDER BY
temp_orders.timestamp DESC";

    // display results

    $result = mysql_query($sql);
    if ($myrow = mysql_fetch_array($result)) {
        echo "<table border=0 cellspacing=0 cellpadding=3>
    <tr class=title3><td colspan=8><b>Current Shopping Cart Contents</b></td></tr>
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

        do {
            echo "<tr>
 <td>" . $myrow["category"] . "</td>
 <td>" . $myrow["format"] . "</td>
 <td>" . $myrow["artist"] . "</td>
 <td><a href=\"?module=viewitem.php&command=ALL&search=itemid&keyword=" . $myrow["itemid"] . "\">" . $myrow["title"] . "</a></td>
 <td>" . $myrow["label"] . " " . $myrow["catalog"] . "</td>
 <td>" . $myrow["condition"] . "</td>";

            // show how many are in stock, depending on how many are ordered as opposed to the number actually in

            $orderQuantity = $myrow["orderQuantity"];
            $discount = getDiscount($usertype, $myrow);
            $price = calcPrice($myrow, $discount) * $orderQuantity;

            // $retail = retailPrice($myrow["itemid"]);

            if ($myrow["quantity"] < 1) {
                echo "<td>OUT</font></td><td>$0</td>";
            } else {
                if ($myrow["orderQuantity"] > $myrow["quantity"]) {
                    echo "<td>" . $myrow["instock"] . "</font></td> <td>$" . number_format($price, 2, '.', '') . "</td>";
                    $total = $total + $price;
                } else {
                    echo "<td>" . $myrow["orderQuantity"] . "</td> <td>$" . number_format($price, 2, '.', '') . "</td>";
                    $total = $total + $price;
                };
            };
            echo "</tr>";
        } while ($myrow = mysql_fetch_array($result));

        echo "<tr><td>
        </td><td colspan=5></td><td><b>Total:<b></td><td>$" . number_format($total, 2, '.', '') . "</td></tr>";
        echo "</table>";
    } else {
        echo "No Items in your Shopping Cart";
    };

    echo "<form action=\"$PHP_SELF\"><P>";

    echo "<b>Billing Method</b><br>
      <select class=form1 style=\"font-name: Arial; font-size: 12px;\" name=\"billing_method\" size=\"1\">";

    $sql = "SELECT * FROM billing_method WHERE access=1";
    $result = mysql_query($sql);

    if ($bill = mysql_fetch_array($result)) {
        do {
            echo "<option value=\"" . $bill["billing_methodid"] . "\" ";
            if ($bill["billing_methodid"] == $userinfo["billing_method"]) {
                echo "SELECTED ";
            };
            echo ">" . $bill["name"];
        } while ($bill = mysql_fetch_array($result));
    };
    echo "</select><P>";

    echo "<b>Shipping Method</b><br>
      <select class=form1 name=\"shipping_method\" size=\"1\">";

    $sql = "SELECT * FROM shipping";
    $result = mysql_query($sql);

    if ($ship = mysql_fetch_array($result)) {
        do {
            echo "<option value=\"" . $ship["shippingid"] . "\" ";
            if ($ship["shippingid"] == $userinfo["shipping"]) {
                echo "SELECTED ";
            };
            echo ">" . $ship["type"];
        } while ($ship = mysql_fetch_array($result));
    };
    echo "</select><p>";
    echo "<b>Note</b><br><textarea name=\"note\" cols=\"40\" rows=\"5\" class=form1></textarea><p>";

    echo "<b>Address</b><br>";
    echo $userinfo["first_name"] . " " . $userinfo["last_name"] . "<br>";
    echo $userinfo["address"] . "<br>";
    echo $userinfo["city"] . ", " . $userinfo["state"] . " " . $userinfo["zip"] . "<br>";
    echo $userinfo["country"] . "<br>";
    echo "(<a href=\"?module=account.php&command=EDIT\">Update Info</a>)<P> ";

    $tax = getTax($userinfo["state"], $total);
    $shipping = 0;

    echo "<b>Sub Total:</b> $" . number_format($total, 2, '.', '') . "<br>";
    echo "<b>Tax (PA Residents):</b> $" . number_format($tax, 2, '.', '') . "<br>";
    echo "<b>Shipping:</b> $" . $shipping . " (pending) <br>";
    echo "<b>Grand Total:</b> $" . number_format($total + $shipping + $tax, 2, '.', '') . " (pending) <p>";
    echo "<input type=\"hidden\" name=\"tax\" value=\"" . $tax . "\">";
    echo "<input type=\"hidden\" name=\"total\" value=\"" . $total . "\">";
    echo "<input type=\"hidden\" name=\"command\" value=\"PROCESS\">";
    echo "<input type=\"hidden\" name=\"module\" value=\"$module\">";
    echo "<input class=button1 type=\"submit\" name=\"submit\"
value=\"Place Order\" ></form>";
}

function getTax($state, $total) {
    if ($state == "PA") {
        return ($total * .07);
    } else {
        return 0;
    };
}

function showUser($username) {
    GLOBAL $user_types;

    echo "<font size=+1><b>MY INFO</b></font><p>";

    dbConnect();

    $sql = "SELECT *, DATE_FORMAT(start_date,'%b %D, %Y') AS date_format FROM users WHERE username='$username'";
    $userinfo = mysql_fetch_array(mysql_query($sql));

    extract($userinfo, EXTR_PREFIX_SAME, "wddx");
    echo "<b>Username:</b> " . $username . "<br>";
    echo "<b>Name:</b> " . $first_name . " " . $last_name . "<br>";
    echo "<b>Email:</b> " . $email . "<br>";
    echo "<b>Phone:</b> " . $phone . "<br>";

    echo "<b>Usertype:</b> " . $ut . " " . $user_types[$usertype] . "<br>";
    echo "<p>";

    echo "<b>Address</b><br>";
    echo $first_name . " " . $last_name . "<br>";
    echo $address . "<br>";
    echo $city . ", " . $state . " " . $zip . "<br>";
    echo $country . "<p>";

    $sql = "SELECT * FROM billing_method WHERE billing_methodid='" . $billing_method . "'";
    $billinginfo = mysql_fetch_array(mysql_query($sql));

    if (!$billinginfo["name"]) {
        $billing_method = "None";
    } else {
        $billing_method = $billinginfo["name"];
    };

    echo "<b>Default Billing Method:</b> " . $billing_method . "<p>";
    echo "<b>Started on " . $userinfo["date_format"];
    echo "<p>(<a href=\"?module=account.php&command=EDIT\">Update Info</a>) ";
}

function showCart($username) {
    GLOBAL $usertype;
    GLOBAL $module;

    echo "<font size=+1><b>CHECK CART</b></font><p>";

    dbConnect();

    $sql = "SELECT *, temp_orders.quantity AS orderQuantity, items.quantity AS quantity,
TO_DAYS(CURRENT_DATE)-TO_DAYS(released) AS days_old, TO_DAYS(CURRENT_DATE) AS current_day, TO_DAYS(restocked) AS
restocked_days  FROM temp_orders, items, users WHERE
users.username='$username' AND temp_orders.userid=users.userid AND temp_orders.itemid=items.itemid ORDER BY
temp_orders.timestamp DESC";
    $result = mysql_query($sql);
    if ($myrow = mysql_fetch_array($result)) {
        echo "<table border=0 cellspacing=0 cellpadding=3><tr><td class=title3 colspan=8>
<b>Current Shopping Cart Contents</b></tr>
<tr class=title4>
<td><b>Category</td>
<td><b>Format</td>
<td><b>Artist</td>
<td><b>Title</td>
<td><b>Label</td>
<td>Cond</td>
<td>#</td>
<td>Cost</td></tr>";

        $total = 0;

        do {
            echo "<tr>
 <td class=dotbot>" . $myrow["category"] . "</td>
 <td class=dotbot>" . $myrow["format"] . "</td>
 <td class=dotbot>" . $myrow["artist"] . "</td>
 <td class=dotbot><a
href=\"?module=viewitem.php&command=ALL&search=itemid&keyword=" . $myrow["itemid"] . "\">" . $myrow["title"] . "</a></td>
 <td class=dotbot>" . $myrow["label"] . "</td>
 <td class=dotbot>" . $myrow["condition"] . "</td>";

            if ($myrow["quantity"] < 1) {
                echo "<td class=dotbot><font color=ff0000>OUT</font></td>";
            } else {
                if ($myrow["orderQuantity"] > $myrow["quantity"]) {
                    echo "<td class=dotbot><font color=ff0000>NOT ENOUGH</font></td>";
                } else {
                    echo "<td class=dotbot>" . $myrow["orderQuantity"] . "</td>";
                };
            };

            $orderQuantity = $myrow["orderQuantity"];
            $discount = getDiscount($usertype, $myrow);
            $price = calcPrice($myrow, $discount) * $orderQuantity;
            $price = number_format($price, 2, '.', '');

            // $retail = retailPrice($myrow["itemid"]);

            echo "<td class=dotbot>$" . $price . "</td>";

            $total = $total + $price;

            echo "<td>(<a href=\"?module=$module&command=DELETE&itemid=" . $myrow["itemid"] . "\">REMOVE</a>)</td></tr>";
        } while ($myrow = mysql_fetch_array($result));

        echo "<tr><td>
  <form action=\"$PHP_SELF\">
        <input class=form1 type=\"hidden\" name=\"module\" value=\"$module\">
        <input class=form1 type=\"hidden\" name=\"command\" value=\"OUT\">
        <input class=form1 type=\"submit\" name=\"submit\" value=\"Check Out\"
class=button1></form>
  </td><td colspan=5></td><td><b>Total:<b></td><td>$" . number_format($total, 2, '.', '') . "</td></tr>";
        echo "</table>";
    } else {
        echo "No Items in your Shopping Cart";
    };
} function deleteItem($itemid) {
    dbConnect();

    //removes one of the items from an order, or deletes the item entirely
    $username = $_SESSION['username'];

    $check = "SELECT * FROM temp_orders, users WHERE users.username='$username' AND temp_orders.userid=users.userid AND
temp_orders.itemid='$itemid'";
    $checkresult = mysql_query($check);

    $checkrow = mysql_fetch_array($checkresult);
    $quantity = $checkrow["quantity"];
    $userid = $checkrow["userid"];

    if ($quantity > 1) {
        $sql = "UPDATE temp_orders SET quantity=quantity-1 WHERE userid='$userid' AND itemid='$itemid'";
        $result = mysql_query($sql);
        echo "<i>selections UPDATED</i><P>";
    } else {
        $sql = "DELETE FROM temp_orders WHERE userid='$userid' AND itemid='$itemid'";
        $result = mysql_query($sql);
        echo "<i>selection REMOVED from your cart</i><P>";
    };
}

function time_format($timestamp) {
    $hour = substr($timestamp, 8, 2);
    $minute = substr($timestamp, 10, 2);
    $second = substr($timestamp, 12, 2);
    $month = substr($timestamp, 4, 2);
    $day = substr($timestamp, 6, 2);
    $year = substr($timestamp, 0, 4);
    $mktime = mktime($hour, $minute, $second, $month, $day, $year);
    $formated = date("F j, Y, g:i a", $mktime);
    return $formated;
}
?>
