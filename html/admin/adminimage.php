<?php
  include_once("db.php");
?>

<html>
<head>
<title>image admin</title>
</head>

<body>

<b>IMAGE admin</b>

<p>

<?php
   dbConnect();

   if ($submit)
   {
    // here if no ID then adding, else we're editing

     if ($imageid)
     {
      $sql = "UPDATE images SET itemid='$itemid', caption='$caption', url='$url' WHERE imageid='$imageid'";
      echo "Update of ".$imageid."\n";
     }
     else
     {
       $sql = "INSERT INTO images (imageid, itemid, caption, url) VALUES(0,'$itemid','$caption','$url')";

      echo "inserting ".$imageid."\n";

     }

     // run SQL against the DB

      $result = mysql_query($sql);

      echo "Record updated.<p>";

      echo "<a href=\"$PHP_SELF?itemid=$itemid&artist=$artist&sort=$sort&lower=$lower&number=$number&desc=$desc\">more images</a>";

     } elseif ($delete) {

       // delete a record

       $sql = "DELETE FROM images WHERE imageid='$imageid'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";
       echo "<a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";

     } else {

      // this part happens if we don't press submit

     if (!$imageid) {
    // print the list if there is not editing

     $result = mysql_query("SELECT * FROM images WHERE itemid='$itemid'");

     if ($myrow = mysql_fetch_array($result))
     {

       echo "<table border=0 cellspacing=0 cellpadding=3>\n";

       echo "<tr><td class=\"title1\" colspan=5><b>Images</b></td></tr>\n";
       echo "<tr class=\"title2\">
             <td>
<a href=\"$PHP_SELF?sort=imageid&lower=$lower&number=$number&desc=$desc\">ImageID</a></td>
             <td>
<a href=\"$PHP_SELF?sort=itemid&lower=$lower&number=$number&desc=$desc\">ItemID</a></td>
             <td>
<a href=\"$PHP_SELF?sort=caption&lower=$lower&number=$number&desc=$desc\">Caption</a></td>
             <td>
<a href=\"$PHP_SELF?sort=url&lower=$lower&number=$number&desc=$desc\">URL</a></td>
             <td>
<a href=\"$PHP_SELF?sort=url&lower=$lower&number=$number&desc=$desc\">IMAGE</a></td>

             </tr>\n";

       do
       {
        printf("<tr><td>%s</td> <td>%s</td> <td>%s</td> <td><a href=\"%s\">%s</a></td><td><img
src=\"%s\"></td>",
        $myrow["imageid"],$myrow["itemid"], $myrow["caption"],$myrow["url"], $myrow["url"],
$myrow["url"]);

        printf("<td><a
href=\"%s?imageid=%s&delete=yes&sort=$sort&lower=$lower&number=$number&desc=$desc\">(DELETE)</a></td><td><a
href=\"%s?imageid=%s&sort=$sort&lower=$lower&number=$number&desc=$desc\">(EDIT)</a></td></tr>",
        $PHP_SELF,$myrow["imageid"],$PHP_SELF,$myrow["imageid"]);

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

     <?

     if ($imageid)
     {

     // editing so select a record

     $sql = "SELECT * FROM images WHERE imageid='$imageid'";

     $result = mysql_query($sql);

     $myrow = mysql_fetch_array($result);

     $itemid = $myrow["itemid"];

     $caption = $myrow["caption"];

     $url = $myrow["url"];

     // print the id for editing

     ?>

     <input type=hidden name="imageid" value="<?php echo $imageid; ?>">

     <?
     }

     ?>

     Fill in all fields to add a new image<br>     *'d fields are optional.<p>
     <table>


     <tr><td>

    <tr><td>
     <font class="text3">

     <a href="adminitem.php">Item</a></td>
     <td>
     <select name="itemid" size="1">

     <?php
      $sql = "SELECT itemid, artist, title, label, catalog, format FROM items WHERE itemid='$itemid'";
      $result = mysql_query($sql);

      if ($itemlist=mysql_fetch_array($result))
      {
        do
        {
         echo "<option value=\"".$itemlist["itemid"]."\" ";
         if ($itemlist["itemid"]==$myrow["itemid"])
          {
            echo "selected";
          };
          echo ">".$itemlist["artist"]." - ".$itemlist["title"]." - ".$itemlist["label"]." ".$itemlist["catalog"]." - ".$itemlist["format"];
        } while ($itemlist = mysql_fetch_array($result));
      };
     ?>
     </select>
     </td></tr>

     <tr><td>
     <font class="text3">
        Caption
        </td><td><input type="Text" name="caption" width=64 value="<? echo $myrow["caption"]; ?>">
     </td></tr>

     <tr><td>
     <font class="text3">
     URL</td>
     <td>
     <input type="Text" name="url" value="<? echo $myrow["url"] ?>">
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

<?php   echo "<P><a href=\"adminitem.php?sort=$sort&lower=$lower&number=$number&desc=$desc\">back to item admin</a>";  ?>
</body>
</html>
