<?php
// generates a valid XML file to used as an rss feed

include("db.php");
include("parseImageRSS.php");
include("parseaudioRSS.php");

dbConnect();

$sql = "SELECT * FROM items WHERE quantity > 0 ORDER BY restocked DESC LIMIT 100";
$result = mysql_query($sql);

header("Content-Type: text/xml;charset=utf-8");
echo "<rss version=\"2.0\"
xmlns:content=\"http://purl.org/rss/1.0/modules/content/\"
xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\"
xmlns:dc=\"http://purl.org/dc/elements/1.1/\"
xmlns:itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\"
xmlns:dtvmedia=\"http://participatoryculture.org/RSSModules/dtv/1.0\"
xmlns:media=\"http://search.yahoo.com/mrss/\"
>\n";
echo "<channel>\n";
echo "<title>Wrecked-Distro.com New Stock </title>\n";
echo "<link>http://wrecked-distro.com/</link>\n";
echo "<description>Updates on new releases</description>\n";
echo "<pubDate>".date("D, d M Y H:i:s")." EST</pubDate>\n"; 
if ($myrow = mysql_fetch_array($result))
{
	do
	{
		echo "<item>\n";
		echo "<title>\n";
		$title = $myrow["artist"]." - ".$myrow["title"]." - ".$myrow["label"]." ".$myrow["catalog"]." - ".$myrow["format"]." - $".$myrow["retail"];
		echo htmlspecialchars($title,ENT_NOQUOTES)."\n";
		echo "</title>\n";
		echo "<guid>http://wrecked-distro.com/rssitem.php?itemid=".$myrow["itemid"]."</guid>";
		echo "<link>\n";
	//	echo "http://wrecked-distro.com";
		echo "<![CDATA[http://wrecked-distro.com/index.php?module=viewitem3.php&search=itemid&keyword=".$myrow["itemid"]."]]>\n";
		echo "</link>\n";
		echo "<description>\n";
		echo "<![CDATA[".str_replace(array('\'','\"','/`','!'),"",$myrow["description"])."]]>\n";
		echo "</description>\n";
//		echo parseaudio($myrow["itemid"]);
		echo "</item>\n";
	} while ($myrow = mysql_fetch_array($result));
};

echo "</channel>";
echo "</rss>";
?>
