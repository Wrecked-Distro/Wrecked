<?php
// displayes new items summary on page

include_once("db.php");
include_once("header.php");
include_once("showPicks.php");

function showlist($start,$end)
{
  echo "<table style='width: 500px' class='stock'>
       <tr class=\"newrelease1\">
         <td><b>artist</b></td>
         <td><b>title</b></td>
         <td><b>label</b></td>
         <td><b>format</b></td>
         <td><b>cost</b></td>
       </tr>";

  dbConnect();

  $query = sprintf("SELECT * FROM items WHERE (released >= '%s' AND released <= '%s') OR (restocked >= '%s' AND restocked <= '%s') and `condition` = 'NEW'",$start, $end, $start, $end);

  $result = mysql_query($query);


  if ($myrow = mysql_fetch_array($result))
  {
    do
    {
      echo "<tr class='newrelease2'>";
      echo "<td>".htmlentities($myrow["artist"])."</td>";
      echo "<td><a class='newrelease' href=\"?module=viewitem.php&amp;command=command&amp;search=itemid&amp;keyword=".$myrow["itemid"]."\">".htmlentities($myrow["title"])."</a></td>";
      echo "<td>".htmlentities($myrow["label"])." ".$myrow["catalog"]."</td>";
      echo "<td>".$myrow["format"]."</td>";
      echo "<td>$".$myrow["retail"]."</td>";
      echo "</tr>";
    } while ($myrow=mysql_fetch_array($result));

  };
  echo "</table>";
};

?>

<table>
<tr>
<td style='padding: 3px; font-size: 9pt; font-family: verdana; vertical-align: top;'>
<b>who we are</b><p>
wrecked distro is a source for
records, CDs, DVDs and other media
operating out of pittsburgh, pa.<p>

we carry a wide variety of different music and related items specializing in:
breakcore, drill'n'bass, experimental hardcore, rhythmic noise,
ragga-jungle, weird dub, robot electro, melodic idm, alt hiphop, dubstep and bassline, post-rave,
fractured ambient and the many other uncategorizable variations of new electronic music.
we especially focus labels and artists with a diy outlook.<p>
we don't have  a storefront, but we sell in person, at shows,
through local shops, and primarily through mail-order through this site.<p>

questions about anything? <br>
drop an email to <a href="mailto:sales@wrecked-distro.com">sales@wrecked-distro.com</a><p>

<div>
<a href="wrecked-rss.php"><img src="feed-icon-28x28.png" alt=''> SUBSCRIBE TO THE NEW RELEASE RSS FEED</a><p>
</div>

<br>
</td>
<td style='vertical-align: top;'>
<b>news</b><br>

<?php

if (!$limit) {$limit=10;};

dbConnect();

$result = mysql_query("SELECT *, DATE_FORMAT(date,'%M %D, %Y') AS date_format FROM news ORDER BY date DESC LIMIT 10");

if ($myrow = mysql_fetch_array($result))
{
  do
  { 
    echo "<table><tr><td>";
    echo "<a href=\"#\" onclick=\"toggleHeight('".$myrow["newsid"]."')\"><i><b>".$myrow["date_format"]."</b></i></a><br>";
    echo "<div id=\"".$myrow["newsid"]."\">".$myrow["text"];
    if ($myrow["showstock"]) {
      echo "<P>";
      showlist($myrow["start"] ,$myrow["date"]);
    };
    echo "</div>";
    echo "</td></tr></table><p>";
  } while ($myrow=mysql_fetch_array($result));
};

?>
</td>
</tr>
</table>