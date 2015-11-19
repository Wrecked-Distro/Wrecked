<?php
// includes database functions

include_once("db.php");

// takes an itemid and searches audiofolders database for an audio folder, then displays links for any files

function parseaudio($itemID)
{

  $rootdirectory = "audio";
  $rooturl = "http://wrecked-distro.com";
  $username = $_SESSION["username"];

  dbConnect();

  $sql = "SELECT * FROM items WHERE itemID = $itemID";
  $result = mysql_query($sql);

  if ($myrow=mysql_fetch_array($result))
  {
    // create directory path

   $audiodirectory = strtolower($rootdirectory."/".$myrow["folder"]);

   if (is_dir($audiodirectory))
   {
	// open directory

        $d = dir($audiodirectory);

	// read through directory
	while ($entry=$d->read())
	{
	 // break filename into folder name and remainder
 
	 list($folder,$remainder) = explode("-",$entry);

	 // break remainder of filename into the track title and the file extension

	 list($trackname,$extension) = explode(".",$remainder);  

	 if ($extension == "mp3")
	 {
//		$list = $list."<content:encoded><![CDATA[".$myrow["description"]."<a href=\"$rooturl/$audiodirectory/$entry\">$trackname</a>]]></content:encoded>\n";  
//		$list = $list."<enclosure url='$rooturl/$audiodirectory/$entry' type='audio/mpeg'/ length='3000000'>\n";
	 };

        };
	echo $list;
 	$d->close(); };
   };
};


?>
