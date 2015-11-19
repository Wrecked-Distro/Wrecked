<?php
// adminledger.php
// tool for sumarrizing sales for wrecked
// written by geoff maddock / cutups@rhinoplex.org
// updated july 25th 2005 / all rights reserved
    
// include("header.php"); 
// include("db.php");

// ledger functions for this module

/**
 * Returns all sales items restocked during the passed year
 * 
 * @param integer $year
 * @return array
 **/
function wholesaleYear($year)
{
	$sql = printf("SELECT *, YEAR(items.restocked) as year FROM sales_items, items WHERE year='%s' AND items.itemid = sales_items.itemid", $year);
  $result = mysql_fetch_array(mysql_query($sql)); 
};

/**
 * Returns the actual final cost for a discounted item
 * 
 * @param integer $sales_itemid
 * @return decimal
 **/
function discountCost($sales_itemid)
{
  $sql = printf("SELECT *, sales_items.quantity AS quantity FROM sales_items,items WHERE sales_itemid='%s' AND items.itemid=sales_items.itemid", $sales_itemid);
  $result = mysql_fetch_array(mysql_query($sql));

  $wholesale = $result["cost"];
  $retail = $result["retail"];
  $discount = $result["discount"];
  $quantity = $result["quantity"];

  switch ($discount)
  {
    case 1: $cost=$retail; break;
    case 2: $cost=$wholesale; break;
    case 3: $cost=($wholesale+.5); break;
    case 4: $cost=($wholesale+1); break;
    case 5: $cost=$wholesale; break;
    case 6: $cost=($retail*.90); break;
    case 7: $cost=($retail*.85); break;
    case 8: $cost=($retail*.80); break;
    case 9: $cost=($retail*.75); break;
    default: $cost=$retail;
  };

  $cost = ($cost*$quantity);

  return $cost;
};


/**
 * Returns the tax that a user should be paying on a total
 * 
 * @param string $username
 * @param decimal $total
 * @return decimal
 **/

function getTax($username, $total)
{
  $sql = printf("SELECT state FROM users WHERE username='%s'", $username);
  $result = mysql_fetch_array(mysql_query($sql));
  $state = $result["state"];

  if ($state=="PA") {
    return ($total*.07);
  } else {
    return 0;
  };
}

/**
 * Checks if all items on the passed order are confirmed
 * 
 * @param integer $sales_orderid
 * @return boolean
 **/

function itemConfirm($sales_orderid)
{
  $itemConfirm = 1;

  $sql = printf("SELECT confirm FROM sales_items WHERE sales_orderid='%s'", $sales_order_id);
  $result= mysql_query($sql);
  
  if ($myrow=mysql_fetch_array($result))
  {
   do
   {
    if ($myrow["confirm"] == 0) {
      $itemConfirm = 0;
    }
   } while ($myrow = mysql_fetch_array($result));
  };

  return $itemConfirm;
}

/**
 * Checks the confirm status of a sales order
 * 
 * @param integer $sales_orderid
 * @return boolean
 **/
function saleConfirm($sales_orderid)
{
  $sql = "SELECT confirm FROM sales_orders WHERE sales_orderid = '$sales_orderid'";
  $result = mysql_fetch_array(mysql_query($sql));

  $saleConfirm = $result["confirm"];

  return $saleConfirm;
}

function getSent($sales_orderid)
{

  $db = mysql_connect("localhost","root","");

  mysql_select_db("db9372_distro",$db);

  $sql="SELECT sent FROM sales_orders WHERE sales_orderid='$sales_orderid'";
  $result=mysql_fetch_array(mysql_query($sql,$db));
  $sent=$result["sent"];

  return $sent;
}


function getWholesale($sales_orderid)
{
 $sql="SELECT * FROM sales_items, items WHERE sales_orderid=$sales_orderid AND sales_items.itemid=items.itemid";
 $query=mysql_query($sql);
 $total=0;

 if ($result=mysql_fetch_array($query))
 {
 do 
 {
  $total=$total+$result["cost"];
 } while ($result=mysql_fetch_array($query));
 };
 return $total;
}

function getRetail($sales_orderid)
{
 $sql="SELECT * FROM sales_items, items WHERE sales_orderid = $sales_orderid AND sales_items.itemid=items.itemid";
 $query=mysql_query($sql);
 $total=0;

 if ($result=mysql_fetch_array($query))
 {
 do 
 {
  $total = $total + $result["retail"];
 } while ($result=mysql_fetch_array($query));
 };
 return $total;
}

