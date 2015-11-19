<?php
// newuser.php
// create a new user account
// written by geoff maddock latest revision 3/28/2005

// include external functions
include_once("header.php");
include_once("db.php");
include_once("authSendEmail.php");

ini_set("sendmail_from","sales@wrecked-distro.com");

// initialize variables
$adminemail = "sales@wrecked-distro.com";
$site = "WRECKED";
$url = "http://wrecked-distro.com";
$columns = array("userid","username","password","hint","first_name","last_name","phone","address","address2","city","state","zip","country","email","billing_method","shipping","cc_type","cc_number","cc_expire","start_date","note","mailinglist","usertype","sessions","approved","confirmation");

// validates an email address

function CheckEmail($Email = "") {
  if (ereg("[[:alnum:]]+@[[:alnum:]]+\.[[:alnum:]]+", $Email)) {
    return true;
  } else {
    return false;
  }
}

// returns true of the supplied arguments match the confirmation field of the specified user 

function matchConfirm ($username, $confirmation)
{
 dbConnect($database);

 $match = 0;

 $sql = "SELECT username FROM users WHERE confirmation='$confirmation' AND username='$username'";
 $result = mysql_query($sql) or die("Unable to match a confirmation.");
 if ($myrow = mysql_fetch_array($result))
 { if ($myrow["username"] == $username) {$match = 1;} };

 return $match;
}

// BEGIN MAIN 

