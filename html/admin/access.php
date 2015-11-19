<?php // accesscontrol.php


if ($reset!="yes")
{session_start();}
else
{
 session_unregister(username);
 session_unregister(password);
 session_destroy();};


include("db.php");
include("common.php");


if(!isset($username)) {

  ?>
  <html>
  <head>
  <title>kill yourself</title>
  </head>
  <? include("header.php");?>
  <body>

   <form method="post" action="<?=$PHP_SELF?>">
   <table cellspacing=0 cellpadding=2 border=0 align=middle>
  <tr><td bgcolor="aaaaaa"><font color="ffffff"><B>Please log in</b></font></td></tr>

  <tr><td bgcolor="dddddd"><b>Username</b>: <input name="username" type="text" size=12></td></tr>
  <tr><td bgcolor="dddddd"><b>Password</b>: <input name="password" type="password" size=12></td></tr>
  <tr><td bgcolor="dddddd"><input name="login" type="submit" value="Log in" size=12></td></tr>
  </table>
  </form><br>
  new users <a href="newuser.php">signup</a><br>
  <a href="forgot.php">forgot</a> your password?
  </body>

  </html>
  <?php
  exit;
}

session_register("username");
session_register("password");

dbConnect();
$sql = "SELECT * FROM users WHERE
        username = '$username' AND password = '$password'";
$result = mysql_query($sql);

if (!$result) {
  error("A database error occurred while checking your ".
        "login details.\\nIf this error persists, please ".
        "contact cutup@andythepooh.com");
}

if (mysql_num_rows($result) == 0) {

  session_unregister("username");
  session_unregister("password");

  ?>
  <html>
  <head>
  <title>kill yourself</title>
  </head>
  <? include("header.php");?>
  <body>
  Your user ID or password is incorrect.<br>
  To try logging in again, click <a href="<?=$PHP_SELF?>">here</a>.<br>
  To register for access, click <a href="newuser.php">here</a>.</p>
  </body>

  </html>
  <?php
  exit;
}

$username = mysql_result($result,0,"username");
?>
