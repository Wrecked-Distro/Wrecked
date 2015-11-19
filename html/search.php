<?php
   function logSearch($username, $type, $keyword)
   {
        dbConnect();
        if (!$username) { $username = " ";};

        $IP = getenv("REMOTE_ADDR");

        // disable itemid searches - they are rarely useful
        if ($type != 'itemid')
		{
		    $sql = "INSERT INTO search (searchID,searchTime,searchUsername,searchIP,searchType,searchKeyword) VALUES (0, NOW(),'$username','$IP','$type','$keyword')";
        	$result = mysql_query($sql) or die("Unable to insert search log. test");
        };
   };
?>
