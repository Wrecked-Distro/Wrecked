<?php 
/**
 * adminusersseasrch.php
 * functions to manage and create users as well as search for them.
 **/

function getTotalSpending($username)
{
 dbConnect("db9372_distro");
 $sql = "SELECT SUM(sales_orders.order_cost) as total FROM sales_orders, users WHERE sales_orders.userid=users.userid AND username='$username'";
 $query = mysql_query($sql);

 $result = mysql_fetch_array($query);
 $total = $result["total"];
return $total;
};

function getOrders($username)
{ 
 dbConnect("db9372_distro");
 $sql = mysql_query("SELECT COUNT(sales_orders.userid) as ordercount FROM sales_orders, users WHERE sales_orders.userid=users.userid AND username='$username'");
 if ($result = mysql_fetch_array($sql))
  { $ordercount = $result["ordercount"];  }
  else
  { $ordercount = 0;}; 
 return $ordercount;
};

echo "<b>USER admin</b><p>";

$table="users";
$primary_key="userid";

$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : $primary_key;
$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : null;
$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : 'username';
$userid = isset($_REQUEST['userid']) ? $_REQUEST['userid'] : null;

?>

<table>
<tr>
<td><form action="index.php" method="post">
ENTER SEARCH KEYWORD(S)
<input type="hidden" name="module" value="<? echo $module; ?>">
<input type="hidden" name="number"  value="<? echo $number; ?>">
<input type="hidden" name="lower"  value="<? echo $lower;?>">
<input type="hidden" name="mode" value="<? echo $mode;?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="text" name="keyword" size="12" value="<? echo $keyword; ?>" class="form1">
<select  class="form1">
<option <? if ($search=="username") echo "selected";?>  value="username" class="form1">username
<option <? if 
($search=="first_name") echo "selected";?> value="first_name">firstname
<option <? if ($search=="last_name") echo "selected";?>  value="last_name">lastname
<option <? if ($search=="email") echo "selected";?>  value="email">email
<option <? if ($search=="city") echo "selected";?>  value="city">city
<option <? if ($search=="state") echo "selected";?>  value="state">state
<option <? if ($search=="country") echo "selected";?>  value="country">country
<option <? if ($search=="zip") echo "selected";?>  value="zip">zip
</select>
<input type="submit" name="show" value="Search" class="button1">
</form>
</td></tr>
</table>

