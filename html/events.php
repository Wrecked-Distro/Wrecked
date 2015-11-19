<?php
// events.php
// events listing
// set up for drop in use
// coded by geoff maddock cutups@rhinoplex.org 8.2.2001

if (!$performer) {$performer="";};
if (!$code) {$code="long";};

function showEvents($performer,$code)
{
	dbConnect();

	$sql = "SELECT *,DATE_FORMAT(date,'%c/%e/%y') as date, TO_DAYS(date) as days FROM events WHERE
	details LIKE \"%$performer%\" AND TO_DAYS(date)>=TO_DAYS(CURRENT_DATE()) ORDER BY days";

	$result = mysql_query($sql);

	if ($myrow=mysql_fetch_array($result))
	{
		echo "<table>";
		do
		{
			if ($code == "long") {
				echo "<tr><td>";
				echo $myrow["date"];
				echo "</td><td>";
				echo "<b>".htmlentities($myrow["name"])."</b> <br>".htmlentities($myrow["brief"])." <br> ".$myrow["venue"]." in ".$myrow["city"]." ".$myrow["ages"]." ".$myrow["start"]."PM $".$myrow["cost"];
				if ($myrow["link"] != "") { echo " <a href=\"".urlencode($myrow["link"])."\">".htmlentities($myrow["link"])."</a>"; };
				echo "</td></tr>";
			} else {
				echo "<tr><td>";
				echo $myrow["date"];
				echo "</td><td>";
				echo "<b>".htmlentities($myrow["name"])."</b> @ ".$myrow["venue"]." in ".$myrow["city"];
				if ($myrow["link"] != "") { echo " <a href=\"".urlencode($myrow["link"])."\">".htmlentities($myrow["link"])."</a>"; };
				echo "</td></tr>";
			};
		} while ($myrow = mysql_fetch_array($result));
			echo "</table>";
	} else 	{
		echo "No upcomming events.";
	};

};

function showPastEvents($performer,$code)
{
	dbConnect();

	$sql = "SELECT *,DATE_FORMAT(date,'%c/%e/%y') as date, TO_DAYS(date) as days FROM events WHERE TO_DAYS(date)<TO_DAYS(CURRENT_DATE()) AND details LIKE \"%$performer%\" ORDER BY days DESC";
	$result = mysql_query($sql);

	if ($myrow = mysql_fetch_array($result)) {

		echo "<table>";
		do
		{
			if ($code == "long") {
				echo "<tr><td>";
				echo $myrow["date"];
				echo "</td><td>";
				echo "<b>".htmlentities($myrow["name"])."</b> <br>".htmlentities($myrow["brief"])." <br> ".$myrow["venue"]." in ".$myrow["city"]." ".$myrow["ages"]." ".$myrow["start"]."PM $".$myrow["cost"];
				if ($myrow["link"] != "") { echo " <a href=\"".urlencode($myrow["link"])."\">".htmlentities($myrow["link"])."</a>"; };
				echo "</td></tr>";
			} else {
				echo "<tr><td>";
				echo $myrow["date"];
				echo "</td><td>";
				echo "<b>".htmlentities($myrow["name"])."</b> @ ".htmlentities($myrow["venue"])." in ".$myrow["city"];
				if ($myrow["link"] != "") { echo " <a href=\"".urlencode($myrow["link"])."\">".htmlentities($myrow["link"])."</a>"; };
				echo "</td></tr>";
			};
		} while ($myrow = mysql_fetch_array($result));
		echo "</table>";
	} else	{
		echo "No upcomming events.";
	};

};

?>
	<form name='events' action='<? echo $_SERVER['PHP_SELF']?>' method='post'>
		<input type=text name='performer' class='form1' value="<? echo $performer;?>">
		<input type=submit name='submit' value='search' class='button1'>
		<input type=hidden name='module' value="events.php">
		<input type=submit name='code' value='short' class='button1'>
		<input type=submit name='code' value='long' class='button1'>
	</form>

<a href="wrecked-events-rss.php"><img src="feed-icon-28x28.png" alt=''> Subscribe to the Events RSS Feed</a>

<p>
<b>FUTURE</b><p>
<?php
	showEvents($performer,$code);
?>

<p>
<b>HISTORY</b><p>
<?php
	showPastEvents($performer,$code);
?>

<p>

<b>PAST</b><p>
<a href="events/freqnasty"><img src="events/freqnasty/kittysmall2.jpg"  alt="freq nasty"></a>
<a href="events/gangstabass"><img src="events/gangstabass/gangstshortsm.gif" alt="gangstabass inc tour"></a>
<a href="events/sonicterror"><img src="flyers/sonic3smaller.jpg" alt="sonic terror"></a>
<a href="events/discomutants"><img src="events/discomutants/discomutantssm.gif" alt="disco mutants"></a>
<a href="events/zod"><img src="events/zod/zodflyersm.jpg"  alt="zod records @ the shadow lounge"></a>
<a href="events/smashingart"><img src="events/smashingart/smashingsm.jpg" alt="smashing art @ the warhol"></a>
<a href="events/ihate"><img src="events/ihate/ihatefullsm.gif" alt="i hate myself and want to die"></a>
<a href="http://www.412dnb.com/~cutup/events/soundclash/"><img src="events/soundclash/soundclashsm.jpg" alt="soundclash @ the rex"></a>
<a href="events/hackfest"><img src="events/hackfest/hackflyersm.gif"  alt="hackfest @ milk" ></a>
<a href="events/widerstand/widerstand.php"><img src="flyers/flyeranarchysm.jpg" alt="widerstand/peaceoff tour 2001"></a>
<br>