// OUTPUT STARTS

echo "<b>WRECKED Distro Purchases vs Sales Orders</b>";

echo "<p>";

// initialize variables

$this_year = date("Y"); 

$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : "order_date";

$sday = isset($_REQUEST['sday']) ? $_REQUEST['sday'] : 1;
$smonth = isset($_REQUEST['smonth']) ? $_REQUEST['smonth'] : 1;
$syear = isset($_REQUEST['syear']) ? $_REQUEST['syear'] : $this_year;

$eday = isset($_REQUEST['eday']) ? $_REQUEST['eday'] : 31;
$emonth = isset($_REQUEST['emonth']) ? $_REQUEST['emonth'] : 12;
$eyear = isset($_REQUEST['eyear']) ? $_REQUEST['eyear'] : $this_year;

// $currentMonth = $smonth;

$day = $sday;
$month = $smonth;   
$year = $syear;

dbConnect("db9372_distro");

$result = mysql_query("SELECT COUNT(sales_orderid) FROM sales_orders");
$total = mysql_fetch_array($result);

if ($lower<0) {$lower=$total[0];};
if ($lower>$total[0]) {$lower=0;};

// print the list if there is not editing

$runningsales=0;
$runningsalestotal=0;
$runningtaxtotal=0;
$runningdistro=0;
$runningdistrototal=0;
$runninggrandtotal=0;

echo "<table>\n";

echo "<tr><td class='title1' colspan='12'><b>Current Sales Orders</b></td></tr>\n";
echo "<tr class=\"title2\">
     <td><a href=\"$PHP_SELF?sort=sales_orderid&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Month-Year</a></td>
     <td><a href=\"$PHP_SELF?sort=username&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Sales Orders</a></td>
     <td><a href=\"$PHP_SELF?sort=order_date&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Total Sales</a></td>
     <td><a href=\"$PHP_SELF?sort=order_date&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Wholesale</a></td>
     <td><a href=\"$PHP_SELF?sort=order_date&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Retail</a></td>
     <td><a href=\"$PHP_SELF?sort=order_date&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Net Items</a></td>
     <td><a href=\"$PHP_SELF?sort=order_date&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">PA Sales</a></td>
     <td><a href=\"$PHP_SELF?sort=order_date&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Taxable Sales</a></td>
     <td><a href=\"$PHP_SELF?sort=order_date&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Total Taxes</a></td>
     <td><a href=\"$PHP_SELF?sort=confirmed&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Distro Purchaces</a></td>
     <td><a href=\"$PHP_SELF?sort=paid_date&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Total Purchaces</a></td>
     <td><a href=\"$PHP_SELF?sort=paid&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Net Income</a></td>
     </tr>\n";

