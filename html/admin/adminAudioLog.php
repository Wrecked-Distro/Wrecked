<?php
// adminsearch.php
// tool for monitoring what searches users are making


  $title = "Admin Audio Logs";
  if (!$sort) {$sort = "audioLogID";}
  if (!$lower) {$lower = 0;};
  if (!$number) {$number = 20;};

  echo "<b>$title</b>";
  echo "<p>";

   if ($submit)
   {
    // here if no ID then adding, else we're editing

     if ($audioLogID)
     {
      $sql = "UPDATE audioLog SET audioLogTime='$audioLogTime', audioLogUsername='$audioLogUsername', audioLogIP='$audioLogIP',audioLogItemID='$audioLogItemID',audioLogURL='$audioLogURL' WHERE audioLogID='$audioLogID'";
      echo "Update of ".$audioLogID."\n";
     } else {
      $sql = "INSERT INTO audioLog (audioLogID, audioLogTime,audioLogUsername, audioLogIP, audioLogItemID, audioLogURL) VALUES (0,NOW(),'$audioLogUsername','$audioLogIP','$audioLogItemID','$audioLogURL')";
      echo "inserting ".$audioLogURl."\n";
     };

     // run SQL against the DB
      $result = mysql_query($sql);

      echo "Record updated.<p>";
    
      echo "<a href=\"$PHP_SELF?audioLogID=$audioLogID&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">more searches</a>";
     } elseif ($delete) {
      
       // delete a record

       $sql = "DELETE FROM audioLog WHERE audioLogID='$audioLogID'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";
      echo "<a href=\"$PHP_SELF?sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">back</a>";
      
     } else {

      // this part happens if we don't press submit

     if (!$audioLogID) {
    // print the list if there is not editing

      if ($keyword)
      {
        $sql = "SELECT *, DATE_FORMAT(audioLogTime,'%c/%e/%y at %h:%i:%s %p') AS audioLogTimeF FROM audioLog WHERE  audioLogItemID = '$audioLogItemID' ORDER BY $sort DESC LIMIT $lower,$number ";
      };

      if ($username)
      {
       $sql = "SELECT *, DATE_FORMAT(audioLogTime,'%c/%e/%y at %h:%i:%s %p') AS audioLogTimeF FROM audioLog WHERE audioLogUsername = '$username' ORDER BY $sort DESC LIMIT $lower,$number "; 
      };

      if (!$username AND !$audioLogItemID)
      {
        $sql = "SELECT *, DATE_FORMAT(audioLogTime,'%c/%e/%y at %h:%i:%s %p') AS audioLogTimeF FROM audioLog ORDER BY $sort DESC LIMIT $lower,$number ";
      };

        $result = mysql_query($sql);
	      echo "<div id='query'>Query: ".$sql."</div>";

     if ($result)
     {
      
       echo "<table>\n";
     
       echo "<tr><td class=\"title1\" colspan=6><b>Searches</b></td></tr>\n";
       echo "<tr class=\"title2\">
             <td><a href=\"$PHP_SELF?sort=audioLogID&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">audioLogID</a></td>
             <td><a href=\"$PHP_SELF?sort=audioLogTimeF&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">audioLogTimeF</a></td>
             <td><a href=\"$PHP_SELF?sort=audioLogUsername&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Username</a></td>
             <td><a href=\"$PHP_SELF?sort=audioLogIP&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">IP</a></td>
             <td><a href=\"$PHP_SELF?sort=audioLogItemID&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">ItemID</a></td>
             <td><a href=\"$PHP_SELF?sort=audioLogURL&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">URL</a></td>       
             </tr>\n";
      
       while ($myrow = mysql_fetch_array($result))
       {

        printf("<tr><td>%s</td> <td>%s</a></td> <td><a href=\"adminSearch.php?username=%s\">%s</a></td> <td>%s</td> <td>%s</td><td><a href=\"adminSearch.php?keyword=%s\">%s</a></td>",
        $myrow["audioLogID"],$myrow["audioLogTimeF"],$myrow["audioLogUsername"], $myrow["audioLogUsername"],$myrow["audioLogIP"],$myrow["audioLogItemID"],$myrow["audioLogURL"],$myrow["audioLogURL"]);
    
        printf("<td><a href=\"%s?audioLogID=%s&amp;delete=yes&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">(DELETE)</a></td><td><a href=\"%s?audioLogID=%s&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">(EDIT)</a></td></tr>",
        $PHP_SELF,$myrow["audioLogID"],$PHP_SELF,$myrow["audioLogID"]);

       };
       echo "</table>\n";
      }
       
     echo "<p>";
       
     }
             
    ?>

<table>
<tr><td>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="sort" value="<? echo $sort;?>">
<input type="submit" name="show" value="Show:" class="button1">
<input type="text" name="number"  value="<? echo $number; ?>" class="form1">
rows beginning with number
<input type="text" name="lower"  value="<? echo $lower; ?>" class="form1">
in
<select name="desc" class="form1">  
<option value="&amp;nbsp;" <? if ($desc!="DESC") echo " SELECTED ";?> >ASCENDING
<option value="DESC" <? if ($desc=="DESC") echo " SELECTED ";?> >DESCENDING
</select>
order.
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="number"  value="<? echo $number; ?>">
<input type="hidden" name="lower"  value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Previous <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="number"  value="<? echo $number; ?>">
<input type="hidden" name="lower"  value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Next <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="number"  value="<? echo $total[0]; ?>">
<input type="hidden" name="lower"  value="0">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Show All" class="button1">
</form>
</td>
</tr>
</table>
     <p>
             
     <a href="<?php echo $PHP_SELF?>">ADD A log entry</a>
             
     <p>
             
     <form method="post" name="addform" action="<?php echo $_SERVER['PHP_SELF']?>" >
       
     <?
      
     if ($audioLogID)
     {  
        
     // editing so select a record
        
     $sql = "SELECT * FROM audioLog WHERE audioLogID='$audioLogID'";
    
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
     
     <a href="adminitem.php">Item</a></td>
     <td>
     <select name="itemID" size="1">
     
     <?php
      $sql = "SELECT itemid, artist, title, label, catalog, format FROM items ORDER BY artist";
      $result = mysql_query($sql);
     
      if ($itemlist=mysql_fetch_array($result))
      {   
      do  
      {
       echo "<option value=\"".$itemlist["itemid"]."\" ";
       if ($itemlist["itemid"] == $itemID) {
        echo "selected";
       };
       echo ">".htmlentities($itemlist["artist"])." - ".htmlentities($itemlist["title"])." - ".htmlentities($itemlist["label"])." ".$itemlist["catalog"]." - ".$itemlist["format"];
      } while ($itemlist=mysql_fetch_array($result));
      };  
     ?>   
     </select>
     </td></tr>


    <tr><td>
     

     <a href="adminusers.php">Username</a></td>
     <td>
     <select name="username" size="1">
     
     <?php
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
     
     Keywords</td>
     <td>
     <input type="Text" name="keywords" value="<? echo $myrow["keywords"] ?>">
     </td>
     </tr>


     <tr><td>
     Message</td>
     <td>
     <textarea name="message" rows="7" cols="40" ><? echo $myrow["message"] ?></textarea>
     </td>
     </tr>     

     
     <tr><td>
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


     <tr><td colspan='2'>
        <input type="hidden" name="sort" value="<? echo $sort ?>">
        <input type="hidden" name="lower" value="<? echo $lower ?>">
        <input type="hidden" name="number" value="<? echo $number ?>">
        <input type="hidden" name="desc" value="<? echo $desc ?>">
        <input type="Submit" name="submit" value="Enter information" class="button1">
     </td></tr>
     
     </table>
     </form>  
     <?
     }
     
?>
<P>
     
<?php echo "<P><a href=\"adminitem.php?sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">back to item admin</a>"; ?>
