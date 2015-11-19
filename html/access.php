<?php

// handles user account access

function addSession($username) {
    if (!$username) {
        $username = " ";
    };
    $IP = getenv("REMOTE_ADDR");
    
    $sessionValue = session_id();
    
    $sql = "INSERT INTO sessions (sessionID, sessionValue,sessionTimestamp, sessionUser,sessionIP) VALUES (0, '$sessionValue',NOW(),'$username','$IP')";
    $result = mysql_query($sql) or die("Unable to insert search log.");
};

include_once ("db.php");

if (!$_SESSION["username"])
 // if there is no session...
{
    if (!$_REQUEST["username"]) {
        include_once ("header.php");
?>

 <form method="post" action="<? echo $_SERVER['PHP_SELF'];?>">
  <table>
  <tr><td class='title3'><B>Please log in</b></td></tr>
  <tr><td class='title4'><b>Username</b>: <input name="username" type="text" size="12" class="form1"></td></tr>
  <tr><td class='title4'><b>Password</b>: <input name="password" type="password" size="12" class="form1"></td></tr>
  <tr><td>
  <input name="login" type="submit" value="Log in" class="button1">
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
  <input type="hidden" name="module" value="<? echo $module;?>">
  </td></tr>
  
  </table>
  </form><br>
  new users <a href="?module=newuser.php&amp;command=NEW">signup</a><br>
  <a href="?module=forgot.php&amp;command=FORGOT">forgot</a> your password?<p>

  <?php
  	echo "</div></div></body></html>";
        exit;
    } else
     // else if there is a posted username, verify the username and password
    {
        echo "A username was passed, trying";
        dbConnect();
        
        $sql = "SELECT * FROM users WHERE username = '" . $_REQUEST['username'] . "' AND password = '" . $_REQUEST['password'] . "' AND approved = 1";
        $result = mysql_query($sql);
        
        if (mysql_num_rows($result) == 0)
         // if they don't match print this
        {
            include_once ("header.php");
            echo "<body>
   Your user ID or password is incorrect.<br>
   To try logging in again, click <a href=\"?module=login.php&command=LOGIN\">here</a>.<br>
   To register for access, click <a href=\"?module=newuser.php&command=NEW\">here</a>.</p>
   </body></html>";
              	echo "</div></div></body></html>";
            exit;
        } else
         // if they do match, register variables
        {
            
            $userinfo = mysql_fetch_array($result);
            $usertype = $userinfo['usertype'];
            $username = $_REQUEST['username'];
            $password = $_REQUEST['password'];
            
            $_SESSION['usertype'] = $usertype;
            $_SESSION['username'] = $username;
            $_SESSION['password'] = $password;
            
            $sql = "UPDATE users SET sessions = sessions+1 WHERE username='$username' ";
            $update = mysql_query($sql);
            
            echo "<meta http-equiv=\"refresh\" content=\"0;url=http://wrecked-distro.com/index.php?module=login.php\">";
        };
    };
};
?>
  
