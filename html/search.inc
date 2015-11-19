<?php
// search.inc 
// a php class for handling searches
// written by gmaddock@expedient.com / geoff maddock


// to add: will just add searches to database initially
// want to track search history for users?
// store favorite searches? 
// return summary of most popular search 

include_once("db.php");

// declare class Search

class Search {

	var $type;
	var $keyword;
	var $username;
	var $sql;

	function Search($username,$type,$keyword)
	{
	 $this->username = $username;
	 $this->type = $type;
	 $this->keyword = $keyword;
	 $IP = getenv("REMOTE_ADDR");
	 
	}

	function Result($database, $table)
	{
		dbConnect($database);
		$sql = "SELECT $this->type LIKE $this->keyword FROM $table"; 
		$result = mysql_query($this->$sql) or die("Unable to handle results.");
	}

	function Save()
	{
		dbConnect($database, $table);
		$sql = "INSERT INTO $table (searchID,searchTime,searchUsername,searchIP,searchType,searchKeyword)";
		$result = mysql_query($sql) or die("Save Failed.");
	}

};

