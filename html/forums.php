<?php

// forums.php
// a simple web-based discussion forum in php and mysql
// drop in version for inclusion

// coded by geoff maddock / cutups at rhinoplex.org
// revision nov 13 2006

include_once ("authSendEmail.php");
include_once ("db.php");

// initialize variables

$prefix = "?module=forums.php&amp;";
$forumstable = "forums";
$poststable = "posts";
$threadstable = "threads";
$database = FORUM_DATABASE;
$adminuser = ADMIN_USER;
$rootpage = "index.php";
$width = 800;
$edit = 1;
$limit1 = 25;
$limit2 = 50;
$badhtml = "<a>,<i>,<b>,<br>,<p>,<img>,<pre>";
$guestpost = 0;

dbConnect($database);

// function to format a given mysql timestamp

function time_format($timestamp) {
    $hour = substr($timestamp, 8, 2);
    $minute = substr($timestamp, 10, 2);
    $second = substr($timestamp, 12, 2);
    $month = substr($timestamp, 4, 2);
    $day = substr($timestamp, 6, 2);
    $year = substr($timestamp, 0, 4);
    $mktime = mktime($hour, $minute, $second, $month, $day, $year);
    $formated = date("M j, Y g:i a", $mktime);
    return $formated;
}

// shows user function menu

function userMenu($username) {
    GLOBAL $database;
    if ($username) {
        echo "<b>MENU</b>: <a href=\"?module=adminuser.php?user=$username\">Edit Profile</a><br>";
    }
};

// display a users profile

function showUser($username) {
    
    dbConnect();
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysql_query($sql) or die("Unable to locate user.\n");
    
    if ($myrow = mysql_fetch_array($result)) {
        echo "<B>USERS</B><br>";
        
        if ($myrow["view"] == 1) {
            if ($myrow["image"] != "") {
                echo "<img src=\"" . $myrow["image"] . "\"><br>";
            };
            echo "<b>Username</b>: " . $myrow["username"] . "<br>";
            echo "<b>Real Name</b>: " . $myrow["first_name"] . " " . $myrow["last_name"] . "<br>";
            echo "<b>Email</b>: " . $myrow["email"] . "<br>";
            echo "<b>Location</b>: " . $myrow["city"] . ", " . $myrow["state"] . " " . $myrow["country"] . "<br>";
            if ($myrow["aim"]) {
                echo "<b>AIM</b>: " . $myrow["aim"] . "<br>";
            };
            if ($myrow["icq"]) {
                echo "<b>ICQ</b>: " . $myrow["icq"] . "<br>";
            };
        } else {
            echo $username . " is not visible<p>";
        };
    } else {
        echo "No such user.<p>";
    };
    
    echo "<p><a href=\"?module=forums.php&amp;thread=$thread\" >Back to Forum</a>.<p>";
};

function addPosts($thread, $username, $message, $usesig, $useIP, $emailreply, $firstpost) {
    GLOBAL $poststable;
    GLOBAL $database;
    dbConnect();
    $sql = "INSERT INTO $poststable (post_id, thread_id, message, author, datetime, usesig, useIP, emailreply, firstpost) VALUES (0, '$thread','$message', '$username', NOW(), '$usesig','$useIP','$emailreply', $firstpost)";
    $result = mysql_query($sql) or die("Unable to update posts.\n");
    echo "<i>added your post</i><p>";
};

function addThreads($forum, $username, $subject, $message, $usesig, $useIP, $emailreply) {
    GLOBAL $threadstable;
    GLOBAL $database;
    
    dbConnect();
    $sql = "INSERT INTO $threadstable (thread_id, forum_id, subject, views, replies, datetime, topped, author, usesig, useIP, emailreply) VALUES (0, '$forum','$subject',0,0, NOW(),0, '$username', '$usesig',NULL,'$emailreply')";
    $result = mysql_query($sql) or die("Unable to update thread.\n" . $sql);
    
    echo "<i>added your thread</i><p>";
    
    $latestthread = latestThread($forum);
    $firstpost = 1;
    
    if ($username != "guest") {
        addPosts($latestthread, $username, $message, $usesig, $useIP, $emailreply, $firstpost);
    };
};

function latestThread($forum) {
    GLOBAL $threadstable;
    GLOBAL $database;
    dbConnect();
    $sql = "SELECT thread_id FROM $threadstable WHERE forum_id='$forum' ORDER BY thread_id DESC LIMIT 1";
    $result = mysql_query($sql) or die("Unable to update thread.\n");
    $answer = mysql_fetch_array($result);
    $thread_id = $answer["thread_id"];
    return $thread_id;
};

function updatePost($post, $username, $message, $usesig, $emailreply) {
    GLOBAL $poststable;
    GLOBAL $database;
    dbConnect();
    $sql = "UPDATE $poststable SET message='$message', usesig='$usesig', emailreply='$emailreply', datetime = datetime WHERE post_id='$post'";
    $result = mysql_query($sql) or die("Unable to update post.\n");
    echo "<i>updated your post</i><p>";
};

function deletePost($post, $username) {
    GLOBAL $poststable;
    GLOBAL $database;
    dbConnect();
    $sql = "DELETE FROM $poststable WHERE post_id='$post'";
    $result = mysql_query($sql) or die("Unable to delete post.\n");
    echo "<i>deleted your post</i><p>";
};

function deleteThread($thread, $username) {
    GLOBAL $threadstable;
    GLOBAL $poststable;
    GLOBAL $database;
    
    dbConnect();
    $sql = "DELETE FROM $threadstable WHERE thread_id='$thread'";
    $result = mysql_query($sql) or die("Unable to delete thread.\n");
    
    $sql = "DELETE FROM $poststable WHERE thread_id='$thread'";
    $result = mysql_query($sql) or die("Unable to delete post.\n");
    
    echo "<i>Deleted your thread ".$thread.".</i><p>";
};

function updateThread($thread, $username, $subject, $message, $usesig, $emailreply) {
    GLOBAL $threadstable;
    GLOBAL $poststable;
    GLOBAL $database;
    
    dbConnect();
    
    $sql = "UPDATE $threadstable SET subject='$subject', usesig='$usesig', emailreply='$emailreply', datetime=datetime WHERE thread_id='$thread'";
    $result = mysql_query($sql) or die("Unable to update thread.\n");
    
    $sql = "SELECT post_id FROM $poststable WHERE thread_id='$thread' AND firstpost=1";
    $result = mysql_query($sql) or die("Unable to get first post.\n");
    $postinfo = mysql_fetch_array($result);
    $post = $postinfo["post_id"];
    
    echo "<i>updated your thread</i><p>";
    
    updatePost($post, $username, $message, $usesig, $emailreply);
};

