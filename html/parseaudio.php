<?php

//includes database functions
include_once("db.php");

/**
 * Checks to see if the passed item has audio files present of the associated type
 * 
 * @param Item $id
 * @param String $format
 * @return boolean
 **/

function hasAudio($itemID, $format = NULL)
{
  $rootdirectory = "audio";
  $rooturl = "http://wrecked-distro.com";

  dbConnect();

  $sql = sprintf("SELECT * FROM items WHERE itemID = %s", $itemID);
  $result = mysql_query($sql);

  if ($myrow = mysql_fetch_array($result))
  {
	$audiodirectory = strtolower($rootdirectory."/".$myrow["folder"]);

	if (is_dir($audiodirectory))
	{
	 return 1;	
	} else { 
	 return 0;
	};

  } else {
  	return 0;
  }

};

function parseaudio($itemID, $format = NULL)
{
  $list="";
  $rootdirectory = "audio";
  $rooturl = "http://wrecked-distro.com";
  $username = $_SESSION["username"];
  dbConnect();

  $sql = "SELECT * FROM items WHERE itemID = $itemID";
  $result = mysql_query($sql);

  if ($myrow = mysql_fetch_array($result))
  {
   $audiodirectory = strtolower($rootdirectory."/".$myrow["folder"]);

    if (is_dir($audiodirectory))
    {
     $d = scandir($audiodirectory, 2);
      $list .= "<audio controls='controls'>";
      foreach ($d as $entry) {
        list($folder, $remainder) = explode("-",$entry);
        list($trackname,$extension) = explode(".",$remainder);  
        if ($extension == "rm") {
          $list =  "<a href=\"rammaker.php?username=$username&amp;itemID=$itemID&amp;url=$rooturl/".$audiodirectory."/".$entry."\">".htmlentities($trackname)."</a> ".$list;
      	};

        if ($extension == "mp3") {
          //$list =  "<audio src='$rooturl/".$audiodirectory."/".$entry."' controls>".$trackname."</audio> ".$list;
        	if ($format == 'audio') {
				$list .=  "<source src='".$rooturl."/".htmlentities($audiodirectory)."/".htmlentities($entry)."' title='".$trackname."' type='audio/mpeg'/> ";
        	} else {
                $list =  "<a href='$rooturl/".$audiodirectory."/".$entry."'>".htmlentities($trackname)."</a> ".$list; 		
        	};

        };
      };
      $list .="</audio>";

   } else { 
    $list .= "No Samples";
   };
  };
  return $list;
};

// takes an itemid and searches audiofolders database for an audio folder, then displays links for any files

function parseaudio_simple($itemID)
{
  $list="";
  $rootdirectory = "audio";
  $rooturl = "http://wrecked-distro.com";
  $username = $_SESSION["username"];
  dbConnect();

  $sql = "SELECT * FROM items WHERE itemID = $itemID";
  $result = mysql_query($sql);

  if ($myrow = mysql_fetch_array($result))
  {
   $audiodirectory = strtolower($rootdirectory."/".$myrow["folder"]);

    if (is_dir($audiodirectory))
    {
     $d = dir($audiodirectory);
     //echo $entry;
      while ($entry = $d->read()) {
        list($folder, $remainder) = explode("-",$entry);
        list($trackname,$extension) = explode(".",$remainder);  
        if ($extension == "rm") {
          $list =  "<a href=\"rammaker.php?username=$username&amp;itemID=$itemID&amp;url=$rooturl/".$audiodirectory."/".$entry."\">".$trackname."</a> ".$list;
      	};

        if ($extension == "mp3") {
          $list =  "<audio src='$rooturl/".$audiodirectory."/".$entry."' controls>".$trackname."</audio> ".$list;
        };
      };
     $d->close(); 
     $list = $list."</b>";	
   } else { 
    $list .= "No Samples";
   };
  };
  return $list;
};

// returns the proper foldername given an itemid

function foldername($itemID)
{
  dbConnect();
  $sql = "SELECT * FROM items WHERE itemid=$itemID";
  $result = mysql_query($sql);
  if ($myrow=mysql_fetch_array($result))  {
      $label = $myrow["label"];
      $catalog = $myrow["catalog"];	
      $foldername = $label.$catalog;
      return $foldername; 	
   } else  {
    return 0;
  };
}


function logAudio($username,$itemID,$URL)
{
    dbConnect();
    if (!$username) {$username = " ";};
    $IP = getenv("REMOTE_ADDR");

    $sql = "INSERT INTO audioLog (audioLogID,audioLogTime,audioLogUsername,audioLogIP,audioLogItemID,audioLogURL) VALUES (0, NOW(),'$username','$IP','$itemID','$URL')";
    $result = mysql_query($sql) or die("Unable to insert search log.");

};

// takes an itemid and searches audiofolders database for an audio folder, then displays links for any files

function parseaudio2($itemID)
{
  $Listen         = "";
  $rootdirectory  = "audio";
  $rooturl        = "http://wrecked-distro.com";

  $username = $_SESSION["username"];
  
  dbConnect();

  $sql = "SELECT * FROM items WHERE itemID = $itemID";
  $result = mysql_query($sql);

  if ($myrow=mysql_fetch_array($result))
  {
   $audiodirectory = strtolower($rootdirectory."/".$myrow["folder"]);

    if (is_dir($audiodirectory))
    {
     $d = dir($audiodirectory);
      while ($entry=$d->read())
      {
        list($folder,$remainder) = explode("-",$entry);
        list($trackname,$extension) = explode(".",$remainder);  
        if ($extension == "rm") {
          $list =  "[<a href=\"rammaker.php?username=$username&amp;itemID=$itemID&amp;url=$rooturl/".$audiodirectory."/".$entry."\">".$trackname."</a>] ".$list;
        };

        if ($extension == "mp3") {
          $list =  "[<a href=\"$rooturl/".$audiodirectory."/".$entry."\" target=\"listen\">".$trackname."</a>] ".$list;
        };
      };
     $d->close(); 
     $list = $list."</b>";  
   }
  };
 return $list;
};

// returns the proper foldername given an itemid

function foldername2($itemID)
{
  dbConnect();
  $sql = "SELECT * FROM items WHERE itemid=$itemID";
  $result = mysql_query($sql);
  if ($myrow=mysql_fetch_array($result))
   {
      $label=$myrow["label"];
      $catalog=$myrow["catalog"]; 
      $foldername=$label.$catalog;
      return $foldername;   
   } else {
    return 0;
  };
}


function logAudio2($username,$itemID,$URL)
{
  dbConnect();
  if (!$username) {$username = " ";};
  $IP = getenv("REMOTE_ADDR");

  $sql = "INSERT INTO audioLog (audioLogID,audioLogTime,audioLogUsername,audioLogIP,audioLogItemID,audioLogURL) VALUES (0, NOW(),'$username','$IP','$itemID','$URL')";
  $result = mysql_query($sql) or die("Unable to insert search log.");
};


?>
