<? //include("header.php"); 
   //include("db.php");


function getTax($username,$total)
{

  dbConnect("db9372_distro");	

  $sql = "SELECT state FROM users WHERE username='$username'";
  $result = mysql_fetch_array(mysql_query($sql));
  $state = $result["state"];

 if ($state=="PA")
 { return ($total*.07);}
 else
 { return 0;};
}

?>

<body name="main">

<b>Expense Items Admin</b>

<p>

<?php

   if (!$lower) {$lower = 0;};
   if (!$number) {$number = 20;};
   if (!$desc) {$desc = "DESC";};
   $sort = "expenseID";

   dbConnect("db9372_distro");

   $result = mysql_query("SELECT COUNT(expenseID) as total FROM expenses");
   $total = mysql_fetch_array($result);

   $ordercount = $total["total"];

   if ($lower<0) {$lower=$total[0];};
   if ($lower>$ordercount) {$lower=0;};

   if ($_REQUEST['submit'])
   {
    // here if no ID then adding, else we're editing
  
     if ($_REQUEST['expenseID'])
     {
 
      $sql = "UPDATE expenses SET vendor='".$_REQUEST['vendor']."', shipping_cost='".$_REQUEST['shipping_cost']."', tax_cost='".$_REQUEST['tax_cost']."',order_cost='".$_REQUEST['order_cost']."', description='".$_REQUEST['description']."' WHERE expenseID='".$_REQUEST['expenseID']."'";

	echo "Update of ".$_REQUEST['expenseID']."\n";
	}
     else
     {
  $sql = "INSERT INTO expenses (expenseID, vendor, order_cost, shipping_cost, tax_cost, tax, wholesale,order_date, recieved_date, description) VALUES
(0,'".$_REQUEST['vendor']."','".$_REQUEST['order_cost']."','".$_REQUEST['shipping_cost']."','".$_REQUEST['tax_cost']."','".$_REQUEST['tax']."','".$_REQUEST['wholesale']."','".$_REQUEST['oyear']+$_REQUEST['omonth']+$_REQUEST['oday']."','".$_REQUEST['description']."')";

      echo "inserting ".$description."\n";

     };
     // run SQL against the DB

      $result = mysql_query($sql);

      echo "Record updated. <p>".$sql;
    
      echo "<a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";  

     } elseif ($_REQUEST['delete']) {
      
       // delete a record

       $sql = "DELETE FROM expenses WHERE expenseID='".$_REQUEST['expenseID']."'";
       $result = mysql_query($sql);
       
       echo "$expenseID Record deleted!<p>";

      echo "<a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";

      
     } else {

      // this part happens if we don't press submit

     if (!$_REQUEST['expenseID']) {
    // print the list if there is not editing

     $sql = "SELECT *, DATE_FORMAT(order_date,'%m/%d/%y') AS order_date, 
DATE_FORMAT(recieved_date,'%m/%d/%y') AS recieved_date FROM expenses ORDER BY 
$sort $sortArray[$desc] LIMIT $lower, $number";
	echo $sql;
	$result = mysql_query($sql);

     if ($myrow = mysql_fetch_array($result))
     {
      
       echo "<table border=0 cellspacing=0 cellpadding=3>\n";
     
       echo "<tr><td class=\"title1\" colspan=10><b>Expenses</b></font></td></tr>\n";
       echo "<tr class=\"title2\">
             <td>
<a href=\"$PHP_SELF?sort=sales_orderid&lower=$lower&number=$number&desc=$desc\">ExpenseID</a></td>
             <td>
<a href=\"$PHP_SELF?sort=vendor&lower=$lower&number=$number&desc=$desc\">Vendor</a></td>
             <td>
<a href=\"$PHP_SELF?sort=order_cost&lower=$lower&number=$number&desc=$desc\">Order_Cost</a></td>
             <td>
<a href=\"$PHP_SELF?sort=shipping_cost&lower=$lower&number=$number&desc=$desc\">Shipping_Cost</a></td>
             <td>
<a href=\"$PHP_SELF?sort=tax_cost&lower=$lower&number=$number&desc=$desc\">Tax_Cost</a></td>
             <td>
<a href=\"$PHP_SELF?sort=tax&lower=$lower&number=$number&desc=$desc\">Tax</a></td>
             <td>
<a href=\"$PHP_SELF?sort=wholesale&lower=$lower&number=$number&desc=$desc\">Wholesale</a></td>
             <td>
<a href=\"$PHP_SELF?sort=order_date&lower=$lower&number=$number&desc=$desc\">Order&nbsp;Date</a></td>
             <td>
<a href=\"$PHP_SELF?sort=order_date&lower=$lower&number=$number&desc=$desc\">Recieved&nbsp;Date</a></td>
             <td>
<a href=\"$PHP_SELF?sort=description&lower=$lower&number=$number&desc=$desc\">Description</a></td>
       
             </tr>\n";
      
       do
       {

        $fontcolor="text2";

        printf("<tr valign=top>\n<td> <font class=\"$fontcolor\">%s</font></td>\n  <td><font 
class=\"$fontcolor\">%s</a></font></td>\n 
<td><font class=$fontcolor>$%s</font></td>\n
<td><font class=$fontcolor>$%s</font></td>\n
<td><font class=$fontcolor>$%s</font></td>\n
",$myrow["expenseID"], 
$myrow["vendor"],$myrow["order_cost"],$myrow["shipping_cost"],$myrow["tax_cost"]);

        if ($myrow["tax"]==1) {echo "<td><font class=$fontcolor>Yes</font></td>\n";} else {echo 
"<td><font 
class=$fontcolor>No</font></td>\n";};

        if ($myrow["wholesale"]==1) {echo "<td><font class=$fontcolor>Yes</font></td>\n";} else {echo 
"<td><font 
class=$fontcolor>No</font></td>\n";};

        printf("<td><font class=\"$fontcolor\">%s</font></td>\n <td><font class=\"$fontcolor\">$%s</font></td> <td><font 
class=\"$fontcolor\">%s</font></td>", 
$myrow["order_date"], $myrow["recieved_date"], $myrow["description"]);
        
        printf("<td><a 
href=\"%s?expenseID=%s&delete=yes&sort=$sort&lower=$lower&number=$number&desc=$desc\">(DELETE)</a></td>
<td><a href=\"?module=$module&expenseID=%s&sort=$sort&lower=$lower&number=$number&desc=$desc\">(EDIT)</a></td>",
$PHP_SELF,$myrow["expenseID"],$myrow["expenseID"]);

       } while ($myrow = mysql_fetch_array($result));
       echo "</table>\n";
      }
       
     echo "<p>";
       
     }
             
    ?>

<table>
<tr><td>
<form action="<? echo $PHP_SELF;?>" method="post">
<input type="hidden" name="sort" value="<? echo $sort;?>">
<input type="submit" name="show" value="Show&nbsp;:" class="button1">
<input type="text" name="number" size="3" value="<? echo $number; ?>" class="form1">
rows beginning with number
<input type="text" name="lower" size="3" value="<? echo $lower; ?>" class="form1">
in
<select name="desc">  
<option value="&nbsp;" <? if ($desc!="DESC") echo " SELECTED ";?> class="form1" >ASCENDING
<option value="DESC" <? if ($desc=="DESC") echo " SELECTED ";?> class="form1">DESCENDING
</select>
order.
</form>
</td>
<td>
<form action="<? echo $PHP_SELF;?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Previous <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<? echo $PHP_SELF;?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Next <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $ordercount; ?>">
<input type="hidden" name="lower" size="3" value="0">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Show All" class="button1">
</form>
</td>
</tr>
</table>
     <p>
             
     <a href="<?php echo $PHP_SELF?>">ADD an expense</a>
             
     <p>
             
     <form method="post" name="addform" action="<?php echo $PHP_SELF?>" >
       
     <?
      
     if ($_REQUEST['expenseID'])
     {  
        
     // editing so select a record
        
     $sql = "SELECT * FROM expenses WHERE expenseID='".$_REQUEST['expenseID']."'";
    
     $result = mysql_query($sql);
        
     $myrow = mysql_fetch_array($result);
       
     $vendor = $myrow["vendor"];
     
     $order_cost = $myrow["order_cost"];
      
     $shipping_cost = $myrow["shipping_cost"];

     $tax_cost = $myrow["tax_cost"];
        
     $tax = $myrow["tax"];

     $wholesale = $myrow["wholesale"];

     $order_date = $myrow["order_date"];

     $recieved_date = $myrow["recieved_date"];

     $description = $myrow["description"];
     
      
     // print the id for editing
     
     ?>
     
     <input type=hidden name="expenseID" value="<?php echo $expenseID ?>">
     
     <?
     }

     ?>

     Fill in all fields to add a new expense<br>     *'d fields are optional.<p>

     <? echo "<font size=+1>Expense# ".$expenseID."</font><p>";?>


     <table>

     <tr><td>   
     <font class="text3">
     Vendor
     </td>
     <td>
     <input type="Text" name="vendor" value="<? echo $myrow["vendor"] ?>">
     </td>
     </tr>   

     <tr><td>   
     <font class="text3">
     Order Cost
     </td>
     <td>
     <input type="Text" name="order_cost" value="<? echo $myrow["order_cost"] ?>">
     </td>
     </tr>   

     <tr><td>   
     <font class="text3">
     Shipping Amount
     </td>
     <td>
     <input type="Text" name="shipping_cost" value="<? echo $myrow["shipping_cost"] ?>">
     </td>
     </tr>   

     <tr><td>   
     <font class="text3">
     Tax Amount
     </td>
     <td>
     <input type="Text" name="tax_cost" value="<? echo $myrow["tax_cost"] ?>">
     </td>
     </tr>   

     <tr><td>
     <font class="text3">
     Tax Paid?</td>

     <td>
     <select name="tax" size="1">
     <option value="0" <? if ($tax=="0") echo "selected"; ?>>No
     <option value="1" <? if ($tax=="1") echo "selected"; ?>>Yes
     </select>
     </td></tr>            

     <tr><td>
     <font class="text3">
     Wholesale Price?</td>

     <td>
     <select name="wholesale" size="1">
     <option value="0" <? if ($wholesale=="0") echo "selected"; ?>>No
     <option value="1" <? if ($wholesale=="1") echo "selected"; ?>>Yes
     </select>
     </td></tr>            

     
     <tr><td>
     <font class="text3">
     Order Date *</td>
        <?
        $omonth=date("m",strtotime($myrow["order_date"]));
        $oday=date("d",strtotime($myrow["order_date"]));
        $oyear=date("Y",strtotime($myrow["order_date"]));
        ?>


     <td>
     <select name="omonth" size="1">
     <option value="01" <? if ($omonth=="01") echo "selected"; ?>>Jan
     <option value="02" <? if ($omonth=="02") echo "selected"; ?>>Feb
     <option value="03" <? if ($omonth=="03") echo "selected"; ?>>Mar
     <option value="04" <? if ($omonth=="04") echo "selected"; ?>>Apr
     <option value="05" <? if ($omonth=="05") echo "selected"; ?>>May
     <option value="06" <? if ($omonth=="06") echo "selected"; ?>>Jun
     <option value="07" <? if ($omonth=="07") echo "selected"; ?>>Jul
     <option value="08" <? if ($omonth=="08") echo "selected"; ?>>Aug
     <option value="09" <? if ($omonth=="09") echo "selected"; ?>>Sep
     <option value="10" <? if ($omonth=="10") echo "selected"; ?>>Oct
     <option value="11" <? if ($omonth=="11") echo "selected"; ?>>Nov
     <option value="12" <? if ($omonth=="12") echo "selected"; ?>>Dec
     </select>

    <select name="oday" size="1">
     <option value="01" <? if ($oday=="01") echo "selected"; ?>>01
     <option value="02" <? if ($oday=="02") echo "selected"; ?>>02
     <option value="03" <? if ($oday=="03") echo "selected"; ?>>03
     <option value="04" <? if ($oday=="04") echo "selected"; ?>>04
     <option value="05" <? if ($oday=="05") echo "selected"; ?>>05   
     <option value="06" <? if ($oday=="06") echo "selected"; ?>>06   
     <option value="07" <? if ($oday=="07") echo "selected"; ?>>07   
     <option value="08" <? if ($oday=="08") echo "selected"; ?>>08   
     <option value="09" <? if ($oday=="09") echo "selected"; ?>>09   
     <option value="10" <? if ($oday=="10") echo "selected"; ?>>10
     <option value="11" <? if ($oday=="11") echo "selected"; ?>>11
     <option value="12" <? if ($oday=="12") echo "selected"; ?>>12
     <option value="13" <? if ($oday=="13") echo "selected"; ?>>13
     <option value="14" <? if ($oday=="14") echo "selected"; ?>>14
     <option value="15" <? if ($oday=="15") echo "selected"; ?>>15
     <option value="16" <? if ($oday=="16") echo "selected"; ?>>16
     <option value="17" <? if ($oday=="17") echo "selected"; ?>>17
     <option value="18" <? if ($oday=="18") echo "selected"; ?>>18
     <option value="19" <? if ($oday=="19") echo "selected"; ?>>19
     <option value="20" <? if ($oday=="20") echo "selected"; ?>>20
     <option value="21" <? if ($oday=="21") echo "selected"; ?>>21
     <option value="22" <? if ($oday=="22") echo "selected"; ?>>22
     <option value="23" <? if ($oday=="23") echo "selected"; ?>>23
     <option value="24" <? if ($oday=="24") echo "selected"; ?>>24
     <option value="25" <? if ($oday=="25") echo "selected"; ?>>25
     <option value="26" <? if ($oday=="26") echo "selected"; ?>>26
     <option value="27" <? if ($oday=="27") echo "selected"; ?>>27   
     <option value="28" <? if ($oday=="28") echo "selected"; ?>>28   
     <option value="29" <? if ($oday=="29") echo "selected"; ?>>29   
     <option value="30" <? if ($oday=="30") echo "selected"; ?>>30   
     <option value="31" <? if ($oday=="31") echo "selected"; ?>>31   
     </select>
     
     
     <select name="oyear" size="1">
     <? for ($i=1990;$i<=date("Y",time());$i++)
     { echo "<option ";
       if ($oyear==$i) echo "selected";
       echo ">".$i;
     };
     ?>
     </select>
     </td></tr>

     <tr><td>
     <font class="text3">
     Recieved Date 
     </td>
        <?
        $rmonth=date("m",strtotime($myrow["recieved_date"]));
        $rday=date("d",strtotime($myrow["recieved_date"]));
        $ryear=date("Y",strtotime($myrow["recieved_date"]));
        ?>


     <td>
     <select name="rmonth" size="1">
     <option value="01" <? if ($rmonth=="01") echo "selected"; ?>>Jan
     <option value="02" <? if ($rmonth=="02") echo "selected"; ?>>Feb
     <option value="03" <? if ($rmonth=="03") echo "selected"; ?>>Mar
     <option value="04" <? if ($rmonth=="04") echo "selected"; ?>>Apr
     <option value="05" <? if ($rmonth=="05") echo "selected"; ?>>May
     <option value="06" <? if ($rmonth=="06") echo "selected"; ?>>Jun
     <option value="07" <? if ($rmonth=="07") echo "selected"; ?>>Jul
     <option value="08" <? if ($rmonth=="08") echo "selected"; ?>>Aug
     <option value="09" <? if ($rmonth=="09") echo "selected"; ?>>Sep
     <option value="10" <? if ($rmonth=="10") echo "selected"; ?>>Oct
     <option value="11" <? if ($rmonth=="11") echo "selected"; ?>>Nov
     <option value="12" <? if ($rmonth=="12") echo "selected"; ?>>Dec
     </select>

    <select name="rday" size="1">
     <option value="01" <? if ($rday=="01") echo "selected"; ?>>01
     <option value="02" <? if ($rpday=="02") echo "selected"; ?>>02
     <option value="03" <? if ($rday=="03") echo "selected"; ?>>03
     <option value="04" <? if ($rday=="04") echo "selected"; ?>>04
     <option value="05" <? if ($rday=="05") echo "selected"; ?>>05   
     <option value="06" <? if ($rday=="06") echo "selected"; ?>>06   
     <option value="07" <? if ($rday=="07") echo "selected"; ?>>07   
     <option value="08" <? if ($rday=="08") echo "selected"; ?>>08   
     <option value="09" <? if ($rday=="09") echo "selected"; ?>>09   
     <option value="10" <? if ($rday=="10") echo "selected"; ?>>10
     <option value="11" <? if ($rday=="11") echo "selected"; ?>>11
     <option value="12" <? if ($rday=="12") echo "selected"; ?>>12
     <option value="13" <? if ($rday=="13") echo "selected"; ?>>13
     <option value="14" <? if ($rday=="14") echo "selected"; ?>>14
     <option value="15" <? if ($rday=="15") echo "selected"; ?>>15
     <option value="16" <? if ($rday=="16") echo "selected"; ?>>16
     <option value="17" <? if ($rday=="17") echo "selected"; ?>>17
     <option value="18" <? if ($rday=="18") echo "selected"; ?>>18
     <option value="19" <? if ($rday=="19") echo "selected"; ?>>19
     <option value="20" <? if ($rday=="20") echo "selected"; ?>>20
     <option value="21" <? if ($rday=="21") echo "selected"; ?>>21
     <option value="22" <? if ($rday=="22") echo "selected"; ?>>22
     <option value="23" <? if ($rday=="23") echo "selected"; ?>>23
     <option value="24" <? if ($rday=="24") echo "selected"; ?>>24
     <option value="25" <? if ($rday=="25") echo "selected"; ?>>25
     <option value="26" <? if ($rday=="26") echo "selected"; ?>>26
     <option value="27" <? if ($rday=="27") echo "selected"; ?>>27   
     <option value="28" <? if ($rday=="28") echo "selected"; ?>>28   
     <option value="29" <? if ($rday=="29") echo "selected"; ?>>29   
     <option value="30" <? if ($rday=="30") echo "selected"; ?>>30   
     <option value="31" <? if ($rday=="31") echo "selected"; ?>>31   
     </select>
     
     
     <select name="ryear" size="1">
     <? for ($i=1990;$i<=date("Y",time());$i++)
     { echo "<option ";
       if ($ryear==$i) echo "selected";
       echo ">".$i;
     };
     ?>
     </select>
     </td></tr>

     <tr><td>
     <font class="text3">
     Description</td>
     <td>
     <textarea name="description" rows="7" cols="40" wrap="virtual"><? echo $myrow["description"] 
?></textarea>
     </td>
     </tr>

     <tr><td>
        <input type="hidden" name="sort" value="<? echo $sort ?>">
        <input type="hidden" name="lower" value="<? echo $lower ?>">
        <input type="hidden" name="number" value="<? echo $number ?>">
        <input type="hidden" name="desc" value="<? echo $desc ?>">
        <input type="Submit" name="submit" value="Enter information" class="button1"></td></tr>
     
     </table>
     </form>  

<?     }
     
?>
<P>

     

     
     
     
</body>
     
</html>
