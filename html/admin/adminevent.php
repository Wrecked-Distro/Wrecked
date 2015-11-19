<body name="body">

<b>EVENT admin</b>

<p>

<?php

   $sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : "eventID";

   dbConnect("db9372_distro");

   $result = mysql_query("SELECT COUNT(eventID) FROM events");
   $total = mysql_fetch_array($result);

   if ($lower<0) {$lower=$total[0];};
   if ($lower>$total[0]) {$lower=0;};

   if ($_REQUEST['submit'])
   {
    // here if no ID then adding, else we're editing

     if ($_REQUEST['eventID'])
     {
	$timetemp =  time();
      $sql = "UPDATE events SET name='".$_REQUEST['name']."', venue='".$_REQUEST['venue']."', city='".$_REQUEST['city']."',
ages='".$_REQUEST['ages']."', cost = '".$_REQUEST['cost']."', date='".$_REQUEST['year'].$_REQUEST['month'].$_REQUEST['day']."',
start='".$_REQUEST['start']."', length='".$_REQUEST['length']."',details='".$_REQUEST['details']."', 
brief='".$_REQUEST['brief']."', link='".$_REQUEST['link']."', contact='".$_REQUEST['contact']."', 
poster='".$_REQUEST['poster']."', owner='".$_REQUEST['owner']."' WHERE eventID='".$_REQUEST['eventID']."'";
      echo "Update of ".$_REQUEST['itemid']."\n";

     }
     else
     {

  	$timetemp = time();
  	$sql = "INSERT INTO events (eventID, name, venue, city, ages, cost, date ,start, length, details,brief,link,contact,owner, 
poster) VALUES  (0, '".$_REQUEST['name']."', '".$_REQUEST['venue']."', '".$_REQUEST['city']."', '".$_REQUEST['ages']."', '".$_REQUEST['cost']."', '".$_REQUEST['year'].$_REQUEST['month'].$_REQUEST['day']."',
 '".$_REQUEST['start']."','".$_REQUEST['length']."', 
'".$_REQUEST['details']."','".$_REQUEST['brief']."', '".$_REQUEST['link']."', '".$_REQUEST['contact']."', '".$_REQUEST['owner']."', '".$_REQUEST['poster']."')";

       echo "inserting ".$_REQUEST['name']."\n";

     };

     // run SQL against the DB

      $result = mysql_query($sql);

      echo "Record updated.<p>";
    
      echo "<a href=\"$PHP_SELF?module=$module&sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";  

     } elseif ($_REQUEST['delete']) {
      
       // delete a record

       $sql = "DELETE FROM events WHERE eventID='".$_REQUEST['eventID']."'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";
      echo "<a href=\"$PHP_SELF?module=$module&sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";
      
     } else {


      // this part happens if we don't press submit

     if (!$_REQUEST['eventID']) {

    // print the list if there is not editing

     $sql = "SELECT *,DATE_FORMAT(date,'%m/%d/%y') AS date, DATE_FORMAT(timestamp,'%m/%d/%y') AS timestamp 
FROM events ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower, $number";
	//echo $sql;
	
     $result = mysql_query($sql);

     if ($myrow = mysql_fetch_array($result))
     {
      
       echo "<table border=0 cellspacing=0 cellpadding=3>\n";
     
       echo "<tr><td class=\"title1\" colspan=15><b>Current Events</b></font></td></tr>\n";
       echo "<tr class=\"title2\">
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=eventID&lower=$lower&number=$number&desc=$desc\">EventID</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=name&lower=$lower&number=$number&desc=$desc\">Name</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=venue&lower=$lower&number=$number&desc=$desc\">Venue</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=city&lower=$lower&number=$number&desc=$desc\">City</a></td> 
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=ages&lower=$lower&number=$number&desc=$desc\">Ages</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=cost&lower=$lower&number=$number&desc=$desc\">Cost</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=date&lower=$lower&number=$number&desc=$desc\">Date</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=start&lower=$lower&number=$number&desc=$desc\">Time</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=length&lower=$lower&number=$number&desc=$desc\">Length</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=brief&lower=$lower&number=$number&desc=$desc\">Brief</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=link&lower=$lower&number=$number&desc=$desc\">Link</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=contact&lower=$lower&number=$number&desc=$desc\">Contact</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=poster&lower=$lower&number=$number&desc=$desc\">Poster</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=timestamp&lower=$lower&number=$number&desc=$desc\">Timestamp</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=owner&lower=$lower&number=$number&desc=$desc\">Owner</a></td>
       
             </tr>\n";
      
       do
       {
        printf("<tr><td>%s</td> <td>%s</td> <td>%s</td><td>%s</td><td>%s</td> <td>$w%s</td> 
 <td>%s</td> <td>%s</td> <td>%s</td> <td>%s</td> <td>%s</td> <td>%s</td>",
        $myrow["eventID"], $myrow["name"], $myrow["venue"], $myrow["city"], $myrow["ages"], $myrow["cost"], 
$myrow["date"], $myrow["start"],$myrow["length"], $myrow["brief"],  $myrow["link"], $myrow["contact"]); 

echo "<td>";

      $tempuser=$myrow["poster"]; 	
      $sql2 = "SELECT userid, username FROM users WHERE userid='$tempuser'";
      $result2 = mysql_fetch_array(mysql_query($sql2));

	echo $result2["username"];
	
printf("</td><td>%s</td><td>%s</td>", $myrow["timestamp"], $myrow["owner"]);

        printf("<td> <a href=\"%s?module=$module&eventID=%s&delete=yes&sort=$sort&lower=$lower&number=$number&desc=$desc\">(DELETE)</a>
</td>
<td><a href=\"%s?module=$module&eventID=%s&sort=$sort&lower=$lower&number=$number&desc=$desc\">(EDIT)</a></td>
</tr>", $PHPSELF, $myrow["eventID"],$PHP_SELF,$myrow["eventID"]);

       } while ($myrow=mysql_fetch_array($result));
       echo "</table>\n";
      }
       
     echo "<p>";
       
     }
             
    ?>

<table>
<tr><td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="sort" value="<? echo $sort;?>">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="submit" name="show" value="Show&nbsp;:" class=button1>
<input type="text" name="number" size="3" value="<? echo $number; ?>">
rows beginning with number
<input type="text" name="lower" size="3" value="<? echo $lower; ?>">
in
<select name="desc">  
<option value="&nbsp;" <? if ($desc!="DESC") echo " SELECTED ";?> >ASCENDING
<option value="DESC" <? if ($desc=="DESC") echo " SELECTED ";?> >DESCENDING
</select>
order.
</form>
</td>
<td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="submit" name="show" value="Previous <? echo $number;?>" class=button1>
</form>
</td>
<td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="submit" name="show" value="Next <? echo $number;?>" class=button1>
</form>
</td>

<td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $total[0]; ?>">
<input type="hidden" name="lower" size="3" value="0">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="submit" name="show" value="Show All" class=button1>
</form>
</td>
</tr>
</table>
     <p>
             
     <b>ADD an event</b>
             
     <p>
             
     <form method="post" name="addform" action="<?php echo $PHP_SELF?>" >
       
     <?
      
     if ($_REQUEST['eventID'])
     {  
        
     // editing so select a record
        
     $sql = "SELECT * FROM events WHERE eventID='".$_REQUEST['eventID']."'";
    
     $result = mysql_query($sql);
        
     $myrow = mysql_fetch_array($result);
       
     $name = $myrow["name"];
       
     $venue = $myrow["venue"];
     
     $city = $myrow["city"];
      
     $ages = $myrow["ages"];

     $cost = $myrow["cost"];
        
     $date = $myrow["date"];
     
     $start = $myrow["start"];
        
     $length = $myrow["length"];
     
     $details = $myrow["details"];
       
     $brief = $myrow["brief"];

     $link = $myrow["link"];

     $contact = $myrow["contact"];

     $poster = $myrow["poster"];

     $timestamp = $myrow["timestamp"];

     $owner = $myrow["owner"];
     
      
     // print the id for editing
     
     ?>
     
     <input type='hidden' name="eventID" value="<?php echo $_REQUEST['eventID']; ?>">
     
     <?
     }

     ?>

     Fill in all fields to add a new event<br>     *'d fields are optional.<p>
     <table>
     
     <tr><td>
        <font class=text3>
        Name
        </td><td><input type="Text" name="name" value="<? echo $myrow["name"] ?>">
     </td></tr>
       
     
     <tr><td>  
     <font class=text3>
     Venue   
     </td><td><input type="Text" name="venue" value="<? echo $myrow["venue"] ?>"></td>
     </tr>
     
     <tr><td>
     <font class=text3> 
     City
     </td><td><input type="Text" name="city" value="<? echo $myrow["city"] ?>"></td>
     </tr>

     <tr><td>
     <font class=text3> 
     Ages
     </td><td>
	<select name="ages" size="1">
	<option value="All Ages" <? if ($ages=="All Ages") echo "selected"; ?> >All Ages
	<option value="18+" <? if ($ages == "18+") echo "selected"; ?> >18+
	<option value="21+" <? if ($ages == "21+") echo "selected"; ?> >21+
	</select>
	</td>
     </tr>

     <tr><td>
     <font class=text3>
     Cost</td>
     <td>
     <input type="Text" name="cost" size=2 value="<? echo $myrow["cost"] ?>">
     </td>
     </tr>

     <tr><td>
     <font class=text3>
     Date</td>
	<? 
	$month=date("m",strtotime($myrow["date"])); 
	$day=date("d",strtotime($myrow["date"])); 
	$year=date("Y",strtotime($myrow["date"])); 
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
     <? for ($i=1990;$i<=date("Y",time())+1;$i++)
     { echo "<option ";
       if ($year==$i) echo "selected";
       echo ">".$i;
     };
     ?>
     </select>
     </td></tr>

     <tr><td>
     <font class=text3> 
     Start
     </td><td>
	<select name="start" size="1">
	<option value="1" <? if ($start=="1") echo "selected"; ?> >1 PM
	<option value="2" <? if ($start=="2") echo "selected"; ?> >2 PM
	<option value="3" <? if ($start=="3") echo "selected"; ?> >3 PM
	<option value="4" <? if ($start=="4") echo "selected"; ?> >4 PM
	<option value="5" <? if ($start=="5") echo "selected"; ?> >5 PM
	<option value="6" <? if ($start=="6") echo "selected"; ?> >6 PM
	<option value="7" <? if ($start=="7") echo "selected"; ?> >7 PM
	<option value="8" <? if ($start=="8") echo "selected"; ?> >8 PM
	<option value="9" <? if ($start=="9") echo "selected"; ?> >9 PM
	<option value="10" <? if ($start=="10") echo "selected"; ?> >10 PM
	<option value="11" <? if ($start=="11") echo "selected"; ?> >11 PM
	<option value="12"<? if ($start=="12") echo "selected"; ?> >12 AM
	</select>
	</td>
     </tr>

     <tr><td>
     <font class=text3> 
     Length
     </td><td>
	<select name="length" size="1">
	<option value="0" <? if ($length=="0") echo "selected"; ?> >Unknown
	<option value="1" <? if ($length=="1") echo "selected"; ?> >1
	<option value="2" <? if ($length=="2") echo "selected"; ?> >2
	<option value="3" <? if ($length=="3") echo "selected"; ?> >3
	<option value="4" <? if ($length=="4") echo "selected"; ?> >4
	<option value="5" <? if ($length=="5") echo "selected"; ?> >5
	<option value="6" <? if ($length=="6") echo "selected"; ?> >6
	<option value="7"<? if ($length=="7") echo "selected"; ?> >7
	<option value="8"<? if ($length=="8") echo "selected"; ?> >8
	<option value="9"<? if ($length=="9") echo "selected"; ?> >9
	<option value="10"<? if ($length=="10") echo "selected"; ?> >10
	<option value="11"<? if ($length=="11") echo "selected"; ?> >11
	<option value="12"<? if ($length=="12") echo "selected"; ?> >12
	</select>
	</td>
     </tr>

     
     <tr><td>
     <font class=text3>
     Brief</td>
     <td>
     <textarea name="brief" rows="7" cols="40" wrap="virtual"><? echo $myrow["brief"] ?></textarea>
     </td>
     </tr>

     <tr><td>
     <font class=text3>
     Details</td>
     <td>
     <textarea name="details" rows="7" cols="40" wrap="virtual"><? echo $myrow["details"] ?></textarea>
     </td>
     </tr>


     <tr><td>
     <font class=text3>
     Link</td>
     <td>
     <input type="Text" name="link" value="<? echo $myrow["link"] ?>">
     </td>
     </tr>

     <tr><td>
     <font class=text3>
     Contact</td>
     <td>
     <input type="Text" name="contact" value="<? echo $myrow["contact"] ?>">
     </td>
     </tr>

     <tr><td>
     <font class=text3>

     <a href="adminusers.php">poster</a></td>
     <td>
     <select name="poster" size="1">
     
     <?
      $sql = "SELECT userid, username FROM users";
      $result = mysql_query($sql);  
     
      if ($userlist=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$userlist["userid"]."\" ";
       if ($userlist["userid"]==$myrow["poster"]) 
	{echo "selected";};
       echo ">".$userlist["username"];
      } while ($userlist=mysql_fetch_array($result));
      };
     ?>
     </select>
     </td></tr>

     <tr><td>
	<font class=text3>
	owner</a></td>
	<td> <select name="owner" size=1>
	<option value="0" <? if ($myrow["owner"]==0) {echo "selected";}?>>None 
	<option value="1" <? if ($myrow["owner"]==1) {echo "selected";}?>>Cutups 
	</select>
	</td></tr>     
     <tr><td>
        <input type="hidden" name="module" value="<? echo $module ?>">
        <input type="hidden" name="sort" value="<? echo $sort ?>">
        <input type="hidden" name="lower" value="<? echo $lower ?>">
        <input type="hidden" name="number" value="<? echo $number ?>">
        <input type="hidden" name="desc" value="<? echo $desc ?>">
        <input type="Submit" name="submit" value="Enter information" class='button1'></td></tr>
     
     </table>
     </form>       

<?
     };

     
?>
<P>
     
     
     
</body>
     
</html>
