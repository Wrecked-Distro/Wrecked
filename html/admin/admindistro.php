<?php

function getTotalSpending($name)
{
    $sql = mysql_query("SELECT SUM(distro_orders.order_cost) as total FROM distro_orders, distributors WHERE distro_orders.distroid=distributors.distroid AND name='$name'");
    $result = mysql_fetch_array($sql);
    $total = $result["total"];

    return $total;
};

function getOrders($name)
{
    $sql = mysql_query("SELECT COUNT(distro_orders.distroid) as ordercount FROM distro_orders, distributors WHERE distro_orders.distroid=distributors.distroid AND name='$name'");
    $result = mysql_fetch_array($sql);
    $ordercount=$result["ordercount"];

    return $ordercount;
};

?>

<?php

   if (!$lower) {$lower=0;};
   if (!$number) {$number=20;};
   if (!$desc) {$desc="DESC";};
   if (!$sort) {$sort="distroid";};

   $distroid = isset($_REQUEST['distroid']) ? $_REQUEST['distroid'] : null;
   $shippingmethod = isset($_REQUEST['shippingmethod']) ? $_REQUEST['shippingmethod'] : null;
   $sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : "distroid";
   $command = isset($_REQUEST['command']) ? $_REQUEST['command'] : "view";

   $result=mysql_query("SELECT COUNT(distroid) FROM distributors");
   $total=mysql_fetch_array($result);

   if ($lower<0) {$lower=$total[0];};
   if ($lower>$total[0]) {$lower=0;};

   if ($_REQUEST['submit'])
   {
    // here if no ID then adding, else we're editing

     if ($_REQUEST['distroid'])
     {
      $sql = "UPDATE distributors SET name='".$_REQUEST['name']."', contact='".$_REQUEST['contact']."', email='".$_REQUEST['email']."',
phone='".$_REQUEST['phone']."', fax='".$_REQUEST['fax']."', address='".$_REQUEST['address']."', city='".$_REQUEST['city']."',
state='".$_REQUEST['state']."', zip='".$_REQUEST['zip']."', country='".$_REQUEST['country']."', site='".$_REQUEST['site']."',
description='".$_REQUEST['description']."' WHERE distroid='".$_REQUEST['distroid']."'";
      echo "Update of ".$distroid."\n";
     }
     else
     {
  $sql = "INSERT INTO distributors (distroid, name, contact, email, phone, fax, address, city, state, zip, country,
site, description) VALUES
(0,'".$_REQUEST['name']."','".$_REQUEST['contact']."','".$_REQUEST['email']."','".$_REQUEST['phone']."','".$_REQUEST['fax']."',
'".$_REQUEST['address']."','".$_REQUEST['city']."','".$_REQUEST['state']."','".$_REQUEST['zip']."','".$_REQUEST['country']."',
'".$_REQUEST['site']."','".$_REQUEST['description']."')";

      echo "inserting ".$name."\n";

     }
     // run SQL against the DB

      $result = mysql_query($sql);

      echo "Record updated.<p>";
    
      echo "<a href=\"?module=$module&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">back</a>";  

     } elseif ($delete) {
      
       // delete a record

       $sql = "DELETE FROM distributors WHERE distroid='".$_REQUEST['distroid']."'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";
      echo "<a href=\"admindistro.php?module=$module&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">back</a>";
      
     } else {

      // this part happens if we don't press submit

     if (!$_REQUEST['distroid']) {
    // print the list if there is not editing

     $sql = "SELECT * FROM distributors ORDER BY $sort  ".$sortArray[$desc]." LIMIT $lower, $number";

     $result = mysql_query($sql);

     if ($myrow = mysql_fetch_array($result))
     {
      
       echo "<table >\n";
     
       echo "<tr><td class=\"title1\" colspan='16'><b>Current Distributors</b></td></tr>\n";
       echo "<tr class=\"title2\">
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=distroid&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">DistroID</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=name&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Name</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=contact&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Contact</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=email&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Email</a></td> 
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=phone&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Phone</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=fax&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Fax</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=address&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Address</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=city&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">City</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=state&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">State</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=zip&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Zip</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=country&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Country</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=description&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Description</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=description&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Orders</a></td>
             <td><a href=\"$PHP_SELF?module=$module&amp;sort=description&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">Total</a></td>
             <td></td><td></td>
             </tr>\n";
      
       do
       {
        printf("<tr><td>%s</td> <td><a href=\"%s\" target=\"_blank\">%s</a></td> <td>%s</td><td><a href=\"mailto:%s\">%s</a></td><td>%s</td> <td>%s</td> 
 <td>%s</td> <td>%s</td> <td>%s</td> <td>%s</td> <td>%s</td>  <td>%s</td> <td>%s</td> <td>$%s</td>",
        $myrow["distroid"], $myrow["site"], $myrow["name"], $myrow["contact"],$myrow["email"], 
$myrow["email"],$myrow["phone"], $myrow["fax"], $myrow["address"], $myrow["city"], $myrow["state"],  
$myrow["zip"], $myrow["country"],  $myrow["description"], 
getOrders($myrow["name"]),getTotalSpending($myrow["name"])) ;
    
        printf("<td><a 
href=\"%s?module=$module&amp;distroid=%s&amp;delete=yes&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">(DELETE)</a></td><td><a 
href=\"%s?module=$module&amp;distroid=%s&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc\">(EDIT)</a></td></tr>",
        $PHP_SELF,$myrow["distroid"],$PHP_SELF,$myrow["distroid"]);

       } while ($myrow=mysql_fetch_array($result));
       echo "</table>\n";
      }
       
     echo "<p>";
       
     }
             
    ?>

<table>
<tr><td>
<form action="admindistro.php" method="post">
<input type="hidden" name="sort" value="<? echo $sort;?>">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="submit" name="show" value="Show&amp;nbsp;:" class="but
ton1">
<input type="text" name="number"  value="<? echo $number; ?>" class="form1">
rows beginning with number
<input type="text" name="lower"  value="<? echo $lower; ?>" class="form1">
in
<select name="desc" class="form1">  
<option value="&amp;nbsp;" <? if ($desc!="DESC") echo " SELECTED ";?> >ASCENDING
<option value="DESC" <? if ($desc=="DESC") echo " SELECTED ";?> >DESCENDING
</select>
order.
</form>
</td>
<td>
<form action="admindistro.php" method="post">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="number"  value="<? echo $number; ?>">
<input type="hidden" name="lower"  value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Next <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="admindistro.php" method="post">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="number"  value="<? echo $number; ?>">
<input type="hidden" name="lower"  value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Previous <? echo $number;?>" class="button1">
</form>
</td>
<td>
<form action="admindistro.php" method="post">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="number"  value="<? echo $total[0]; ?>">
<input type="hidden" name="lower"  value="0">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Show All" class="button1">
</form>
</td>
</tr>
</table>
     <p>
             
     <a href="<?php echo $PHP_SELF?>">ADD A distributor</a>
             
     <p>
             
     <form method="post" name="addform" action="<?php echo $_SERVER['PHP_SELF']?>" >
	<input type="hidden" name="module" value="<?php echo $module;?>">
       
     <?
      
     if ($distroid)
     {  
        
     // editing so select a record
        
     $sql = "SELECT * FROM distributors WHERE distroid='".$_REQUEST['distroid']."'";
    
     $result = mysql_query($sql);
        
     $myrow = mysql_fetch_array($result);
       
     $name = $myrow["name"];
       
     $contact = $myrow["contact"];
     
     $email = $myrow["email"];
      
     $phone = $myrow["phone"];

     $fax = $myrow["fax"];
        
     $address = $myrow["address"];
     
     $city = $myrow["city"];
        
     $state = $myrow["state"];
     
     $zip = $myrow["zip"];
       
     $country = $myrow["country"];

     $site = $myrow["site"];

     $description = $myrow["description"];

     
      
     // print the id for editing
     
     ?>
     
     <input type=hidden name="distroid" value="<?php echo $_REQUEST['distroid'] ?>">
     
     <?
     }

     ?>

     Fill in all fields to add a new distributor<br>     *'d fields are optional.<p>
     <table>
     

     <tr><td>
        
        Name
        </td><td><input type="Text" name="name" value="<? echo $myrow["name"] ?>">
     </td></tr>
       
     
     <tr><td>  
        
     Contact   
     </td><td><input type="Text" name="contact" value="<? echo $myrow["contact"] ?>"></td>
     </tr>
     
     <tr><td>
        
     Email
     </td><td><input type="Text" name="email" value="<? echo $myrow["email"] ?>"></td>
     </tr>

     <tr><td>
        
     Phone</td>
     <td>
     <input type="Text" name="phone" value="<? echo $myrow["phone"] ?>">
     </td>
     </tr>
     
     <tr><td>
        
     Fax</td>
     <td>
     <input type="Text" name="fax" value="<? echo $myrow["fax"] ?>">
     </td>
     </tr>

     <tr><td>
        
     Address</td>
     <td>
     <input type="Text" name="address" value="<? echo $myrow["address"] ?>">
     </td>
     </tr>

     <tr><td>
        
     City</td>
     <td>
     <input type="Text" name="city" value="<? echo $myrow["city"] ?>">
     </td>
     </tr>

     <tr><td>
        
     State</td>
     <td>
     <input type="Text" name="state" value="<? echo $myrow["state"] ?>">
     </td>
     </tr>

     <tr><td>
        
     Zip</td>
     <td>
     <input type="Text" name="zip" value="<? echo $myrow["zip"] ?>">
     </td>
     </tr>

     <tr><td>
        
     Country</td>
     <td>
     <input type="Text" name="country" value="<? echo $myrow["country"] ?>">
     </td>
     </tr>

     <tr><td>
        
     Site</td>
     <td>
     <input type="Text" name="site" value="<? echo $myrow["site"] ?>">
     </td>
     </tr>

     <tr><td>
        
     Description</td>
     <td>
     <textarea name="description" rows="7" cols="40" ><? echo $myrow["description"] ?></textarea>
     </td>
     </tr>

     <tr><td>
        <input type="hidden" name="sort" value="<? echo $sort ?>">
        <input type="hidden" name="lower" value="<? echo $lower ?>">
        <input type="hidden" name="number" value="<? echo $number ?>">
        <input type="hidden" name="desc" value="<? echo $desc ?>">
        <input type="Submit" name="submit" value="Enter information" class="button1"></td>
        <td></td>
     </tr>
             
     </table>
     </form>  
     <?
     }
     
?>
