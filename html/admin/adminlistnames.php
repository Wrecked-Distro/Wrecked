<?php

function showlist($listnamesID)
{

  // select all 
  $result=mysql_query("SELECT * FROM list, items WHERE list.listitemID=items.itemID AND 
  list.listnamesID=$listnamesID");
   
   if ($myrow=mysql_fetch_array($result))
   {
     echo "<table  border=0 cellspacing=0 cellpadding=3>";
     echo "<tr class=title2>";
     echo "<td>Rank</td><td>Item</td><td>Comment</td>";
     echo "</tr>";
     
     do
     {
       echo "<tr>";
       echo "<td>".$myrow["listrank"]."</td>";
       echo "<td>".$myrow["artist"]." - ".$myrow["title"]." - ".$myrow["label"]." ".$myrow["catalog"]."</td>";
       echo "<td>".$myrow["listcomment"]."</td>";
              printf("<td><a
      href=\"%s?listID=%s&delete=yes&sort=$sort&lower=$lower&number=$number&desc=$desc\">(DELETE)</a></td><td><a
      href=\"%s?listID=%s&listnamesID=%s&sort=$sort&lower=$lower&number=$number&desc=$desc\">(EDIT)</a></td></tr>",     
      "adminlist.php",$myrow["listID"],"adminlist.php",$myrow["listID"],$myrow["listnamesID"]);
     } while ($myrow=mysql_fetch_array($result));
   };
};

?>

<b>Listname admin</b>

<p>

<?php

   if (!$lower) { $lower=0; };
   if (!$number) { $number=20; };
   if (!$desc) { $desc="DESC"; };
   if (!$_REQUEST['sort']) { $sort="listnamesID"; };

   $result = mysql_query("SELECT COUNT(listnamesID) FROM listnames");
   $total = mysql_fetch_array($result);

   if ($lower<0) { $lower = $total[0]; };
   if ($lower>$total[0]) { $lower = 0; };

   
   if ($submit)
   {
    // here if no ID then adding, else we're editing

     if ($listnamesID)
     {
      $sql = "UPDATE listnames SET listnamesName='$listnamesName', listnamesText='$listnamesText', listnamesURL='$listnamesURL', listnamesActive='$listnamesActive' WHERE listnamesID='$listnamesID'";
      echo "Update of ".$listnamesID."\n";
     }
     else
     {
       $sql = "INSERT INTO listnames (listnamesID, listnamesName, listnamesText, listnamesURL, listnamesActive) VALUES (0,'$listnamesName','$listnamesText','$listnamesURL','$listnamesActive')";
       echo "inserting ".$listnamesName."\n";
     };
      // run SQL against the DB

      $result = mysql_query($sql);

      echo "Record updated.<p>";
    
      echo "<a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";  

     } elseif ($delete) {

      
       // delete a record

       $sql = "DELETE FROM listnames WHERE listnameID='$listnameID'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";
      echo "<a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a> to list items";
      echo "<a href=\"adminlistnames.php?listnamesID=$listnamesIDsort=$sort&lower=$lower&number=$number&desc=$desc\">back</a> to list names";
      
     } else {

      // this part happens if we don't press submit

     if (!$listnamesID) {
    // print the list if there is not editing

      $sql = "SELECT * FROM listnames ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower, $number";
             echo "<div id='query'>Query: ".$sql."</div>";
     $result = mysql_query($sql);

     if ($myrow = mysql_fetch_array($result))
     {
      
       echo "<table border=0 cellspacing=0 cellpadding=3>\n";
     
       echo "<tr class=\"title1\"><td colspan=5><b>Current List Names</b></td></tr>\n";
       echo "<tr class=\"title2\">
             <td>
<a href=\"$PHP_SELF?sort=listnamesID&lower=$lower&number=$number&desc=$desc\">listnameID</a></td>
             <td>
<a href=\"$PHP_SELF?sort=listnamesName&lower=$lower&number=$number&desc=$desc\">Name</a></td>
             <td>
<a href=\"$PHP_SELF?sort=listnamesText&lower=$lower&number=$number&desc=$desc\">Text</a></td>
             <td>
<a href=\"$PHP_SELF?sort=listnamesURL&lower=$lower&number=$number&desc=$desc\">URL</a></td>
             <td>
<a href=\"$PHP_SELF?sort=listnamesActive&lower=$lower&number=$number&desc=$desc\">Active</a></td>
       
             </tr>\n";
      
       do
       {
        printf("<tr><td>%s</td> <td>%s</td> <td>%s</td><td><a href=\"%s\" target=\"_blank\">%s</a></td><td>%s</td>",
        $myrow["listnamesID"], $myrow["listnamesName"], $myrow["listnamesText"], $myrow["listnamesURL"], 
$myrow["listnamesURL"],$myrow["listnamesActive"]
);
    
        printf("<td><a 
href=\"%s?listnamesID=%s&delete=yes&sort=$sort&lower=$lower&number=$number&desc=$desc\">(DELETE)</a></td><td><a 
href=\"%s?listnamesID=%s&sort=$sort&lower=$lower&number=$number&desc=$desc\">(EDIT)</a></td></tr>",
        $PHP_SELF,$myrow["listnamesID"],$PHP_SELF,$myrow["listnamesID"]);

       } while ($myrow=mysql_fetch_array($result));
       echo "</table>\n";
 
     };
       
     echo "<p>";

       
     };
  
    
};      
    ?>

<table>
<tr><td>
<form action="<? echo $PHP_SELF; ?>" method="post">
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
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Next <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Previous <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<? echo $PHP_SELF; ?>" method="post">
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
             
     <a href="<?php echo $PHP_SELF?>">ADD A list</a>
             
     <p>
             
     <form method="post" name="addform" action="<?php echo $PHP_SELF?>" >
       
     <?
      
     if ($listnamesID)
     {  
        
     // editing so select a record
        
     $sql = "SELECT * FROM listnames WHERE listnamesID='$listnamesID'";
    
     $result = mysql_query($sql);
        
     $myrow = mysql_fetch_array($result);
       
     $listnamesName = $myrow["listnamesName"];

     $listnamesText = $myrow["listnamesText"];

     $listnamesURL = $myrow["listnamesURL"];     

     $listnamesURL = $myrow["listnamesActive"];     
      
     // print the id for editing
     
     ?>
     
     <input type=hidden name="listnamesID" value="<?php echo $listnamesID ?>">
     
     <?
     }

     ?>

     Fill in all fields to add a new category<br>     *'d fields are optional.<p>
     <table>
     

     <tr><td>
        <font class="text3">
        Name
        </td><td><input type="Text" name="listnamesName" value="<? echo $myrow["listnamesName"] ?>">
     </td></tr>

     <tr><td>
        <font class="text3">
        Text
        </td><td><textarea cols=40 rows=10 name="listnamesText"><? echo $myrow["listnamesText"]?></textarea>
     </td></tr>

     <tr><td>
        <font class="text3">
        URL
        </td><td><input type="Text" name="listnamesURL" value="<? echo $myrow["listnamesURL"] ?>">
     </td></tr>

	<tr><td>
		<font class="text3">
	Active
	</td>
	<td><select name="listnamesActive">
	<option <? if ($myrow["listnamesActive"]=="1") {echo "SELECTED";};?> value=1>Yes
	<option <? if ($myrow["listnamesActive"]=="0") {echo "SELECTED";};?> value=0 >No
	</select>
	</td></tr> 
       
     <tr><td>
        <input type="hidden" name="sort" value="<? echo $sort; ?>">
        <input type="hidden" name="lower" value="<? echo $lower; ?>">
        <input type="hidden" name="number" value="<? echo $number; ?>">
        <input type="hidden" name="desc" value="<? echo $desc; ?>">
        <input type="Submit" name="submit" value="Enter information" class="button1"></td></tr>

     </table>
     </form>  
<? 
 if ($listnamesID)
     {showlist($listnamesID);};
?>
</body>   
</html>
