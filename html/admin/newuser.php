<? include("header.php");

function CheckEmail($Email = "") {
  if (ereg("[[:alnum:]]+@[[:alnum:]]+\.[[:alnum:]]+", $Email)) {
    return true;
  } else {
    return false;
  }
}

?>

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
          $sql = "INSERT INTO users 
(userid,username,password,hint,first_name, last_name, phone,address, city, 
state, zip, country, email, billing_method, shipping, cc_type, cc_number, cc_expire, start_date, note, mailinglist) VALUES 
(0, '$username', '$password', '$password', '$first_name','$last_name','$phone', '$address','$city', 
'$state','$zip','$country','$email',1,1,NULL,NULL,NULL,CURRENT_DATE,NULL,'$mailinglist')";
          $result=mysql_query($sql,$db)
          or die("Unable to add user, user database inaccessable");
          if ($result)
          {
  $to="wrecked@rhinoplex.org";
  $from = "From: wrecked@rhinoplex.org\r\n";
  $from .= "Reply-To: wrecked@rhinoplex.org\r\n";
  $from .= "X-Mailer: PHP/".phpversion();

  mail($to,"WRECKED: New User Added ".$username,"WRECKED - \n\nThis new user was added:\n
username: ".$username."\nreal name: ".$first_name." ".$last_name."\nemail: ".$email."\n\nThanks!
WRECKED diy electronics\nhttp://rhinoplex.org/wrecked",$from);
		echo "Successfully added <b>".$username."</b>. Now go <a href=\"login.php\">log in</a>.<p>"; }
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
     if (mysql_num_rows($result) > 0)
        {
        //if no
         echo "<b>Unable to process</b><p>";
         echo "This username has already been taken.";
	 echo "<br>Click back to fix info or <a href=\"newuser.php\">here</a> to start over.<p>";
        }
        else if ((!$username) or (!$password) or (!$email))
        {
         echo "<b>Unable to process</b><p>";
         echo "You need to enter all the required fields.";
	 echo "<br>Click back to fix info or <a href=\"newuser.php\">here</a> to start over.<p>";
        }
        else if ($password!=$hint)
        {
         echo "<b>Unable to process</b><p>";
         echo "Make sure you entered the password correctly twice to confirm.";
	 echo "<br>Click back to fix info or <a href=\"newuser.php\">here</a> to start over.<p>";
        }
	else if (!CheckEmail($email))
 	{
         echo "<b>Unable to process</b><p>";
         echo "Invalid email address.";
	 echo "<br>Click back to fix info or <a href=\"newuser.php\">here</a> to start over.<p>";
	}
        else
        {
        // if submissions ok, generate confirmation number, email to customer, present the form
    
          echo "<b>Stay here! Check your email for the confirmation code, cut and paste it below.</b><br>";
          srand((double)microtime()*1000000);
          $confirm=rand(1000000000,9999999999);
          mail($email,"WRECKED: New User Confirmation",$username." - \n\nHere is your confirmation number:\n 
".$confirm."\n\nEnter it on the confirmation web page and hit submit to confirm your account.\n\nThanks!\n
WRECKED diy electronics\nhttp://rhinoplex.org/wrecked", "From: wrecked@rhinoplex.org\r\n");
          
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
          <input type=hidden name=\"mailinglist\" value=\"$mailinglist\">
          <input type=hidden name=\"start_date\" value=\"$start_date\">
          Confirmation Number
          <input type=text name=\"number\">
          <input type=submit name=submit value=\"Submit\">
          </form>";
        }
     
     }
   }
    else 
   {
         
     echo "<B> Add New User</b><p>
         
     <form method=\"post\" action=\"".$PHP_SELF."\">
     <input type=hidden name=\"uid\">
        
     Fill in all fields to create a new user.<br> All is info required for mailorder.<P>
     <table>
          
     <tr><td><font color=000066>username</td><td><input type=\"Text\" name=\"username\"></td></tr>
          
     <tr><td><font color=000066>password</td><td><input type=\"password\" name=\"password\"></td></tr>

     <tr><td><font color=000066>password (re-enter)</td><td><input type=\"password\" name=\"hint\"></td></tr>
          
          
     <tr><td>
     <font color=000066>
     First Name 
     </td><td><input type=\"Text\" name=\"first_name\"></td>
     </tr>
          
     <tr><td>
     <font color=000066>
     Last Name
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
     Address </td>
     <td> 
     <input type=\"Text\" name=\"address\">
     </td>
     </tr>
          
     <tr><td>
     <font color=000066>
     City </td>
     <td>
     <input type=\"Text\" name=\"city\">
     </td>
     </tr>
     
     <tr><td>
     <font color=000066>
     State (2-letter abbrev.)</td>
     <td> 
     <input type=\"Text\" name=\"state\">
     </td>
     </tr>   

     <tr><td>
     <font color=000066>
     Zip </td>
     <td> 
     <input type=\"Text\" name=\"zip\">
     </td>
     </tr>   
     
     <tr><td>
     <font color=000066>
     Country </td>
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
     </tr>";
?>
     <tr><td>
     <font color=000066>
     Add me to your email update list</td>
     <td>
     <input type="checkbox" value="1" name="mailinglist" <? if ($mailinglist==1) echo "CHECKED"; ?>>
     </td>
     </tr>

<?
echo "
     <tr><td><input type=\"Submit\" name=\"submit\" value=\"Enter information\"></td></tr>
     </table>
     </form> 
<b> Note:</b><br> We only use this information for order processing.  It will be kept private and confidential.
<br> We will never participate in information sharing w/o your express consent.
<br> If you have any concerns, feel free to contact me at <a href=\"mailto:wrecked@rhinoplex.org\">wrecked@rhinoplex.org</a>.

     <p>
     <a href=\"login.php\">I don't want to sign up now</a>.<p>
";};
     
?>
     
</body>   
     
</html>