do
{
  $saleSQL = "SELECT SUM(order_cost) as salestotal, COUNT(*) as salesnumber FROM sales_orders WHERE YEAR(order_date)=$year AND MONTH(order_date)=$month AND paid=1 ORDER BY $sort ".$sortArray[$desc]." LIMIT  $lower, $number";

  $sales = mysql_query($saleSQL);
  $salesresult = 	mysql_fetch_array($sales);

  $salestotal = $salesresult["salestotal"];
  $salesnumber = $salesresult["salesnumber"];

  $item = mysql_query("SELECT sales_orderid, order_date FROM sales_orders WHERE YEAR(order_date)=$year AND MONTH(order_date)=$month AND paid=1");

  $subwholesale = 0;
  $subretail = 0;

  if ($itemresult=mysql_fetch_array($item))
	{
   do
	 {

	  $itemid = $itemresult["sales_orderid"];

	  $subitem = mysql_query("SELECT SUM(items.cost) AS wholesale, SUM(items.retail) AS retail, 
COUNT(sales_items.sales_itemid) AS 
itemtotal FROM items, sales_items WHERE sales_items.sales_itemid=items.itemid AND sales_items.sales_orderid='$itemid'");
	  $subitemresult = mysql_fetch_array($subitem) or die("unable to poll the database");
           
	  $subwholesale=$subwholesale+getWholesale($itemresult["sales_orderid"]);
	  $subretail=$subretail+getRetail($itemresult["sales_orderid"]);
	 } while ($itemresult=mysql_fetch_array($item));
	};

  $diffitem=$subretail-$subwholesale;

  $tax = mysql_query("SELECT SUM(order_cost) as ordertotal, SUM(tax_cost) as taxtotal, COUNT(*) as taxnumber FROM 
  sales_orders WHERE YEAR(order_date)=$year AND MONTH(order_date)=$month AND tax_cost>0 AND paid=1 ORDER BY $sort ".$sortArray[$desc]." LIMIT 
  $lower, $number");
  $taxresult = 	mysql_fetch_array($tax);

  $taxordertotal = $taxresult["ordertotal"];
  $taxtotal = $taxresult["taxtotal"];
  $taxnumber = $taxresult["taxnumber"];  

  $distro = mysql_query("SELECT SUM(order_cost) as distrototal, SUM(shipping_cost) as shiptotal, COUNT(*) as 
  distronumber FROM distro_orders WHERE YEAR(order_date)=$year AND MONTH(order_date)=$month");
  $distroresult = mysql_fetch_array($distro);

  $distrototal = $distroresult["distrototal"];
  $distronumber = $distroresult["distronumber"];

  $distrototal= $distrototal+$distroresult["shiptotal"];
  $difference=$salestotal-$distrototal;

  $estwhole=$salestotal/1.33;
  $estwhole=number_format($estwhole,2,'.','');
  $diffest=$salestotal-$estwhole;

  echo "<tr>";
  echo "<td>".$month." ".$year."</td>";
  echo "<td class='shade1'>".$salesnumber."</td>";
  echo "<td class='shade1'>$".$salestotal."</td>";
  echo "<td class='shade1'>$".$subwholesale." ($$estwhole)</td>";
  echo "<td class='shade1'>$".$subretail." ($$salestotal)</td>";
  echo "<td class='shade1'>$".$diffitem." ($$diffest)</td>";
  echo "<td class='shade2'>".$taxnumber."</td>";
  echo "<td class='shade2'>$".$taxordertotal."</td>";
  echo "<td class='shade2'>$".$taxtotal."</td>";
  echo "<td class='shade3'>".$distronumber."</td>";
  echo "<td class='shade3'>$".$distrototal."</td>";
  echo "<td>$".$difference."</td>";
  echo "</tr>";
  $month = $month + 1;
  $runningsales = $runningsales+$salesnumber;
  $runningsalestotal = $runningsalestotal+$salestotal;
  $runningtaxtotal = $runningtaxtotal+$taxtotal;
  $runningtaxordertotal = $runningtaxordertotal+$taxordertotal;
  $runningtax = $runningtax+$taxnumber;
  $runningdistro = $runningdistro+$distronumber;
  $runningdistrototal = $runningdistrototal+$distrototal;
  $runninggrandtotal = $runninggrandtotal+$difference;        
  $runningwholesaletotal = $runningwholesaletotal+$subwholesale;	
  $runningretailtotal = $runningretailtotal+$subretail;	
  $runningdiffitem = $runningdiffitem+$diffitem;	

  $runningestwhole=$runningestwhole+$estwhole;
  $runningdiffest=$runningdiffest+$diffest;	

} while ($month <= $emonth);

	echo "<tr><td><b>".$year." total</b></td>
<td class='shade1'><b>$".$runningsales."</b></td>
<td class='shade1'><b>$".$runningsalestotal."</b></td>
<td class='shade1'><b>$".$runningwholesaletotal." ($".$runningestwhole.")</b></td>
<td class='shade1'><b>$$runningretailtotal ($$runningsalestotal)</b></td>
<td class='shade1'><b>$$runningdiffitem ($$runningdiffest)</b></td>
<td class='shade2'><b>$runningtax</b></td> 
<td class='shade2'><b>$$runningtaxordertotal</b></td>
<td class='shade2'><b>$$runningtaxtotal</b></td> 
<td class='shade3'><b>$runningdistro</b></td>
<td class='shade3'><b>$$runningdistrototal</b></td> 
<td><b>$$runninggrandtotal</b></td></tr>";
echo "</table>\n";
echo "<p>";
?>

<table>
<tr><td>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="sort" value="<? echo $sort;?>">
<input type="submit" name="show" value="Show:" class=button1>
<input type="text" name="number" value="<? echo $number; ?>" class=form1>
rows beginning with number
<input type="text" name="lower" value="<? echo $lower; ?>" class=form1>
in
<select name="desc" class=form1>  
<option value="&amp;" <? if ($desc!="DESC") echo " SELECTED ";?> class=form1 >ASCENDING
<option value="DESC" <? if ($desc=="DESC") echo " SELECTED ";?> class=form1 >DESCENDING
</select>
order.
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="number" value="<? echo $number; ?>">
<input type="hidden" name="lower" value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Next <? echo $number;?>" class=button1>
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<input type="hidden" name="number" value="<? echo $number; ?>">
<input type="hidden" name="lower" value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Previous <? echo $number;?>" class=button1>
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" >
<input type="hidden" name="number" value="<? echo $total[0]; ?>">
<input type="hidden" name="lower" value="0">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Show All" class=button1>
</form>
</td>
</tr>
</table>
 <p>
         
 <a href="<?php echo $PHP_SELF?>">ADD A sales order</a>
         
 <p>
         
 <form method="post" name="addform" action="<?php echo $_SERVER['PHP_SELF']?>" >
   
 Fill in all fields to add a new sales order<br>     *'d fields are optional.<p>
 <table>
 

 <tr><td>
 Start Date *</td>

 <td>
 <select name="smonth" size="1">
 <option value="01" <? if ($smonth=="01") echo "selected"; ?>>Jan
 <option value="02" <? if ($smonth=="02") echo "selected"; ?>>Feb
 <option value="03" <? if ($smonth=="03") echo "selected"; ?>>Mar
 <option value="04" <? if ($smonth=="04") echo "selected"; ?>>Apr
 <option value="05" <? if ($smonth=="05") echo "selected"; ?>>May
 <option value="06" <? if ($smonth=="06") echo "selected"; ?>>Jun
 <option value="07" <? if ($smonth=="07") echo "selected"; ?>>Jul
 <option value="08" <? if ($smonth=="08") echo "selected"; ?>>Aug
 <option value="09" <? if ($smonth=="09") echo "selected"; ?>>Sep
 <option value="10" <? if ($smonth=="10") echo "selected"; ?>>Oct
 <option value="11" <? if ($smonth=="11") echo "selected"; ?>>Nov
 <option value="12" <? if ($smonth=="12") echo "selected"; ?>>Dec
 </select>


<select name="sday" size="1">
 <option value="01" <? if ($sday=="01") echo "selected"; ?>>01
 <option value="02" <? if ($sday=="02") echo "selected"; ?>>02
 <option value="03" <? if ($sday=="03") echo "selected"; ?>>03
 <option value="04" <? if ($sday=="04") echo "selected"; ?>>04
 <option value="05" <? if ($sday=="05") echo "selected"; ?>>05   
 <option value="06" <? if ($sday=="06") echo "selected"; ?>>06   
 <option value="07" <? if ($sday=="07") echo "selected"; ?>>07   
 <option value="08" <? if ($sday=="08") echo "selected"; ?>>08   
 <option value="09" <? if ($sday=="09") echo "selected"; ?>>09   
 <option value="10" <? if ($sday=="10") echo "selected"; ?>>10
 <option value="11" <? if ($sday=="11") echo "selected"; ?>>11
 <option value="12" <? if ($sday=="12") echo "selected"; ?>>12
 <option value="13" <? if ($sday=="13") echo "selected"; ?>>13
 <option value="14" <? if ($sday=="14") echo "selected"; ?>>14
 <option value="15" <? if ($sday=="15") echo "selected"; ?>>15
 <option value="16" <? if ($sday=="16") echo "selected"; ?>>16
 <option value="17" <? if ($sday=="17") echo "selected"; ?>>17
 <option value="18" <? if ($sday=="18") echo "selected"; ?>>18
 <option value="19" <? if ($sday=="19") echo "selected"; ?>>19
 <option value="20" <? if ($sday=="20") echo "selected"; ?>>20
 <option value="21" <? if ($sday=="21") echo "selected"; ?>>21
 <option value="22" <? if ($sday=="22") echo "selected"; ?>>22
 <option value="23" <? if ($sday=="23") echo "selected"; ?>>23
 <option value="24" <? if ($sday=="24") echo "selected"; ?>>24
 <option value="25" <? if ($sday=="25") echo "selected"; ?>>25
 <option value="26" <? if ($sday=="26") echo "selected"; ?>>26
 <option value="27" <? if ($sday=="27") echo "selected"; ?>>27   
 <option value="28" <? if ($sday=="28") echo "selected"; ?>>28   
 <option value="29" <? if ($sday=="29") echo "selected"; ?>>29   
 <option value="30" <? if ($sday=="30") echo "selected"; ?>>30   
 <option value="31" <? if ($sday=="31") echo "selected"; ?>>31   
 </select>
 
 
 <select name="syear" size="1">
 <? for ($i=1990;$i<=date("Y",time());$i++)
 { echo "<option ";
   if ($syear==$i) echo "selected";
   echo ">".$i;
 };
 ?>
 </select>
 </td></tr>

 <tr><td>
 End Date 
 <input type="checkbox" value="1" name="end" <? if ($end==1) echo " 
CHECKED"; ?> >
 </td>

 <td>
 <select name="emonth" size="1">
 <option value="01" <? if ($emonth=="01") echo "selected"; ?>>Jan
 <option value="02" <? if ($emonth=="02") echo "selected"; ?>>Feb
 <option value="03" <? if ($emonth=="03") echo "selected"; ?>>Mar
 <option value="04" <? if ($emonth=="04") echo "selected"; ?>>Apr
 <option value="05" <? if ($emonth=="05") echo "selected"; ?>>May
 <option value="06" <? if ($emonth=="06") echo "selected"; ?>>Jun
 <option value="07" <? if ($emonth=="07") echo "selected"; ?>>Jul
 <option value="08" <? if ($emonth=="08") echo "selected"; ?>>Aug
 <option value="09" <? if ($emonth=="09") echo "selected"; ?>>Sep
 <option value="10" <? if ($emonth=="10") echo "selected"; ?>>Oct
 <option value="11" <? if ($emonth=="11") echo "selected"; ?>>Nov
 <option value="12" <? if ($emonth=="12") echo "selected"; ?>>Dec
 </select>

<select name="eday" size="1">
 <option value="01" <? if ($eday=="01") echo "selected"; ?>>01
 <option value="02" <? if ($eday=="02") echo "selected"; ?>>02
 <option value="03" <? if ($eday=="03") echo "selected"; ?>>03
 <option value="04" <? if ($eday=="04") echo "selected"; ?>>04
 <option value="05" <? if ($eday=="05") echo "selected"; ?>>05   
 <option value="06" <? if ($eday=="06") echo "selected"; ?>>06   
 <option value="07" <? if ($eday=="07") echo "selected"; ?>>07   
 <option value="08" <? if ($eday=="08") echo "selected"; ?>>08   
 <option value="09" <? if ($eday=="09") echo "selected"; ?>>09   
 <option value="10" <? if ($eday=="10") echo "selected"; ?>>10
 <option value="11" <? if ($eday=="11") echo "selected"; ?>>11
 <option value="12" <? if ($eday=="12") echo "selected"; ?>>12
 <option value="13" <? if ($eday=="13") echo "selected"; ?>>13
 <option value="14" <? if ($eday=="14") echo "selected"; ?>>14
 <option value="15" <? if ($eday=="15") echo "selected"; ?>>15
 <option value="16" <? if ($eday=="16") echo "selected"; ?>>16
 <option value="17" <? if ($eday=="17") echo "selected"; ?>>17
 <option value="18" <? if ($eday=="18") echo "selected"; ?>>18
 <option value="19" <? if ($eday=="19") echo "selected"; ?>>19
 <option value="20" <? if ($eday=="20") echo "selected"; ?>>20
 <option value="21" <? if ($eday=="21") echo "selected"; ?>>21
 <option value="22" <? if ($eday=="22") echo "selected"; ?>>22
 <option value="23" <? if ($eday=="23") echo "selected"; ?>>23
 <option value="24" <? if ($eday=="24") echo "selected"; ?>>24
 <option value="25" <? if ($eday=="25") echo "selected"; ?>>25
 <option value="26" <? if ($eday=="26") echo "selected"; ?>>26
 <option value="27" <? if ($eday=="27") echo "selected"; ?>>27   
 <option value="28" <? if ($eday=="28") echo "selected"; ?>>28   
 <option value="29" <? if ($eday=="29") echo "selected"; ?>>29   
 <option value="30" <? if ($eday=="30") echo "selected"; ?>>30   
 <option value="31" <? if ($eday=="31") echo "selected"; ?>>31   
 </select>
 

 
 <select name="eyear" size="1">
 <?php for ($i=1990;$i<=date("Y",time());$i++)
 { echo "<option ";
   if ($eyear==$i) echo "selected";
   echo ">".$i;
 };
 ?>
 </select>
 </td></tr>

 <tr>
  <td colspan='2'>
    <input type="hidden" name="sort" value="<?php echo $sort ?>">
    <input type="hidden" name="lower" value="<?php echo $lower ?>">
    <input type="hidden" name="number" value="<?php echo $number ?>">
    <input type="hidden" name="desc" value="<?php echo $desc ?>">
    <input type='hidden' name='module' value='adminledger.php'>
    <input type="Submit" name="submit" value="Enter information" class='button1'>
  </td>
</tr>
 
 </table>
 </form>  
