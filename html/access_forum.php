<?php
 // access.php handles user login

session_start();
 // makes session variables available

include_once ("db.php");
include_once ("common.php");

if (!$_SESSION["username"])
 // if there is no session...
{
    if (!$_REQUEST["username"])
     // .. and if there is no username posted to validate, print the login form
    {
        include ("header.php"); ?>
  <body>

  <form method="post" action="<? echo $PHP_SELF;?>">
  <table cellspacing=0 cellpadding=2 border=0 >
  <tr><td class=title3><B>Please log in</b></td></tr>

  <tr><td class=title4><b>Username</b>: <input name="username" type="text" size=12 class=form1></td></tr>
  <tr><td class=title4><b>Password</b>: <input name="password" type="password" size=12 class=form1></td></tr>
  <tr><td>
  <input name="login" type="submit" value="Log in" size=12 class="button1">
  <input type="hidden" name="page" value="<? echo $page; ?>">
  <input type="hidden" name="itemid" value="<? echo $itemid; ?>">
  <input type="hidden" name="sort" value="<? echo $sort; ?>">
  <input type="hidden" name="lower" value="<? echo $lower; ?>">
  <input type="hidden" name="number" value="<? echo $number; ?>">
  <input type="hidden" name="mode" value="<? echo $mode; ?>">
  <input type="hidden" name="artist" value="<? echo $artist; ?>">
  <input type="hidden" name="label" value="<? echo $label; ?>">
  <input type="hidden" name="search" value="<? echo $search; ?>">
  <input type="hidden" name="category" value="<? echo $category; ?>">
  <input type="hidden" name="module" value="<? echo $module;?>">
  </td></tr>

  </table>
  </form><br>
  new users <a href="newuser.php">signup</a><br>
  <a href="forgot.php">forgot</a> your password?<p>
  back to <a href=".." target="_top">wrecked-distro.com</a>  </body>
  </html>

  <?php
        exit;
    } else
     // else if there is a posted username, verify the username and password
    {
        
        dbConnect();
        
        $sql = "SELECT * FROM users WHERE username = '" . $_REQUEST['username'] . "' AND password = '" . $_REQUEST['password'] . "' AND approved = 1";
        
        $result = mysql_query($sql);
        
        if (mysql_num_rows($result) == 0)
         // if they don't match print this
        {
            include ("header.php");
            echo "<body>
    Your user ID or password is incorrect.<br>
    To try logging in again, click <a href=\"" . $PHP_SELF . "\">here</a>.<br>
    To register for access, click <a href=\"newuser.php\">here</a>.</p>
    </body></html>";
            exit;
        } else {
            
            // if they do match, register variables
            $userinfo = mysql_fetch_array($result);
            $usertype = $userinfo["usertype"];
            $username = $_REQUEST['username'];
            
            // save the username
            $_SESSION['username'] = $_REQUEST['username'];
            
            $sql = "UPDATE users SET sessions = sessions + 1 WHERE username='$username' ";
            $update = mysql_query($sql);
            echo "<meta http-equiv=\"REFRESH\" content=\"0;url=index.php?module=forums.php&forum=1\">";
        };
    };
};
?>