<?
	$sql = "SELECT COUNT(userid) FROM users WHERE $search LIKE \"%$keyword%\" ";

	$result = mysql_query($sql);

	$total = mysql_fetch_array($result);

	if ($lower < 0) {$lower = $total[0];};
	if ($lower > $total[0]) {$lower=0;};

	if ($_REQUEST['submit'])
	{
		// here if no ID then adding, else we're editing

		import_request_variables("gp");

     		if ($_REQUEST['userid'])
		{
			$sql = "UPDATE $table SET username='$name', password='$password', hint='$hint',
first_name='$first_name', last_name='$last_name', phone='$phone', address='$address', 
city='$city', state='$state', zip='$zip', country='$country', email='$email', billing_method='$billing_method', 
shipping='$shipping', cc_type='$cc_type', cc_number='$cc_number', cc_expire='$cc_expire', start_date='$start_date', 
note='$note', mailinglist='$mailinglist', usertype='$usertype', approved='$approved' WHERE userid='$userid'";
			echo "Update of ".$username."\n";

     		} else
		{
			$sql = "INSERT INTO $table (userid, username, password, hint, first_name, last_name, phone, address, city, state, 
zip, country, email, billing_method, shipping, cc_type,  cc_number, cc_expire, start_date, note, mailinglist, approved) VALUES  
(0, '$name', '$password', '$hint', '$first_name', '$last_name', '$phone', '$address', '$city', '$state', '$zip', '$country', '$email', '$billing_method', '$shipping',
'$cc_type', '$cc_number', '$cc_expire', now(), '$note', '$mailinglist', '$approved')";

			echo "inserting ".$username."\n";

     		};

     // run SQL against the DB

      $result = mysql_query($sql);

      echo "Record updated.<p>";
    
      echo "<a href=\"$PHP_SELF?sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">back</a>";  

     } elseif ($_REQUEST['delete']) {
      
       // delete a record
      if ($_REQUEST['confirm'])
        {
          echo "Are you sure you want to delete user = ".$userid."?";
          echo " (<a href=\"?module=$module&amp;userid=$userid&amp;delete=yes&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">YES</a> / <a href=\"javascript:history.back()\">NO</a>)";
        } else
        {

       $sql = "DELETE FROM temp_orders WHERE userid='$userid'";
       $result = mysql_query($sql);
       echo "$sql Temp_Orders deleted!<p>";

       $sql = "DELETE FROM users WHERE userid='$userid'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";
      echo "<a href=\"?module=$module&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">back</a>";
	};      
     } else {

      // this part happens if we don't press submit

     if (!$userid) {
    // print the list if there is not editing

	$sql = "SELECT *, DATE_FORMAT(start_date,'%m/%d/%y') AS start_date FROM users WHERE $search LIKE \"%$keyword%\" ORDER BY $sort ".$sortArray[$desc]." LIMIT  $lower, $number";
    echo "<div id='query'>Query: ".$sql."</div>";
     $result = mysql_query($sql);


     if ($myrow = mysql_fetch_array($result))
     {
      
       echo "<table>\n";
     
       echo "<tr><td class=\"title1\" colspan='20'><b>Current Users</b></td></tr>\n";
       echo "<tr class=\"title2\">
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=userid&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">UserID</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=username&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Username</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=first_name&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">First</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=last_name&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Last</a></td> 
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=phone&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Phone</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=address&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Address</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=city&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">City</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=state&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">State</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=zip&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Zip</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=country&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Country</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=email&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Email</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=mailinglist&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">List</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=start_date&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Start Date</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=start_date&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Orders</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=start_date&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Spending</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=sessions&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Sessions</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=usertype&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Usertype</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=approved&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Approved</a></td>
             <td colspan='2'></td>
             </tr>\n";
      
       do
       {
        printf("<tr><td>%s</td> <td>%s</td> <td>%s</td><td>%s</td><td>%s</td> <td>%s</td> 
 <td>%s</td>  <td>%s</td> <td>%s</td> <td>%s</td> <td>%s</td> 
<td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td> 
",
        $myrow["userid"], $myrow["username"], $myrow["first_name"],$myrow["last_name"], $myrow["phone"], 
$myrow["address"], $myrow["city"], $myrow["state"], $myrow["zip"],  $myrow["country"], $myrow["email"], $myrow["mailinglist"], 
 
$myrow["start_date"],getOrders($myrow["username"]),getTotalSpending($myrow["username"]),$myrow["sessions"],$myrow["usertype"],$myrow["approved"]);
    
        printf("<td><a href=\"%s?module=$module&amp;delete=yes&amp;confirm=yes&amp;userid=%s&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">(DELETE)</a></td><td><a 
href=\"%s?module=$module&amp;userid=%s&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">(EDIT)</a></td></tr>",
        $PHP_SELF,$myrow["userid"],$PHP_SELF,$myrow["userid"]);

       } while ($myrow=mysql_fetch_array($result));
       echo "</table>\n";
      }
       
     echo "<p>";
       
     }
             
    ?>

<table>
<tr><td>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="sort" value="<? echo $sort;?>">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="submit" name="show" value="Show&amp;nbsp;:" class="button1">
<input type="text" name="number"  value="<? echo $number; ?>" class="form1">
rows beginning with number
<input type="text" name="lower"  value="<? echo $lower; ?>" class="form1">
in
<select name="desc" class="form1">  
<option value="&amp;nbsp;" <? if ($desc!="DESC") echo " SELECTED ";?> >ASCENDING
<option value="DESC" <? if ($desc=="DESC") echo " SELECTED ";?> >DESCENDING
</select>
order.
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="number"  value="<? echo $number; ?>">
<input type="hidden" name="lower"  value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Previous <? echo $number;?>" class="button1">
</form>
</td>

<td>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="number"  value="<? echo $number; ?>">
<input type="hidden" name="lower"  value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Next <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="number"  value="<? echo $total[0]; ?>">
<input type="hidden" name="lower"  value="0">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Show All" class="button1">
</form>
</td>
</tr>
</table>
     <p>
             
     <a href="<?php echo $_SERVER['PHP_SELF'];?>">ADD A user</a>
             
     <p>
             
     <form method="post" name="addform" action="<?php echo $_SERVER['PHP_SELF'];?>" >
       
     <?
      
     if ($userid)
     {  
        
     // editing so select a record
        
     $sql = "SELECT * FROM users WHERE userid='$userid'";
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

     $usertype = $myrow["usertype"];
     
     $approved = $myrow["approved"];
	      
     // print the id for editing
     
     ?>
     
     <input type=hidden name="userid" value="<?php echo $userid ?>">

     <?
     }

     ?>

     Fill in all fields to add a new user<br>     *'d fields are optional.<p>
     <table>
     
     <tr><td>
        
        Username
        </td><td><input type="Text" name="name" value="<? echo $myrow["username"];?>">
     </td></tr>
       
     
     <tr><td>  
        
     Password
     </td><td><input type="password"  name="password" value="<? echo $myrow["password"] ?>"></td>
     </tr>
     
     <tr><td>
        
     Hint
     </td><td><input type="Text" name="hint" value="<? echo $myrow["hint"] ?>"></td>
     </tr>

     <tr><td>
        
     First Name
     </td>
     <td>
     <input type="Text" name="first_name" value="<? echo $myrow["first_name"] ?>">
     </td>
     </tr>

     <tr><td>
        
     Last Name
     </td>
     <td>
     <input type="Text" name="last_name" value="<? echo $myrow["last_name"] ?>">
     </td>
     </tr>
     

     <tr><td>
        
     Phone
     </td>
     <td>
     <input type="Text" name="phone" size=12 value="<? echo $myrow["phone"] ?>">
     </td>
     </tr>

     <tr><td>
        
     Address</td>
     <td>
     <textarea name="address" rows="3" cols="24" ><? echo $myrow["address"] ?></textarea>
     </td>
     </tr>

     <tr><td>
        
     City
     </td>
     <td>
     <input type="Text" name="city" value="<? echo $myrow["city"] ?>">
     </td>
     </tr>


     <tr><td>
        
     State
     </td>
     <td>
     <input type="Text" name="state" value="<? echo $myrow["state"] ?>">
     </td>
     </tr>


     <tr><td>
        
     Zip
     </td>
     <td>
     <input type="Text" name="zip" size=10 value="<? echo $myrow["zip"] ?>">
     </td>
     </tr>


     <tr><td>
        
     Country
     </td>
     <td>
     <input type="Text" name="country" value="<? echo $myrow["country"] ?>">
     </td>
     </tr>


     <tr><td>
        
     Email
     </td>
     <td>
     <input type="Text" name="email" value="<? echo $myrow["email"] ?>">
     </td>
     </tr>


     <tr><td>
        
     Billing Address</td>
     <td>
     <textarea name="billing_address" rows="3" cols="24" ><? echo $myrow["billing_address"] 
?></textarea>
     </td>
     </tr>

     <tr><td>
        
     Billing City
     </td>
     <td>
     <input type="Text" name="billing_city" value="<? echo $myrow["billing_city"] ?>">
     </td>
     </tr>


     <tr><td>
        
     Billing State
     </td>
     <td>
     <input type="Text" name="billing_state" value="<? echo $myrow["billing_state"] ?>">
     </td>
     </tr>


     <tr><td>
        
     Billing Zip
     </td>
     <td>
     <input type="Text" name="billing_zip" value="<? echo $myrow["billing_zip"] ?>">
     </td>
     </tr>


     <tr><td>
        
     Billing Country
     </td>
     <td>
     <input type="Text" name="billing_country" value="<? echo $myrow["billing_country"] ?>">
     </td>
     </tr>


     <tr><td>
        

     <a href="adminbilling_method.php">Billing Method</a></td>
     <td>
     <select name="billing_method">
     
     <?PHP
      $sql = "SELECT billing_methodid, name FROM billing_method";
      $result = mysql_query($sql);  
     
      if ($billlist=mysql_fetch_array($result))
      {
          do
          {
           echo "<option value=\"".$billlist["billing_methodid"]."\" ";
           if ($billlist["billing_methodid"] == $myrow["billing_method"]) 
    	   { echo "selected";};
           echo "> ".$billlist["name"]."</option>";
          } while ($billlist=mysql_fetch_array($result));
      };
     ?>
     </select>
     </td></tr>

     <tr><td>
        

     Shipping Method</td>
     <td>
     <select name="shipping" size="1">
     
     <?PHP
      $sql = "SELECT shippingid, type FROM shipping";
      $result = mysql_query($sql);  
     
      if ($sgiplist=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$shiplist["shippingid"]."\" ";
       if ($shiplist["shippingid"] == $myrow["shipping"]) 
	   {echo "selected";};
       echo ">".($shiplist["type"] ? $shiplist["type"] : 'Choose')."</option>";
      } while ($shiplist=mysql_fetch_array($result));
      };
     ?>
     </select>
     </td></tr>

     <tr><td>
        
     Credit Card Type</td>
     <td>
     <select name="cc_type" size="1">
     
     <option value="VISA" <? if ($cc_type=="VISA") echo "SELECTED"; ?>>VISA
     <option value="MASTERCARD" <? if ($cc_type=="MASTERCARD") echo "SELECTED"; ?>>MASTERCARD
     </select>
     </td></tr>

     <tr><td>
        
     Credit Card Number</td>
     <td>
     <input type="text" name="cc_number" value="<? echo $myrow["cc_number"] ?>">
     </td>
     </tr>

     <tr><td>
        
     Expiration Date</td>
     <td>
     <input type="text" name="cc_expire" value="<? echo $myrow["cc_expire"] ?>">
     </td>
     </tr>

     <tr><td>
        
     Start_Date</td>
     <td>
     <? echo $myrow["retail"] ?>
     </td>
     </tr>

     <tr><td>
        
     Note</td>
     <td>
     <textarea name="note" rows="7" cols="40" ><? echo $myrow["note"] ?></textarea>
     </td>
     </tr>

     <tr><td>
        
     Mailing List</td>
     <td> 
     <input type="checkbox" value="1" name="mailinglist" <? if ($mailinglist==1) echo "CHECKED"; ?>>
     </td>   
     </tr>

     <tr><td>
        
     Approved</td>
     <td> 
     <input type="checkbox" value="1" name="approved" <? if ($approved==1) echo "CHECKED"; ?>>
     </td>   
     </tr>

     <tr><td>
        
      User Type
     </td>
     <td>
     <input type="Text" name="usertype" size=10 value="<? echo $myrow["usertype"] ?>">
     </td>
     </tr>
     
     <tr>
        <td colspan='2'>
	    <input type="hidden" name="module" value="<? echo $module;?>">
        <input type="hidden" name="sort" value="<? echo $sort ?>">
        <input type="hidden" name="lower" value="<? echo $lower ?>">
        <input type="hidden" name="number" value="<? echo $number ?>">
        <input type="hidden" name="desc" value="<? echo $desc ?>">
        <input type="hidden" name="start_date" value="<? if ($myrow["start_date"]) {echo $start_date;} else
 {echo date("M d y",time());}; ?>">
        <input type="Submit" name="submit" value="Enter information" class="button1">
        </td>
     </tr>
     
     </table>
     </form>  
     <?
     }

echo $username;     
?>