<?php
include("header.php");
include("db.php");
include("common.php");?>

<font size=+1>
<b>forgot your password?</b>
</font>
<p>

<?

if ($submit)
{

dbConnect();

$sql="SELECT username, password, first_name, last_name,email FROM users WHERE username='$username'";
$result=mysql_query($sql);
if ($userinfo=mysql_fetch_array($result))
{
echo $username." your password has been sent to your email address.<br><a href=\"login.php\">Click here</a> to log in.";

$password=$userinfo["password"];
$first=$userinfo["first_name"];
$last=$userinfo["last_name"];
$email=$userinfo["email"];

  $from = "From: wrecked@rhinoplex.org\r\n";
  $from .= "Reply-To: wrecked@rhinoplex.org\r\n";
  $from .= "X-Mailer: PHP/".phpversion();


 mail($email,"WRECKED: Username and Password",$username." - \n\nHere is your login information for WRECKED\n
Username: ".$username."\nPassword: ".$password."\n\nYou can log in at http://rhinoplex.org/wrecked\n\nThanks!
WRECKED diy electronics\nhttp://rhinoplex.org/wrecked", $from);


}
else
{
echo "No information found on that username.<br><a href=\"mailto:wrecked@rhinoplex.org\">Email</a> the admin if you believe
this
is in error.";
};


}
else
{

echo "enter your username and you will be emailed your password.<br>

 <form method=\"post\" action=$PHP_SELF>
 <input type=text name=\"username\">
 <input type=submit name=submit value=\"Submit\">
 </form>";

};

?>
