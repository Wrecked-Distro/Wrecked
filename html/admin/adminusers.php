<?php
include("header.php");
include("db.php");

function getTotalSpending($username)
{
  dbConnect();
  $sql = mysql_query("SELECT SUM(sales_orders.order_cost) as total FROM sales_orders, users WHERE
  sales_orders.userid=users.userid AND username='$username'");
  $result=mysql_fetch_array($sql);
  $total=$result["total"];
  return $total;
};

function getOrders($username)
{
  dbConnect();
  $sql = mysql_query("SELECT COUNT(sales_orders.userid) as ordercount FROM sales_orders, users WHERE
  sales_orders.userid=users.userid AND username='$username'");
  $result = mysql_fetch_array($sql);
  $ordercount=$result["ordercount"];

  return $ordercount;
};

?>

<html>
<head>
<title>user admin</title>
</head>

<body>

<b>USER admin</b>

<p>

<?php

  $database=;
  $table="users";
  $primary_key="userid";

   if (!$lower) {$lower=0;};
   if (!$number) {$number=20;};
   if (!$desc) {$desc="DESC";};
   if (!$sort) {$sort=$primary_key;};
   if (!$keyword) {$keyword="";};
   if (!$search) {$search="username";};

   dbConnect($database);
?>

<table>
<tr align=right>
<td><form action="adminitemsearch.php" method="post">
ENTER SEARCH KEYWORD(S)
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo $lower;?>">
<input type="hidden" name="mode" value="<? echo $mode;?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="text" name="keyword" size="12" value="<? echo $keyword; ?>" class="form1">
<select name="search" class="form1">
<option <? if ($search=="artist") echo "selected";?> name="search" value="artist" class="form1">artist
<option <? if ($search=="title") echo "selected";?> name="search" value="title">title
<option <? if ($search=="label") echo "selected";?> name="search" value="label">label
<option <? if ($search=="description") echo "selected";?> name="search" value="description">description
</select>
<input type="submit" name="show" value="Search" class="button1">
</form>
</td></tr>
</table>

<?

   $result=mysql_query("SELECT COUNT(userid) FROM users WHERE $search LIKE \"%$keyword%\" ");
   $total=mysql_fetch_array($result);

   if ($lower<0) {$lower=$total[0];};
   if ($lower>$total[0]) {$lower=0;};

   if ($submit)
   {
    // here if no ID then adding, else we're editing

     if ($userid)
     {
      $sql = "UPDATE $table SET username='$username', password='$password', hint='$hint',
first_name='$first_name', last_name='$last_name', phone='$phone', address='$address',
city='$city', state='$state', zip='$zip', country='$country', email='$email', billing_method='$billing_method',
shipping='$shipping', cc_type='$cc_type', cc_number='$cc_number', cc_expire='$cc_expire', start_date='$start_date',
note='$note', mailinglist='$mailinglist', usertype='$usertype' WHERE userid='$userid'";
      echo "Update of ".$username."\n";

     }
     else
     {
  $sql = "INSERT INTO $table (userid, username, password, hint, first_name, last_name, phone, address, city, state,
zip, country, email, billing_method, shipping, cc_type,  cc_number, cc_expire, start_date, note, mailinglist) VALUES  (0,
'$username', '$password', '$hint', '$first_name', '$last_name',
'$phone', '$address', '$city', '$state', '$zip', '$country', '$email', '$billing_method', '$shipping',
'$cc_type', '$cc_number', '$cc_expire', now(), '$note', '$mailinglist')";

      echo "inserting ".$username."\n";

     }

     // run SQL against the DB

      $result = mysql_query($sql);

      echo "Record updated.<p>";

      echo "<a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";

     } elseif ($delete) {

       // delete a record

	if ($confirm)
	{
	  echo "Are you sure you want to delete user = ".$userid."?";
	  echo " (<a
href=\"$PHP_SELF?userid=$userid&delete=yes&sort=$sort&lower=$lower&number=$number&desc=$desc\">YES</a>
/ <a href=\"javascript:history.back()\">NO</a>)";
	} else
	{
       $sql = "DELETE FROM temp_orders WHERE userid='$userid'";
       $result = mysql_query($sql);
       echo "$sql Temp_Orders deleted!<p>";

       $sql = "DELETE FROM users WHERE userid='$userid'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";
      echo "<a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";
      };
     } else {

      // this part happens if we don't press submit

     if (!$userid) {
    // print the list if there is not editing

     $result = mysql_query("SELECT *, DATE_FORMAT(start_date,'%m/%d/%y') AS start_date FROM users WHERE
$search LIKE \"%$keyword%\" ORDER BY $sort $desc LIMIT  $lower, $number");


     if ($myrow = mysql_fetch_array($result))
     {

       echo "<table border=0 cellspacing=0 cellpadding=3>\n";

       echo "<tr><td class=\"title1\" colspan=16><b>Current Users</b></td></tr>\n";
       echo "<tr class=\"title2\">
             <td>
<a href=\"$PHP_SELF?sort=userid&lower=$lower&number=$number&desc=$desc\">UserID</a></td>
             <td>
<a href=\"$PHP_SELF?sort=username&lower=$lower&number=$number&desc=$desc\">Username</a></td>
             <td>
<a href=\"$PHP_SELF?sort=first_name&lower=$lower&number=$number&desc=$desc\">First</a></td>
             <td>
<a href=\"$PHP_SELF?sort=last_name&lower=$lower&number=$number&desc=$desc\">Last</a></td>
             <td>
<a href=\"$PHP_SELF?sort=phone&lower=$lower&number=$number&desc=$desc\">Phone</a></td>
             <td>
<a href=\"$PHP_SELF?sort=address&lower=$lower&number=$number&desc=$desc\">Address</a></td>
             <td>
<a href=\"$PHP_SELF?sort=city&lower=$lower&number=$number&desc=$desc\">City</a></td>
             <td>
<a href=\"$PHP_SELF?sort=state&lower=$lower&number=$number&desc=$desc\">State</a></td>
             <td>
<a href=\"$PHP_SELF?sort=zip&lower=$lower&number=$number&desc=$desc\">Zip</a></td>
             <td>
<a href=\"$PHP_SELF?sort=country&lower=$lower&number=$number&desc=$desc\">Country</a></td>
             <td>
<a href=\"$PHP_SELF?sort=email&lower=$lower&number=$number&desc=$desc\">Email</a></td>
             <td>
<a href=\"$PHP_SELF?sort=mailinglist&lower=$lower&number=$number&desc=$desc\">List</a></td>
             <td>
<a href=\"$PHP_SELF?sort=start_date&lower=$lower&number=$number&desc=$desc\">Start Date</a></td>
             <td>
<a href=\"$PHP_SELF?sort=start_date&lower=$lower&number=$number&desc=$desc\">Orders</a></td>
             <td>
<a href=\"$PHP_SELF?sort=start_date&lower=$lower&number=$number&desc=$desc\">Spending</a></td>
             <td>
<a href=\"$PHP_SELF?sort=sessions&lower=$lower&number=$number&desc=$desc\">Sessions</a></td>

             </tr>\n";

       do
       {
        printf("<tr><td>%s</td> <td>%s</td> <td>%s</td><td>%s</td><td>%s</td> <td>%s</td>
 <td>%s</td>  <td>%s</td> <td>%s</td> <td>%s</td> <td>%s</td>
<td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td>
",
        $myrow["userid"], $myrow["username"], $myrow["first_name"],$myrow["last_name"], $myrow["phone"],
$myrow["address"], $myrow["city"], $myrow["state"], $myrow["zip"],  $myrow["country"], $myrow["email"], $myrow["mailinglist"],
 $myrow["start_date"],getOrders($myrow["username"]),getTotalSpending($myrow["username"],$myrow["sessions"]));

        printf("<td><a
href=\"%s?userid=%s&delete=yes&confirm=yes&sort=$sort&lower=$lower&number=$number&desc=$desc\">(DELETE)</a></td><td><a
href=\"%s?userid=%s&sort=$sort&lower=$lower&number=$number&desc=$desc\">(EDIT)</a></td></tr>",
        $PHP_SELF,$myrow["userid"],$PHP_SELF,$myrow["userid"]);

       } while ($myrow=mysql_fetch_array($result));
       echo "</table>\n";
      }

     echo "<p>";

     }

    ?>

<table>
<tr><td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="sort" value="<? echo $sort;?>">
<input type="submit" name="show" value="Show:" class="button1">
<input type="text" name="number" size="3" value="<? echo $number; ?>" class="form1">
rows beginning with number
<input type="text" name="lower" size="3" value="<? echo $lower; ?>" class="form1">
in
<select name="desc" class="form1">
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
<input type="submit" name="show" value="Previous <? echo $number;?>" class="button1">
</form>
</td>

<td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Next <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $total[0]; ?>">
<input type="hidden" name="lower" size="3" value="0">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Show All" class="button1">
</form>
</td>
</tr>
</table>
     <p>

     <a href="<?php echo $PHP_SELF?>">ADD A user</a>

     <p>

     <form method="post" name="addform" action="<?php echo $PHP_SELF?>" >

     <?

     if ($userid)
     {

     // editing so select a record

     $sql = "SELECT * FROM users WHERE userid=$userid";

     $result = mysql_query($sql);

     $myrow = mysql_fetch_array($result);

     $username = $myrow["username"];

     $password = $myrow["password"];

     $hint = $myrow["hint"];

     $first_name = $myrow["first_name"];

     $last_name = $myrow["last_name"];

     $phone = $myrow["phone"];

     $address = $myrow["address"];

     $city = $myrow["city"];

     $state = $myrow["state"];

     $zip = $myrow["zip"];

     $country = $myrow["country"];

     $email = $myrow["email"];

     $billing_method = $myrow["billing_method"];

     $shipping = $myrow["shipping"];

     $cc_type = $myrow["cc_type"];

     $cc_number = $myrow["cc_number"];

     $cc_expire = $myrow["cc_expire"];

     $start_date = $myrow["start_date"];

     $note = $myrow["note"];

     $mailinglist = $myrow["mailinglist"];

     $sessions = $myrow["sessions"];

     $usertype = $myrow["usertype"];

     // print the id for editing

     ?>

     <input type=hidden name="userid" value="<?php echo $userid ?>">

     <?
     }

     ?>

     Fill in all fields to add a new user<br>     *'d fields are optional.<p>
     <table>


     <tr><td>
        <font class=text3>
        Username
        </td><td><input type="Text" size=12 name="username" value="<? echo $username; ?>">
     </td></tr>


     <tr><td>
        <font class=text3>
     Password
     </td><td><input type="password" size=12 name="password" value="<? echo $myrow["password"] ?>"></td>
     </tr>

     <tr><td>
        <font class=text3>
     Hint
     </td><td><input type="Text" name="hint" value="<? echo $myrow["hint"] ?>"></td>
     </tr>

     <tr><td>
        <font class=text3>
     First Name
     </td>
     <td>
     <input type="Text" name="first_name" value="<? echo $myrow["first_name"] ?>">
     </td>
     </tr>

     <tr><td>
        <font class=text3>
     Last Name
     </td>
     <td>
     <input type="Text" name="last_name" value="<? echo $myrow["last_name"] ?>">
     </td>
     </tr>


     <tr><td>
        <font class=text3>
     Phone
     </td>
     <td>
     <input type="Text" name="phone" size=12 value="<? echo $myrow["phone"] ?>">
     </td>
     </tr>

     <tr><td>
        <font class=text3>
     Address</td>
     <td>
     <textarea name="address" rows="3" cols="24" wrap="virtual"><? echo $myrow["address"] ?></textarea>
     </td>
     </tr>

     <tr><td>
        <font class=text3>
     City
     </td>
     <td>
     <input type="Text" name="city" value="<? echo $myrow["city"] ?>">
     </td>
     </tr>


     <tr><td>
        <font class=text3>
     State
     </td>
     <td>
     <input type="Text" name="state" value="<? echo $myrow["state"] ?>">
     </td>
     </tr>


     <tr><td>
        <font class=text3>
     Zip
     </td>
     <td>
     <input type="Text" name="zip" size=10 value="<? echo $myrow["zip"] ?>">
     </td>
     </tr>


     <tr><td>
        <font class=text3>
     Country
     </td>
     <td>
     <input type="Text" name="country" value="<? echo $myrow["country"] ?>">
     </td>
     </tr>


     <tr><td>
        <font class=text3>
     Email
     </td>
     <td>
     <input type="Text" name="email" value="<? echo $myrow["email"] ?>">
     </td>
     </tr>


     <tr><td>
        <font class=text3>
     Billing Address</td>
     <td>
     <textarea name="billing_address" rows="3" cols="24" wrap="virtual"><? echo $myrow["billing_address"]
?></textarea>
     </td>
     </tr>

     <tr><td>
        <font class=text3>
     Billing City
     </td>
     <td>
     <input type="Text" name="billing_city" value="<? echo $myrow["billing_city"] ?>">
     </td>
     </tr>


     <tr><td>
        <font class=text3>
     Billing State
     </td>
     <td>
     <input type="Text" name="billing_state" value="<? echo $myrow["billing_state"] ?>">
     </td>
     </tr>


     <tr><td>
        <font class=text3>
     Billing Zip
     </td>
     <td>
     <input type="Text" name="billing_zip" value="<? echo $myrow["billing_zip"] ?>">
     </td>
     </tr>


     <tr><td>
        <font class=text3>
     Billing Country
     </td>
     <td>
     <input type="Text" name="billing_country" value="<? echo $myrow["billing_country"] ?>">
     </td>
     </tr>


     <tr><td>
        <font class=text3>

     <a href="adminbilling_method.php">Billing Method</a></td>
     <td>
     <select name="billing_method" size="1">

     <?
      $sql = "SELECT billing_methodid, name FROM billing_method";
      $result = mysql_query($sql);

      if ($billlist=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$billlist["billing_methodid"]."\" ";
       if ($billlist["billing_methodid"]==$myrow["billing_method"])
	{echo "selected";};
       echo ">".$billlist["name"];
      } while ($billlist=mysql_fetch_array($result));
      };
     ?>
     </select>
     </td></tr>

     <tr><td>
        <font class=text3>

     Shipping Method</td>
     <td>
     <select name="shipping" size="1">

     <?
      $sql = "SELECT shippingid, type FROM shipping";
      $result = mysql_query($sql);

      if ($sgiplist=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$shiplist["shippingid"]."\" ";
       if ($shiplist["shippingid"]==$myrow["shipping"])
	{echo "selected";};
       echo ">".$shiplist["type"];
      } while ($shiplist=mysql_fetch_array($result));
      };
     ?>
     </select>
     </td></tr>

     <tr><td>
        <font class=text3>
     Credit Card Type</td>
     <td>
     <select name="cc_type" size="1">

     <option value="VISA" <? if ($cc_type=="VISA") echo "SELECTED"; ?>>VISA
     <option value="MASTERCARD" <? if ($cc_type=="MASTERCARD") echo "SELECTED"; ?>>MASTERCARD
     </select>
     </td></tr>

     <tr><td>
        <font class=text3>
     Credit Card Number</td>
     <td>
     <input type="text" name="cc_number" value="<? echo $myrow["cc_number"] ?>">
     </td>
     </tr>

     <tr><td>
        <font class=text3>
     Expiration Date</td>
     <td>
     <input type="text" name="cc_expire" value="<? echo $myrow["cc_expire"] ?>">
     </td>
     </tr>

     <tr><td>
        <font class=text3>
     Start_Date</td>
     <td>
     <? echo $myrow["retail"] ?>
     </td>
     </tr>

     <tr><td>
        <font class=text3>
     Note</td>
     <td>
     <textarea name="note" rows="7" cols="40" wrap="virtual"><? echo $myrow["note"] ?></textarea>
     </td>
     </tr>

     <tr><td>
        <font class=text3>
     Mailing List</td>
     <td>
     <input type="checkbox" value="1" name="mailinglist" <? if ($mailinglist==1) echo "CHECKED"; ?>>
     </td>
     </tr>

     <tr><td>
        <font class=text3>
	User Type
     </td>
     <td>
     <input type="Text" name="usertype" value="<? echo $myrow["usertype"] ?>">
     </td>
     </tr>


     <tr><td>
        <input type="hidden" name="sort" value="<? echo $sort ?>">
        <input type="hidden" name="lower" value="<? echo $lower ?>">
        <input type="hidden" name="number" value="<? echo $number ?>">
        <input type="hidden" name="desc" value="<? echo $desc ?>">
        <input type="hidden" name="start_date" value="<? if ($myrow["start_date"]) {echo $start_date;} else
 {echo date("M d y",time());}; ?>">
        <input type="Submit" name="submit" value="Enter information" class="button1"></td></tr>

     </table>
     </form>
     <?
     }

?>
<P>



</body>

</html>
