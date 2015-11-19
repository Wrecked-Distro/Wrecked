<?php

// function to create a control table for any db

function controlTable($dbConnection,$table,$module,$schema,$search,$keyword,$lower,$number,$desc,$sort)
{
     // initialize local vars

	$sortArray = array(0=>"DESC",1=>"ASC");
	$primaryKey = key($schema);

  // create sql query
  $sql = "SELECT * FROM $table WHERE $search LIKE '%".$keyword."%'  ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower, $number";
 	$result = mysql_query($sql);

  echo "<div id='query'>Query: ".$sql."</div>";

	$total = mysql_num_rows($result);

     if ($myrow = mysql_fetch_array($result))
     {

       echo "<table>\n";

       echo "<tr class=\"title1\"><td colspan='6'><b>Current $title</b></td></tr>\n";
       echo "<tr class=\"title2\">";
        while (list($key,$val) = each($schema))
        {
                echo "<td><a href=\"$PHP_SELF?module=$module&amp;sort=$key&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."\">$val</a></td>";
        };
        echo "<td colspan='2'></td>";
       echo "</tr>\n";

       do
       {
        echo "<tr>";
        reset($schema);
        while (list($key,$val) = each($schema))
        {
          echo "<td>".$myrow[$key]."</td>";
        };
        echo "<td><a href=\"$PHP_SELF?$primaryKey=".$myrow[$primaryKey]."&amp;delete=yes&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">(DELETE)</a></td>";
        echo "<td><a href=\"$PHP_SELF?$primaryKey=".$myrow[$primaryKey]."&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">(EDIT)</a></td>";
        echo "</tr>";
       } while ($myrow=mysql_fetch_array($result));
       echo "</table>\n";
      }

     echo "<p>";

	// show nav buttons
echo "<table>
<tr><td>
<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">
<input type=\"hidden\" name=\"module\"  value=\"".$module."\" >
<input type=\"hidden\" name=\"sort\" value=\"".$sort."\">
<input type=\"submit\" name=\"show\" value=\"Show:\" class=button1>
<input type=\"text\" name=\"number\"  value=\"".$number."\" class=form1>
rows beginning with number
<input type=\"text\" name=\"lower\"  value=\"".$lower."\" class=form1>
in <select name=\"desc\" class=form1>
<option value=\"".$sortArray[$desc]."\" SELECTED ";
echo " class=form1>".$sortArray[$desc];
echo "<option value=\"".$sortArray[!$desc]."\"";

echo " >".$sortArray[!$desc]."</select> order.
</form>
</td>
<td>

<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">
<input type=\"hidden\" name=\"module\"  value=\"".$module."\" >
<input type=\"hidden\" name=\"number\"  value=\"".$number."\">
<input type=\"hidden\" name=\"lower\"  value=\"".($lower-$number)."\">
<input type=\"hidden\" name=\"desc\" value=\"".$desc."\">
<input type=\"hidden\" name=\"sort\" value=\"".$sort."\">
<input type=\"submit\" name=\"show\" value=\"< Previous ".$number."\" class=button1>

</form>
</td>

<td>
<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">
<input type=\"hidden\" name=\"module\"  value=\"".$module."\" >
<input type=\"hidden\" name=\"number\"  value=\"".$number."\">
<input type=\"hidden\" name=\"lower\"  value=\"".($lower+$number)."\">
<input type=\"hidden\" name=\"desc\" value=\"".$desc."\">
<input type=\"hidden\" name=\"sort\" value=\"".$sort."\">
<input type=\"submit\" name=\"show\" value=\" Next ".$number." >\" class=\"button1\">
</form>
</td>

<td>
<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">
<input type=\"hidden\" name=\"module\"  value=\"".$module."\" >
<input type=\"hidden\" name=\"number\"  value=\"".$total."\" >
<input type=\"hidden\" name=\"lower\"  value=\"0\">
<input type=\"hidden\" name=\"desc\" value=\"".$desc."\">
<input type=\"hidden\" name=\"sort\" value=\"".$sort."\">
<input type=\"submit\" name=\"show\" value=\"Show All ".$total."\" class=button1>
</form>
</td>
</tr>

</table>";
};

