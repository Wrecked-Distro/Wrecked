<html>
<head>
<title>ADMIN - AUDIO TRACK</title>
</head>

<body>

<b>AUDIO TRACK admin</b>

<p>

<?php

   if (!$lower) {$lower=0;};
   if (!$number) {$number=20;};
   if (!$desc) {$desc="DESC";};
   if ($_REQUEST['sort']) {
    $sort = $_REQUEST['sort'];
  } else {
    $sort = 'itemid';
  };
  // if a track id was passed
    if (!$_REQUEST['trackid']) {
      $trackid = $_REQUEST['trackid'];
    };

   $result = mysql_query("SELECT COUNT(trackid) FROM tracks");
   $total = mysql_fetch_array($result);

   if ($lower<0) {$lower=$total[0];};
   if ($lower>$total[0]) {$lower=0;};

   if ($_REQUEST['submit'])
   {
    // here if no ID then adding, else we're editing

     if ($_REQUEST['trackid'])
     {
      $sql = "UPDATE tracks SET itemid='$itemid', tracknumber='$tracknumber', artist='$artist',
title='$title', url='$url' WHERE trackid='$trackid'";
      echo "Update of ".$title."\n";
     }
     else
     {
  $sql = "INSERT INTO tracks (trackid, itemid, tracknumber, artist, title, url) VALUES 
(0,'$itemid','$tracknumber','$artist','$title','$url')";

      echo "inserting ".$title."\n";

     }

     // run SQL against the DB

      $result = mysql_query($sql);

      echo "Record updated.<p>";
    
      echo "<a href=\"$PHP_SELF?module=$module&sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";  

     } elseif ($delete) {
      
       // delete a record

       $sql = "DELETE FROM tracks WHERE trackid='$trackid'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";
      echo "<a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";
      
     } else {

      // this part happens if we don't press submit

     if (!$_REQUEST['trackid']) {
      // print the list if there is not editing
      $sql = "SELECT * FROM tracks ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower, $number";
      echo $sql;
      $result = mysql_query($sql);

     if ($myrow = mysql_fetch_array($result))
     {
      
       echo "<table border=0 cellspacing=0 cellpadding=3>\n";
     
       echo "<tr><td bgcolor=ffcc00 colspan=6 class=\"title1\"><b>Audio Tracks</b></td></tr>\n";
       echo "<tr class=\"title2\">
             <td>
            <a href=\"$PHP_SELF?module=$module&sort=trackid&lower=$lower&number=$number&desc=$desc\">TrackID</a></td>
                         <td>
            <a href=\"$PHP_SELF?module=$module&sort=itemid&lower=$lower&number=$number&desc=$desc\">ItemID</a></td>
                         <td>
            <a href=\"$PHP_SELF?module=$module&sort=tracknumber&lower=$lower&number=$number&desc=$desc\">Track #</a></td>
                         <td>
            <a href=\"$PHP_SELF?module=$module&sort=artist&lower=$lower&number=$number&desc=$desc\">Artist</a></td> 
                         <td>
            <a href=\"$PHP_SELF?module=$module&sort=title&lower=$lower&number=$number&desc=$desc\">Title</a></td>
                         <td>
            <a href=\"$PHP_SELF?module=$module&sort=url&lower=$lower&number=$number&desc=$desc\">URL</a></td>
       
             </tr>\n";
      
       do
       {
        printf("<tr><td>%s</td> <td>%s</td> <td>%s</td><td>%s</td><td>%s</td> <td><a href=\"%s\">%s</a></td>",
        $myrow["trackid"],$myrow["itemid"], $myrow["tracknumber"],$myrow["artist"], $myrow["title"], $myrow["url"], $myrow["url"]);
    
        printf("<td><a href=\"%s?module=$module&trackid=%s&delete=yes&sort=$sort&lower=$lower&number=$number&desc=$desc\">(DELETE)</a></td><td><a href=\"%s?module=$module&trackid=%s&sort=$sort&lower=$lower&number=$number&desc=$desc\">(EDIT)</a></td></tr>",
        $PHP_SELF,$myrow["trackid"],$PHP_SELF,$myrow["trackid"]);

       } while ($myrow=mysql_fetch_array($result));
       echo "</table>\n";
      }
       
     echo "<p>";
       
     }
             
    ?>

<table>
<tr><td>
<form action"admintrack.php" method="post">
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
<form action="admintrack.php" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Next <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="admintrack.php" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Previous <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="admintrack.php" method="post">
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
             
     <a href="<?php echo $PHP_SELF?>">ADD A track</a>
             
     <p>
             
     <form method="post" name="addform" action="<?php echo $PHP_SELF?>" >
       
     <?php
      
     if ($_REQUEST['trackid'])
     {  
        
     // editing so select a record
        
     $sql = sprintf("SELECT * FROM tracks WHERE trackid = %s", $_REQUEST['trackid']);
    
     $result = mysql_query($sql);
        
     $myrow = mysql_fetch_array($result);
       
     $itemid = $myrow["itemid"];
       
     $tracknumber = $myrow["tracknumber"];
     
     $artist = $myrow["artist"];
      
     $title = $myrow["title"];

     $url = $myrow["url"];
        
     // print the id for editing
     
     ?>
     
     <input type=hidden name="trackid" value="<?php echo $trackid; ?>">
     
     <?
     }

     ?>

     Fill in all fields to add a new track<br>     *'d fields are optional.<p>
     <table>
     

     <tr><td>
     <font class="text3">

    <tr><td>
     <font class="text3">
     
     <a href="adminitem.php">Item</a></td>
     <td>
     <select name="itemid" size="1" class="form1">
     
     <?
      $itemtemp=$myrow["itemid"];
      $sql = "SELECT itemid, artist, title, label, catalog, format FROM items WHERE itemid='$itemtemp'";
      $result = mysql_query($sql);
     
      if ($itemlist=mysql_fetch_array($result))
      {   
      do  
      {
       echo "<option value=\"".$itemlist["itemid"]."\" ";
       if ($itemlist["itemid"]==$myrow["itemid"])
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
        Track #
        </td><td><input type="Text" name="tracknumber" value="<? echo $myrow["tracknumber"]; ?>" class="form1">
     </td></tr>

     
     <tr><td>
        <font class="text3">
        Artist
        </td><td><input type="Text" name="artist" value="<? echo $myrow["artist"]; ?>" class="form1">
     </td></tr>

     
     <tr><td>  
     <font class="text3">
     Title   
     </td><td><input type="Text" name="title" value="<? echo $myrow["title"]; ?>" class="form1"></td>
     </tr>

            

     <tr><td>
     <font class="text3">
     URL</td>
     <td>
     <input type="Text" name="url" value="<? echo $myrow["url"] ?>" class="form1">
     </td>
     </tr>
     
     <tr><td>
        <input type="hidden" name="sort" value="<? echo $sort ?>">
        <input type="hidden" name="lower" value="<? echo $lower ?>">
        <input type="hidden" name="number" value="<? echo $number ?>">
        <input type="hidden" name="desc" value="<? echo $desc ?>">
        <input type="hidden" name="review_date" value="<? echo date("M d y",time()) ?>">
        <input type="Submit" name="submit" value="Enter information" class="button1"></td></tr>
     
     </table>
     </form>  
     <?
     }
     
?>
<P>
     
     
     
</body>
     
</html>
