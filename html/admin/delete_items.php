<?include("header.php")?>
<b><font size=+1>Delete Item</b>

<? 

echo $itemid."<p>";
echo "<a 
href=\"adminitem.php?itemid=$itemid&delete=yes&sort=$sort&lower=$lower&number=$number&desc=$desc\" target=\"B\">YES</a> ";

?>
/
<a onClick="javascript:window.close()">NO</a>
