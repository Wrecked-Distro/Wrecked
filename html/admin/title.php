<?php include("header.php");?>


<table width=500>
<tr> <td align=left>

<font class="bigtitle"><b>WRECKED</b><br>
<font size=4>
<b>
<?
srand ((double) microtime() * 10000000);

$imagelist = array("DIY ELECTRONICS & NOISE",
"EMERGENCY MEDIA DISTRIBUTION", "PITTSBURGH VINYL SYNDICATE");
$randomkey=array_rand($imagelist);

echo "$imagelist[$randomkey]";
?>

</font><br>
</center>
</td> </tr>

</table>

</body>
</html>
