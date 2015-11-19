<?php // account.php
   // update user account information
   // written by geoffrey maddock 2003
   // updated july 19th 2005

   // make sure they are logged in

   dbConnect();

   extract($_POST);

   if (!$command) {$command = "DISPLAY";};

   // if information has been submitted

   if ($submit)
   {
    // if a new user subscription form has been submitted

    if ($pw1 == $pw2)
    {
          $sql = "UPDATE users SET first_name='$first_name', last_name='$last_name', phone='$phone',
address='$address', city='$city', state='$state', country='$country', zip='$zip', email='$email',
billing_method='$billing_method', password='$pw1' WHERE username='$username'";

          $result = mysql_query($sql)
          or die("Unable to update ".$username);
     }
	 else
 	{
		echo "<i>Retype your new password (twice), it does not match.</i><p>";
	};

           if ($result)
          {echo "Successfully updated ".$username."<p>"; };
    echo  "<a href=\"?module=login.php&command=INFO\">Back</a>.<p>";

   }
   else
   {
    dbConnect();

    $sql= "SELECT * FROM users WHERE username='$username'";
    $result = mysql_query($sql);
    $myrow=mysql_fetch_array($result);

     echo "<B>Modify User Information</b><p>

     <form method=\"post\" action=\"".$PHP_SELF."\">
     <input type=\"hidden\" name=\"module\" value=\"account.php\">
     <input type=\"hidden\" name=\"command\" value=\"SUBMIT\">
     <input type=hidden name=\"uid\" value=\"".$myrow["userid"]."\">

     <table>

     <tr><td>
     First Name *
     </td><td><input type=\"Text\" name=\"first_name\" value=\"".$myrow["first_name"]."\" class=form1></td>
     </tr>

     <tr><td>
     Last Name *
     </td><td><input type=\"Text\" name=\"last_name\" value=\"".$myrow["last_name"]."\" class=form1></td>
     </tr>

     <tr><td>
     Phone Number *</td>
     <td>
     <input type=\"Text\" name=\"phone\" value=\"".$myrow["phone"]."\" class=form1>
     </td>
     </tr>

     <tr><td>
     Address *</td>
     <td>
     <input type=\"Text\" name=\"address\" value=\"".$myrow["address"]."\" class=form1>
     </td>
     </tr>

     <tr><td>
     City *</td>
     <td>
     <input type=\"Text\" name=\"city\" value=\"".$myrow["city"]."\" class=form1>
     </td>
     </tr>

     <tr><td>
     State *</td>
     <td>
     <input type=\"Text\" name=\"state\" value=\"".$myrow["state"]."\" class=form1>
     </td>
     </tr>

     <tr><td>
     Zip *</td>
     <td>
     <input type=\"Text\" name=\"zip\" value=\"".$myrow["zip"]."\" class=form1>
     </td>
     </tr>

     <tr><td>
     Country *</td>
     <td>
     <input type=\"Text\" name=\"country\" value=\"".$myrow["country"]."\" class=form1>
     </td>
     </tr>

     <tr><td>
     Email Address </td>
     <td>
     <input type=\"Text\" name=\"email\" value=\"".$myrow["email"]."\" class=form1>
     </td>
     </tr>";
?>

     <tr><td>

     Billing Method *</td>
     <td>
     <select name="billing_method" size="1" class=form1>

     <?

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

     ?>

     </select>
     </td></tr>
<?
     echo "
     <tr><td>
     Password </td>
     <td>
     <input type=\"password\" name=\"pw1\" value=\"".$myrow["password"]."\" class=form1>
     </td>
     </tr>

     <tr><td>
     Re-type Password </td>
     <td>
     <input type=\"password\" name=\"pw2\" value=\"".$myrow["password"]."\" class=form1>
     </td>
     </tr>

     <tr><td>
	<input type=\"HIDDEN\" name=\"command\" value=\"UPDATE\">
	<input type=\"Submit\" name=\"submit\" value=\"Enter information\" class=button1>
     </td></tr>
     </table>
     </form>
     <p>
     <a href=\"?module=login.php&command=INFO\">Back</a>.<p>";

};

?>
