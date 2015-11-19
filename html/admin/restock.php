<?   $db = mysql_connect("localhost","root","");

   mysql_select_db("wrecked",$db);

   $result=mysql_query("SELECT * FROM items",$db);

   do {

     $itemid = $myrow["itemid"];

     $category = $myrow["category"];

     $format = $myrow["format"];
      
     $artist = $myrow["artist"];
       
     $title = $myrow["title"];
     
     $label = $myrow["label"];

     $catalog = $myrow["catalog"];
     
     $description = $myrow["description"];
      
     $condition = $myrow["condition"];
     
     $released = $myrow["released"];  
    
     $distro_order = $myrow["distro_order"];
      
     $cost = $myrow["cost"];
     
     $quantity = $myrow["quantity"];
       
     $retail = $myrow["retail"];
       
     $restocked = $myrow["restocked"];
       

      $sql = "UPDATE items SET category='$category', format='$format', artist='$artist',
title='$title', label='$label', catalog='$catalog', description='$description', 
condition='$condition', released='$released', cost='$cost', 
quantity='$quantity', retail='$retail', restocked='$released' WHERE itemid='$itemid'";


      $updateme = mysql_query($sql,$db);

      echo $itemid." ".$released;
      echo "Record updated.<p>";
     
     } while ($myrow=mysql_fetch_array($result));    

     
?>
