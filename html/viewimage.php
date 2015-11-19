<?php

$count = 0;

$sql = "SELECT itemid FROM items ORDER BY itemid DESC LIMIT 200";
$result = mysql_query($sql);

if ($itemlist = mysql_fetch_array($result))
{
  echo "<div id='itemlist'>";
  do
  {
   $itemid = $itemlist["itemid"];

   $imagesql = "SELECT * FROM images WHERE itemid=$itemid";
   $imageresult = mysql_query($imagesql);
   $imagerow = mysql_fetch_array($imageresult);

   $flagurl = 1;
   $count++;

   // check to see if there is an image in the image db 

   if (!$imagerow["url"]) {
        $flagurl = 0;
    } else {
        $url = $imagerow["url"];
    };


   if ($flagurl) {
      echo "<img src=\"".$url."\"  align='left'>";
    } else {
      if (parseimage($itemlist["itemid"])) {

      };
    };

  } while ($itemlist = mysql_fetch_array($result));
  echo "</div>";
};

?>
