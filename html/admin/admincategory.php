<b>Category admin</b>

<p>

<?php

if (!isset($_REQUEST['lower'])) { $lower = 0; } else { $lower = $_REQUEST['lower']; };
if (!isset($_REQUEST['number'])) { $number = 20; } else { $number = $_REQUEST['number']; };
if (!isset($_REQUEST['desc'])) { $desc = 1; } else { $desc = $_REQUEST['desc']; };
if (!isset($_REQUEST['sort'])) { $sort = "catid"; } else { $sort = $_REQUEST['sort']; };
if (!isset($_REQUEST['scope'])) { $scope = 1000; } else { $scope = $_REQUEST['scope']; };
if (!isset($_REQUEST['itemselect'])) { $itemselect = NULL; } else { $itemselect = $_REQUEST['itemselect']; };
if (!isset($_REQUEST['search'])) { $search = "artist"; } else {$search = $_REQUEST['search']; };

   $result = mysql_query("SELECT COUNT(catid) FROM category");
   $total = mysql_fetch_array($result);

   if ($lower<0) {$lower=$total[0];};
   if ($lower>$total[0]) {$lower=0;};

   if ($_REQUEST['submit'])
   {
    // here if no ID then adding, else we're editing

     if ($_REQUEST['catid'])
     {
        $sql = "UPDATE category SET name='$name', parent='$parent' WHERE catid='$catid'";
        echo "Update of ".$catid."\n";
     }
     else
     {
        $sql = "INSERT INTO category (catid, name, parent) VALUES (0,'$name','$parent')";
        echo "inserting ".$name."\n";
     }
     // run SQL against the DB

      $result = mysql_query($sql);

      echo "Record updated.<p>";
    
      echo "<a href=\"admincategory.php?sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">back</a>";  

     } elseif ($delete) {
      
       // delete a record

       $sql = "DELETE FROM category WHERE catid='$catid'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";
      echo "<a href=\"admincategory.php?sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">back</a>";
      
     } else {

      // this part happens if we don't press submit

     if (!$catid) {
    // print the list if there is not editing

      $sql = "SELECT * FROM category ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower, $number";
        echo "<div id='query'>Query: ".$sql."</div>";
     if ( $result = mysql_query($sql))
     {
      
       echo "<table>\n";
     
       echo "<tr class=\"title1\"><td colspan='5'><b>Current Categories</b></td></tr>\n";
       echo "<tr class=\"title2\">
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=catid&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."\">DistroID</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=name&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."\">Name</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=parent&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."\">Parent</a></td>       
             <td colspan='2'></td>
             </tr>\n";
      
       while ($myrow = mysql_fetch_array($result))
       {
        printf("<tr><td>%s</td> <td>%s</td> <td>%s</td>",
        $myrow["catid"], $myrow["name"], $myrow["parent"]);
    
        printf("<td><a href=\"%s?catid=%s&amp;delete=yes&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">(DELETE)</a></td><td><a href=\"%s?catid=%s&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">(EDIT)</a></td></tr>",
        $PHP_SELF,$myrow["catid"],$PHP_SELF,$myrow["catid"]);

       };
       echo "</table>\n";
      }
       
     echo "<p>";
       
     }
             
    ?>

<table>
<tr><td>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
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
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
  <input type="hidden" name="module" value="<?php echo $module ?>">
  <input type="hidden" name="number"  value="<?php echo $number; ?>">
  <input type="hidden" name="lower"  value="<?php echo ($lower+$number);?>">
  <input type="hidden" name="desc" value="<?php echo $desc; ?>">
  <input type="hidden" name="sort" value="<?php echo $sort; ?>">
  <input type="submit" name="show" value="Next <?php echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
  <input type="hidden" name="module" value="<?php echo $module ?>">
  <input type="hidden" name="number"  value="<?php echo $number; ?>">
  <input type="hidden" name="lower"  value="<?php echo ($lower-$number);?>">
  <input type="hidden" name="desc" value="<?php echo $desc; ?>">
  <input type="hidden" name="sort" value="<?php echo $sort; ?>">
  <input type="submit" name="show" value="Previous <?php echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
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
             
     <a href="<?php echo $_SERVER['PHP_SELF'];?>">ADD A category</a>
             
     <p>
             
     <form method="post" name="addform" action="<?php echo $_SERVER['PHP_SELF'];?>" >
       
     <?
      
     if ($catid)
     {  
        
     // editing so select a record
        
     $sql = "SELECT * FROM category WHERE catid='$catid'";
    
     $result = mysql_query($sql);
        
     $myrow = mysql_fetch_array($result);
       
     $name = $myrow["name"];
       
     $parent = $myrow["parent"];
     
      
     // print the id for editing
     
     ?>
     
     <input type=hidden name="catid" value="<?php echo $catid ?>">
     
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
     
     
     Parent</td>
     <td>
     <select name="parent" size="1">
      
     <?
      $sql = "SELECT name FROM category";
      $result = mysql_query($sql);
     
      if ($catlist=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$catlist["name"]."\" ";
       if ($catlist["name"]==$myrow["parent"])
        {echo "selected";};
       echo ">".$catlist["name"];
      } while ($catlist=mysql_fetch_array($result));
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
<P>