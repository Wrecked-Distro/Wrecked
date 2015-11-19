<?php

// keywords.php functions to handle keywords on wrecked

// includes & definitions

include_once("db.php");

// ShowKeywords: prints a sorted list of all keywords with a count of the items holding that keyword
 
function ShowKeywords()
{	
	GLOBAL $dbName;
	dbConnect("db9372_distro");

	$sql = "SELECT COUNT(keywordID) AS count, keyword FROM keywords GROUP BY keyword ORDER BY 
count DESC";
	$result = mysql_query($sql);
	if ($myrow = mysql_fetch_array($result))
	{
		do
		{
		echo $myrow["keyword"]."(".$myrow["count"].")<br>";
		} while ($myrow = mysql_fetch_array($result));
	};
	
};

// ShowKeyword: returns a space delimited list of keywords for a given item

function ShowKeyword($itemID)
{
        dbConnect("db9372_distro");

        $sql = "SELECT keyword FROM keywords WHERE itemID='$itemID' ORDER BY keyword DESC";
        $result = mysql_query($sql);
        if ($myrow = mysql_fetch_array($result))
        {
                do
                {
                $list = $list.$myrow["keyword"].",";
               } while ($myrow = mysql_fetch_array($result));
        };
	$list = substr($list,0,-1);
	
	return $list;
};

// AddKeywords: takes a comma seperated $field variable and adds the keywords not already added for an item

function AddKeywords($itemID, $addkeys)
{
 dbConnect("db9372_distro");
 
 $listkeys = explode(",",$addkeys);

 $sql1 = "DELETE FROM keywords WHERE itemID='$itemID'";
 $result1 = mysql_query($sql1);   
 
 while (list($key,$val) = each ($listkeys))
 {
   $val = strtolower(trim($val));

   if (!isKeyword($itemID,$val))
	{
	 $sql = "INSERT INTO keywords (keywordID, itemID, keyword) VALUES (0, '$itemID', '$val')";
	 $result = mysql_query($sql);
	 echo $val;
	};	
 };
};

// isKeyword: checks if an item is already associated with a keyword in the table

function isKeyword($itemID,$keyword)
{
 dbConnect("db9372_distro");

 $value=0;
 $sql = "SELECT keywordID FROM keywords WHERE itemID='$itemID' AND keyword='$keyword'";
 $result = mysql_query($sql); 
 if ($myrow = mysql_fetch_array($result))
 { $value = 1; };

 return $value;
};


// print a list of keywords by popularity in the table, which link to each keyword page
// generate an sql statement to pull all items given a keyword, sorted by restock date
// create an admin panel to add keywords

?>