function increaseViews($thread) {
    GLOBAL $threadstable;
    GLOBAL $database;
    
    dbConnect();
    
    $sql = "UPDATE $threadstable SET views=views+1, datetime=datetime WHERE thread_id='$thread'";
    $result = mysql_query($sql) or die("Unable to update views.\n");
};

function increaseReplies($thread) {
    GLOBAL $threadstable;
    GLOBAL $database;
    
    dbConnect();
    
    $sql = "UPDATE $threadstable SET replies=replies+1, datetime=datetime WHERE thread_id='$thread'";
    $result = mysql_query($sql) or die("Unable to update replies.\n");
};

function getEmail($username) {
    GLOBAL $database;
    dbConnect();
    
    $sql = "SELECT email FROM users WHERE username='$username'";
    $result = mysql_query($sql) or die("Unable to retreive data.\n");
    $userinfo = mysql_fetch_array($result);
    $email = $userinfo["email"];
    
    return $email;
};

function getReplies($thread) {
    GLOBAL $database;
    GLOBAL $poststable;
    
    dbConnect();
    
    $sql = "SELECT COUNT(post_id) FROM $poststable WHERE thread_id='$thread' AND firstpost=0";
    $result = mysql_query($sql) or die("Unable to retreive data.\n");
    $threadinfo = mysql_fetch_array($result);
    $replies = $threadinfo[0];
    return $replies;
};

function getThreads($forum) {
    GLOBAL $database;
    GLOBAL $threadstable;
    
    dbConnect();
    
    $sql = "SELECT COUNT(thread_id) FROM $threadstable WHERE forum_id='$forum'";
    $result = mysql_query($sql) or die("Unable to retreive data.\n");
    $threadinfo = mysql_fetch_array($result);
    $threads = $threadinfo[0];
    return $threads;
};

function getForumName($thread) {
    GLOBAL $forumstable;
    GLOBAL $threadstable;
    GLOBAL $database;
    
    dbConnect();
    
    $sql = "SELECT name FROM $forumstable, $threadstable WHERE $threadstable.thread_id='$thread' AND $forumstable.forum_id=$threadstable.forum_id";
    $result = mysql_query($sql) or die("Unable to retreive data.\n");
    $threadinfo = mysql_fetch_array($result);
    $forum = $threadinfo[0];
    return $forum;
};

function getForumID($thread) {
    GLOBAL $forumstable;
    GLOBAL $threadstable;
    GLOBAL $database;
    
    dbConnect();
    $sql = "SELECT $forumstable.forum_id FROM $forumstable, $threadstable WHERE $threadstable.thread_id='$thread' AND $forumstable.forum_id=$threadstable.forum_id";
    $result = mysql_query($sql) or die("Unable to retreive data.\n");
    $threadinfo = mysql_fetch_array($result);
    $forum_id = $threadinfo[0];
    return $forum_id;
};

function mostRecent($forum) {
    
    // currently gives you the most recent post
    GLOBAL $forumstable;
    GLOBAL $threadstable;
    GLOBAL $poststable;
    GLOBAL $database;
    
    dbConnect();
    
    $sql = "SELECT thread_id, author, datetime, subject FROM $threadstable WHERE forum_id='$forum' ORDER BY thread_id DESC LIMIT 1";
    $result = mysql_query($sql) or die("Unable to retreive data.\n");
    $threadinfo = mysql_fetch_array($result);
    
    $sql = "SELECT $poststable.post_id, $poststable.thread_id, $poststable.author AS author, $poststable.datetime AS datetime, 
  $threadstable.thread_id, 
  $threadstable.forum_id FROM $poststable, $threadstable WHERE $threadstable.forum_id='$forum' AND 
  $threadstable.thread_id=$poststable.thread_id ORDER BY post_id DESC LIMIT 1";
    
    $result = mysql_query($sql) or die("Unable to retreive data.\n");
    $postinfo = mysql_fetch_array($result);
    
    if ($postinfo["author"] == "") {
        return "None.";
    } else {
        return "<a href=\"?module=forums.php&amp;userInfo=1&amp;user=" . $postinfo["author"] . "\">" . $postinfo["author"] . "</a> on <b>" . time_format($postinfo["datetime"]) . "</b>";
    };
};

function mostRecentPost($thread) {
    
    // currently gives you the most recent post
    
    GLOBAL $poststable;
    GLOBAL $database;
    
    dbConnect();
    
    $sql = "SELECT $poststable.post_id, $poststable.thread_id, $poststable.author AS author, $poststable.datetime AS datetime FROM 
$poststable WHERE $poststable.thread_id='$thread' ORDER BY post_id DESC LIMIT 1";
    
    $result = mysql_query($sql) or die("Unable to retreive data.\n");
    $postinfo = mysql_fetch_array($result);
    
    if ($postinfo["author"] == "") {
        return "None.";
    } else {
        return "<b><a href=\"?module=forums.php&amp;userInfo=1&amp;user=" . $postinfo["author"] . "\">" . $postinfo["author"] . "</a></b><br> 
on 
<b>" . time_format($postinfo["datetime"]) . "</b>";
    };
};

function printSig($username) {
    GLOBAL $database;
    
    dbConnect();
    
    $sql = "SELECT bio FROM users WHERE username='$username'";
    $result = mysql_query($sql) or die("Unable to retreive data.\n");
    $userinfo = mysql_fetch_array($result);
    $bio = $userinfo["bio"];
    echo $bio;
};

function showForum($forum) {
    GLOBAL $forumstable;
    GLOBAL $prefix;
    GLOBAL $database;
    GLOBAL $width;
    
    dbConnect();
    
    $sql = "SELECT * FROM $forumstable WHERE privatepost=0";
    
    $result = mysql_query($sql) or die("Unable to access database " . $database);
    
    if ($forums = mysql_fetch_array($result)) {
        heading();
        echo "<table class='item-list'>";
        echo "<tr class='title2'><td><b>Name</b></td><td><b>Description</b></td><td><b>Threads</b></td><td><b>Most Recent Post</b></td> </tr>";
        do {
            $tempforum = $forums["forum_id"];
            
            if ($tempforum == $forum) {
                echo "<tr class=forumsselect><td><a href=\"" . $PHP_SELF . $prefix . "forum=" . $forums["forum_id"] . "\">" . $forums["name"] . "</a></td><td>" . $forums["description"] . "</td><td>" . getThreads($forums["forum_id"]) . "</td><td>" . mostRecent($forums["forum_id"]) . "</td></tr>";
            } else {
                echo "<tr class=forumshilight><td><a href=\"" . $PHP_SELF . $prefix . "forum=" . $forums["forum_id"] . "\">" . $forums["name"] . "</a></td><td>" . $forums["description"] . "</td><td>" . getThreads($forums["forum_id"]) . "</td><td>" . mostRecent($forums["forum_id"]) . "</td></tr>";
            };
        } while ($forums = mysql_fetch_array($result));
        echo "</table>";
    } else {
        echo "No FORUMS available.";
    };
};

