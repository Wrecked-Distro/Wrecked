<? 
include("header.php"); 
include("db.php"); 

?>
<html>
<head>
<title>to do list</title>
</head>

<body>

<b><a href="<? echo $PHP_SELF?>">TO DO LIST</a></b>

<p>

<?php

  function showLists($sort,$desc,$lower,$number )
  {
	$number=10;
	$sort=date;
	dbConnect("cutup");
	$result = mysql_query("SELECT *, DATE_FORMAT(date,'%m/%d/%y') AS date_format FROM freetext ORDER BY $sort $desc LIMIT 
$lower, $number");
	
	if ($myrow=mysql_fetch_array($result))
	{
	echo "<td valign=top>";
 echo " <b>SAVED</b><br>";

		do 
		{
		$textID=$myrow["textID"];
		$freetag=$myrow["freetag"];
		$date=$myrow["date_format"];
		echo "<i>$date_format</i> <a href=\"$PHP_SELF?textID=$textID\">$freetag</a> <br>";
		} while ($myrow=mysql_fetch_array($result));
	echo "</td>";
	};
    
  };
	


  $database="cutup";
  $table="freetext";
  $primary_key="date";


   if (!$lower) {$lower=0;};
   if (!$number) {$number=1;};
   if (!$desc) {$desc="DESC";};
   if (!$sort) {$sort=$primary_key;};

   dbConnect($database);

   $result=mysql_query("SELECT COUNT($primary_key) FROM $table");
   $total=mysql_fetch_array($result);

   if ($lower<0) {$lower=$total[0];};
   if ($lower>$total[0]) {$lower=0;};


   if ($submit || $archive || $delete)
   {
    // here if no ID then adding, else we're editing

      if ($archive)
    {  
  $sql = "INSERT INTO freetext (textID, freetag, freetext, date) VALUES (0,'$freetag','$freetext',now())";
	echo "archive inserting ";}
     else if ($delete)
	{   $sql = "DELETE FROM freetext WHERE textID='$textID'";
;}
	else
    {
   $sql = "UPDATE $table SET freetext='$freetext', freetag='$freetag', date=now() WHERE textID='$textID'";
  echo "updated existing ".$textID;
};
     // run SQL against the DB

      $result = mysql_query($sql);

      echo "Record updated.<p>";
    
      echo "<a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";  

     };

      // this part happens if we don't press submit

    // print the list if there is not editing

	if ($textID)
	{$result = mysql_query("SELECT *, DATE_FORMAT(date,'%m/%s/%y') AS date_format FROM $table WHERE textID=$textID");}
	else
	{$result = mysql_query("SELECT *, DATE_FORMAT(date,'%m/%d/%y') AS date_format FROM $table ORDER BY $sort $desc LIMIT 
$lower, $number");};

     if ($myrow = mysql_fetch_array($result))
     {
      

       echo "<table border=0 cellspacing=0 cellpadding=0>\n";
       do
       {
$textid=$myrow["textID"];
$freetag=$myrow["freetag"];
echo  "<tr><td><form action=\"admintodo.php\" method=\"post\"><textarea name=\"freetext\" rows=\"25\" 
cols=\"100\" 
style=\"font-family:impact,helvetica,courier;font-size:12pt;color:0066ff;background-color:dddddd\">".$myrow["freetext"]."</textarea></td>";
 showLists($sort,$desc,$lower,$number);
echo "</tr>";
 echo "<tr><td><input type=\"hidden\" name=\"textID\" value=\"$textid\"><input type=\"submit\" name=\"submit\" 
value=\"Save&nbsp;:\" class=\"button1\">";
 echo "<input type=\"submit\" name=\"archive\" 
value=\"Archive&nbsp;:\" class=\"button1\">
<input type=text name=\"freetag\" value=\"$freetag\" class=\"form1\"></input>
<input type=\"submit\" name=\"delete\" value=\"delete\" class=\"button1\" >";

 echo "</form></td>";
 echo "</tr>";
       } while ($myrow=mysql_fetch_array($result));
       echo "</table>\n";
      }
      else
     { 
        echo  "<tr><td><form action=\"$PHPSELF\" method=\"post\"><textarea name=\"freetext\" rows=\"25\"
cols=\"100\"></textarea></form></td><tr>";
 echo "<tr><td><input type=\"submit\" name=\"show\" value=\"Show&nbsp;:\" class=\"button1\">";
 echo "</form></td></tr>";
	};
             
    ?>
     
</body>
     
</html>