// admindiscount.php
// create, delete or edit discount options from the database  

   // initialize variables taht aren't passed to the script

	$pageName = "adminDiscount.php";
	$title = "Discount";
	$table = "discount";
	$primaryKey = "discountID";
  $module = $pageName;

	$schema = array("discountID" => "DiscountID", "discountNAME" => "Name", "discountVALUE" => "Value", "discountDESCRIPTION" => "Description"); 
	$schemaForm = array("discountID" => "input", "discountNAME" => "input", "discountVALUE" => "input", "discountDESCRIPTION" => 
"textarea"); 

	if (!$lower) {$lower = 0;};
 	if (!$number) {$number = 20;};
 	if (!$desc) {$desc = 0;};
 	if (!$_REQUEST['sort']) {$sort = $primaryKey;};
	if (!$_REQUEST['search']) {$search = $primaryKey;};
	if ($keyword) {$keyword = "";};

  // connect to the database
	echo "<b>$title admin</b>";
	echo "<P>";


   $result=mysql_query("SELECT COUNT($primaryKey) FROM $table");
   $total=mysql_fetch_array($result);

   if ($lower<0) {$lower=$total[0];};
   if ($lower>$total[0]) {$lower=0;};

   if ($submit)
   {
    // here if no ID then adding, else we're editing

     if ($$primaryKey)
     {
	   $sql = "UPDATE $table SET discountNAME='$discountNAME', discountVALUE='$discountVALUE', discountDESCRIPTION='$discountDESCRIPTION' WHERE $primaryKey='".$$primaryKey."'";
      echo "Update of ".$$primaryKey."\n";
     }
     else
     {
	     $valwrap = "'";

        foreach($schema AS $key => $value) {
           $ret[] = $valwrap.$$key.$valwrap;
        }

    	$valueString =  implode(",", $ret);
    	$keyString = implode(",", array_keys($schema));

    	$sql = "INSERT INTO $table (".$keyString.") VALUES (".$valueString.")";
    	
    	echo $sql;
     }
     // run SQL against the DB

      $result = mysql_query($sql);

      echo "Record updated.<p>";
    
      echo "<a href=\"$PHP_SELF?sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">back</a>";  

     } elseif ($delete) {
      
       // delete a record

       $sql = "DELETE FROM $table WHERE $primaryKey = '".$$primaryKey."'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";
       echo "<a href=\"$PHP_SELF?module=$module&sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">back</a>";
      
     } else {

      // this part happens if we don't press submit

     if (!$$primaryKey) {
 
      // print the table list if there is not editing
    	controlTable($dbConnection,$table,$module,$schema,$search,$keyword,$lower,$number,$desc,$sort);
     }
             

     echo "<p>";
     echo "<a href=\"$PHP_SELF\">ADD A $title</a>";             
     echo "<p>";             
     echo "<form method=\"post\" name=\"addform\" action=\"".$_SERVER['PHP_SELF']."\">";
       
      
     if ($$primaryKey)
     {  
        
     // editing so select a record
        
     $sql = "SELECT * FROM $table WHERE $primaryKey = '".$$primaryKey."'";
        echo "<div id='query'>Query: ".$sql."</div>";

     $result = mysql_query($sql);
        
     $myrow = mysql_fetch_array($result);

     extract($myrow);	
       
     // print the id for editing
     echo "<input type=\"hidden\" name=\"$primaryKey\" value=\"$$primaryKey\">";
     };

  	echo "Fill in all fields to add a new $title <br>     *'d fields are optional.<p>";
	echo "<table>";

	while (list($key,$val) = each($schema))
	{
		echo "<tr>";
		echo "<td class=\"text3\">".$val."</td>";
		echo "<td>";
		switch ($schemaForm[$key])
		{
			case "input":
					echo "<input type=\"Text\" name=\"$key\" value=\"".$myrow[$key]."\">\n";
					break;
			case "textarea":
					echo "<textarea name=\"$key\" rows=\"7\" cols=\"40\" >".$myrow[$key]."</textarea>\n";	
					break;
		};	
		echo "</td>";
		echo "</tr>";
	};


	echo "<tr><td colspan='2'>";

	echo "<input type=\"hidden\" name=\"sort\" value=\"$sort\">";
        echo "<input type=\"hidden\" name=\"lower\" value=\"$lower\">";
        echo "<input type=\"hidden\" name=\"number\" value=\"$number\">";
        echo "<input type=\"hidden\" name=\"desc\" value=\"$desc\">";
        echo "<input type=\"Submit\" name=\"submit\" value=\"Enter information\" class=\"button1\"></td></tr>";
	echo "</table>";
	echo "</form>";  
     }
?>
