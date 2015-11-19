<?php
include("header.php");
include("db.php");
?>
<html>
<head>
<title>item view</title>
</head>

<body>

<b>INVENTORY VALUE</b>

<p>

<?php

   $total = 0;

   dbConnect();

   $result = mysql_query("SELECT * FROM items WHERE quantity > 0");

   if ($myrow = mysql_fetch_array($result))
     {

	do
	{
		$total += $myrow["cost"]*$myrow["quantity"];

       } while ($myrow=mysql_fetch_array($result));

	echo "<tr><td colspan=7></td><td>$".$total."</td><td> $".$total*1.33."</td></tr>";
       echo "</table>\n";
      };

?>

</body>

</html>
