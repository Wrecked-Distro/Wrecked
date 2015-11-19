<?include("header.php")?>
<b><font size=+1>Delete Order </b>

<? 

echo $sales_orderid."<p>";
echo "<a 
href=\"adminsales_orders.php?sales_orderid=$sales_orderid&delete=yes&sort=$sort&lower=$lower&number=$number&desc=$desc\" 
target=\"main\">YES</a> ";

?>
/
<a href="Javascript:window.close()">NO</a>
