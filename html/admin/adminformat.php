<?php

	echo "<b>Format admin</b>";
	echo "<P>";

   // initialize variables taht aren't passed to the script

if (!isset($_REQUEST['lower'])) { $lower = 0; } else { $lower = $_REQUEST['lower']; };
if (!isset($_REQUEST['number'])) { $number = 20; } else { $number = $_REQUEST['number']; };
if (!isset($_REQUEST['desc'])) { $desc = 1; } else { $desc = $_REQUEST['desc']; };
if (!isset($_REQUEST['sort'])) { $sort = "formatid"; } else { $sort = $_REQUEST['sort']; };
if (!isset($_REQUEST['scope'])) { $scope = 1000; } else { $scope = $_REQUEST['scope']; };
if (!isset($_REQUEST['itemselect'])) { $itemselect = NULL; } else { $itemselect = $_REQUEST['itemselect']; };
if (!isset($_REQUEST['search'])) { $search = "artist"; } else {$search = $_REQUEST['search']; };

   $result=mysql_query("SELECT COUNT(formatid) FROM format");
   $total=mysql_fetch_array($result);

   if ($lower<0) { $lower = $total[0]; };
   if ($lower>$total[0]) { $lower = 0; };

   if ($_REQUEST['submit'])
   {
    // here if no ID then adding, else we're editing

   if ($_REQUEST['formatid'])
     {
      $sql = "UPDATE format SET name='$name', description='$description', weight='$weight',parent='$parent' WHERE formatid='$formatid'";
      echo "Update of ".$formatid."\n";
     }
     else
     {
       $sql = "INSERT INTO format (formatid, name, description, weight, parent) VALUES (0,'$name', '$description', '$weight','$parent')";

      echo "inserting ".$name."\n";

     }
     // run SQL against the DB

      $result = mysql_query($sql);

      echo "Record updated.<p>";
    
      echo "<a href=\"$PHP_SELF?sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">back</a>";  

     } elseif ($delete) {
      
       // delete a record

       $sql = "DELETE FROM format WHERE formatid='$formatid'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";
      echo "<a href=\"$PHP_SELF?sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">back</a>";
      
     } else {

      // this part happens if we don't press submit

     if (!$formatid) {
    // print the list if there is not editing

     $sql= "SELECT * FROM format ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower, $number";

    echo "<div id='query'>Query: ".$sql."</div>";

     if ($result = mysql_query($sql))
     {
      
       echo "<table>\n";
     
       echo "<tr class=\"title1\"><td colspan='6'><b>Current Formats</b></td></tr>\n";
       echo "<tr class=\"title2\">
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=formatid&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."\">FormatID</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=name&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."\">Name</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=description&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."\">Description</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=weight&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."\">Weight</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=parent&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."\">Parent</a></td>
            <td></td>
             </tr>\n";
      
       while ($myrow = mysql_fetch_array($result))
       {
        printf("<tr><td>%s</td> <td>%s</td> <td>%s</td> <td>%s</td>",
        $myrow["formatid"], $myrow["name"], $myrow["description"], $myrow["weight"],$myrow["parent"]);
    
        printf("<td><a href=\"%s?formatid=%s&amp;delete=yes&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">(DELETE)</a></td><td><a 
href=\"%s?formatid=%s&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">(EDIT)</a></td></tr>",
        $PHP_SELF,$myrow["formatid"],$PHP_SELF,$myrow["formatid"]);

       };
       echo "</table>\n";
      }
       
     echo "<p>";
       
     }
             
    ?>

<table>
<tr><td>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="module" value="<?php echo $module ?>">
<input type="hidden" name="sort" value="<?php echo $sort;?>">
<input type="submit" name="show" value="Show:" class="button1">
<input type="text" name="number"  value="<?php echo $number; ?>" class="form1">
rows beginning with number
<input type="text" name="lower"  value="<?php echo $lower; ?>" class="form1">
in
<select name="desc" class="form1">  
<option value="&amp;nbsp;" <?php if ($desc!="DESC") echo " SELECTED ";?> >ASCENDING
<option value="DESC" <?php if ($desc=="DESC") echo " SELECTED ";?> >DESCENDING
</select>
order.
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="module" value="<?php echo $module ?>">
<input type="hidden" name="number"  value="<?php echo $number; ?>">
<input type="hidden" name="lower"  value="<?php echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<?php echo $desc; ?>">
<input type="hidden" name="sort" value="<?php echo $sort; ?>">
<input type="submit" name="show" value="Next <?php echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="module" value="<?php echo $module ?>">
<input type="hidden" name="number"  value="<?php echo $number; ?>">
<input type="hidden" name="lower"  value="<?php echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<?php echo $desc; ?>">
<input type="hidden" name="sort" value="<?php echo $sort; ?>">
<input type="submit" name="show" value="Previous <?php echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="module" value="<?php echo $module ?>">
<input type="hidden" name="number"  value="<?php echo $total[0]; ?>">
<input type="hidden" name="lower"  value="0">
<input type="hidden" name="desc" value="<?php echo $desc; ?>">
<input type="hidden" name="sort" value="<?php echo $sort; ?>">
<input type="submit" name="show" value="Show All" class="button1">
</form>
</td>
</tr>
</table>
     <p>
             
     <a href="<?php echo $_SERVER['PHP_SELF'];?>">ADD A format</a>
             
     <p>
             
     <form method="post" name="addform" action="<?php echo $_SERVER['PHP_SELF'];?>" >
       
     <?
      
     if ($formatid)
     {  
        
     // editing so select a record
        
     $sql = "SELECT * FROM format WHERE formatid='$formatid'";
    
     $result = mysql_query($sql);
        
     $myrow = mysql_fetch_array($result);
       
     $name = $myrow["name"];

     $description = $myrow["description"];

     $weight = $myrow["weight"];
       
     $parent = $myrow["parent"];
     
      
     // print the id for editing
     
     ?>
     
     <input type=hidden name="formatid" value="<?php echo $formatid ?>">
     
     <?
     }

     ?>

     Fill in all fields to add a new category<br>     *'d fields are optional.<p>
     <table>
     

     <tr><td>
        
       Name
        </td><td><input type="Text" name="name" value="<?php echo $myrow["name"] ?>">
     </td></tr>

     <tr><td>
        
     
     Description</td>
     <td>
     <textarea name="description" rows="7" cols="40" ><?php echo $myrow["description"] ?></textarea>
     </td>
     </tr>

     <tr><td>
        
       Weight
        </td><td><input type="Text" name="weight" value="<?php echo $myrow["weight"] ?>">
     </td></tr>

       
     <tr><td>
     
        
     
     Parent</td>
     <td>
     <select name="parent" size="1">
      
     <?
      $sql = "SELECT formatid, name FROM format";
      $result = mysql_query($sql);
     
      if ($formatlist=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$formatlist["formatid"]."\" ";
       if ($formatlist["formatid"]==$myrow["parent"])
        {echo "selected";};
       echo ">".$formatlist["name"];
      } while ($formatlist=mysql_fetch_array($result));
      };
     ?>
     </select>
     </td></tr>     

     <tr>
      <td colspan='2'>
        <input type="hidden" name="module" value="<?php echo $module ?>">
        <input type="hidden" name="sort" value="<?php echo $sort ?>">
        <input type="hidden" name="lower" value="<?php echo $lower ?>">
        <input type="hidden" name="number" value="<?php echo $number ?>">
        <input type="hidden" name="desc" value="<?php echo $desc ?>">
        <input type="Submit" name="submit" value="Enter information" class="button1">
      </td>
    </tr>

     </table>
     </form>  
     <?
     }
     
?>