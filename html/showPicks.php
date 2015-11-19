<?php

// shows the current list of top pics
function showPicks($keywords)
{
	dbConnect();

	if (!$keywords) {
		$keywords="Top Picks";
	};

	$sql = "SELECT * FROM comments,items WHERE keywords='$keywords' AND items.itemid=comments.itemID AND items.quantity>0 ORDER BY rank";
	$result = mysql_query($sql);

	if ($keyrow = mysql_fetch_array($result))
	{
		echo "<b>".$keywords."</b> posted by <i>".$keyrow["username"]."</i> <br>";
		echo "<table cellspacing=0 cellpadding=2 style=\"border-style:dotted;border-width:1pxl\" >";
		do
		{
	        $itemid = $keyrow["itemid"];
	        $imagesql = "SELECT * FROM images WHERE itemid=$itemid";
	        $imageresult = mysql_query($imagesql);
	        $imagerow = mysql_fetch_array($imageresult);

	        $flagurl = 1;
	        if (!$imagerow["url"]) {$url = "images/noimage.gif"; $flagurl = 0;}
	        else {$url = $imagerow["url"];};
	        echo "<tr class=release2 valign=top>";
	        echo "<td>";
	        echo "<a href=\"viewitem.php?itemselect=$itemid\">";
	        if ($flagurl) {
	        	echo "<img src=\"".$url."\" align=left width=20 height=20 border=0>";
	     	} elseif (parseimageTiny($keyrow["itemid"])) {
	            echo "";
	        } else {
	            echo "<img src=\"".$url."\" align=left width=20 height=20 border=0>";
	        };

			$artist = substr($keyrow["artist"], 0, 20);
			$title = substr($keyrow["title"], 0, 20);
			$label = substr($keyrow["label"], 0, 20);
			$full = $artist." - ".$title." - ".$label." ".$keyrow["catalog"];
			$full = substr($full,0,50);
			echo "<b>".$full." ".$keyrow["format"]."</a>  ";
			echo "<i>$".$keyrow["retail"]."</i></b>";
			if ($keyrow["quantity"] > 0) {
			    echo " [<a href=\"additem.php?page=item&amp;itemid=".$keyrow["itemid"]."&amp;sort=".urlencode($sort)."&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;mode=$mode\">ADD</a>]";
			} else {
				echo " OUT OF STOCK";
			};
			echo "</td></tr>";
		} while ($keyrow = mysql_fetch_array($result));
		echo "</table>";
	};
};


?>
