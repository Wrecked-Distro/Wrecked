<?php 

function showOrder($username)
{
dbConnect();

$sql = "SELECT *, temp_orders.quantity AS quantity, items.quantity AS instock FROM temp_orders, items, users WHERE
users.username='$username' AND
temp_orders.userid=users.userid AND temp_orders.itemid=items.itemid ORDER BY temp_orders.timestamp DESC";
$result = mysql_query($sql);
   if ($myrow=mysql_fetch_array($result))
{
  echo
"<table border=0 cellspacing=0 cellpadding=3><tr><td bgcolor=ffcc00 colspan=8>
<font color=ff0000><b>Current Shopping Cart Contents</b></tr>
<tr bgcolor=000066>
<td><font color=00cccc><b>Category</td>
<td><font color=00cccc><b>Format</td>
<td><font color=00cccc><b>Artist</td>
<td><font color=00cccc><b>Title</td>
<td><font color=00cccc><b>Label</td>
<td><font color=00cccc><b>Cond</td>
<td><font color=00cccc><b>#</td>
<td><font color=00cccc><b>Cost</td></tr>";

 $total=0;

  do
   {
    echo
"<tr>
 <td>".$myrow["category"]."</td>
 <td>".$myrow["format"]."</td>
 <td>".$myrow["artist"]."</td>
 <td><a href=\"viewitem.php?itemselect=".$myrow["itemid"]."\">".$myrow["title"]."</a></td>
 <td>".$myrow["label"]."</td>
 <td>".$myrow["condition"]."</td>";

 if ($myrow["instock"]==0)
 {echo "<td><font color=ff0000>OUT</font></td>";}
 else
 {
   if ($myrow["quantity"] > $myrow["instock"])
   {echo "<td><font color=ff0000>NOT ENOUGH</font></td>";}
   else
   { echo "<td>".$myrow["quantity"]."</td>";};
 };

 echo "
 <td>$".$myrow["retail"]*$myrow["quantity"]."</td>";
 $total=$total+($myrow["retail"]*$myrow["quantity"]);
   echo "<td>(<a href=\"login.php?code=DELETE&itemid=".$myrow["itemid"]."\">REMOVE</a>)</td></tr>";
    } while ($myrow=mysql_fetch_array($result));

  echo "<tr><td colspan=6></td><td><b>Total:<b></td><td>$".$total."</td></tr>";
  echo "</table>";
}
else
{echo "No Items in your Shopping Cart";};

}

include("access.php");
include("header.php");

   dbConnect();

//  $db = mysql_connect("localhost","root","");
//   mysql_select_db(,$db);



    // here if no ID then adding, else we're editing

     if ($itemid)
     {

     $result=mysql_query("SELECT userid FROM users WHERE username='$username'");
     $temp=mysql_fetch_array($result);
     $userid=$temp[0];

     $check = "SELECT * FROM temp_orders WHERE temp_orders.userid='$userid' AND temp_orders.itemid='$itemid'";
     $checkresult = mysql_query($check);

     $myrow=mysql_fetch_array($checkresult);
     $quantity=$myrow["quantity"];

     if ($quantity<1)
      {
       $sql = "INSERT INTO temp_orders (temp_orderid, userid, itemid, quantity, timestamp) VALUES  (0, '$userid', '$itemid', 1 ,now())";
       $result = mysql_query($sql);
      }
      else
      {
       $sql = "UPDATE temp_orders SET quantity=quantity+1 WHERE itemid='$itemid'";
       $result = mysql_query($sql);
      };

     echo "<b><i>A new item was added to your shopping cart!</i></b><p>";
     showOrder($username);
     }
     else
     {
      showOrder($username);
     };


     // run SQL against the DB

      switch ($page)
     {
      case "artist" :
       echo "<p><a href=\"view$page.php?sort=$sort&lower=$lower&number=$number&desc=$desc&artist=$artist\">back</a>";
       break;
      case "label" :
       echo "<p><a href=\"view$page.php?sort=$sort&lower=$lower&number=$number&desc=$desc&label=$label\">back</a>";
       break;
      case "genre" :
       echo "<p><a href=\"view$page.php?sort=$sort&lower=$lower&number=$number&desc=$desc&category=$category\">back</a>";
       break;
      case "format" :
       echo "<p><a href=\"view$page.php?sort=$sort&lower=$lower&number=$number&desc=$desc&artist=$artist\">back</a>";
       break;
      case "search" :
       echo "<p><a href=\"view$page.php?sort=$sort&lower=$lower&number=$number&desc=$desc&label=$label&search=$search\">back</a>";
       break;
      default :
      echo "<p><a href=\"view$page.php?sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";
     };

?>
