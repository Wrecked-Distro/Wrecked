<?php 
  include_once("header.php");
  include_once("db.php");

  dbConnect();

   if ($submit)
   {
    // here if no ID then adding, else we're editing

     if ($trackid)
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

      $result = mysql_query($sql);

      echo "Record updated.<p>";

      echo "<a href=\"$PHP_SELF?itemid=$itemid&artist=$artist&sort=$sort&lower=$lower&number=$number&desc=$desc\">more
tracks</a>";

     } elseif ($delete) {

       // delete a record

       $sql = "DELETE FROM tracks WHERE trackid='$trackid'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";
      echo "<a href=\"$PHP_SELF?itemid=$itemidsort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";

     } else {


      $sql = "SELECT * FROM tracks WHERE itemid='$itemid'";
      $result = mysql_query($sql);

      if ($tracklist=mysql_fetch_array($result))
      {

       echo "<table border=0 cellspacing=0 cellpadding=3>\n";

       echo "<tr><td class=\"title1\" colspan=12><b>Audio</b></td></tr>\n";
       echo "<tr class=\"title2\">
             <td>TrackID</td>
             <td>ItemID</td>
             <td>Track #</td>
             <td>Artist</td>
             <td>Title</td>
             <td>URL</td></tr>";

      do
      {
 	echo "<tr><td>".$tracklist["trackid"]."</td><td>".$tracklist["itemid"]."</td><td>".$tracklist["tracknumber"]."</td>
<td>".$tracklist["artist"]."</td><td><a
href=\"".$tracklist["url"]."\">".$tracklist["title"]."</a></td><td>".$tracklist["url"]."</td></tr>";

      } while ($tracklist=mysql_fetch_array($result));
      echo "</table>";

      };
?>

     <form method="post" name="addform" action="<?php echo $PHP_SELF?>" >

     <?

     if ($trackid)
     {

     // editing so select a record

     $sql = "SELECT * FROM tracks WHERE trackid='$trackid'";

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

     <a href="adminitem.php">Item</a></td>
     <td>
     <select name="itemid" size="1">

     <?
      $sql = "SELECT itemid, artist, title, label, catalog, format FROM items WHERE itemid='$itemid'";
      $result = mysql_query($sql);

      if ($itemlist=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$itemlist["itemid"]."\" ";
       if ($itemlist["itemid"]==$itemid)
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
        </td><td><input type="Text" name="tracknumber" value="<? echo $myrow["tracknumber"]; ?>">
     </td></tr>


     <tr><td>
        <font class="text3">
        Artist
        </td><td><input type="Text" name="artist" value="<? echo $artist; ?>">
     </td></tr>


     <tr><td>
     <font class="text3">
     Title
     </td><td><input type="Text" name="title" value="<? echo $myrow["title"]; ?>"></td>
     </tr>



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
        <input type="Submit" name="submit" value="Enter information" class="button1"></td></tr>

     </table>
     </form>

<? };

      echo "<P><a href=\"adminitem.php?sort=$sort&lower=$lower&number=$number&desc=$desc\">back to item admin</a>";

 ?>

</body>

</html>
