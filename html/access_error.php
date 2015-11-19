<?php // access.php handles user login

function addSession($username)
{
        dbConnect();

        if (!$username) {$username = " ";};
        $IP = getenv("REMOTE_ADDR");

        $sessionValue = session_id();

        $sql = "INSERT INTO sessions (sessionID, sessionValue,sessionTimestamp, sessionUser,sessionIP) VALUES (0, '$sessionValue',NOW(),'$username','$IP')";
        $result = mysql_query($sql) or die("Unable to insert search log.");
};


include_once("db.php");

if (!$_SESSION["username"]) // if there is no session...
{
 if (!$_REQUEST["username"])
 {
  include_once("header.php");
?>
  <body>

   <form method="post" action="<? echo $PHP_SELF;?>">
   <table cellspacing=0 cellpadding=2 border=0>
  <tr><td class=title3><B>Please log in</b></td></tr>

  <tr><td class=title4><b>Username</b>: <input name="username" type="text" size=12 class=form1></td></tr>
  <tr><td class=title4><b>Password</b>: <input name="password" type="password" size=12 class=form1></td></tr>
  <tr><td>
  <input name="login" type="submit" value="Log in" size=12 class="button1">
  <input type="hidden" name="back" value="<? echo $back; ?>">
  <input type="hidden" name="itemid" value="<? echo $itemid; ?>">
  <input type="hidden" name="module" value="<? echo $module; ?>">
  <input type="hidden" name="command" value="<? echo $command; ?>">
  <input type="hidden" name="back" value="<? echo $back; ?>">
  <input type="hidden" name="backcommand" value="<? echo $backcommand; ?>">
  <input type="hidden" name="sort" value="<? echo $sort; ?>">
  <input type="hidden" name="lower" value="<? echo $lower; ?>">
  <input type="hidden" name="number" value="<? echo $number; ?>">
  <input type="hidden" name="search" value="<? echo $search; ?>">
  <input type="hidden" name="keyword" value="<? echo $keyword; ?>">
  </td></tr>
  
  </table>
  </form><br>
  new users <a href="?module=newuser.php&command=NEW">signup</a><br>
  <a href="?module=forgot3.php&command=FORGOT">forgot</a> your password?<p>
  </body>

  </html>

  <?php
  exit;
 } else // else if there is a posted username, verify the username and password
 {

  dbConnect();

  $sql = "SELECT * FROM users WHERE username = '".$_REQUEST['username']."' AND password = '".$_REQUEST['password']."' AND approved = 1";
  $result = mysql_query($sql);
  
  if (mysql_num_rows($result) == 0) // if they don't match print this 
  {  
   include_once("header.php");
   echo "<body>
   Your user ID or password is incorrect.<br>
   To try logging in again, click <a href=\"?module=login.php&command=LOGIN\">here</a>.<br>
   To register for access, click <a href=\"?module=newuser.php&command=NEW\">here</a>.</p>
   </body></html>";
   exit;
  } else  // if they do match, register variables
    {
   $userinfo = mysql_fetch_array($result);
   $usertype = $userinfo['usertype'];
   $username = $_REQUEST['username'];
   $password = $_REQUEST['password'];
   session_register("usertype"); // saves the usertype into a session variable
   session_register("username"); // saves the content of username into a session variable 
   session_register("password"); // saves the content of password into a session variable
	
   $sql = "UPDATE users SET sessions = sessions+1 WHERE username='$username' ";
   $update = mysql_query($sql);

   addSession($username);
   echo "<meta http-equiv=\"refresh\" content=\"0;url=http://wrecked-distro.com/index.php?module=login.php\">";
  };
 };   
}; 
?>
	
