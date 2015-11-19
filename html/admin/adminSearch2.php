<?php
// adminsearch.php
// tool for monitoring what searches users are making

function countUniqueIP($scope)
{
 $sql = "SELECT COUNT(DISTINCT searchIP) as numberofIP FROM search WHERE TO_DAYS(CURRENT_DATE)<TO_DAYS(searchTime)+'$scope'";

 $result = mysql_query($sql) or die("Aunable to connect to database");
 $myrow = mysql_fetch_array($result);
 $numberofIP = $myrow["numberofIP"];

 return $numberofIP;
};

function countUniqueSearches($keyword, $scope)
{
		// returns the number of unique users who searched for a keyword
	 	if ($keyword!="") {
	  
		$sql = sprintf("SELECT COUNT(DISTINCT searchUsername) as numberofusers FROM search WHERE searchKeyword = '%s' AND searchUsername !='' AND  TO_DAYS(searchTime) + %s > TO_DAYS(CURRENT_DATE)", mysql_real_escape_string($keyword), $scope);

		$result = mysql_query($sql) or die("Unable to connect to database ".$sql); 

		$myrow = mysql_fetch_array($result);
		$numberofusers = $myrow["numberofusers"];
		  
		return $numberofusers;
	};
};

	$title = "Admin Saved Searches";
	
	echo "<b>".$title."</b>";
	if (!$scope) { $scope = "10000";};

	echo "<p>";
	echo "<a href=\"$PHP_SELF?scope=365\" ";
	if ($scope==365) { echo " style=\"background-color:cccccc;\"";};
	echo ">365</a> | ";
	echo "<a href=\"$PHP_SELF?scope=90\" ";
	if ($scope==90) { echo " style=\"background-color:cccccc;\"";};
	echo ">90</a> | ";
	echo "<a href=\"$PHP_SELF?scope=30\" ";
	if ($scope==30) { echo " style=\"background-color:cccccc;\"";};
	echo ">30</a> | ";
	echo "<a href=\"$PHP_SELF?scope=7\" ";
	if ($scope==7) { echo " style=\"background-color:cccccc;\"";};
	echo ">7</a> | ";
	echo "<a href=\"$PHP_SELF?scope=1\" ";
	if ($scope==1) { echo " style=\"background-color:cccccc;\"";};
	echo ">1</a> <br> ";



   if ($_REQUEST['submit'])
   {
    	// here if no ID then adding, else we're editing

		if ($_REQUEST['searchID'])
		{
			$sql = "UPDATE search SET searchTime='$searchTime', searchUsername='$searchUsername', 
			searchIP='$searchIP',searchType='$searchType',searchKeyword='$searchKeyword' WHERE searchID='$searchID'";
			echo "Update of ".$searchID."\n";
		}
		else
		{
			$sql = "INSERT INTO search (searchID,searchTime,searchUsername,searchIP,searchType,searchKeyword) 
			VALUES (0,NOW(),'$searchUsername','$searchIP','$searchType','$searchKeyword')";

			echo "Inserting ".$searchKeyword."\n";
		}

		// run SQL against the DB

		$result = mysql_query($sql);

		echo "Record updated.<p>";

		echo "<a href=\"$PHP_SELF?searchID=$searchID&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">more searches</a>";


     } elseif ($_REQUEST['delete']) {
      
       // delete a record

       $sql = "DELETE FROM search WHERE searchID='$searchID'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";
		echo "<a href=\"$PHP_SELF?sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">back</a>";
      
     } else {

      // this part happens if we don't press submit

     if (!$searchID) {

    	// print the list if there is not editing
     	$sql = "SELECT searchKeyword, COUNT(searchID) as totalKeyword, COUNT(DISTINCT searchIP) as totalIP FROM search WHERE  TO_DAYS(searchTime) + 2000 > TO_DAYS(CURRENT_DATE) GROUP BY searchKeyword ORDER BY totalKeyword DESC";

		echo "<div id='query'>Query: ".$sql."</div>";
		if ($result = mysql_query($sql))
		{
			echo "<table>\n";

			echo "<tr><td class='title1' colspan='5'><b>Keywords</b> (Unique IP ".countUniqueIP($scope).")</td></tr>\n";
			echo "<tr class='title2'> <td> <a href=\"$PHP_SELF?sort=searchKeyword&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">SearchKeyword</a></td>
			     <td> <a href=\"$PHP_SELF?sort=totalKeyword&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">SearchTotal</a></td>
			     <td> <a href=\"$PHP_SELF?sort=totalKeyword&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">SearchIP</a></td>
			     <td colspan='2'></td>
			     </tr>\n";
			while($myrow = mysql_fetch_array($result))
			{
				printf("<tr><td><a href='adminSearch.php?keyword=%s'>%s</a></td> <td>%s (%s users)</td><td> %s</td>", urlencode($myrow["searchKeyword"]), urlencode($myrow["searchKeyword"]),$myrow["totalKeyword"], countUniqueSearches($myrow["searchKeyword"], $scope), $myrow["totalIP"]);

				printf("<td><a href='%s?commentID=%s&amp;delete=yes&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc'>(DELETE)</a></td>
					   <td><a href='%s?commentID=%s&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc'>(EDIT)</a></td></tr>",
				$_SERVER['PHP_SELF'], urlencode($myrow["searchKeyword"]), $_SERVER['PHP_SELF'] , urlencode($myrow["searchKeyword"]));

			};
			echo "</table>\n";
		}
       
     echo "<p>";
       
     }
             
    ?>

<table>
<tr><td>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="sort" value="<? echo $sort;?>">
<input type="submit" name="show" value="Show&amp;nbsp;:" class="button1">
<input type="text" name="number" value="<? echo $number; ?>" class="form1">
rows beginning with number
<input type="text" name="lower" value="<? echo $lower; ?>" class="form1">
in
<select name="desc" class="form1">  
<option value="&amp;nbsp;" <? if ($desc!="DESC") echo " SELECTED ";?> >ASCENDING
<option value="DESC" <? if ($desc=="DESC") echo " SELECTED ";?> >DESCENDING
</select>
order.
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="number" value="<? echo $number; ?>">
<input type="hidden" name="lower" value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Next <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="number"  value="<? echo $number; ?>">
<input type="hidden" name="lower"  value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Previous <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="number"  value="<? echo $total[0]; ?>">
<input type="hidden" name="lower"  value="0">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Show All" class="button1">
</form>
</td>
</tr>
</table>
<?
};

?>
