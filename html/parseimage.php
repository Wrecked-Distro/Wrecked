<?php

// takes an itemid and searches image folder then displays images for item

function parseimage($itemID)
{
	$rootdirectory = "images/";
	$rooturl = "http://wrecked-distro.com";

	dbConnect();

	$sql = "SELECT folder FROM items WHERE itemid = $itemID";
	$result = mysql_query($sql);

	if ($myrow = mysql_fetch_array($result))
	{
		 $imagedirectory = $rootdirectory.$myrow["folder"];

		 if (is_dir($imagedirectory)) {

		 $d = dir($imagedirectory);

		 while ($entry=$d->read())
		 {
		  list($imagename,$extension)=explode(".",$entry);

		  if (strtoupper($extension)=="JPG" OR strtoupper($extension)=="GIF" OR strtoupper($extension)=="JPEG")
		  {
		   $list =  "<a href=\"?module=viewitem.php&amp;command=SEARCH&amp;search=itemid&amp;keyword=$itemID\"><img src=\"$rooturl/".htmlentities($imagedirectory)."/".htmlentities($entry)."\" class='cover' alt=''></a>".$list;
		  };	
		 };
		 echo $list;
		 return 1;
		 echo "</b>";	
		 $d->close();
		};
	};
};

// takes an itemid and searches audiofolders database for an audio folder, then displays links for any files

function parseimageTiny($itemID)
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
      $list =  "<a href=\"viewitem?itemselect=$itemID\"><img src=\"$rooturl/".$imagedirectory."/".$entry."\" class='cover' width='20' height='20' alt=''>".$list;
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
