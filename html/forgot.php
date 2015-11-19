<? 
include_once("header.php");
include_once("db.php");
include_once("common.php");
include_once("authSendEmail.php");
?>

<b>forgot your password?</b>

<p>

<?

if ($_REQUEST['submit'])
{

	$sql = sprintf("SELECT username, password, first_name, last_name,email FROM users WHERE username='%s'",$_REQUEST['username']);
	$result = mysql_query($sql);
	if ($userinfo = mysql_fetch_array($result))
	{
		echo $username." your password has been sent to your email address.<br><a href=\"?module=login.php&command=LOGIN\">Click 
		here</a> to log in.";

		$password=$userinfo["password"];
		$first=$userinfo["first_name"];
		$last=$userinfo["last_name"];
		$email=$userinfo["email"];

		ini_set("sendmail_from",SALES_EMAIL);

		$from = "From: SALES_EMAIL\r\n";
		$from .= "Reply-To: SALES_EMAIL\r\n";
		$from .= "X-Mailer: PHP/".phpversion();
		$namefrom = "SALES_EMAIL\r\n";
		$nameto = $username;

		$subject = "WRECKED: Username and Password Recovery "; 
		$content = $username." - \r\n\n<br>Here is your login information for WRECKED\r\n<br>Username: 
		".$username."\r\n<br>Password: 
		".$password."\r\n\n<br>You can log in at http://wrecked-distro.com\r\n\n<br>Thanks!\r\n<br>WRECKED diy 
		electronics\r\nhttp://wrecked-distro.com";

		$from = "SALES_EMAIL";
		$message = $content;

		authSendEmail($from, $namefrom, $email, $nameto, $subject, $message); 

	} else 	{
		echo "No information found on that username.<br><a href=\"mailto:SALES_EMAIL\">Email</a> the admin if you believe this is in error.";
	};


} else {      

	echo "enter your username and you will be emailed your password.<br>
	     
	<form method=\"post\" action='".$_SERVER['PHP_SELF']."'>
	<input type=text name=\"username\" class=\"form1\">
	<input type=\"hidden\" name=\"module\" value=\"forgot.php\" class=\"form1\">
	<input type=\"hidden\" name=\"command\" value=\"SUBMIT\" class=\"form1\">
	<input type=submit name=submit value=\"Submit\" class=\"button1\">
	</form>";
};

?>
