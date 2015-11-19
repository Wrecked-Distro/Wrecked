<? // admindistro_orders.php
   // tool for updating and adding new distribubtor purchases
   // written by geoff maddock / cutups@rhinoplex.org
   // last revision july 15, 2005


   echo "<html>";
   echo "<head>";
   echo "<title>Distro Orders admin</title>";
   echo "</head>";

   echo "<body>";

   echo "<b>Orders from Distributors admin</b>";

   echo "<p>";

   $sales_orderid = isset($_REQUEST['sales_orderid']) ? $_REQUEST['sales_orderid'] : null;
   $shippingmethod = isset($_REQUEST['shippingmethod']) ? $_REQUEST['shippingmethod'] : null;
   $sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : "distro_orderid";
   $command = isset($_REQUEST['command']) ? $_REQUEST['command'] : "view";

   dbConnect("db9372_distro");
  
   $result=mysql_query("SELECT COUNT(distro_orderid) FROM distro_orders");
   $total=mysql_fetch_array($result);

   if ($lower<0) {$lower=$total[0];};
   if ($lower>$total[0]) {$lower=0;};

   if ($_REQUEST['submit'])
   {
    // here if no ID then adding, else we're editing

     if ($_REQUEST['distro_orderid'])
     {
      $sql = "UPDATE distro_orders SET distroid='".$_REQUEST['distroid']."', order_cost='".$_REQUEST['order_cost']."', shipping_cost='".$_REQUEST['shipping_cost']."',
order_date='".$_REQUEST['oyear'].$_REQUEST['omonth'].$_REQUEST['oday']."', paid_date='".$_REQUEST['pyear'].$_REQUEST['pmonth'].$_REQUEST['pday']."', paid='".$_REQUEST['paid']."', 
paymentMethod = '".$_REQUEST['paymentMethod']."', received_date='".$_REQUEST['ryear'].$_REQUEST['rmonth'].$_REQUEST['rday']."',
received='".$_REQUEST['received']."', description='".$_REQUEST['description']."' WHERE distro_orderid='".$_REQUEST['distro_orderid']."'";
      echo "Update of ".$distroid."\n";
     }
     else
     {
  $sql = "INSERT INTO distro_orders (distro_orderid, distroid, order_cost, shipping_cost, order_date, paid_date, 
paid, paymentMethod,received_date, received, description) VALUES
(0,'".$_REQUEST['distroid']."','".$_REQUEST['order_cost']."','".$_REQUEST['shipping_cost']."','".$_REQUEST['oyear'].$_REQUEST['omonth'].$_REQUEST['oday']."',
'".$_REQUEST['pyear'].$_REQUEST['pmonth'].$_REQUEST['pday']."','".$_REQUEST['paid']."', '".$_REQUEST['paymentMethod']."','".$_REQUEST['ryear'].$_REQUEST['rmonth'].$_REQUEST['rday']."',
'".$_REQUEST['received']."','".$_REQUEST['description']."')";

      echo "inserting ".$distro_orderid."\n";

     }
     // run SQL against the DB

      $result = mysql_query($sql);

      echo "Record updated.<p>";
    
      echo "<a href=\"$PHP_SELF?module=$module&sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";  

     } elseif ($delete) {
      
       // delete a record

       $sql = "DELETE FROM distro_orders WHERE distro_orderid='".$_REQUEST['distro_orderid']."'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";
      echo "<a href=\"$PHP_SELF?module=$module&sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";
      
     } else {

      // this part happens if we don't press submit

     if (!$_REQUEST['distro_orderid']) {
    // print the list if there is not editing

     $result = mysql_query("SELECT *, distro_orders.description AS description, DATE_FORMAT(order_date,'%m/%d/%y') AS order_date, 
DATE_FORMAT(paid_date,'%m/%d/%y') AS paid_date, DATE_FORMAT(received_date,'%m/%d/%y') AS received_date FROM 
distro_orders, distributors WHERE distro_orders.distroid=distributors.distroid ORDER BY $sort ".$sortArray[$desc]." LIMIT $lower, $number");

     if ($myrow = mysql_fetch_array($result))
     {
      
       echo "<table border=0 cellspacing=0 cellpadding=3>\n";
     
       echo "<tr><td class=\"title1\" colspan=12><b>Current 
Distro Orders</b></font></td></tr>\n";
       echo "<tr class=\"title2\">
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=distro_orderid&lower=$lower&number=$number&desc=$desc\">ID</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=name&lower=$lower&number=$number&desc=$desc\">Distributor</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=order_cost&lower=$lower&number=$number&desc=$desc\">Order Cost</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=shipping_cost&lower=$lower&number=$number&desc=$desc\">Shipping Cost</a></td> 
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=order_cost&lower=$lower&number=$number&desc=$desc\">Total</a></td> 
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=order_date&lower=$lower&number=$number&desc=$desc\">Order Date</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=paid_date&lower=$lower&number=$number&desc=$desc\">Paid Date</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=paid&lower=$lower&number=$number&desc=$desc\">Paid?</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=paymentMethod&lower=$lower&number=$number&desc=$desc\">PaymentMethod</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=received_date&lower=$lower&number=$number&desc=$desc\">Received Date</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=received&lower=$lower&number=$number&desc=$desc\">Received?</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?module=$module&sort=distro_orders.description&lower=$lower&number=$number&desc=$desc\">Description</a></td>
       
             </tr>\n";
      
       do
       {
        printf("<tr><td>%s</td> <td><a href=\"admindistro.php?distroid=%s\">%s</a></td> 
<td>$%s</td><td>$%s<td>$%s</td><td>%s</td>",$myrow["distro_orderid"], $myrow["distroid"],
$myrow["name"], $myrow["order_cost"],$myrow["shipping_cost"],$myrow["order_cost"]+$myrow["shipping_cost"],$myrow["order_date"]);
  

        if ($myrow["paid"]==1) {echo "<td>".$myrow["paid_date"]."</td><td>Yes</td>";} else {echo "<td></td><td>No</td>";};

	echo "<td>".$myrow["paymentMethod"]."</td>";

        if ($myrow["received"]==1) {echo "<td>".$myrow["received_date"]."</td><td>Yes</td>";} else {echo 
"<td></td><td>No</td>";};


        
        echo "<td>".$myrow["description"]."</td>";
    
        printf("<td><a 
href=\"%s?module=$module&distro_orderid=%s&delete=yes&sort=$sort&lower=$lower&number=$number&desc=$desc\">(DELETE)</a></td>
<td><a href=\"admindistro_items.php?distro_orderid=%s&sort=$sort&lower=$lower&number=$number&desc=$desc\">(ADD)</a></td>
<td><a href=\"%s?module=$module&distro_orderid=%s&sort=$sort&lower=$lower&number=$number&desc=$desc\">(EDIT)</a></td></tr>",
        $PHP_SELF,$myrow["distro_orderid"],$myrow["distro_orderid"],$PHP_SELF,$myrow["distro_orderid"]);

       } while ($myrow=mysql_fetch_array($result));
       echo "</table>\n";
      }
       
     echo "<p>";
       
     }
             
    ?>

<table>
<tr><td>
<form action="<? echo $PHP_SELF;?>" method="post">
<input type="hidden" name="sort" value="<? echo $sort;?>">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="submit" name="show" value="Show&nbsp;:" class="button1">
<input type="text" name="number" size="3" value="<? echo $number; ?>" class="form1">
rows beginning with number
<input type="text" name="lower" size="3" value="<? echo $lower; ?>" class="form1">
in
<select name="desc" class="form1">  
<option value="&nbsp;" <? if ($desc!="DESC") echo " SELECTED ";?> >ASCENDING
<option value="DESC" <? if ($desc=="DESC") echo " SELECTED ";?> >DESCENDING
</select>
order.
</form>
</td>
<td>
<form action="<? echo $PHP_SELF;?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="submit" name="show" value="Next <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<? echo $PHP_SELF;?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="submit" name="show" value="Previous <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $total[0]; ?>">
<input type="hidden" name="lower" size="3" value="0">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="submit" name="show" value="Show All" class="button1">
</form>
</td>
</tr>
</table>
     <p>
             
     <a href="<?php echo $PHP_SELF?>">ADD A distro order</a>
             
     <p>
             
     <form method="post" name="addform" action="<?php echo $PHP_SELF?>" >
       
     <?
      
     if ($_REQUEST['distro_orderid'])
     {  
        
     // editing so select a record
        
     $sql = "SELECT *, distro_orders.description AS description FROM distro_orders, distributors WHERE 
distro_orderid='".$_REQUEST['distro_orderid']."' AND distro_orders.distroid=distributors.distroid";
    
     $result = mysql_query($sql);
        
     $myrow = mysql_fetch_array($result);
       
     $distroid = $myrow["distroid"];
       
     $order_cost = $myrow["order_cost"];
     
     $shipping_cost = $myrow["shipping_cost"];
      
     $order_date = $myrow["order_date"];

     $paid_date = $myrow["paid_date"];

     $paid = $myrow["paid"];

     $paymentMethod = $myrow["paymentMethod"];
        
     $received_date = $myrow["received_date"];

     $received = $myrow["received"];
     
     $description = $myrow["description"];

     
      
     // print the id for editing
     
     ?>
     
     <input type=hidden name="distro_orderid" value="<?php echo $_REQUEST['distro_orderid'] ?>">
     
     <?
     }

     ?>

     Fill in all fields to add a new distro order<br>     *'d fields are optional.<p>
     <table>
     

     <tr><td>
     <font class="text3">
     
     <a href="admindistro.php">Distributor</a></td>
     <td>
     <select name="distroid" size="1" class="form1">
     
     <?
      $sql = "SELECT distroid, name FROM distributors ORDER BY name";
      $result = mysql_query($sql);
     
      if ($distrolist=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value=\"".$distrolist["distroid"]."\" ";
       if ($distrolist["distroid"]==$myrow["distroid"])
        {echo "selected";};
       echo ">".$distrolist["name"];
      } while ($distrolist=mysql_fetch_array($result));
      };
     ?>
     </select>
     
     </td></tr>
       
     
     <tr><td>  
     <font class="text3">
     Order Cost   
     </td><td><input type="Text" name="order_cost" class="form1" value="<? echo $myrow["order_cost"] ?>"></td>
     </tr>
     
     <tr><td>
     <font class="text3"> 
     Shipping Cost
     </td><td><input type="Text" name="shipping_cost" class="form1" value="<? echo $myrow["shipping_cost"] ?>"></td>
     </tr>

     <tr><td>
     <font class="text3">
     Order Date *</td>
        <?
        $omonth=date("m",strtotime($myrow["order_date"]));
        $oday=date("d",strtotime($myrow["order_date"]));
        $oyear=date("Y",strtotime($myrow["order_date"]));
        ?>


     <td>
     <select name="omonth" size="1" class="form1">
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

    <select name="oday" size="1" class="form1">
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
     
     
     <select name="oyear" size="1" class="form1">
     <? for ($i=1999;$i<=date("Y",time())+2;$i++)
     { echo "<option ";
       if ($oyear==$i) echo "selected";
       echo ">".$i;
     };
     ?>
     </select>
     </td></tr>

     <tr><td>
     <font class="text3">
     Paid Date 
     <input type="checkbox" value="1" name="paid" class="form1"<? if ($paid==1) echo "CHECKED"; ?> >
     </td>
        <?
        $pmonth=date("m",strtotime($myrow["paid_date"]));
        $pday=date("d",strtotime($myrow["paid_date"]));
        $pyear=date("Y",strtotime($myrow["paid_date"]));
        ?>


     <td>
     <select name="pmonth" size="1" class="form1">
     <option value="01" <? if ($pmonth=="01") echo "selected"; ?>>Jan
     <option value="02" <? if ($pmonth=="02") echo "selected"; ?>>Feb
     <option value="03" <? if ($pmonth=="03") echo "selected"; ?>>Mar
     <option value="04" <? if ($pmonth=="04") echo "selected"; ?>>Apr
     <option value="05" <? if ($pmonth=="05") echo "selected"; ?>>May
     <option value="06" <? if ($pmonth=="06") echo "selected"; ?>>Jun
     <option value="07" <? if ($pmonth=="07") echo "selected"; ?>>Jul
     <option value="08" <? if ($pmonth=="08") echo "selected"; ?>>Aug
     <option value="09" <? if ($pmonth=="09") echo "selected"; ?>>Sep
     <option value="10" <? if ($pmonth=="10") echo "selected"; ?>>Oct
     <option value="11" <? if ($pmonth=="11") echo "selected"; ?>>Nov
     <option value="12" <? if ($pmonth=="12") echo "selected"; ?>>Dec
     </select>

    <select name="pday" size="1" class="form1">
     <option value="01" <? if ($pday=="01") echo "selected"; ?>>01
     <option value="02" <? if ($pday=="02") echo "selected"; ?>>02
     <option value="03" <? if ($pday=="03") echo "selected"; ?>>03
     <option value="04" <? if ($pday=="04") echo "selected"; ?>>04
     <option value="05" <? if ($pday=="05") echo "selected"; ?>>05   
     <option value="06" <? if ($pday=="06") echo "selected"; ?>>06   
     <option value="07" <? if ($pday=="07") echo "selected"; ?>>07   
     <option value="08" <? if ($pday=="08") echo "selected"; ?>>08   
     <option value="09" <? if ($pday=="09") echo "selected"; ?>>09   
     <option value="10" <? if ($pday=="10") echo "selected"; ?>>10
     <option value="11" <? if ($pday=="11") echo "selected"; ?>>11
     <option value="12" <? if ($pday=="12") echo "selected"; ?>>12
     <option value="13" <? if ($pday=="13") echo "selected"; ?>>13
     <option value="14" <? if ($pday=="14") echo "selected"; ?>>14
     <option value="15" <? if ($pday=="15") echo "selected"; ?>>15
     <option value="16" <? if ($pday=="16") echo "selected"; ?>>16
     <option value="17" <? if ($pday=="17") echo "selected"; ?>>17
     <option value="18" <? if ($pday=="18") echo "selected"; ?>>18
     <option value="19" <? if ($pday=="19") echo "selected"; ?>>19
     <option value="20" <? if ($pday=="20") echo "selected"; ?>>20
     <option value="21" <? if ($pday=="21") echo "selected"; ?>>21
     <option value="22" <? if ($pday=="22") echo "selected"; ?>>22
     <option value="23" <? if ($pday=="23") echo "selected"; ?>>23
     <option value="24" <? if ($pday=="24") echo "selected"; ?>>24
     <option value="25" <? if ($pday=="25") echo "selected"; ?>>25
     <option value="26" <? if ($pday=="26") echo "selected"; ?>>26
     <option value="27" <? if ($pday=="27") echo "selected"; ?>>27   
     <option value="28" <? if ($pday=="28") echo "selected"; ?>>28   
     <option value="29" <? if ($pday=="29") echo "selected"; ?>>29   
     <option value="30" <? if ($pday=="30") echo "selected"; ?>>30   
     <option value="31" <? if ($pday=="31") echo "selected"; ?>>31   
     </select>
     
     
     <select name="pyear" size="1" class="form1">
     <? for ($i=1999;$i<=date("Y",time())+2;$i++)
     { echo "<option ";
       if ($pyear==$i) echo "selected";
       echo ">".$i;
     };
     ?>
     </select>
     </td></tr>

     <tr><td>  
     <font class="text3">
     Payment Method   
     </td><td><input type="Text" name="paymentMethod" class="form1" value="<? echo $myrow["paymentMethod"] 
?>"></td>
     </tr>


     <tr><td>
     <font class="text3">
     Received Date 
    <input type="checkbox" value="1" name="received" class="form1"<? if ($received==1) echo "CHECKED"; ?>>
</td>
        <?
        $rmonth=date("m",strtotime($myrow["received_date"]));
        $rday=date("d",strtotime($myrow["received_date"]));
        $ryear=date("Y",strtotime($myrow["received_date"]));
        ?>


     <td>
     <select name="rmonth" size="1" class="form1">
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

    <select name="rday" size="1" class="form1">
     <option value="-" <? if ($rday=="-") echo "selected"; ?>>-
     <option value="01" <? if ($rday=="01") echo "selected"; ?>>01
     <option value="02" <? if ($rday=="02") echo "selected"; ?>>02
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
     
     
     <select name="ryear" size="1" class="form1">
     <? for ($i=1999;$i<=date("Y",time())+2;$i++)
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
     <textarea name="description" rows="7" cols="40" wrap="virtual" class="form1"><? echo $myrow["description"] ?></textarea>
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
    
     <P>


     <?

     // print all items included in order, and show form to add a new item.

     $sql = "SELECT *, distro_items.quantity AS quantity, distro_items.cost AS cost FROM distro_items, items WHERE 
distro_orderid='$distro_orderid' AND distro_items.itemid=items.itemid";
       
     $result = mysql_query($sql);
       
    
     if ($itemrow = mysql_fetch_array($result))
	{

       echo "<table border=0 cellspacing=0 cellpadding=3>\n";
        
       echo "<tr><td class=\"title1\" colspan=6><b>Distro Order Contents</b></td></tr>\n"; 
       echo "<tr class=\"title2\">
             <td>ItemID</td>
             <td>Artist</td>
             <td>Title</td>
             <td>Label</td>
             <td>Cost</td>
             <td>Quantity</td></tr>";

	do
	{
	echo 
"<tr><td>".$itemrow["itemid"]."</td><td>".$itemrow["artist"]."</td><td>".$itemrow["title"]." 
".$itemrow["catalog"]."</td><td>".$itemrow["label"]."</td> 
<td>$".$itemrow["cost"]."</td><td>".$itemrow["quantity"]."</td>";

        printf("<td><a
href=\"%s?distro_itemid=%s&delete=yes&sort=$sort&lower=$lower&number=$number&desc=$desc\">(DELETE)</a></td><td><a
href=\"%s?distro_itemid=%s&distro_orderid=%s&sort=$sort&lower=$lower&number=$number&desc=$desc\">(EDIT)</a></td></tr>",
       
"admindistro_items.php",$itemrow["distro_itemid"],"admindistro_items.php",$itemrow["distro_itemid"],$itemrow["distro_orderid"]);


	} while ($itemrow=mysql_fetch_array($result));
         
         echo "</table>";
	};
        
     
     }
     
?>
<P>
     
     
     
</body>
     
</html>