dbConnect($database);

   if ($_REQUEST['submit'])
   {
    // if a new user subscription form has been submitted

     if ($_REQUEST['confirmation'])
     {
      // if the confirmation number has been entered
      if (matchConfirm($_REQUEST['username'], $_REQUEST['confirmation']))
        {
          // if the confirmation number matches, then add to the database

          // select all attributes from given uid from table users_temp, and add to users

          $sql = "UPDATE users SET approved = 1 WHERE username = '".$_REQUEST['username']."'";
          $result = mysql_query($sql) or die("Unable to add user, user database inaccessable");
          $username = $_REQUEST['username'];

          if ($result)
          {
      		  $to = $adminemail;
      		  $from = "From: $adminemail\r\n";
      		  $from .= "Reply-To: $adminemail\r\n";
      		  $from .= "X-Mailer: PHP/".phpversion();

      		  mail($to,"$site: New User Added ".$username,"$site - \n\nThis new user was added:\nusername: ".$username."\n\nThanks! $site - $url",$from);
            echo "Successfully added <b>".$username."</b>. Now go <a href=\"?module=login.php&command=LOGIN\">log in</a>.<p>"; 
          }
       } else {
        // if not error out and ask them to re-enter info
        echo "Wrong confirmation number.  <a href=\"?module=newuser.php\">Try again</a>?<p>";}
       } else  {
        // if submitted but no confirmation enterered
        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = mysql_query($sql);

        if (mysql_num_rows($result) > 0)
        {
        //if no
         echo "<b>Unable to process</b><p>";
         echo "This username has already been taken.";
         echo "<br>Click back to fix info or <a href=\"?module=newuser.php\">here</a> to start over.<p>";
        }
        else if ((!$_REQUEST['username']) or (!$_REQUEST['password']) or (!$_REQUEST['email']) or (!$_REQUEST['first_name']) or (!$_REQUEST['address']) or (!$_REQUEST["city"]) or (!$_REQUEST['state']))
        {
         echo "<b>Unable to process</b><p>";
         echo "You need to enter all the required fields.";
         echo "<br>Click back to fix info or <a href=\"?module=newuser.php\">here</a> to start over.<p>";
        }
        else if ($_REQUEST['password'] != $_REQUEST['hint'])
        {
         echo "<b>Unable to process</b><p>";
         echo "Make sure you entered the password correctly twice to confirm.";
         echo "<br>Click back to fix info or <a href=\"?module=newuser.php\">here</a> to start over.<p>";
        }
        else if (!CheckEmail($_REQUEST['email']))
        {
         echo "<b>Unable to process</b><p>";
         echo "Invalid email address.";
         echo "<br>Click back to fix info or <a href=\"?module=newuser.php\">here</a> to start over.<p>";
        }
        else
        {
        // if submission is ok, generate confirmation number, email to customer, present the form

          echo "<b>EMAIL SENT!</b><p> Check your email for the confirmation link, and click it to confirm your account.<br>";
          echo "<a href=\"?module=login.php\">Click here</a> to return to the main page.";
          srand((double)microtime()*1000000);
          $confirmation = rand(1000000000, 9999999999);
          $to = $_REQUEST['email'];
          $subject = "$site: New User Confirmation";
          $body = "<html><body>";
          $body .= $_REQUEST['first_name']." - <br><br>You can finish creating your new account by clicking the following link.<br>
          If you did not request this account, please delete and disregard this email.<br><br>";
          $header = "From: $adminemail\r\n";
          $header .= "Reply-To: $adminemail\r\n";
          $header .= "Return-Path: $adminemail\r\n";
          $header .= "MIME-Version: 1.0\r\n";
          $header .= "Content-type: text/html; charset=ISO-8859-1\r\n";

          $body .= "<a href=\"$url/index.php?module=newuser.php&command=SUBMIT&submit=1&username=".$_REQUEST['username']."&confirmation=$confirmation\">$url/index.php?module=newuser.php&command=SUMBIT&submit=1&username=".$_REQUEST['username']."&confirmation=$confirmation</a>";

          $body .= "<br><br>Thanks!<br>$adminemail<br>$site - $url";
          $body .= "</body></html>";

          mail($to, $subject, $body, $header);
          $test = authSendEmail($adminemail, $adminemail, $to, $to, $subject, $body);
          $sql = "INSERT INTO users (userid, username, password, hint, first_name, last_name, phone, address, address2, city,
state, zip, country, email, billing_method, shipping, cc_type, cc_number,cc_expire,start_date,note,mailinglist,usertype,sessions,approved,confirmation) VALUES
(0, '".$_REQUEST['username']."', '".$_REQUEST['password']."', '".$_REQUEST['hint']."', '".$_REQUEST['first_name']."','".$_REQUEST['last_name']."','".$_REQUEST['phone']."', '".$_REQUEST['address']."', '".$_REQUEST['address2']."','".$_REQUEST['city']."',
'".$_REQUEST['state']."','".$_REQUEST['zip']."','".$_REQUEST['country']."','".$_REQUEST['email']."', 1, 1, NULL, NULL, NULL, CURRENT_DATE, NULL,'".$_REQUEST['mailinglist']."', 0, 0, 0, '".$confirmation."')";

          $result = mysql_query($sql) or die("Unable to add user, user database inaccessable");

        }

     }
   } else {

     echo "<span id='search-header'> Add New User</span><p>

     <form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">
     <input type='hidden' name=\"uid\">
     <input type='hidden' name=\"module\" value=\"newuser.php\">
     <input type='hidden' name=\"command\" value=\"submit\">

     Fill in all fields to create a new account.
     <table>

     <tr><td>Username </td><td><input type=\"Text\" name=\"username\" maxlength=24 class='form1'></td></tr>

     <tr><td>Password </td><td><input type=\"password\" name=\"password\" maxlength=24 class='form1'></td></tr>
     <tr><td>Password (re-enter) </td><td><input type=\"password\" name=\"hint\" class='form1'></td></tr>

     <tr><td>
     Email Address </td>
     <td>
     <input type=\"Text\" name=\"email\" class='form1'>
     </td>
     </tr>

     <tr><td>
     First Name
     </td><td><input type=\"Text\" name=\"first_name\" class=form1></td>
     </tr>

     <tr><td>
     Last Name
     </td><td><input type=\"Text\" name=\"last_name\" class=form1></td>
     </tr>

     <tr><td>
     Address </td>
     <td>
     <input type=\"Text\" name=\"address\" class=form1>
     </td>
     </tr>

     <tr><td>
     Address Line 2</td>
     <td>
    <input type=\"Text\" name=\"address2\" class=form1>
     </td>
     </tr>

     <tr><td>
     City </td>
     <td>
     <input type=\"Text\" name=\"city\" class=form1>
     </td>
     </tr>

     <tr><td>
     State (2-letter abbrev.)</td>
     <td>
     <input type=\"Text\" name=\"state\" class=form1>
     </td>
     </tr>

     <tr><td>
     Zip </td>
     <td>
     <input type=\"Text\" name=\"zip\" class=form1>
     </td>
     </tr>

     <tr><td>
     Country </td>
     <td>
     <input type=\"Text\" name=\"country\" class=form1>
     </td>
     </tr>

     <tr><td>
     Phone Number </td>
     <td>
     <input type=\"Text\" name=\"phone\" class=form1>
     </td>
     </tr>
";
?>
     <tr><td>
      Add me to your email update list</td>
     <td>
     <input type="checkbox" value="1" name="mailinglist" <? if ($mailinglist==1) echo "CHECKED";?>>
     </td>
     </tr>

<?
echo "<tr><td><input type=\"Submit\" name=\"submit\" value=\"Enter information\" class='button1'></td><td>&nbsp;</td></tr>
     </table>
     </form>
<br> Note: If you have any concerns, feel free to contact me at <a
href=\"mailto:$adminemail\">$adminemail</a>.

     <p>
     > <a href=\"?module=login.php\" >I don't want to sign up now</a>.<p>
";};

?>