function showThreads($username, $forum_id, $lowerthread, $numberthread, $descthread, $sortthread) {
    GLOBAL $forumstable;
    GLOBAL $threadstable;
    GLOBAL $poststable;
    GLOBAL $prefix;
    GLOBAL $database;
    GLOBAL $adminuser;
    GLOBAL $width;
    GLOBAL $limit1;
    GLOBAL $limit2;
    
    $sql = "SELECT * FROM $forumstable WHERE forum_id = " . $forum_id;
    
    $result = mysql_query($sql) or die("Unable to access database. " . $sql);
    $forum = mysql_fetch_array($result);
    
    $forumname = $forum["name"];
    
    echo "<B>FORUM <a href=\"$PHP_SELF" . $prefix . "forum=$forum_id\" class=forumlink>" . $forumname . " top</a> >> THREADS</B>";
    
    $totalthreads = getThreads($forum_id);
    $pages = 0;
    $page = floor($lowerthread / $numberthread) + 1;
    
    echo " >>  Jump to Page: ";
    do {
        echo "<a href=\"" . $PHPSELF . $prefix . "forum=$forum_id&amp;numberthread=$numberthread&amp;lowerthread=" . ($numberthread * $pages) . "&amp;descthread=$desc&amp;sortthread=$sortthread\"";
        if ($page == ($pages + 1)) {
            echo " class=\"select2\"";
        };
        echo ">" . ($pages + 1) . "</a>";
        $pages = $pages + 1;
        echo " | ";
    } while (($totalthreads / $numberthread) > $pages);
    echo "<a  href=\"$PHP_SELF" . $prefix . "forum=$forum_id&amp;numberthread=$limit1&amp;lowerthread=0&amp;descthread=$desc&amp;sortthread=$sortthread\"";
    if ($numberthread == $limit1) {
        echo " class=\"select2\"";
    };
    echo ">Limit $limit1 </a> | ";
    echo "<a 
  href=\"$PHP_SELF" . $prefix . "forum=$forum_id&amp;numberthread=$limit2&amp;lowerthread=0&amp;descthread=$desc&amp;sortthread=$sortthread\"";
    if ($numberthread == $limit2) {
        echo " class=\"select2\"";
    };
    echo ">Limit $limit2 </a> | ";
    echo "<a 
  href=\"$PHP_SELF" . $prefix . "forum=$forum_id&amp;numberthread=$totalthreads&amp;lowerthread=0&amp;descthread=$desc&amp;sortthread=$sortthread\"";
    if (($numberthread != $limit2) AND ($numberthread != $limit1)) {
        echo " class=\"select2\"";
    };
    echo ">Show All</a>";
    echo " | Page $page";
    
    echo "<table class='item-list'>";
    echo "<tr class=title2><td><b>Author</b></td><td style='width:" . ($width - 300) . "px'><b>Subject</b></td><td style='width:150px;'><b> Latest Post</b></td><td><b>Views / Replies</b></td></tr>";
    
    $sql = "SELECT $poststable.thread_id, MAX($poststable.datetime) AS max FROM $poststable, $threadstable WHERE $poststable.thread_id=$threadstable.thread_id AND 
  $threadstable.forum_id='$forum_id' AND topped=1 GROUP BY $poststable.thread_id ORDER BY max $descthread LIMIT $lowerthread,  $numberthread";
    
    $result = mysql_query($sql) or die("Unable to access database");
    
    $color = "forumbody";
    
    if ($threads = mysql_fetch_array($result)) {
        do {
            if ($color == "forumbody") {
                echo "<tr class=$color>";
                $color = "forumhilight";
            } else {
                echo "<tr class=$color>";
                $color = "forumbody";
            };
            
            echo "<td><a href=\"?module=forums.php&amp;userInfo=1&amp;user=" . $threads["author"] . "\">" . $threads["author"] . "</a></td>
      <td style='width:" . ($width - 300) . "px;'><a href=\"" . $PHP_SELF . "?thread=" . $threads["thread_id"] . "\">" . htmlentities($threads["subject"]) . "</a></td> 
      <td style='width:200px;'>" . mostRecentPost($threads["thread_id"]) . "<td align=center>" . $threads["views"] . " / " . getReplies($threads["thread_id"]) . "</td></tr>";
        } while ($threads = mysql_fetch_array($result));
    };
    
    $sql = "SELECT $poststable.thread_id, MAX($poststable.datetime) AS max FROM $poststable, $threadstable WHERE 
  $poststable.thread_id=$threadstable.thread_id AND $threadstable.forum_id='" . $forum_id . "' GROUP BY $poststable.thread_id ORDER BY max $descthread LIMIT $lowerthread, $numberthread";
    $result = mysql_query($sql) or die("Unable to access database");
    
    if ($threads = mysql_fetch_array($result)) {
        do {
            if ($color == "forumbody") {
                echo "<tr class=$color>";
                $color = "forumhilight";
            } else {
                echo "<tr class=$color>";
                $color = "forumbody";
            };
            
            $thread_id = $threads["thread_id"];
            $sqlthread = "SELECT * FROM $threadstable WHERE thread_id='$thread_id'";
            $resultthread = mysql_query($sqlthread) or die("Unable to access database");
            
            $threadinfo = mysql_fetch_array($resultthread);
            
            echo "<td class=dotbot><a href=\"?module=forums.php&amp;userInfo=1&amp;user=" . $threadinfo["author"] . "\">" . $threadinfo["author"] . "</a></td><td class=dotbot style='width:200px;'> 
      <a href=\"index.php" . $prefix . "thread=" . $threadinfo["thread_id"] . "\">" . htmlentities($threadinfo["subject"]) . "</a></td> <td class=dotbot>" . mostRecentPost($threadinfo["thread_id"]) . "</td><td class=dotbot>" . $threadinfo["views"] . " / 
      " . getReplies($threadinfo["thread_id"]) . "</td></tr>";
        } while ($threads = mysql_fetch_array($result));
        
        $totalthreads = getThreads($forum_id);
        $pages = 0;
        $page = floor($lowerthread / $numberthread) + 1;
        
        echo "<tr><td colspan=4> Jump to Page: ";
        do {
            echo "<a href=\"" . $PHP_SELF . $prefix . "forum=$forum_id&amp;numberthread=$numberthread&amp;lowerthread=" . ($numberthread * $pages) . "&amp;descthread=$desc&amp;sortthread=$sortthread\"";
            if ($page == ($pages + 1)) {
                echo " class=\"select2\"";
            };
            echo ">" . ($pages + 1) . "</a>";
            $pages = $pages + 1;
            echo " | ";
        } while (($totalthreads / $numberthread) > $pages);
        
        echo "<a href=\"$PHP_SELF" . $prefix . "forum=$forum_id&amp;numberthread=$limit1&amp;lowerthread=0&amp;descthread=$desc&amp;sortthread=$sortthread\"";
        if ($numberthread == $limit1) {
            echo " class=\"select2\"";
        };
        echo ">Limit $limit1 </a> | ";
        echo "<a href=\"$PHP_SELF" . $prefix . "forum=$forum_id&amp;numberthread=$limit2&amp;lowerthread=0&amp;descthread=$desc&amp;sortthread=$sorttread\"";
        if ($numberthread == $limit2) {
            echo " class=\"select2\"";
        };
        echo ">Limit $limit2 </a> | ";
        echo "<a href=\"$PHP_SELF" . $prefix . "forum=$forum_id&amp;numberthread=$totalthreads&amp;lower=0&amp;descthread=$desc&amp;sortthread=$sortthread\"";
        if (($numberthread != $limit2) AND ($numberthread != $limit1)) {
            echo " class=\"select2\"";
        };
        echo ">Show All</a>";
        echo " | Page $page";
        echo "</td></tr>";
        
        echo "\n<tr><td>&amp;nbsp;</td></tr>";
    } else {
        echo "<tr><td colspan=4>No THREADS available.</td></tr>";
    };
    
    echo "<tr class='title2'><td colspan='4'><b>Create a New Thread</b></td></tr>";
    
    if (!$username || $username == "guest") {
        echo "<tr><td colspan='4'><b>Username:</b></td><td>guest ";
        echo "(<a href=\"?module=access_forum.php\">Login</a>) ";
        echo "(<a href=\"?module=newuser.php&amp;command=NEWUSER\">Sign Up</a>) ";
        echo "</td></tr>";
    } else {
        echo "<tr><td><b>Username:</b></td><td><a  href=\"?module=forums.php&amp;userInfo=1&amp;user=" . $username . "\">" . $username . "</a>";
        echo " (<a href=\"?module=adminuser.php&amp;user=$username\">Edit Profile</a>)";
        echo " (<a href=\"?module=logout.php&amp;logout=logout\">Log Out</a>)";
        echo "</td></tr>";
        echo "<tr><td><b>Your Email</b>: </td><td>" . getEmail($username) . "</td></tr>";
        
        echo "<form method=\"post\" action=\"$rootpage\">";
        echo "<tr><td><b>Subject</b>: </td><td> <input name=\"subject\" type=\"text\" class=form1></input></td></tr>";
        echo "<tr><td colspan=5><textarea name=\"message\" class=form1 rows=\"10\" cols=\"80\"  wrap=\"virtual\"></textarea></td></tr>";
        echo "<tr><td><b>include sig:</b></td> <td><input type=\"checkbox\" value=\"1\" name=\"usesig\"></td></tr>";
        echo "<input name=\"addthread\" type=\"hidden\" value=\"$forum_id\">";
        echo "<input name=\"main\" type=\"hidden\" value=\"FORUMS\">";
        echo "<tr><td><input name=\"submit\" type=\"Submit\" value=\"post\" class=form1></td></tr>";
        echo "</form>";
    };
    
    echo "</table>";
};

