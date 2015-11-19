<?php

//includes database functions
include_once("db.php");

// takes an itemid and searches audiofolders database for an audio folder, then displays links for any files

function listkeywords($itemID)
{
	$rootdirectory = "audio";
	$rooturl = "http://wrecked-distro.com";
	$username = $_SESSION["username"];

	dbConnect();

	$sql = "SELECT * FROM keywords WHERE itemID = $itemID ORDER BY keyword";
	$result = mysql_query($sql);

		if ($myrow = mysql_fetch_array($result))
		{
		 do
		 {
		  echo "<a href=\"?module=viewitem.php&amp;command=KEYWORDS&amp;sort=released&amp;keyword=".urlencode($myrow["keyword"])."\">".$myrow["keyword"]."</a> "; 
		 } while ($myrow = mysql_fetch_array($result)); 
		};
};
 
?>
