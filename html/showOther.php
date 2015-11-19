<?php
include_once("parseimage.php");

// shows a list of other items by the same artist
function showOther($keywords)
{
    dbConnect();

    if (!$keywords) {$keywords="Top Picks";};

    $sql = "SELECT * FROM items WHERE artist='$keywords' AND items.quantity>0 ORDER BY itemid";
    $result = mysql_query($sql);

    if ($keyrow = mysql_fetch_array($result))
    {
    echo "Other titles in stock by <b>".$keywords."</b><br>";
    echo "<table cellspacing=0 cellpadding=2 style=\"border-style:dotted;border-width:1pxl\" >";
    do
    {
            $itemid = $keyrow["itemid"];
            $imagesql = "SELECT * FROM images WHERE itemid=$itemid";
            $imageresult = mysql_query($imagesql);
            $imagerow = mysql_fetch_array($imageresult);

            $flagurl = 1;
            if (!$imagerow["url"]) {$url = "http://wrecked-distro.com/images/noimage.gif"; $flagurl = 0;}
            else {$url = $imagerow["url"];};
            echo "<tr class=release2 valign=top>";
            echo "<td>";
            echo "<a href=\"viewitem.php?itemselect=$itemid\">";
            if ($flagurl)
                    { echo "<img src=\"".$url."\" align=left width=20 height=20 border=0>";}
            else
                    {if  (parseimage($keyrow["itemid"]))
                    { echo "";} else {echo "<img src=\"".$url."\" align=left width=20 height=20 border=0>";};
                 };
    $artist=substr($keyrow["artist"],0,20);
    $title=substr($keyrow["title"],0,20);
    $label=substr($keyrow["label"],0,20);
    $full = $artist." - ".$title." - ".$label." ".$keyrow["catalog"];
    $full = substr($full,0,50);
            echo "<b>".$full." ".$keyrow["format"]."</a>  ";
    echo "<i>$".$keyrow["retail"]."</i></b>";
            if ($keyrow["quantity"]>0)
            {
            echo " (<a
    href=\"additem.php?page=item&itemid=".$keyrow["itemid"]."&sort=$sort&lower=$lower&number=$number&desc=$desc&mode=$mode\">ADD</a>)";
               }
            else
            {echo " OUT OF STOCK";
            };
            echo "</td></tr>";

    } while ($keyrow = mysql_fetch_array($result));
    echo "</table>";
    };
};


?>
