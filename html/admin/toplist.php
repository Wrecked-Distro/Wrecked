<?php

include("header.php"); 
include("db.php");

class list
{
// handles all elements of top ten lists

var listID;
var username
var title
var description
var date;

function createlist($username,$title,$description)
{
 dbConnect("cutups");
 $sql = "INSERT INTO list (listID, username,title,description,date) VALUES (0,'$username','$title','$description',now())";
 $result = mysql_query($sql);
 return listID;
 };

};



function getlistname($listnamesID)
{
dbConnect();
$result=mysql_query("SELECT * FROM listnames WHERE listnamesID='$listnamesID'");
$temp=mysql_fetch_array($result);
$listname=$temp["listnamesName"];

return $listname;
};

function getlistnamesID($listID)
{
dbConnect();
$result=mysql_query("SELECT * FROM list WHERE listID='$listID'");
$temp=mysql_fetch_array($result);
$listnamesID=$temp["listnamesID"];

return $listnamesID;
};

function getiteminfo($listitemID)
{
dbConnect();
$result=mysql_query("SELECT * FROM items WHERE itemID='$listitemID'");
$temp=mysql_fetch_array($result);
$artist=$temp["artist"];
$title=$temp["title"];
$label=$temp["label"];
$catalog=$temp["catalog"];

$iteminfo=$artist." - ".$title." - ".$label." ".$catalog;

return $iteminfo;
};

function getitemID($listnamesID)
{};

?>
<html>
<head>
<title>listname admin</title>
</head>

<body>

<b>Listname admin</b>

<p>

<?php

   if (!$lower) {$lower=0;};
   if (!$number) {$number=20;};
   if (!$desc) {$desc="DESC";};
   if (!$sort) {$sort="listnamesID, listrank";};

   dbConnect();

   $result=mysql_query("SELECT COUNT(listID) FROM list");
   $total=mysql_fetch_array($result);

   if ($lower<0) {$lower=$total[0];};
   if ($lower>$total[0]) {$lower=0;};

   if ($submit)
   {
    // here if no ID then adding, else we're editing

     if ($down)
	{
	};

     if ($listID)
     {
      $sql = "UPDATE list SET listnamesID='$listnamesID', listitemID='$listitemID',
listrank='$listrank', listcomment='$listcomment' WHERE listID='$listID'";
      echo "Update of ".$listID."\n";
     }
     else
     {
  $sql = "INSERT INTO list (listID, listnamesID, listitemID, listrank, listcomment) VALUES
(0,'$listnamesID','$listitemID','$listrank', '$listcomment')";

      echo "inserting ".$listnamesID."\n";

     }
     // run SQL against the DB

      $result = mysql_query($sql);

      echo "Record updated.<p>";

      echo "<a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";

     } elseif ($delete) {

       // delete a record

       $sql = "DELETE FROM list WHERE listID='$listID'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";
      echo "<a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";

     } else {

      // this part happens if we don't press submit

     if (!$listID) {
    // print the list if there is not editing

     $result = mysql_query("SELECT * FROM list ORDER BY $sort $desc LIMIT $lower, $number");

     if ($myrow = mysql_fetch_array($result))
     {

       echo "<table border=0 cellspacing=0 cellpadding=3>\n";

       echo "<tr class=\"title1\"><td colspan=5><b>Current List Items</b></td></tr>\n";
       echo "<tr class=\"title2\">
             <td>
<a href=\"$PHP_SELF?sort=listID&lower=$lower&number=$number&desc=$desc\">listID</a></td>
             <td>
<a href=\"$PHP_SELF?sort=listnameID&lower=$lower&number=$number&desc=$desc\">listnameID</a></td>
             <td>
<a href=\"$PHP_SELF?sort=listitemID&lower=$lower&number=$number&desc=$desc\">listitemID</a></td>
             <td>
<a href=\"$PHP_SELF?sort=listnamesURL&lower=$lower&number=$number&desc=$desc\">Rank</a></td>
             <td>
<a href=\"$PHP_SELF?sort=listnamesURL&lower=$lower&number=$number&desc=$desc\">Comment</a></td>

             </tr>\n";

       do
       {
        printf("<tr><td>%s</td> <td>%s</td> <td>%s</td><td>%s</td><td>%s</td>",
        $myrow["listID"], getlistname($myrow["listnamesID"]), getiteminfo($myrow["listitemID"]),
$myrow["listrank"], $myrow["listcomment"]
);

        printf("<td><a
href=\"%s?listID=%s&delete=yes&sort=$sort&lower=$lower&number=$number&desc=$desc\">(DELETE)</a></td><td><a
href=\"%s?listID=%s&sort=$sort&lower=$lower&number=$number&desc=$desc\">(EDIT)</a></td>
<td><a href=\"%s?&down=%s&sort=$sort&lower=$lower&number=$number&desc=$desc\">(DOWN)</a>
</tr>",
        $PHP_SELF,$myrow["listID"],$PHP_SELF,$myrow["listID"],$PHP_SELF, $myrow["listID"]);

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

     <a href="<?php echo $PHP_SELF?>">ADD A list item</a>

     <p>

     <form method="post" name="addform" action="<?php echo $PHP_SELF?>" >

     <?

     if ($listID)
     {

     // editing so select a record

     $sql = "SELECT * FROM list WHERE listID='$listID'";

     $result = mysql_query($sql);

     $myrow = mysql_fetch_array($result);

     $listnamesID = $myrow["listnamesID"];

     $listitemID = $myrow["listitemID"];

     $listrank = $myrow["listrank"];

    $listcomment = $myrow["listcomment"];

     // print the id for editing

     ?>

     <input type=hidden name="listID" value="<?php echo $listID ?>">

     <?
     }

     ?>

     Fill in all fields to add a new list item <br>     *'d fields are optional.<p>
     <table>

     <tr><td>
     <font class="text3">
     List Title
     </td>
     <td>
     <select name="listnamesID" size="1">

     <?
      $sql = "SELECT * FROM listnames";
      $result = mysql_query($sql);

      if ($catlist=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$catlist["listnamesID"]."\" ";
       if ($catlist["listnamesID"]==$myrow["listnamesID"])
        {echo "selected";};
       echo ">".$catlist["listnamesName"];
      } while ($catlist=mysql_fetch_array($result));
      };
     ?>
     </select>
     </td></tr>

    <tr><td>
     <font class=\"text3\">

     <a href="adminitem.php">Item</a></td>
     <td>
     <select name="listitemID" size="1">

     <?
      $sql = "SELECT itemid, artist, title, label, catalog, format, quantity FROM items ORDER BY artist";
      $result = mysql_query($sql);

      if ($itemlist=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$itemlist["itemid"]."\" ";
       if ($itemlist["itemid"]==$listitemID)
        {echo "selected";};
       echo ">".$itemlist["artist"]." - ".$itemlist["title"]." - ".$itemlist["label"]."
".$itemlist["catalog"]." -
".$itemlist["format"]."(".$itemlist["quantity"].")";
      } while ($itemlist=mysql_fetch_array($result));
      };
     ?>

     </select>
     </td></tr>


     <tr><td>
        <font class="text3">
        Rank
        </td><td><input type="Text" name="listrank" size="2" value="<? echo $myrow["listrank"] ?>">
     </td></tr>

     <tr><td>
        <font class="text3">
        Comment
        </td><td><textarea cols=40 rows=10 name="listcomment"><? echo
$myrow["listcomment"]?></textarea>
     </td></tr>


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
