<?php
 /**
  * adminnews.php
  * geoff maddock updated january 2005
  * designed to update the news page for wrecked-distro, and display new shipments of records
  * 
  * CHANGING TO PDO 3.17.15
  **/
?>
<?php
// PDO

$statement = $pdo->query("SELECT username from users");

foreach ($statement as $row)
{
    //echo $row['username'];
};?>

<b>NEWS admin</b>

<p>

<?php
    // REQUEST
   $sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : "newsid";
   $newsid = isset($_REQUEST['newsid']) ? $_REQUEST['newsid'] : null;

   // GET COUNT OF EXISTING 
   $result = mysql_query("SELECT COUNT(newsid) FROM news");
   $total = mysql_fetch_array($result);

   if ($lower < 0) {$lower = $total[0];};
   if ($lower > $total[0]) { $lower=0; };

   if ($_REQUEST['submit'])
   {
    // here if no ID then adding, else we're editing

     if ($_REQUEST['newsid'])
     {
        // PDO
        $statement = $pdo->prepare("UPDATE news SET date=?, text=?, showstock=?, start=? WHERE newsid=?");
        $statement->execute(array($_REQUEST['dyear'].$_REQUEST['dmonth'].$_REQUEST['dday'],$_REQUEST['text'],$_REQUEST['showstock'], $_REQUEST['year'].$_REQUEST['month'].$_REQUEST['day'],$_REQUEST['newsid']));

        echo "Update of ".$newsid."\n";
     } else  {
        $statement = $pdo->prepare("INSERT INTO news (newsid, date, text, showstock, start)  VALUES (0, ?, ?, ?, ?)");
        $statement->execute(array($_REQUEST['dyear'].$_REQUEST['dmonth'].$_REQUEST['dday'],$_REQUEST['text'],$_REQUEST['showstock'], $_REQUEST['year'].$_REQUEST['month'].$_REQUEST['day']));

        echo "Inserting news\n";
     };

     // run SQL against the DB

      $result = mysql_query($sql);

      echo "Record updated.<p>";
    
      echo "<a href=\"$PHP_SELF?module=$module&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">back</a>";  

     } elseif ($_REQUEST['delete']) {
      
        // delete a record
        $sql = "DELETE FROM news WHERE newsid='".$_REQUEST['newsid']."'";
        $result = mysql_query($sql);
        echo "$sql Record deleted!<p>";
        echo "<a href=\"$PHP_SELF?module=$module&sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">back</a>";
      
     } else {

      // this part happens if we don't press submit

     if (!$_REQUEST['newsid']) {
    // print the list if there is not editing

    $sql = "SELECT *, DATE_FORMAT(date,'%m/%d/%y') AS date, DATE_FORMAT(start,'%m/%d/%y') AS start FROM news ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower, $number";

     if ($result = mysql_query($sql))
     {
      
       echo "<table>\n";
       echo "<tr><td class=\"title1\" colspan='8'><b>Current News</b></td></tr>\n";
       echo "<tr class=\"title2\">
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=newsid&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">NewsID</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=date&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Date</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=text&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Text</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=startdate&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Start Date</a></td> 
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=showstock&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Show Stock</a></td>
             <td colspan='3'></td>
             </tr>\n";

      while ($myrow = mysql_fetch_array($result))  {
        printf("<tr><td>%s</td> <td>%s</td> <td>%s</td>",$myrow["newsid"],$myrow["date"], $myrow["text"]);
    
        if ($myrow["showstock"] == 1) {echo "<td>".$myrow["start"]."</td><td>Yes</td>";} else {echo "<td></td><td>No</td>";};   
            
        echo "<td>".htmlspecialchars($myrow["description"])."</td>";
     
        printf("<td><a href=\"%s?module=$module&amp;newsid=%s&amp;delete=yes&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\" onclick=\"return confirm('Are you sure ?')\">(DELETE)</a></td><td><a href=\"%s?module=$module&amp;newsid=%s&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">(EDIT)</a></td></tr>",
        $PHP_SELF,$myrow["newsid"],$PHP_SELF,$myrow["newsid"]);
   
       };
      
       echo "</table>\n";
      }
       
     echo "<p>";
       
     }
             
    ?>

<table>
<tr><td>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="sort" value="<? echo $sort;?>">
<input type="submit" name="show" value="Show&amp;nbsp;:" class="button1">
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
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="number"  value="<? echo $number; ?>">
<input type="hidden" name="lower"  value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Previous <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="number"  value="<? echo $number; ?>">
<input type="hidden" name="lower"  value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Next <? echo $number;?>" class="button1">
</form>
</td>

<td>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="module" value="<? echo $module;?>">
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
             
     <a href="<?php echo $PHP_SELF?>">ADD news</a>
             
     <p>
             
     <form method="post" name="addform" action="<?php echo $_SERVER['PHP_SELF']?>" >
       
     <?
      
     if ($newsid)
     {  
        
     // editing so select a record
        
     $sql = "SELECT * FROM news WHERE newsid='".$_REQUEST['newsid']."'";
    
     $result = mysql_query($sql);
        
     $myrow = mysql_fetch_array($result);
       
     $date = $myrow["date"];
       
     $text = $myrow["text"];
     
     $showstock = $myrow["showstock"];
      
     $start = $myrow["start"];

      
     // print the id for editing
     
     ?>
     
     <input type=hidden name="newsid" value="<?php echo $_REQUEST['newsid']; ?>">
     
     <?
     }

     ?>

     Fill in all fields to add a new item<br>     *'d fields are optional.<p>
     <table>

     <tr><td>
     
     Date</td>
	<? 
	$dmonth=date("m",strtotime($myrow["date"])); 
	$dday=date("d",strtotime($myrow["date"])); 
	$dyear=date("Y",strtotime($myrow["date"])); 
	?>
  

     <td> 
     <select name="dmonth" size="1">
     <option value="01" <? if ($dmonth=="01") echo "selected"; ?>>Jan 
     <option value="02" <? if ($dmonth=="02") echo "selected"; ?>>Feb 
     <option value="03" <? if ($dmonth=="03") echo "selected"; ?>>Mar 
     <option value="04" <? if ($dmonth=="04") echo "selected"; ?>>Apr 
     <option value="05" <? if ($dmonth=="05") echo "selected"; ?>>May 
     <option value="06" <? if ($dmonth=="06") echo "selected"; ?>>Jun 
     <option value="07" <? if ($dmonth=="07") echo "selected"; ?>>Jul 
     <option value="08" <? if ($dmonth=="08") echo "selected"; ?>>Aug 
     <option value="09" <? if ($dmonth=="09") echo "selected"; ?>>Sep 
     <option value="10" <? if ($dmonth=="10") echo "selected"; ?>>Oct 
     <option value="11" <? if ($dmonth=="11") echo "selected"; ?>>Nov 
     <option value="12" <? if ($dmonth=="12") echo "selected"; ?>>Dec 
     </select>

     <select name="dday" size="1">
     <option value="01" <? if ($dday=="01") echo "selected"; ?>>01
     <option value="02" <? if ($dday=="02") echo "selected"; ?>>02
     <option value="03" <? if ($dday=="03") echo "selected"; ?>>03
     <option value="04" <? if ($dday=="04") echo "selected"; ?>>04
     <option value="05" <? if ($dday=="05") echo "selected"; ?>>05
     <option value="06" <? if ($dday=="06") echo "selected"; ?>>06
     <option value="07" <? if ($dday=="07") echo "selected"; ?>>07
     <option value="08" <? if ($dday=="08") echo "selected"; ?>>08
     <option value="09" <? if ($dday=="09") echo "selected"; ?>>09
     <option value="10" <? if ($dday=="10") echo "selected"; ?>>10
     <option value="11" <? if ($dday=="11") echo "selected"; ?>>11
     <option value="12" <? if ($dday=="12") echo "selected"; ?>>12
     <option value="13" <? if ($dday=="13") echo "selected"; ?>>13
     <option value="14" <? if ($dday=="14") echo "selected"; ?>>14
     <option value="15" <? if ($dday=="15") echo "selected"; ?>>15
     <option value="16" <? if ($dday=="16") echo "selected"; ?>>16
     <option value="17" <? if ($dday=="17") echo "selected"; ?>>17
     <option value="18" <? if ($dday=="18") echo "selected"; ?>>18
     <option value="19" <? if ($dday=="19") echo "selected"; ?>>19
     <option value="20" <? if ($dday=="20") echo "selected"; ?>>20
     <option value="21" <? if ($dday=="21") echo "selected"; ?>>21
     <option value="22" <? if ($dday=="22") echo "selected"; ?>>22
     <option value="23" <? if ($dday=="23") echo "selected"; ?>>23
     <option value="24" <? if ($dday=="24") echo "selected"; ?>>24
     <option value="25" <? if ($dday=="25") echo "selected"; ?>>25
     <option value="26" <? if ($dday=="26") echo "selected"; ?>>26
     <option value="27" <? if ($dday=="27") echo "selected"; ?>>27
     <option value="28" <? if ($dday=="28") echo "selected"; ?>>28
     <option value="29" <? if ($dday=="29") echo "selected"; ?>>29
     <option value="30" <? if ($dday=="30") echo "selected"; ?>>30
     <option value="31" <? if ($dday=="31") echo "selected"; ?>>31
     </select>


     <select name="dyear" size="1">
     <? for ($i=1990;$i<=date("Y",time());$i++)
     { echo "<option ";
       if ($dyear==$i) echo "selected";
       echo ">".$i;
     };
     ?>
     </select>
     </td></tr>


     <tr><td>
     
     Text</td>
     <td>
     <textarea name="text" rows="7" cols="40" ><? echo $myrow["text"] ?></textarea>
     </td>
     </tr>

     <tr><td>
     
     Show Stock</td>
     
     <td>
     <select name="showstock" size="1">
     <option value="0" <? if ($showstock=="0") echo "selected"; ?>>No
     <option value="1" <? if ($showstock=="1") echo "selected"; ?>>Yes
     </select>
     </td></tr>

     <tr><td>
     
     Start *</td>
	<? 
	$month=date("m",strtotime($myrow["start"])); 
	$day=date("d",strtotime($myrow["start"])); 
	$year=date("Y",strtotime($myrow["start"])); 
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
     
     <tr><td colspan='2'>
        <input type="hidden" name="module" value="<? echo $module;?>">
        <input type="hidden" name="sort" value="<? echo $sort ?>">
        <input type="hidden" name="lower" value="<? echo $lower ?>">
        <input type="hidden" name="number" value="<? echo $number ?>">
        <input type="hidden" name="desc" value="<? echo $desc ?>">
        <input type="Submit" name="submit" value="Enter information" class="button1">
        </td>
     </tr>
     </table>
     </form>  
     <?
     }
     
?>