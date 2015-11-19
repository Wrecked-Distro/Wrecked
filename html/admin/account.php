<?php

   include_once("access.php");
   include_once("header.php");

   dbConnect();

   if ($submit)
   {
    // if a new user subscription form has been submitted
    if ($pw1==$pw2)
    {
          $sql = "UPDATE users SET first_name='$first_name', last_name='$last_name', phone='$phone',
address='$address', city='$city', state='$state', country='$country', zip='$zip', email='$email',
billing_method='$billing_method', password='$pw1' WHERE username='$username'";

          $result=mysql_query($sql)
          or die("Unable to update ".$username);
 } else
 { echo "<i>Retype your new password (twice)</i><p>";};

           if ($result)
          {echo "Successfully updated ".$username."<p>"; };
    echo  "<a href=\"login.php?code=INFO\">Back</a>.<p>";

   }
   else
   {
    dbConnect();

    $sql= "SELECT * FROM users WHERE username='$username'";
    $result = mysql_query($sql);
    $myrow=mysql_fetch_array($result);

     echo "<B>Modify User Information</b><p>

     <form method=\"post\" action=\"".$PHP_SELF."\">
     <input type=hidden name=\"uid\" value=\"".$myrow["userid"]."\">

     <br>* fields are optional.<p>

     <table>

     <tr><td>
     <font class=\"text3\">
     First Name *
     </td><td><input type=\"Text\" name=\"first_name\" value=\"".$myrow["first_name"]."\"></td>
     </tr>

     <tr><td>
     <font class=\"text3\">
     Last Name *
     </td><td><input type=\"Text\" name=\"last_name\" value=\"".$myrow["last_name"]."\"></td>
     </tr>


     <tr><td>
     <font class=\"text3\">
     Phone Number *</td>
     <td>
     <input type=\"Text\" name=\"phone\" value=\"".$myrow["phone"]."\">
     </td>
     </tr>

     <tr><td>
     <font class=\"text3\">
     Address *</td>
     <td>
     <input type=\"Text\" name=\"address\" value=\"".$myrow["address"]."\">
     </td>
     </tr>

     <tr><td>
     <font class=\"text3\">
     City *</td>
     <td>
     <input type=\"Text\" name=\"city\" value=\"".$myrow["city"]."\">
     </td>
     </tr>

     <tr><td>
     <font class=\"text3\">
     State *</td>
     <td>
     <input type=\"Text\" name=\"state\" value=\"".$myrow["state"]."\">
     </td>
     </tr>

     <tr><td>
     <font class=\"text3\">
     Zip *</td>
     <td>
     <input type=\"Text\" name=\"zip\" value=\"".$myrow["zip"]."\">
     </td>
     </tr>

     <tr><td>
     <font color=000066>
     Country *</td>
     <td>
     <input type=\"Text\" name=\"country\" value=\"".$myrow["country"]."\">
     </td>
     </tr>

     <tr><td>
     <font class=\"text3\">
     Email Address </td>
     <td>
     <input type=\"Text\" name=\"email\" value=\"".$myrow["email"]."\">
     </td>
     </tr>";
?>

     <tr><td>
     <font class="text3">
     <font color=000066>

     Billing Method *</td>
     <td>
     <select name="billing_method" size="1">

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
     <font class=\"text3\">
     Password </td>
     <td>
     <input type=\"password\" name=\"pw1\" value=\"".$myrow["password"]."\">
     </td>
     </tr>

     <tr><td>
     <font class=\"text3\">
     Re-type Password </td>
     <td>
     <input type=\"password\" name=\"pw2\" value=\"".$myrow["password"]."\">
     </td>
     </tr>

     <tr><td><input type=\"Submit\" name=\"submit\" value=\"Enter information\"></td></tr>
     </table>
     </form>
     <p>
     <a href=\"login.php?code=INFO\">Back</a>.<p>";

};

?>
