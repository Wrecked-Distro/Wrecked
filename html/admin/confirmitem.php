<?php

function getStatus($sales_itemid)
{
   $db = mysql_connect("localhost","root","");
   mysql_select_db(,$db);

   $sql = "SELECT confirm FROM sales_items WHERE sales_itemid=$sales_itemid";
   $result = mysql_fetch_array(mysql_query($sql));

   $status=$result["confirm"];

   return $status;

}

include("header.php");
include("db.php");

   dbConnect();

     if ($sales_itemid)
     {
      if ((getStatus($sales_itemid)==0) && ($confirm==1))
      { // subtract item from live db

      $sql_remove = "UPDATE items SET quantity=quantity-'$quantity', confirm=1 WHERE itemid='$itemid'";
      $remove = mysql_query($sql_remove);

      echo "REMOVED ITEM";

      };

      if ((getStatus($sales_itemid)==1) && ($confirm==0))
      { // re-add item to live db

      $sql_readd = "UPDATE items SET quantity=quantity+'$quantity', confirm=0 WHERE itemid='$itemid'";
      $readd = mysql_query($sql_readd);

      echo "ADDED ITEM";
      };

     };


?>
