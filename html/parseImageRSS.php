<?php
//includes database functions
include_once("db.php");

// takes an itemid and searches audiofolders database for an audio folder, then displays links for any files

function parseimage($itemID)
{
	$rootdirectory = "images/";
	$rooturl = "http://wrecked-distro.com";

	dbConnect();

	$sql = "SELECT folder FROM items WHERE itemid=$itemID";
	$result = mysql_query($sql);

	if ($myrow=mysql_fetch_array($result))
	{
	 	$imagedirectory = $rootdirectory.$myrow["folder"];

		if (is_dir($imagedirectory))
		{
			$d = dir($imagedirectory);

			while ($entry=$d->read())
			{
				list($imagename,$extension)=explode(".",$entry);

				if (strtoupper($extension)=="JPG" OR strtoupper($extension)=="GIF" OR strtoupper($extension)=="JPEG")
				{
					$list =  "<a href=\"$rooturl?module=viewitem3.php&command=ALL&search=itemid&keyword=$itemID\"><img src=\"$rooturl/".$imagedirectory."/".$entry."\" align=left border=0/></a>".$list;
				};	
			};
			echo $list;
			return 1;
			echo "</b>";	
			$d->close();
		};
	};
};

?>
