<?php
include_once("header.php");
include_once("db.php");
?>
<html>
<head>
<title></title>
</head>

<body>

<b>AUDIO FOLDER admin</b>

<p>

<?php

   if (!$lower) {$lower=0;};
   if (!$number) {$number=20;};
   if (!$desc) {$desc="DESC";};
   if (!$sort) {$sort="audiofolderID";};

   dbConnect();

   $result=mysql_query("SELECT COUNT(audiofolderID) FROM audio_folders");
   $total=mysql_fetch_array($result);

   if ($lower<0) {$lower=$total[0];};
   if ($lower>$total[0]) {$lower=0;};

   if ($submit)
   {
    // here if no ID then adding, else we're editing

     if ($audiofolderID)
     {
      $sql = "UPDATE audio_folders SET itemID='$itemID', directory='$directory' WHERE audiofolderID='$audiofolderID'";
      echo "Update of ".$directory."\n";
     }
     else
     {
  $sql = "INSERT INTO audio_folders (audiofolderID, itemID, directory) VALUES
(0,'$itemID','$directory')";

      echo "inserting ".$directory."\n";

     }

     // run SQL against the DB

      $result = mysql_query($sql);

      echo "Record updated.<p>";

      echo "<a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";

     } elseif ($delete) {

       // delete a record

       $sql = "DELETE FROM audio_folders WHERE audiofolderID='$audiofolderID'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";
      echo "<a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";

     } else {

      // this part happens if we don't press submit

     if (!$audiofolderID) {
    // print the list if there is not editing

     $result = mysql_query("SELECT * FROM audio_folders ORDER BY $sort $desc LIMIT $lower, $number");

     if ($myrow = mysql_fetch_array($result))
     {

       echo "<table border=0 cellspacing=0 cellpadding=3>\n";

       echo "<tr><td bgcolor=ffcc00 colspan=3 class=\"title1\"><b>Audio Folders</b></td></tr>\n";
       echo "<tr class=\"title2\">
             <td>
<a href=\"$PHP_SELF?sort=audiofolderID&lower=$lower&number=$number&desc=$desc\">AudiofolderID</a></td>
             <td>
<a href=\"$PHP_SELF?sort=itemID&lower=$lower&number=$number&desc=$desc\">ItemID</a></td>
             <td>
<a href=\"$PHP_SELF?sort=tracknumber&lower=$lower&number=$number&desc=$desc\">Directory</a></td>
             </tr>\n";

       do
       {
        printf("<tr><td>%s</td> <td>%s</td> <td><a href=\"%s\">%s</a></td>",
        $myrow["audiofolderID"],$myrow["itemID"], $myrow["directory"], $myrow["directory"]);

        printf("
<td><a href=\"%s?audiofolderID=%s&delete=yes&sort=$sort&lower=$lower&number=$number&desc=$desc\">(DELETE)</a></td>
<td><a href=\"%s?audiofolderID=%s&sort=$sort&lower=$lower&number=$number&desc=$desc\">(EDIT)</a></td>
</tr>",
        $PHP_SELF,$myrow["audiofolderID"],$PHP_SELF,$myrow["audiofolderID"]);

       } while ($myrow=mysql_fetch_array($result));
       echo "</table>\n";
      }

     echo "<p>";

     }

    ?>

<table>
<tr><td>
<form action"<?php echo $PHP_SELF;?>" method="post">
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
<form action="<?php echo $PHP_SELF;?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Next <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<?php echo $PHP_SELF;?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Previous <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<?php echo $PHP_SELF;?>" method="post">
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

     <a href="<?php echo $PHP_SELF?>">ADD A folder</a>

     <p>

     <form method="post" name="addform" action="<?php echo $PHP_SELF?>" >

     <?

     if ($audiofolderID)
     {

     // editing so select a record

     $sql = "SELECT * FROM audio_folders WHERE audiofolderID='$audiofolderID'";

     $result = mysql_query($sql);

     $myrow = mysql_fetch_array($result);

     $itemID = $myrow["itemID"];

     $directory = $myrow["directory"];

     ?>

     <input type=hidden name="audiofolderID" value="<?php echo $audiofolderID; ?>">

     <?
     }

     ?>

     Fill in all fields to add a new audio folder<br>     *'d fields are optional.<p>
     <table>


     <tr><td>
     <font class="text3">

    <tr><td>
     <font class="text3">

     <a href="adminitem.php">Item</a></td>
     <td>
     <select name="itemID" size="1" class="form1">

     <?
      $itemtemp=$myrow["itemID"];
      $sql = "SELECT itemid, artist, title, label, catalog, format FROM items ORDER BY itemid DESC";
      $result = mysql_query($sql);

      if ($itemlist=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$itemlist["itemid"]."\" ";
       if ($itemlist["itemid"]==$myrow["itemID"])
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
     URL</td>
     <td>
     <input type="Text" name="directory" value="<? echo $myrow["directory"] ?>" class="form1">
     </td>
     </tr>

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



</body>

</html>