function showEntry($username, $thread_id) {
    GLOBAL $threadstable;
    GLOBAL $poststable;
    GLOBAL $prefix;
    GLOBAL $database;
    
    dbConnect();
    
    $sql = "SELECT * FROM $threadstable WHERE thread_id=$thread_id";
};

function showBlog($username, $forum_id, $lowerthread, $numberthread, $descthread, $sortthread) {
    GLOBAL $forumstable;
    GLOBAL $threadstable;
    GLOBAL $poststable;
    GLOBAL $prefix;
    GLOBAL $database;
    GLOBAL $limit1;
    GLOBAL $limit2;
    
    dbConnect($database);
    
    $sql = "SELECT * FROM $forumstable WHERE forum_id=$forum_id";
    $result = mysql_query($sql) or die("Unable to access database");
    $forum = mysql_fetch_array($result);
    $forumname = $forum["name"];
    
    echo "<B>FORUM <font class=\"hilight\"><a href=\"$PHP_SELF" . $prefix . "forum=$forum_id\">" . $forumname . "</a></font> >> THREADS</B>";
    
    $totalthreads = getThreads($forum_id);
    $pages = 0;
    $page = floor($lowerthread / $numberthread) + 1;
    
    echo " >>  Jump to Page: ";
    do {
        echo "<a 
  href=\"" . $PHP_SELF . $prefix . "forum=$forum_id&amp;numberthread=$numberthread&amp;lowerthread=" . ($numberthread * $pages) . "&amp;descthread=$desc&amp;sortthread=$sortthread\"";
        if ($page == ($pages + 1)) {
            echo " class=\"select2\"";
        };
        echo ">" . ($pages + 1) . "</a>";
        $pages = $pages + 1;
        echo " | ";
    } while (($totalthreads / $numberthread) > $pages);
    echo "<a 
  href=\"$PHP_SELF" . $prefix . "forum=$forum_id&amp;numberthread=$limit1&amp;lowerthread=0&amp;descthread=$desc&amp;sortthread=$sortthread\"";
    if ($numberthread == $limit1) {
        echo " class=\"select2\"";
    };
    echo ">Limit $limit1 </a> | ";
    echo "<a 
  href=\"$PHP_SELF" . $prefix . "forum=$forum_id&amp;numberthread=$limit2&amp;lowerthread=0&amp;descthread=$desc&amp;sortthread=$sortthread\"";
    if ($numberthread == $limit2) {
        echo " class=\"select2\"";
    };
    echo ">Limit $limit2 </a> | ";
    echo "<a 
  href=\"$PHP_SELF" . $prefix . "forum=$forum_id&amp;numberthread=$totalthreads&amp;lowerthread=0&amp;descthread=$desc&amp;sortthread=$sortthread\"";
    if (($numberthread != $limit2) AND ($numberthread != $limit1)) {
        echo " class=\"select2\"";
    };
    echo ">Show All</a>";
    echo " | Page $page";
    
    echo "<br>";
    
        echo "<table class='item-list'>";
    echo "<tr class=title2><td width=5%><b>Author</b></td><td><b>Subject</b></td><td width=20%><b> 
  Latest Post</b></td><td><b>Views / Replies</b></td></tr>";
    
    $sql = "SELECT $poststable.thread_id, MAX($poststable.datetime) AS max FROM $poststable, $threadstable WHERE 
  $poststable.thread_id=$threadstable.thread_id AND 
  $threadstable.forum_id='$forum_id' AND topped=1 GROUP BY $poststable.thread_id ORDER BY max $descthread LIMIT $lowerthread, 
  $numberthread";
    $result = mysql_query($sql) or die("Unable to access database. " . $sql);
    
    $color = "forumsbody";
    
    if ($threads = mysql_fetch_array($result)) {
        do {
            if ($color == "shade1") {
                echo "<tr class='$color'>";
                $color = "forumshilight";
            } else {
                echo "<tr class=$color>";
                $color = "forumsbody";
            };
            
            echo "<td><a href=\"?module=forums.php&amp;userInfo=1&amp;user=" . $threads["author"] . "\">" . $threads["author"] . "</a></td><td><a href=\"" . $PHP_SELF . "?thread=" . $threads["thread_id"] . "\">" . $threads["subject"] . "</a></td> 
  <td>" . mostRecentPost($threads["thread_id"]) . "<td align=center>" . $threads["views"] . " / " . getReplies($threads["thread_id"]) . "</td></tr>";
        } while ($threads = mysql_fetch_array($result));
    };
    
    $sql = "SELECT $poststable.thread_id, MAX($poststable.datetime) AS max FROM $poststable, $threadstable WHERE 
  $poststable.thread_id=$threadstable.thread_id AND 
  $threadstable.forum_id='$forum_id' GROUP BY $poststable.thread_id ORDER BY max $descthread LIMIT $lowerthread, $numberthread";
    $result = mysql_query($sql) or die("Unable to access database. " . $sql);
    
    if ($threads = mysql_fetch_array($result)) {
        do {
            if ($color == "forumsbody") {
                echo "<tr class=$color>";
                $color = "forumshilight";
            } else {
                echo "<tr class=$color>";
                $color = "forumsbody";
            };
            
            $thread_id = $threads["thread_id"];
            $sqlthread = "SELECT * FROM $threadstable WHERE thread_id='$thread_id'";
            $resultthread = mysql_query($sqlthread) or die("Unable to access database. " . $sqlthread);
            $threadinfo = mysql_fetch_array($resultthread);
            
            echo "<td><a href=\"?module=forums.php&amp;userInfo=1&amp;user=" . $threadinfo["author"] . "\">" . $threadinfo["author"] . "</a></td><td> 
  <a href=\"" . $PHP_SELF . $prefix . "thread=" . $threadinfo["thread_id"] . "\">" . $threadinfo["subject"] . "</a></td> 
  <td>" . mostRecentPost($threadinfo["thread_id"]) . "</td><td>" . $threadinfo["views"] . " / 
  " . getReplies($threadinfo["thread_id"]) . "</tr>";
        } while ($threads = mysql_fetch_array($result));
        
        $totalthreads = getThreads($forum_id);
        $pages = 0;
        $page = floor($lowerthread / $numberthread) + 1;
        
        echo "<tr><td colspan=5> Jump to Page: ";
        do {
            echo "<a 
  href=\"" . $PHP_SELF . $prefix . "forum=$forum_id&amp;numberthread=$numberthread&amp;lowerthread=" . ($numberthread * $pages) . "&amp;descthread=$desc&amp;sortthread=$sortthread\"";
            if ($page == ($pages + 1)) {
                echo " class=\"select2\"";
            };
            echo ">" . ($pages + 1) . "</a>";
            $pages = $pages + 1;
            echo " | ";
        } while (($totalthreads / $numberthread) > $pages);
        
        echo "<a 
  href=\"$PHP_SELF" . $prefix . "forum=$forum_id&amp;numberthread=$limit1&amp;lowerthread=0&amp;descthread=$desc&amp;sortthread=$sortthread\"";
        if ($numberthread == $limit1) {
            echo " class=\"select2\"";
        };
        echo ">Limit $limit1 </a> | ";
        echo "<a 
  href=\"$PHP_SELF" . $prefix . "forum=$forum_id&amp;numberthread=$limit2&amp;lowerthread=0&amp;descthread=$desc&amp;sortthread=$sortthread\"";
        if ($numberthread == $limit2) {
            echo " class=\"select2\"";
        };
        echo ">Limit $limit2 </a> | ";
        echo "<a 
  href=\"$PHP_SELF" . $prefix . "forum=$forum_id&amp;numberthread=$totalthreads&amp;lower=0&amp;descthread=$desc&amp;sortthread=$sortthread\"";
        if (($numberthread != $limit2) AND ($numberthread != $limit1)) {
            echo " class=\"select2\"";
        };
        echo ">Show All</a>";
        echo " | Page $page";
        
        echo "</td></tr>";
        
        echo "\n<tr><td>&amp;nbsp;</td></tr>";
    } else {
        echo "<tr><td colspan=4>No THREADS available.</td></tr>";
    };
    
    echo "<tr><td>&amp;nbsp;</td></tr>";
    echo "<tr class=title2><td colspan='4'><b>Create a New Thread</b></td></tr>";
    echo "<tr><td colspan='4'><b>Username:</b></td><td>" . $username;
    
    if ($username == "guest") {
        echo " (<a href=\"?module=newuser.php\">Sign Up</a>)";
    } else {
        echo "(<a href=\"?module=logout.php&amp;logout=logout\">Log Out</a>)";
    };
    echo "</td></tr>";
    
    echo "<tr><td><b>Your Email</b>: </td><td>" . getEmail($username) . "</td></tr>";
    echo "<form method=\"post\" action=\"$rootpage\">";
    echo "<tr><td><b>Subject</b>: </td><td> <input name=\"subject\" type=\"text\" class=form1></input></td></tr>";
    echo "<tr><td colspan=5><textarea name=\"message\" class=form1 rows=\"10\" cols=\"80\" 
  wrap=\"virtual\"></textarea></td></tr>";
    echo "<tr><td><b>include sig:</b></td> <td><input type=\"checkbox\" value=\"1\" name=\"usesig\"></td></tr>";
    echo "<input name=\"addthread\" type=\"hidden\" value=\"$forum_id\">";
    echo "<input name=\"main\" type=\"hidden\" value=\"FORUMS\">";
    echo "<tr><td><input name=\"submit\" type=\"Submit\" value=\"post\" class=button1></td></tr>";
    echo "</form>";
    
    echo "</table>";
};

