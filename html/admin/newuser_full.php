
<html>
<head>
<title></title>

<link   rel= STYLESHEET
        href="wrecked.css"
        Type = "text/css">
</head>
<body bgcolor=#eeeeee text=#ff9900>


<?php

   $db = mysql_connect("localhost","root","");

   mysql_select_db("wrecked",$db);



   if ($submit)
   {
    // if a new user subscription form has been submitted

     if ($confirm)
     {
        // if the confirmation number has been entered

      if ($confirm==$number)
        {
          // if the confirmation number matches, then add to the database
          $sql = "INSERT INTO users (uid,username,password,hint,first_name,last_name,phone,address, city, state, 
zip, country, email, billing_name, billing_address,billing_city, billing_state, billing_zip, billing_country,
billing_method, cc_type, cc_number, cc_expire, start_date, note) VALUES
(0, '$username', '$password', '$hint', 
'$first_name','$last_name','$phone','$address','$city','$state','$zip','$country','$email','$billing_name','$billing_address','$billing_city','$billing_state', 
'$billing_zip', '$billing_country', '$billing_method', '$cc_type', '$cc_number', '$cc_expire', now(), '$note' )";
          $result=mysql_query($sql)
          or die("Unable to add user");
          if ($result)
          {echo "Successfully added ".$username.". Now go <a href=\"login.php\">log in</a>.<p>"; }
        } 
      else
          {// if not error out and ask them to re-enter info
                echo "Wrong confirmation number.  <a href=\"newuser.php\">Try again</a>?<p>";}

     }
     else
     { 
        // if submitted but no confirmation enterered

     $sql = "SELECT * FROM users WHERE username='$username'";
     $result = mysql_query($sql,$db);
     if (mysql_num_rows($result) > 0 or (!$username) or (!$password) or (!$email))
        {
        //if no
         echo "This username has already been taken.";
          echo "<br><a href=\"newuser.php\">go back</a><p>";
        }
        else
        {
        // if submissions ok, generate confirmation number, email to customer, present the form
    
          echo "Check your email for the confirmation code.<br>";
          srand((double)microtime()*1000000);
          $confirm=rand(1000000000,9999999999);
          mail($email,"New User Confirmation","This is your confirmation number: ".$confirm."\nEnter it on the web 
page and 
submit.");
          
          echo "
          <form method=\"post\" action=$PHP_SELF>
          <input type=hidden name=\"confirm\" value=\"$confirm\">  
          <input type=hidden name=\"username\" value=\"$username\">
          <input type=hidden name=\"password\" value=\"$password\">   
          <input type=hidden name=\"hint\" value=\"$hint\">
          <input type=hidden name=\"first_name\" value=\"$last_name\">
          <input type=hidden name=\"phone\" value=\"$phone\">
          <input type=hidden name=\"address\" value=\"$address\">
          <input type=hidden name=\"city\" value=\"$city\">  
          <input type=hidden name=\"state\" value=\"$state\">
          <input type=hidden name=\"zip\" value=\"$zip\">
          <input type=hidden name=\"country\" value=\"$country\">
          <input type=hidden name=\"email\" value=\"$email\">
          <input type=hidden name=\"billing_name\" value=\"$billing_name\">
          <input type=hidden name=\"billing_address\" value=\"$billing_address\">
          <input type=hidden name=\"billing_city\" value=\"$billing_city\">
          <input type=hidden name=\"billing_state\" value=\"$billing_state\">
          <input type=hidden name=\"billing_zip\" value=\"$billing_zip\">
          <input type=hidden name=\"billing_country\" value=\"$billing_country\">
          <input type=hidden name=\"billing_method\" value=\"$billing_method\">
          <input type=hidden name=\"cc_type\" value=\"$cc_type\">
          <input type=hidden name=\"cc_number\" value=\"$cc_number\">
          <input type=hidden name=\"cc_expire\" value=\"$cc_expire\">
          <input type=hidden name=\"start_date\" value=\"$start_date\">
          <input type=hidden name=\"note\" value=\"$note\">
          Confirmation Number
          <input type=text name=\"number\">
          <input type=submit name=submit>
          </form>";
        }
     
     }
   }
    else 
   {
         
     echo "<B> Add New User</b><p>
         
     <form method=\"post\" action=\"".$PHP_SELF."\">
     <input type=hidden name=\"uid\">
        
     Fill in all fields to create a new user.<br>*'d fields are optional.<p>
     <table>
          
     <tr><td><font color=000066>username</td><td><input type=\"Text\" name=\"username\"></td></tr>
          
     <tr><td><font color=000066>password</td><td><input type=\"password\" name=\"password\"></td></tr>

     <tr><td><font color=000066>hint</td><td><input type=\"Text\" name=\"hint\"></td></tr>
          
          
     <tr><td>
     <font color=000066>
     First Name *
     </td><td><input type=\"Text\" name=\"first_name\"></td>
     </tr>
          
     <tr><td>
     <font color=000066>
     Last Name *
     </td><td><input type=\"Text\" name=\"last_name\"></td>  
     </tr>

      
     <tr><td>
     <font color=000066>
     Phone Number *</td>
     <td>
     <input type=\"Text\" name=\"phone\">
     </td>
     </tr>
     
     <tr><td>
     <font color=000066>
     Address *</td>
     <td> 
     <input type=\"Text\" name=\"address\">
     </td>
     </tr>
          
     <tr><td>
     <font color=000066>
     City *</td>
     <td>
     <input type=\"Text\" name=\"city\">
     </td>
     </tr>
     
     <tr><td>
     <font color=000066>
     State *</td>
     <td> 
     <input type=\"Text\" name=\"state\">
     </td>
     </tr>   

     <tr><td>
     <font color=000066>
     Zip *</td>
     <td> 
     <input type=\"Text\" name=\"zip\">
     </td>
     </tr>   
     
     <tr><td>
     <font color=000066>
     Country *</td>
     <td> 
     <input type=\"Text\" name=\"country\">
     </td>
     </tr>   

     <tr><td>
     <font color=000066>
     Email Address </td>
     <td>
     <input type=\"Text\" name=\"email\">
     </td>
     </tr>

     <tr><td>
     <font color=000066>
     Billing Address *</td>
     <td> 
     <input type=\"Text\" name=\"address\">
     </td>
     </tr>

     <tr><td>
     <font color=000066>
     Billing Address *</td>
     <td> 
     <input type=\"Text\" name=\"billing_address\">
     </td>
     </tr>
          
     <tr><td>
     <font color=000066>
     Billing_City *</td>
     <td>
     <input type=\"Text\" name=\"billing_city\">
     </td>
     </tr>
     
     <tr><td>
     <font color=000066>
     Billing State *</td>
      
     <td> 
     <input type=\"Text\" name=\"billing_state\">
     </td>
     </tr>   

     <tr><td>
     <font color=000066>
     Billing Zip *</td>
     <td> 
     <input type=\"Text\" name=\"billing_zip\">
     </td>
     </tr>   
     
     <tr><td>
     <font color=000066>
     Billing Country *</td>
     <td> 
     <input type=\"Text\" name=\"billing_country\">
     </td>
     </tr>   
";
?>
     <tr><td>
     <font color=000066>

     Billing Method *</td>
     <td>
     <select name="billing_method" size="1">
     
     <?


      $sql = "SELECT * FROM billing_method";
      $result = mysql_query($sql,$db);
     
      if ($bill=mysql_fetch_array($result))
      { 
      do
      {
       echo "<option value=\"".$bill["name"]."\" ";
       echo ">".$bill["name"];
      } while ($bill=mysql_fetch_array($result));
      };
     ?>

     </select>
     </td></tr>
<?
     echo "    
     <tr><td>
     <font color=000066>
     Note *</td>
     <td> 
        <textarea name=\"note\" rows=7 cols=40 wrap=virtual></textarea>
     </td>
     </tr>   
     
     
     <tr><td><input type=\"Submit\" name=\"submit\" value=\"Enter information\"></td></tr>
     </table>
     </form> 
     <p>
     <a href=\"login.php\">I don't want to sign up now</a>.<p>
";};
     
?>
     
</body>   
     
</html>
