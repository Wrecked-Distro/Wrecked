<?php
// admincomment.php
// tool for users to post comments about items, and to build recommendation lists
// is passed an itemID from adminitems to list comments given an item

  include("header.php");
  include("db.php");
?>

<html>
<head>
<title>Admin Item Comments</title>
</head>

<body>

<b>Item Comment Admin</b>

<p>

<?php
   dbConnect();

   if ($submit)
   {
    // here if no ID then adding, else we're editing

     if ($commentID)
     {
      $sql = "UPDATE comments SET itemID='$itemID', username='$username', message='$message',
keywords='$keywords',rank='$rank',datetime='$year+$month+$day',expire='$expire',expiredate='$pyear+$pmonth+$pday' WHERE
commentID='$commentID'";
      echo "Update of ".$itemID."\n";
     }
     else
     {
  $sql = "INSERT INTO comments (commentID,itemID,username,message,keywords,rank,datetime,expire,expiredate)
VALUES
(0,'$itemID','$username','$message','$keywords','$rank','$year+$month+$day','$expire','$pyear+$pmonth+$pday')";

      echo "inserting ".$itemID."\n";

     }

     // run SQL against the DB

      $result = mysql_query($sql);

      echo "Record updated.<p>";

      echo "<a href=\"$PHP_SELF?itemID=$itemID&sort=$sort&lower=$lower&number=$number&desc=$desc\">more
comments</a>";


     } elseif ($delete) {

       // delete a record

       $sql = "DELETE FROM comments WHERE commentID='$commentID'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";
      echo "<a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";

     } else {

      // this part happens if we don't press submit

     if (!$commentID) {
    // print the list if there is not editing

     $result = mysql_query("SELECT * FROM comments,items WHERE comments.itemID=items.itemid AND
comments.itemID='$itemID'");

     if ($myrow = mysql_fetch_array($result))
     {

       echo "<table border=0 cellspacing=0 cellpadding=3>\n";

       echo "<tr><td class=\"title1\" colspan=9><b>Comments</b></td></tr>\n";
       echo "<tr class=\"title2\">
             <td>
<a href=\"$PHP_SELF?sort=commentID&lower=$lower&number=$number&desc=$desc\">CommentID</a></td>
             <td>
<a href=\"$PHP_SELF?sort=itemID&lower=$lower&number=$number&desc=$desc\">ItemID</a></td>
             <td>
<a href=\"$PHP_SELF?sort=username&lower=$lower&number=$number&desc=$desc\">Username</a></td>
             <td>
<a href=\"$PHP_SELF?sort=message&lower=$lower&number=$number&desc=$desc\">Message</a></td>
             <td>
<a href=\"$PHP_SELF?sort=keywords&lower=$lower&number=$number&desc=$desc\">Keywords</a></td>
             <td>
<a href=\"$PHP_SELF?sort=rank&lower=$lower&number=$number&desc=$desc\">Rank</a></td>
             <td>
<a href=\"$PHP_SELF?sort=datetimes&lower=$lower&number=$number&desc=$desc\">DateTime</a></td>
             <td>
<a href=\"$PHP_SELF?sort=expire&lower=$lower&number=$number&desc=$desc\">Expire</a></td>
             <td>
<a href=\"$PHP_SELF?sort=expiredate&lower=$lower&number=$number&desc=$desc\">ExpireDate</a></td>

             </tr>\n";

       do
       {
        printf("<tr><td>%s</td> <td><a href=\"admincommentB.php=itemid=%s\">%s - %s - %s %s</a></td> <td>%s</td>
<td>%s</td><td><a href=\"admincommentA.php?keywords=%s\">%s</a></td>
<td>%s</td><td>%s</td><td>%s</td><td>%s</td>",
        $myrow["commentID"],$myrow["itemID"], $myrow["artist"], $myrow["title"], $myrow["label"], $myrow["format"],
$myrow["username"],$myrow["message"], $myrow["keywords"], $myrow["keywords"],
$myrow["rank"],$myrow["datetime"],$myrow["expire"],$myrow["expiredate"]) ;

        printf("<td><a
href=\"%s?commentID=%s&delete=yes&sort=$sort&lower=$lower&number=$number&desc=$desc\">(DELETE)</a></td><td><a
href=\"%s?commentID=%s&sort=$sort&lower=$lower&number=$number&desc=$desc\">(EDIT)</a></td></tr>",
        $PHP_SELF,$myrow["commentID"],$PHP_SELF,$myrow["commentID"]);

       } while ($myrow=mysql_fetch_array($result));
       echo "</table>\n";
      }

     echo "<p>";

     }

    ?>

<table>
<tr><td>
<form action"<? echo $PHP_SELF;?>" method="post">
<input type="hidden" name="sort" value="<? echo $sort;?>">
<input type="submit" name="show" value="Show&nbsp;:" class="button1">
<input type="text" name="number" size="3" value="<? echo $number; ?>" class="form1">
rows beginning with number
<input type="text" name="lower" size="3" value="<? echo $lower; ?>" class="form1">
in
<select name="desc" class="form1">
<option value="&nbsp;" <? if ($desc!="DESC") echo " SELECTED ";?> >ASCENDING
<option value="DESC" <? if ($desc=="DESC") echo " SELECTED ";?> >DESCENDING
</select>
order.
</form>
</td>
<td>
<form action="<? echo $PHP_SELF;?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Next <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<? echo $PHP_SELF;?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Previous <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<? echo $PHP_SELF;?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $total[0]; ?>">
<input type="hidden" name="lower" size="3" value="0">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Show All" class="button1">
</form>
</td>
</tr>
</table>
     <p>

     <a href="<?php echo $PHP_SELF?>">ADD A comment</a>

     <p>

     <form method="post" name="addform" action="<?php echo $PHP_SELF?>" >

     <?

     if ($commentID)
     {

     // editing so select a record

     $sql = "SELECT * FROM comments WHERE commentID='$commentID'";

     $result = mysql_query($sql);

     $myrow = mysql_fetch_array($result);

     $itemid = $myrow["itemID"];

     $username = $myrow["username"];

     $message = $myrow["message"];

     $keywords = $myrow["keywords"];

     $rank = $myrow["rank"];

     $datetime = $myrow["datetime"];

     $expire = $myrow["expire"];

     $expiredate = $myrow["expiredate"];

     // print the id for editing

     ?>

     <input type=hidden name="commentID" value="<?php echo $commentID; ?>">

     <?
     }

     ?>

     Fill in all fields to add a new comment<br>     *'d fields are optional.<p>
     <table>


    <tr><td>
     <font class="text3">

     <a href="adminitem.php">Item</a></td>
     <td>
     <select name="itemID" size="1">

     <?
      $sql = "SELECT itemid, artist, title, label, catalog, format FROM items ORDER BY artist";
      $result = mysql_query($sql);

      if ($itemlist=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$itemlist["itemid"]."\" ";
       if ($itemlist["itemid"]==$itemID)
        {echo "selected";};
       echo ">".$itemlist["artist"]." - ".$itemlist["title"]." - ".$itemlist["label"]." ".$itemlist["catalog"]." -
".$itemlist["format"];
      } while ($itemlist=mysql_fetch_array($result));
      };
     ?>
     </select>
     </td></tr>


    <tr><td>
     <font class="text3">

     <a href="adminusers.php">Username</a></td>
     <td>
     <select name="username" size="1">

     <?
      $sql = "SELECT username FROM users ORDER BY username";
      $result = mysql_query($sql);
	if (!$_SESSION["username"]) {$username="cutups";} else {$username=$_SESSION["username"];};

      if ($itemlist=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$itemlist["username"]."\" ";
       if ($itemlist["username"]==$username)
        {echo "selected";};
       echo ">".$itemlist["username"];
      } while ($itemlist=mysql_fetch_array($result));
      };
     ?>
     </select>
     </td></tr>

     <tr><td>
     <font class="text3">
     Keywords</td>
     <td>
     <input type="Text" name="keywords" value="<? echo $myrow["keywords"] ?>">
     </td>
     </tr>


     <tr><td>
     <font class=text3>
     Message</td>
     <td>
     <textarea name="message" rows="7" cols="40" wrap="virtual"><? echo $myrow["message"] ?></textarea>
     </td>
     </tr>


     <tr><td>
     <font class=text3>
     Rank </td>

     <td>
     <select name="rank" size="1">
     <option value="01" <? if ($rank=="01") echo "selected"; ?>>01
     <option value="02" <? if ($rank=="02") echo "selected"; ?>>02
     <option value="03" <? if ($rank=="03") echo "selected"; ?>>03
     <option value="04" <? if ($rank=="04") echo "selected"; ?>>04
     <option value="05" <? if ($rank=="05") echo "selected"; ?>>05
     <option value="06" <? if ($rank=="06") echo "selected"; ?>>06
     <option value="07" <? if ($rank=="07") echo "selected"; ?>>07
     <option value="08" <? if ($rank=="08") echo "selected"; ?>>08
     <option value="09" <? if ($rank=="09") echo "selected"; ?>>09
     <option value="10" <? if ($rank=="10") echo "selected"; ?>>10
     </select>
	</td></tr>

     <tr><td>
     <font class=text3>
     Date Entered</td>
        <?
        $month=date("m",strtotime($myrow["datetime"]));
        $day=date("d",strtotime($myrow["datetime"]));
        $year=date("Y",strtotime($myrow["datetime"]));
        ?>


     <td>
     <select name="month" size="1">
     <option value="01" <? if ($month=="01") echo "selected"; ?>>Jan
     <option value="02" <? if ($month=="02") echo "selected"; ?>>Feb
     <option value="03" <? if ($month=="03") echo "selected"; ?>>Mar
     <option value="04" <? if ($month=="04") echo "selected"; ?>>Apr
     <option value="05" <? if ($month=="05") echo "selected"; ?>>May
     <option value="06" <? if ($month=="06") echo "selected"; ?>>Jun
     <option value="07" <? if ($month=="07") echo "selected"; ?>>Jul
     <option value="08" <? if ($month=="08") echo "selected"; ?>>Aug
     <option value="09" <? if ($month=="09") echo "selected"; ?>>Sep
     <option value="10" <? if ($month=="10") echo "selected"; ?>>Oct
     <option value="11" <? if ($month=="11") echo "selected"; ?>>Nov
     <option value="12" <? if ($month=="12") echo "selected"; ?>>Dec
     </select>

     <select name="day" size="1">
     <option value="01" <? if ($day=="01") echo "selected"; ?>>01
     <option value="02" <? if ($day=="02") echo "selected"; ?>>02
     <option value="03" <? if ($day=="03") echo "selected"; ?>>03
     <option value="04" <? if ($day=="04") echo "selected"; ?>>04
     <option value="05" <? if ($day=="05") echo "selected"; ?>>05
     <option value="06" <? if ($day=="06") echo "selected"; ?>>06
     <option value="07" <? if ($day=="07") echo "selected"; ?>>07
     <option value="08" <? if ($day=="08") echo "selected"; ?>>08
     <option value="09" <? if ($day=="09") echo "selected"; ?>>09
     <option value="10" <? if ($day=="10") echo "selected"; ?>>10
     <option value="11" <? if ($day=="11") echo "selected"; ?>>11
     <option value="12" <? if ($day=="12") echo "selected"; ?>>12
     <option value="13" <? if ($day=="13") echo "selected"; ?>>13
     <option value="14" <? if ($day=="14") echo "selected"; ?>>14
     <option value="15" <? if ($day=="15") echo "selected"; ?>>15
    <option value="16" <? if ($day=="16") echo "selected"; ?>>16
     <option value="17" <? if ($day=="17") echo "selected"; ?>>17
     <option value="18" <? if ($day=="18") echo "selected"; ?>>18
     <option value="19" <? if ($day=="19") echo "selected"; ?>>19
     <option value="20" <? if ($day=="20") echo "selected"; ?>>20
     <option value="21" <? if ($day=="21") echo "selected"; ?>>21
     <option value="22" <? if ($day=="22") echo "selected"; ?>>22
     <option value="23" <? if ($day=="23") echo "selected"; ?>>23
     <option value="24" <? if ($day=="24") echo "selected"; ?>>24
     <option value="25" <? if ($day=="25") echo "selected"; ?>>25
     <option value="26" <? if ($day=="26") echo "selected"; ?>>26
     <option value="27" <? if ($day=="27") echo "selected"; ?>>27
     <option value="28" <? if ($day=="28") echo "selected"; ?>>28
     <option value="29" <? if ($day=="29") echo "selected"; ?>>29
     <option value="30" <? if ($day=="30") echo "selected"; ?>>30
     <option value="31" <? if ($day=="31") echo "selected"; ?>>31
     </select>


     <select name="year" size="1">
     <? for ($i=1990;$i<=date("Y",time());$i++)
     { echo "<option ";
       if ($year==$i) echo "selected";
       echo ">".$i;
     };
     ?>
     </select>
     </td></tr>


     <tr><td>
     <font class="text3">
     Expire Date
     <input type="checkbox" value="1" name="expire" <? if ($expire==1) echo "CHECKED"; ?> >
     </td>
        <?
        $pmonth=date("m",strtotime($myrow["expiredate"]));
        $pday=date("d",strtotime($myrow["expiredate"]));
        $pyear=date("Y",strtotime($myrow["expiredate"]));
        ?>


     <td>
     <select name="pmonth" size="1">
     <option value="01" <? if ($pmonth=="01") echo "selected"; ?>>Jan
     <option value="02" <? if ($pmonth=="02") echo "selected"; ?>>Feb
     <option value="03" <? if ($pmonth=="03") echo "selected"; ?>>Mar
     <option value="04" <? if ($pmonth=="04") echo "selected"; ?>>Apr
     <option value="05" <? if ($pmonth=="05") echo "selected"; ?>>May
     <option value="06" <? if ($pmonth=="06") echo "selected"; ?>>Jun
     <option value="07" <? if ($pmonth=="07") echo "selected"; ?>>Jul
     <option value="08" <? if ($pmonth=="08") echo "selected"; ?>>Aug
     <option value="09" <? if ($pmonth=="09") echo "selected"; ?>>Sep
     <option value="10" <? if ($pmonth=="10") echo "selected"; ?>>Oct
     <option value="11" <? if ($pmonth=="11") echo "selected"; ?>>Nov
     <option value="12" <? if ($pmonth=="12") echo "selected"; ?>>Dec
     </select>

    <select name="pday" size="1">
     <option value="01" <? if ($pday=="01") echo "selected"; ?>>01
     <option value="02" <? if ($pday=="02") echo "selected"; ?>>02
     <option value="03" <? if ($pday=="03") echo "selected"; ?>>03
     <option value="04" <? if ($pday=="04") echo "selected"; ?>>04
     <option value="05" <? if ($pday=="05") echo "selected"; ?>>05
     <option value="06" <? if ($pday=="06") echo "selected"; ?>>06
     <option value="07" <? if ($pday=="07") echo "selected"; ?>>07
     <option value="08" <? if ($pday=="08") echo "selected"; ?>>08
     <option value="09" <? if ($pday=="09") echo "selected"; ?>>09
     <option value="10" <? if ($pday=="10") echo "selected"; ?>>10
     <option value="11" <? if ($pday=="11") echo "selected"; ?>>11
     <option value="12" <? if ($pday=="12") echo "selected"; ?>>12
     <option value="13" <? if ($pday=="13") echo "selected"; ?>>13
     <option value="14" <? if ($pday=="14") echo "selected"; ?>>14
     <option value="15" <? if ($pday=="15") echo "selected"; ?>>15
     <option value="16" <? if ($pday=="16") echo "selected"; ?>>16
     <option value="17" <? if ($pday=="17") echo "selected"; ?>>17
     <option value="18" <? if ($pday=="18") echo "selected"; ?>>18
     <option value="19" <? if ($pday=="19") echo "selected"; ?>>19
     <option value="20" <? if ($pday=="20") echo "selected"; ?>>20
     <option value="21" <? if ($pday=="21") echo "selected"; ?>>21
     <option value="22" <? if ($pday=="22") echo "selected"; ?>>22
     <option value="23" <? if ($pday=="23") echo "selected"; ?>>23
     <option value="24" <? if ($pday=="24") echo "selected"; ?>>24
     <option value="25" <? if ($pday=="25") echo "selected"; ?>>25
     <option value="26" <? if ($pday=="26") echo "selected"; ?>>26
     <option value="27" <? if ($pday=="27") echo "selected"; ?>>27
     <option value="28" <? if ($pday=="28") echo "selected"; ?>>28
     <option value="29" <? if ($pday=="29") echo "selected"; ?>>29
     <option value="30" <? if ($pday=="30") echo "selected"; ?>>30
     <option value="31" <? if ($pday=="31") echo "selected"; ?>>31
     </select>


     <select name="pyear" size="1">
     <? for ($i=1990;$i<=date("Y",time());$i++)
     { echo "<option ";
       if ($pyear==$i) echo "selected";
       echo ">".$i;
     };
     ?>
     </select>
     </td></tr>


     <tr><td>
        <input type="hidden" name="sort" value="<? echo $sort ?>">
        <input type="hidden" name="lower" value="<? echo $lower ?>">
        <input type="hidden" name="number" value="<? echo $number ?>">
        <input type="hidden" name="desc" value="<? echo $desc ?>">
        <input type="Submit" name="submit" value="Enter information" class="button1"></td></tr>

     </table>
     </form>
     <?
     }

?>
<P>

<?

      echo "<P><a href=\"adminitem.php?sort=$sort&lower=$lower&number=$number&desc=$desc\">back to item admin</a>";

 ?>


</body>

</html>
