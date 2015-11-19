<?php
// generates a valid XML file to used as an rss feed

include("db.php");

dbConnect();

$sql = "SELECT *, date_format(date,'%m/%d/%y') AS datef FROM events ORDER BY date DESC LIMIT 100";
$result = mysql_query($sql);

header("Content-Type: text/xml;charset=windows-1252");
echo "<rss version=\"2.0\" xmlns:media=\"http://search.yahoo.com/mrss/\">";

echo "<channel>";
echo "<title>Wrecked-Distro.com Events List</title>";
echo "<link>http://wrecked-distro.com/</link>";
echo "<description>Upcoming events and shows</description>";

if ($myrow = mysql_fetch_array($result))
{
	do
	{
		echo "<item>";
		echo "<title>";
		$title = $myrow["datef"]." ".$myrow["name"]." at ".$myrow["venue"];
		echo htmlspecialchars($title,ENT_NOQUOTES);
		echo "</title>";
		echo "<link>";
		echo htmlspecialchars("http://wrecked-distro.com/index.php?module=events.php&search=eventID&keyword=".$myrow["eventID"],ENT_NOQUOTES);
		echo "</link>";
		echo "<description>";
		echo htmlspecialchars($myrow["brief"],ENT_QUOTES);
		echo " $".$myrow["cost"]." / ".$myrow["ages"]." / ".$myrow["start"]." PM";
		echo "</description>";
		echo "</item>";
	} while ($myrow = mysql_fetch_array($result));
};

echo "</channel>";
echo "</rss>";
?>