function showPosts($username, $thread_id, $lower, $number, $desc, $sortpost) {
    GLOBAL $_SESSION;
    GLOBAL $forumstable;
    GLOBAL $threadstable;
    GLOBAL $poststable;
    GLOBAL $prefix;
    GLOBAL $database;
    GLOBAL $adminuser;
    GLOBAL $width;
    GLOBAL $limit1;
    GLOBAL $limit2;
    GLOBAL $badhtml;
    
    dbConnect($database);
    
    $sql = "SELECT * FROM $threadstable WHERE thread_id=$thread_id";
    $result = mysql_query($sql) or die("Unable to access " . $threadstable . " = " . $thread_id . " in database " . $database);
    
    $thread = mysql_fetch_array($result);
    $threadsubject = $thread["subject"];
    
    echo "<table>";
    echo "<tr><td>";
    echo "<B>FORUM <a href=\"$PHP_SELF" . $prefix . "forum=" . getForumID($thread_id) . "\" class=forumlink>" . getForumName($thread_id) . " 
  top</a></font></td><td> >> THREAD <font 
  class=\"hilight\"><a href=\"$PHP_SELF" . $prefix . "thread=$thread_id\" 
  class=forumlink>\"" . substr($threadsubject, 0, 40) . "...\"</a></font> >> 
  POSTS</B>";
    
    $totalreplies = getReplies($thread_id);
    $pages = 0;
    $page = floor($lower / $number) + 1;
    
    echo " >>  Jump to Page: ";
    
    do {
        echo "<a 
    href=\"" . $PHP_SELF . $prefix . "thread=$thread_id&amp;number=$number&amp;lower=" . ($number * $pages) . "&amp;desc=$desc&amp;sortpost=$sortpost\"";
        if ($page == ($pages + 1)) {
            echo " class=\"select2\"";
        };
        echo ">" . ($pages + 1) . "</a>";
        $pages = $pages + 1;
        echo " | ";
    } while (($totalreplies / $number) > $pages);
    
    echo "<a href=\"$PHP_SELF" . $prefix . "thread=$thread_id&amp;number=$limit1&amp;lower=0&amp;desc=$desc&amp;sortpost=$sortpost\"";
    if ($number == $limit1) {
        echo " class=\"select2\"";
    };
    echo ">Limit $limit1 </a> | ";
    echo "<a href=\"$PHP_SELF" . $prefix . "thread=$thread_id&amp;number=$limit2&amp;lower=0&amp;desc=$desc&amp;sortpost=$sortpost\"";
    if ($number == $limit2) {
        echo " class=\"select2\"";
    };
    echo ">Limit $limit2 </a> | ";
    echo "<a href=\"$PHP_SELF" . $prefix . "thread=$thread_id&amp;number=$totalreplies&amp;lower=0&amp;desc=$desc&amp;sortpost=$sortpost\"";
    if (($number != $limit2) AND ($number != $limit1)) {
        echo " class=\"select2\"";
    };
    echo ">Show All</a>";
    echo " | Page $page </td></tr></table>";
    
    echo "<table class='item-list'>";
    echo "<tr class=title2><td width=100><b>Author</b></td><td width=" . ($width - 300) . "><b>Subject</b></td><td 
  width=100><b>Posted</b></td><td width=25><b>Views</b></td><td width=25><b>Replies</b></td></tr>";
    
    $sql = "SELECT * FROM $threadstable WHERE thread_id=$thread_id";
    $result = mysql_query($sql) or die("Unable to access database. " . $sql);
    
    if ($threads = mysql_fetch_array($result)) {
        do {
            $sqlfirst = "SELECT * FROM $poststable WHERE thread_id = $thread_id AND firstpost=1";
            $resultfirst = mysql_query($sqlfirst) or die("Unable to access database" . $sqlfirst);
            $firstinfo = mysql_fetch_array($resultfirst);
            
            $message = $firstinfo["message"];
            $message = strip_tags($message, $badhtml);
            
            echo "<tr class=\"forumtitle\"><td><a href=\"?module=forums.php&amp;userInfo=1&amp;user=" . $threads["author"] . "\">" . $threads["author"] . "</a></td><td>" . $threads["subject"] . "</td> <td>" . time_format($threads["datetime"]) . "<td>" . $threads["views"] . "</td><td>" . getReplies($threads["thread_id"]) . "</td></tr>";
            echo "<tr><td colspan=5 class=dotbot>" . $message . "</td></tr>";
            
            if (($threads["author"] == $username && $threads["author"] != "guest") OR $username == $adminuser) {
                echo "<tr class=forumshilight><td colspan=5><font size=-1>[<a href=\"$PHP_SELF" . $prefix . "editthread=" . $threads["thread_id"] . "\" class=\"forummenu\">edit/delete</a>]</td></tr>";
            };
            
            $subject = $threads["subject"];
        } while ($threads = mysql_fetch_array($result));
        echo "<tr><td>&amp;nbsp;</td></tr>";
    };
    
    $sql = "SELECT * FROM $poststable WHERE thread_id=$thread_id AND firstpost=0 ORDER BY $sortpost " . $sortArray[$desc] . " LIMIT $lower, $number";
    $result = mysql_query($sql) or die("Unable to access database. SQL = " . $sql);
    
    if ($posts = mysql_fetch_array($result)) {
        $replynumber = 0;
        $replynumber = $replynumber + $lower;
        do {
            $message = $posts["message"];
            $message = strip_tags($message, $badhtml);
            $replynumber = $replynumber + 1;
            
            echo "<tr class=\"forumtitle\"><td><a href=\"?module=forums.php&amp;userInfo=1&amp;user=" . $posts["author"] . "\">" . $posts["author"] . "</td><td></td><td>" . time_format($posts["datetime"]) . "</td><td></td><td>#" . $replynumber . "</td></tr>";
            echo "<tr><td colspan=5 class=dotbot><font class=forummessage>" . $message;
            if ($posts["usesig"] == 1) {
                echo "<p>";
                printSig($posts["author"]);
            };
            echo "</td></tr>";
            if (($posts["author"] == $username && $posts["author"] != "guest") OR $username == $adminuser) {
                echo "<tr 
      class=\"forumshilight\"><td 
      colspan=5><font size=-1>[<a 
      href=\"$PHP_SELF" . $prefix . "editpost=" . $posts["post_id"] . "&amp;passthread=" . $thread_id . "\" class=\"forummenu\">edit/delete</a>] 
      </td></tr>";
            };
            
            echo "\n<tr><td>&amp;nbsp;</td></tr>";
        } while ($posts = mysql_fetch_array($result));
        
        $totalreplies = getReplies($thread_id);
        $pages = 0;
        $page = floor($lower / $number) + 1;
        
        echo "<tr><td colspan=5> Jump to Page: ";
        do {
            echo "<a 
      href=\"" . $PHP_SELF . $prefix . "thread=$thread_id&amp;number=$number&amp;lower=" . ($number * $pages) . "&amp;desc=$desc&amp;sortpost=$sortpost\"";
            if ($page == ($pages + 1)) {
                echo " class=\"select2\"";
            };
            echo ">" . ($pages + 1) . "</a>";
            $pages = $pages + 1;
            echo " | ";
        } while (($totalreplies / $number) > $pages);
        
        echo "<a href=\"$PHP_SELF" . $prefix . "thread=$thread_id&amp;number=$limit1&amp;lower=0&amp;desc=$desc&amp;sortpost=$sortpost\"";
        if ($number == $limit1) {
            echo " class=\"select2\"";
        };
        echo ">Limit $limit1 </a> | ";
        echo "<a href=\"$PHP_SELF" . $prefix . "thread=$thread_id&amp;number=$limit2&amp;lower=0&amp;desc=$desc&amp;sortpost=$sortpost\"";
        if ($number == $limit2) {
            echo " class=\"select2\"";
        };
        echo ">Limit $limit2 </a> | ";
        echo "<a href=\"$PHPSELF" . $prefix . "thread=$thread_id&amp;number=$totalreplies&amp;lower=0&amp;desc=$desc&amp;sortpost=$sortpost\"";
        if (($number != $limit2) AND ($number != $limit1)) {
            echo " class=\"select2\"";
        };
        echo ">Show All</a>";
        echo " | Page $page";
        
        echo "</td></tr>";
        
        echo "\n<tr><td>&amp;nbsp;</td></tr>";
    } else {
        echo "<tr><td colspan=4>No replies.</td></tr>";
    };
    
    echo "<tr><td>&amp;nbsp;</td></tr>";
    echo "<tr class=title2><td colspan=5><b>Reply to This Thread</b></td></tr>";
    
    if (!$username || $username == "guest") {
        echo "<tr><td><b>Username:</b></td><td>guest ";
        echo "(<a href=\"?module=access_forum.php\">Login</a>) ";
        echo "(<a href=\"?module=newuser.php&amp;command=NEWUSER\">Sign Up</a>) ";
        
        echo "</td></tr>";
    } else {
        echo "<tr><td><b>Username:</b></td><td>" . $username;
        echo " (<a href=\"?module=adminuser.php&amp;user=$username\">Edit Profile</a>)";
        echo " (<a href=\"?module=logout.php&amp;command=logout\">Log Out</a>)";
        echo "</td></tr>";
        echo "<tr><td><b>Your Email</b>: </td><td>" . getEmail($username) . "</td></tr>";
        
        echo "<tr><td><b>RE</b>: </td><td>" . $subject . "</td></tr>";
        echo "<form method=\"post\" action=\"$rootpage\">";
        echo "<tr><td colspan=5><textarea name=\"message\" class=form1 rows=\"7\" cols=\"80\" 
  wrap=\"virtual\"></textarea></td></tr>";
        echo "<tr><td><b>include sig:</b></td> <td><input type=\"checkbox\" value=\"1\" name=\"usesig\"></td></tr>";
        echo "<input name=\"addpost\" type=\"hidden\" value=\"$thread_id\">";
        echo "<input name=\"main\" type=\"hidden\" value=\"FORUMS\">";
        echo "<input name=\"lower\" type=\"hidden\" value=\"$lower\">";
        echo "<input name=\"number\" type=\"hidden\" value=\"$number\">";
        echo "<tr><td><input name=\"submit\" type=\"Submit\" value=\"post\" class=button1></td></tr>";
        echo "</form>";
    };
    
    echo "</table>";
};

function editPost($username, $post, $thread) {
    GLOBAL $poststable;
    GLOBAL $database;
    
    dbConnect($database);
    
    $sql = "SELECT * FROM $poststable WHERE post_id='$post'";
    $result = mysql_query($sql) or die("Unable to access database");
    $postcontent = mysql_fetch_array($result);
    
    echo "<table>";
    echo "<tr class=title2><td colspan=2><b>Edit this Post</b></td></tr>";
    
    echo "<tr><td><b>Username:</b></td><td>" . $username;
    if ($username == "guest") {
        echo "()";
    } else {
        echo "(<a href=\"?module=logout.php\">Log Out</a>)";
    };
    echo "</td></tr>";
    
    echo "<tr><td><b>Your Email</b>: </td><td>" . getEmail($username) . "</td></tr>";
    echo "<form method=\"post\" action=\"$rootpage\">";
    echo "<tr><td colspan=2><textarea name=\"message\" rows=\"10\" cols=\"80\" 
  wrap=\"virtual\" class=form1>" . $postcontent["message"] . "</textarea></td></tr>";
    echo "<tr><td><b>include sig:</b></td> <td><input type=\"checkbox\" value=\"1\" name=\"usesig\" ";
    if ($postcontent["usesig"] == 1) {
        echo "CHECKED";
    };
    echo "></td></tr>";
    echo "<input name=\"updatepost\" type=\"hidden\" value=\"$post\">";
    echo "<input name=\"passthread\" type=\"hidden\" value=\"$thread\">";
    echo "<input name=\"main\" type=\"hidden\" value=\"FORUMS\">";
    echo "<tr><td><input name=\"updateP\" type=\"Submit\" value=\"update\" class=button1><input name=\"deleteP\" type=\"Submit\" value=\"delete\" class=button1>
  <p><a href=\"?module=forums.php&amp;thread=$thread\">back to thread</a> </td></tr>";
    echo "</form>";
    echo "</table>";
};

function editThread($username, $thread) {
    GLOBAL $poststable;
    GLOBAL $threadstable;
    GLOBAL $database;
    GLOBAL $rootpage;
    
    dbConnect($database);
    
    $sql = "SELECT * FROM $threadstable WHERE thread_id='$thread'";
    $result = mysql_query($sql) or die("Unable to access database");
    $threadcontent = mysql_fetch_array($result);
    
    $sqlfirst = "SELECT * FROM $poststable WHERE thread_id='$thread' AND firstpost=1";
    $resultfirst = mysql_query($sqlfirst) or die("Unable to access database");
    $firstinfo = mysql_fetch_array($resultfirst);
    
    echo "<table>";
    echo "<tr class=title2><td colspan=3><b>Edit this Thread</b></td></tr>";
    echo "<tr><td><b>Username:</b></td><td>" . $username;
    if ($username == "guest") {
        echo " ()";
    } else {
        echo "(<a href=\"?module=logout.php\">Log Out</a>)";
    };
    echo "</td><td></td></tr>";
    echo "<tr><td><b>Your Email</b>: </td><td>" . getEmail($username) . "</td><td></td></tr>";
    echo "<form method=\"post\" action=\"$rootpage\">";
    echo "<tr><td><b>Subject:</b></td><td><input name=\"subject\" type=\"text\" 
  value=\"" . $threadcontent["subject"] . "\" class=form1></input></td><td></td></tr>";
    echo "<tr><td colspan=3><textarea name=\"message\" rows=\"7\" cols=\"80\" 
  wrap=\"virtual\" class=form1>" . $firstinfo["message"] . "</textarea></td></tr>";
    echo "<tr><td><b>include sig:</b></td> <td><input type=\"checkbox\" value=\"1\" name=\"usesig\" ";
    if ($threadcontent["usesig"] == 1) {
        echo "CHECKED";
    };
    echo "></td></tr>";
    echo "<input name=\"updatethread\" type=\"hidden\" value=\"$thread\">";
    echo "<input name=\"main\" type=\"hidden\" value=\"FORUMS\">";
    echo "<tr><td><input name=\"updateT\" type=\"Submit\" value=\"update\" class=button1><input name=\"deleteT\" type=\"Submit\" value=\"delete\" class=button1>
  <p><a href=\"?module=forums.php&amp;thread=$thread\">back to thread</a>
  </td></tr>";
    echo "</form>";
    echo "</table>";
};

function searchPost() {
    
    // displays a search box to search by thread, user, or content
    
    
};

function heading() {
    
    // displays a big ass heading
    GLOBAL $rootpage;
    GLOBAL $module;
    
    echo "<a href=\"$rootpage?module=$module&amp;forum=1\" style=\"font-family:impact,arial;font-size:24pt;font-weight:normal;\">PGHELECTRO</a>  
  <a style=\"font-family:impact,arial;font-size:24pt;font-weight:normal;\">@</a>
  <a href=\"http://wrecked-distro.com\" style=\"font-family:impact,arial;font-size:24pt;font-weight:normal;\">WRECKED-DISTRO.COM</a><br>";
};

function listUsers() {
    GLOBAL $database;
    echo "<b>Users</b> <a href=\"$rootpage?page=forums.php\">back to the forum</a><p>";
    
    dbConnect($database);
    
    $sql = "SELECT * FROM users ORDER BY username";
    $result = mysql_query($sql);
    
    if ($myrow = mysql_fetch_array()) {
    };
};

if (!$lower) {
    $lower = 0;
};
if (!$number) {
    $number = 25;
};
if (!$desc) {
    $desc = "";
};
if (!$sortpost) {
    $sortpost = "datetime";
};
if (!$username) {
    $username = "";
};
if (!$module) {
    $module = 'forums.php';
};
if (!$_SESSION['forum']) {
    $forum = 1;
} else {
    $forum = $_SESSION['forum'];
}

if (!$lowerthread) {
    $lowerthread = 0;
};
if (!$numberthread) {
    $numberthread = 25;
};
if (!$descthread) {
    $descthread = "DESC";
};
if (!$sortthread) {
    $sortthread = "datetime";
};

if ($_SESSION['username']) {
    $username = $_SESSION['username'];
} else {
    $username = 'guest';
}

if ($blog) {
    showBlog($_REQUEST['forum']);
    echo "<p>";
    showBlogPost();
};

if ($_REQUEST['thread']) {
    showForum(getForumID($_REQUEST['forum']));
    echo "<P>";
    increaseViews($thread);
    
    showPosts($username, $_REQUEST['thread'], $lower, $number, $desc, $sortpost);
};

if ($_REQUEST['addpost'] && ($username != "guest")) {
    $emailreply = 0;
    $useIP = 0;
    $firstPost = 0;
    addPosts($addpost, $username, $message, $usesig, $useIP, $emailreply, $firstPost);
    
    showForum($forum);
    echo "<P>";
    increaseReplies($addpost);
    showPosts($username, $addpost, $lower, $number, $desc, $sortpost);
};

if ($_REQUEST['addthread'] && ($username != "guest")) {
    $addthread = $_REQUEST['addthread'];
    
    $emailreply = 0;
    $useIP = 0;
    addThreads($addthread, $username, $_REQUEST['subject'], $_REQUEST['message'], $_REQUEST['usesig'], $useIP, $emailreply);
};

if ($_REQUEST['editpost']) {
    editPost($username, $editpost, $passthread);
};

if ($_REQUEST['updateP']) {
    $emailreply = 0;
    updatePost($updatepost, $username, $message, $usesig, $emailreply);
    showForum(getForumID($passthread));
    echo "<p>";
    showPosts($username, $passthread, $lower, $number, $desc, $sortpost);
};

if ($_REQUEST['deleteP']) {
    deletePost($updatepost, $username);
    showForum(getForumID($passthread));
    echo "<P>";
    showPosts($username, $passthread, $lower, $number, $desc, $sortpost);
};

if ($_REQUEST['editthread']) {
    editThread($username, $_REQUEST['editthread']);
};

if ($_REQUEST['updateT']) {
    $emailreply = 0;
    updateThread($updatethread, $username, $subject, $message, $usesig, $emailreply);
    showForum(getForumID($updatethread));
    echo "<P>";
    showThreads($username, getForumID($updatethread), $lowerthread, $numberthread, $descthread, $sortthread);
};

if ($_REQUEST['deleteT']) {
	$updatethread = $_REQUEST['updatethread'];
    $forum_id = getForumID($updatethread);
    deleteThread($updatethread, $username);
    showForum(1);
    echo "<P>";
    showThreads($username, 1, $lowerthread, $numberthread, $descthread, $sortthread);
};

if ($_REQEUEST['userInfo']) {
    showUser($user);
};

if (!$_REQUEST['blog'] && !$_REQUEST['thread'] && !$_REQUEST['addpost'] && !$_REQUEST['addthread'] && !$_REQUEST['editpost'] && !$_REQUEST['updateP'] && !$_REQUEST['deleteP'] && !$_REQUEST['editthread'] && !$_REQUEST['updateT'] && !$_REQUEST['deleteT'] && !$_REQUEST['userInfo']) {
    showForum($forum);
    echo "<p>";
    showThreads($username, $forum, $lowerthread, $numberthread, $descthread, $sortthread);
} else {
    
};
?> 
