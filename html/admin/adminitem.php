<?php
// get total count of items in the database
$result = mysql_query("SELECT COUNT(itemid) FROM items");
$final = mysql_fetch_array($result);
$total = $final[0];

if (!isset($_REQUEST['lower'])) { $lower = 0; } else { $lower = $_REQUEST['lower']; };
if (!isset($_REQUEST['number'])) { $number = 20; } else { $number = $_REQUEST['number']; };
if (!isset($_REQUEST['desc'])) { $desc = 1; } else { $desc = $_REQUEST['desc']; };
if (!isset($_REQUEST['sort'])) { $sort = "itemid"; } else { $sort = $_REQUEST['sort']; };
if (!isset($_REQUEST['scope'])) { $scope = 1000; } else { $scope = $_REQUEST['scope']; };
if (!isset($_REQUEST['itemselect'])) { $itemselect = NULL; } else { $itemselect = $_REQUEST['itemselect']; };
if (!isset($_REQUEST['search'])) { $search = "artist"; } else {$search = $_REQUEST['search']; };
?>

<b>ITEM admin</b>

<p>

<table>
<tr>
<td><form action="index.php" method="post">
ENTER SEARCH KEYWORD(S)
<input type="hidden" name="module" value="adminitem.php">
<input type="hidden" name="number" value="<? echo $number; ?>">
<input type="hidden" name="lower" value="<? echo $lower;?>">
<input type="hidden" name="mode" value="<? echo $mode;?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="text" name="keyword" size="12" value="<? echo $keyword; ?>" class="form1">
<select name="search" class="form1">
<option <? if ($search == "artist") echo "selected";?>  value="artist" class="form1">artist
<option <? if ($search == "title") echo "selected";?>  value="title">title
<option <? if ($search == "label") echo "selected";?>  value="label">label
<option <? if ($search == "format") echo "selected";?>  value="format">format
<option <? if ($search == "description") echo "selected";?>  value="description">description
</select>
<input type="submit" name="show" value="Search" class="button1">
</form>
</td></tr>
</table>

<?php

   // handle requests

   if ($_REQUEST['submit'])
   {
    // here if no ID then adding, else we're editing

     if ($_REQUEST['itemid'])
     {
      $sql = "UPDATE items SET category='".$_REQUEST['category']."', format='".$_REQUEST['format']."', artist='".$_REQUEST['artist']."', title='".$_REQUEST['title']."', label='".$_REQUEST['label']."', catalog='".$_REQUEST['catalog']."', description='".$_REQUEST['description']."', released='".$_REQUEST['year'].$_REQUEST['month'].$_REQUEST['day']."', cost='".$_REQUEST['cost']."', 
quantity='".$_REQUEST['quantity']."', retail='".$_REQUEST['retail']."', restocked='".$_REQUEST['ryear'].$_REQUEST['rmonth'].$_REQUEST['rday']."',folder='".$_REQUEST['folder']."' WHERE itemid='".$_REQUEST['itemid']."'";
      echo "Update of ".$_REQUEST['itemid']."<p>";

      AddKeywords($_REQUEST['itemid'], $_REQUEST['keywords']);

     } else {
      // sets folder variable equal to the label plus catalog number, cuts spaces, and makes lowercase

      $folder = $_REQUEST['label'].$_REQUEST['catalog'];
      $folder = strtolower(str_replace(" ","",$folder));

      $sql = "INSERT INTO items (itemid, category, format, artist, title, label, catalog, description, condition, released, cost, 
    quantity, retail, restocked,folder) VALUES  (0, '".$_REQUEST['category']."', '".$_REQUEST['format']."', '".$_REQUEST['artist']."', '".$_REQUEST['title']."', '".$_REQUEST['label']."', 
    '".$_REQUEST['catalog']."', '".$_REQUEST['description']."','".$_REQUEST['condition']."', '".$_REQUEST['year'].$_REQUEST['month'].$_REQUEST['day']."', '".$_REQUEST['cost']."', '".$_REQUEST['quantity']."', '".$_REQUEST['retail']."',
    '".$_REQUEST['ryear'].$_REQUEST['rmonth'].$_REQUEST['rday']."','$folder')";

      echo "Inserting ".$_REQUEST['title']."\n";
     };

     // run SQL against the DB

      $result = mysql_query($sql) or die(mysql_error());

      echo "Record updated.<p>";

	// add keywords for new items 

	if (!$_REQUEST['itemid']) 
	{
		$itemid = getRecent(); 
		AddKeywords($itemid,$keywords);
	};


	// link to appropriate distro order

	if ($_REQUEST['distroadd']) 
	{
		if (!$_REQUEST['itemid'])
		{
			$itemid = getRecent();
		};

		addDistroItems($_REQUEST['distro_order'],$_REQUEST['itemid'], $_REQUEST['cost'], $_REQUEST['quantity']);
	};

      echo "<a href='$PHP_SELF?module=$module&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;keyword=$keyword&amp;search=$search'>back</a>";  

     } elseif ($_REQUEST['delete']) {
      
       // delete a record

       $sql = "DELETE FROM items WHERE itemid='".$_REQUEST['itemid']."'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";

      echo "<a href='$PHP_SELF?module=$module&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc&amp;keyword=$keyword&amp;search=$search'>back</a>";
      
     } else {

      // this part happens if we don't press submit - displays all items info

     if (!$_REQUEST['itemid']) {
    // print the list if there is not editing

    $sql = "SELECT *,DATE_FORMAT(released,'%m/%d/%y') AS released,DATE_FORMAT(restocked,'%m/%d/%y') AS restocked_format 
    FROM items WHERE $search LIKE '%$keyword%' ORDER BY `$sort` ".$sortArray[$desc]." LIMIT $lower, $number";
  	echo "<div id='query'>Query: ".$sql."</div>";

	$result = mysql_query($sql);


	if ($myrow = mysql_fetch_array($result))
	{

		echo "<table class='list'>\n";

		echo "<tr><td class='title1' colspan='22'><b>Current Items</b></td></tr>\n";
		echo "<tr class='title2'>
		     <th> <a href='$PHP_SELF?module=$module&amp;sort=itemid&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."'>ItemID</a></th>
		     <th> <a href='$PHP_SELF?module=$module&amp;sort=category&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."'>Category</a></th>
		     <th> <a href='$PHP_SELF?module=$module&amp;sort=format&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."'>Format</a></th>
		     <th> <a href='$PHP_SELF?module=$module&amp;sort=artist&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."'>Artist</a></th> 
		     <th> <a href='$PHP_SELF?module=$module&amp;sort=title&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."'>Title</a></th>
		     <th> <a href='$PHP_SELF?module=$module&amp;sort=label&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."'>Label</a></th>
		     <th> <a href='$PHP_SELF?module=$module&amp;sort=catalog&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."'>Catalog</a></th>
		     <th> <a href='$PHP_SELF?module=$module&amp;sort=condition&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."'>Cond</a></th>
		     <th> <a href='$PHP_SELF?module=$module&amp;sort=released&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."'>Released</a></th>
		     <th> <a href='$PHP_SELF?module=$module&amp;sort=cost&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."'>Cost</a></th>
		     <th> <a href='$PHP_SELF?module=$module&amp;sort=quantity&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."'>#</a></th>
		     <th> <a href='$PHP_SELF?module=$module&amp;sort=retail&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."'>Retail</a></th>
		     <th> <a href='$PHP_SELF?module=$module&amp;sort=restocked&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."'>Restocked</a></th>
		     <th> <a href='$PHP_SELF?module=$module&amp;sort=retail&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."'>Earned/Profit</a></th>
		     <th> <a href='$PHP_SELF?module=$module&amp;sort=folder&amp;lower=$lower&amp;number=$number&amp;desc=".(($desc == 1) ? 0 : 1)."'>Folder</a></th>
		     <th>Keywords</th>
		     <th colspan='6'></th>
		</tr>\n";
      
       do
       {
        printf("<tr><td>%s</td> <td>%s</td> <td>%s</td><td>%s</td><td>%s</td> <td>%s</td> <td>%s</td> <td>%s</td> <td>%s</td> <td>%s</td> <td>%s (%s)</td> <td>$%s</td> <td>%s</td><td>$%d/%d</td><td>%s</td>",
        $myrow["itemid"], $myrow["category"], $myrow["format"],$myrow["artist"], $myrow["title"], $myrow["label"], $myrow["catalog"], $myrow["condition"], $myrow["released"],  $myrow["cost"], 
        $myrow["quantity"], getItemsSold($myrow["itemid"]), $myrow["retail"], $myrow["restocked_format"],($myrow["retail"]-$myrow["cost"]) * getItemsSold($myrow["itemid"]), 
		($myrow["retail"]*getItemsSold($myrow["itemid"])) - $myrow["cost"]*($myrow["quantity"]+getItemsSold($myrow[itemid])),$myrow["folder"]);

		echo "<td>".showkeyword($myrow["itemid"])."</td>";

		$artist = $myrow["artist"];
    
        printf("<td><a href='?module=%s&amp;itemid=%s&amp;delete=yes&amp;sort=%s&amp;lower=%s&amp;number=%s&amp;desc=%s' onClick='return confirm(\"Sure you want to delete?\");'>(DEL)</a></td>
				<td><a href='?module=%s&amp;itemid=%s&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;desc=$desc'>(EDIT)</a></td>
				<td><a href='?module=%s&amp;itemid=%s&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;artist=%s&amp;desc=$desc'>(A)</a></td>
				<td><a href='?module=%s&amp;itemid=%s&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;artist=%s&amp;desc=$desc'>(I)</a></td>
				<td><a href='?module=%s&amp;itemID=%s&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;artist=%search&amp;desc=$desc'>(C)</a></td>
				<td><a href='?module=%s&amp;itemid=%s&amp;sort=$sort&amp;lower=$lower&amp;number=$number&amp;artist=%search&amp;desc=$desc'>(O)</a></td>
				</tr>", 
				$module, $myrow["itemid"], $sort, $lower, $number, $desc,
				$module, $myrow["itemid"], $sort, $lower, $number, $desc,
				'admintrackB.php', $myrow["itemid"], $sort, $lower, $number,$search, $desc,
				'adminimage.php', $myrow["itemid"], $sort, $lower, $number,$search, $desc,
				'admincomment.php', $myrow["itemid"], $sort, $lower, $number,$search, $desc,
				'adminorder_itemsB.php', $myrow["itemid"], $sort, $lower, $number,$search, $desc
        		);

       } while ($myrow = mysql_fetch_array($result));
       echo "</table>\n";
      }
       
     echo "<p>";
       
     }
             
    ?>

<table>
<tr><td>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="sort" value="<? echo $sort;?>">
<input type="hidden" name="search" value="<? echo $search;?>">
<input type="hidden" name="keyword" value="<? echo $keyword;?>">
<input type="submit" name="show" value="Show:" class=button1>
<input type="text" name="number"  value="<? echo $number; ?>">
rows beginning with number
<input type="text" name="lower"  value="<? echo $lower; ?>">
in
<select name="desc">  
<option value="&amp;nbsp;" <? if ($desc!="DESC") echo " SELECTED ";?> >ASCENDING
<option value="DESC" <? if ($desc=="DESC") echo " SELECTED ";?> >DESCENDING
</select>
order.
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF'];  ?>" method="post">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="number"  value="<? echo $number; ?>">
<input type="hidden" name="lower"  value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Previous <? echo $number;?>" class=button1>
</form>
</td>
<td>
<form action="<?php echo $_SERVER['PHP_SELF'];  ?>" method="post">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="number"  value="<? echo $number; ?>">
<input type="hidden" name="lower"  value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Next <? echo $number;?>" class=button1>
</form>
</td>

<td>
<form action="<?php echo $_SERVER['PHP_SELF'];  ?>" method="post">
<input type="hidden" name="module" value="<? echo $module;?>">
<input type="hidden" name="number"  value="<? echo $total[0]; ?>">
<input type="hidden" name="lower"  value="0">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Show All" class=button1>
</form>
</td>
</tr>
</table>
     <p>
             
     <b>ADD an item</b>
             
     <p>
             
     <form method="post" name="addform" action="<?php echo $_SERVER['PHP_SELF']?>" >
       
     <?php
      
     if ($_REQUEST['itemid'])
     {  
        
		// editing so select a record

		$sql = "SELECT * FROM items WHERE itemid='".$_REQUEST['itemid']."'";

		$result = mysql_query($sql);

		$myrow = mysql_fetch_array($result);

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

		$folder = $myrow["folder"];     
      
     	// print the id for editing
     
     ?>
     
     <input type=hidden name="itemid" value="<?php echo $_REQUEST['itemid'] ?>">
     
     <?php
     }

     ?>

     Fill in all fields to add a new item<br>     *'d fields are optional.<p>
     <table>
     

     <tr><td>
     <a href="admincategory.php">category</a></td>
     <td>
     <select name="category" size="1">
     
     <?
      $sql = "SELECT name FROM category";
      $result = mysql_query($sql);  
     
      if ($catlist=mysql_fetch_array($result))
      {
      	do
      	{
       	echo "<option value='".$catlist["name"]."' ";
       	if ($catlist["name"]==$myrow["category"]) 
		{echo "selected";};
       	echo ">".$catlist["name"];
      	} while ($catlist=mysql_fetch_array($result));
      };
     ?>
     </select>
     </td></tr>

    <tr><td>     
     <a href="adminformat.php">Format</a></td>
     <td>
     <select name="format" size="1">
        
     <?
      $sql = "SELECT name FROM format";
      $result = mysql_query($sql);
     
      if ($formlist=mysql_fetch_array($result))
      {
      	do
      	{
      	 echo "<option value='".$formlist["name"]."' ";
        if ($formlist["name"] == $myrow["format"])    
        {echo "selected";};
        echo ">".$formlist["name"];
      } while ($formlist=mysql_fetch_array($result));
      };
     ?>   
     </select>
     </td></tr>
     
     <tr><td>
        Artist
        </td><td><input type="Text" name="artist" value="<? echo $myrow["artist"] ?>">
     </td></tr>
       
     
     <tr><td>  
     Title   
     </td><td><input type="Text" name="title" value="<? echo $myrow["title"] ?>"></td>
     </tr>
     
     <tr><td>
     Label
     </td><td><input type="Text" name="label" value="<? echo $myrow["label"] ?>"></td>
     </tr>



     <tr><td>
     Catalog</td>
     <td>
     <input type="Text" name="catalog" value="<? echo $myrow["catalog"] ?>">
     </td>
     </tr>
     
     <tr><td>
     Description</td>
     <td>
     <textarea name="description" rows="7" cols="40" ><? echo $myrow["description"] ?></textarea>
     </td>
     </tr>

    <tr><td>     
     Condition</td>
     <td>
     <select name="condition" size="1">
     
     <?
      $sql = "SELECT name FROM conditions";
      $result = mysql_query($sql);
     

      if ($condlist=mysql_fetch_array($result) or die(mysql_error()))
      {   
        do  
        {
         echo "<option value='".$condlist["name"]."' ";
         if ($condlist["name"] == $myrow["condition"])
          {echo "selected";};
         echo ">".$condlist["name"]."</option>";
        } while ($condlist = mysql_fetch_array($result));
      };  
     ?>   
     </select>
     </td></tr> 

     
     <tr><td>
     
     Datetime *</td>
	<? 
	$month=date("m",strtotime($myrow["released"])); 
	$day=date("d",strtotime($myrow["released"])); 
	$year=date("Y",strtotime($myrow["released"])); 
	?>
  

     <td> 
     <select name="month" size="1">
     <option value="01" <? if ($month=="01") echo "selected"; ?>>Jan 
     <option value="02" <? if ($month=="02") echo "selected"; ?>>Feb 
     <option value="03" <? if ($month=="03") echo "selected"; ?>>Mar 
     <option value="04" <? if ($month=="04") echo "selected"; ?>>Apr 
     <option value="05" <? if ($month=="05") echo "selected"; ?>>May 
     <option value="06" <? if ($month=="06") echo "selected"; ?>>Jun 
     <option value="07" <? if ($month=="07") echo "selected"; ?>>Jul 
     <option value="08" <? if ($month=="08") echo "selected"; ?>>Aug 
     <option value="09" <? if ($month=="09") echo "selected"; ?>>Sep 
     <option value="10" <? if ($month=="10") echo "selected"; ?>>Oct 
     <option value="11" <? if ($month=="11") echo "selected"; ?>>Nov 
     <option value="12" <? if ($month=="12") echo "selected"; ?>>Dec 
     </select>

     <select name="day" size="1">
     <option value="01" <? if ($day=="01") echo "selected"; ?>>01
     <option value="02" <? if ($day=="02") echo "selected"; ?>>02
     <option value="03" <? if ($day=="03") echo "selected"; ?>>03
     <option value="04" <? if ($day=="04") echo "selected"; ?>>04
     <option value="05" <? if ($day=="05") echo "selected"; ?>>05
     <option value="06" <? if ($day=="06") echo "selected"; ?>>06
     <option value="07" <? if ($day=="07") echo "selected"; ?>>07
     <option value="08" <? if ($day=="08") echo "selected"; ?>>08
     <option value="09" <? if ($day=="09") echo "selected"; ?>>09
     <option value="10" <? if ($day=="10") echo "selected"; ?>>10
     <option value="11" <? if ($day=="11") echo "selected"; ?>>11
     <option value="12" <? if ($day=="12") echo "selected"; ?>>12
     <option value="13" <? if ($day=="13") echo "selected"; ?>>13
     <option value="14" <? if ($day=="14") echo "selected"; ?>>14
     <option value="15" <? if ($day=="15") echo "selected"; ?>>15
     <option value="16" <? if ($day=="16") echo "selected"; ?>>16
     <option value="17" <? if ($day=="17") echo "selected"; ?>>17
     <option value="18" <? if ($day=="18") echo "selected"; ?>>18
     <option value="19" <? if ($day=="19") echo "selected"; ?>>19
     <option value="20" <? if ($day=="20") echo "selected"; ?>>20
     <option value="21" <? if ($day=="21") echo "selected"; ?>>21
     <option value="22" <? if ($day=="22") echo "selected"; ?>>22
     <option value="23" <? if ($day=="23") echo "selected"; ?>>23
     <option value="24" <? if ($day=="24") echo "selected"; ?>>24
     <option value="25" <? if ($day=="25") echo "selected"; ?>>25
     <option value="26" <? if ($day=="26") echo "selected"; ?>>26
     <option value="27" <? if ($day=="27") echo "selected"; ?>>27
     <option value="28" <? if ($day=="28") echo "selected"; ?>>28
     <option value="29" <? if ($day=="29") echo "selected"; ?>>29
     <option value="30" <? if ($day=="30") echo "selected"; ?>>30
     <option value="31" <? if ($day=="31") echo "selected"; ?>>31
     </select>


     <select name="year" size="1">
     <? for ($i=1999;$i<=date("Y",time());$i++)
     { echo "<option ";
       if ($year==$i) echo "selected";
       echo ">".$i;
     };
     ?>
     </select>
     </td></tr>

     <tr><td>
     
     Restocked *</td>
	<? 
	$rmonth=date("m",strtotime($myrow["restocked"])); 
	$rday=date("d",strtotime($myrow["restocked"])); 
	$ryear=date("Y",strtotime($myrow["restocked"])); 
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


     <select name="ryear" size="1">
     <? for ($i=1999;$i<=date("Y",time());$i++)
     { echo "<option ";
       if ($ryear==$i) echo "selected";
       echo ">".$i;
     };
     ?>
     </select>
     </td></tr>
     

     
     <tr><td>
     

     <a href="admindistro_orders.php">Distro-Order</a>
     <input type="checkbox" value="1" name="distroadd"  >
     </td>
     <td>
     <select name="distro_order" size="1">
     
     <?
      $sql = "SELECT distro_orderid, distro_orders.distroid, distributors.distroid, distributors.name, distro_orders.order_date 
      FROM distro_orders, distributors WHERE distro_orders.distroid=distributors.distroid ORDER BY distro_orderid DESC";
      $result = mysql_query($sql);  
     
      if ($distrolist=mysql_fetch_array($result))
      {
      do
      {
       echo "<option value='".$distrolist["distro_orderid"]."' ";
       if ($distrolist["distro_orderid"]==$myrow["distro_order"]) {
        echo "selected";
        };
       echo ">".$distrolist["distro_orderid"]." ".$distrolist["name"]." ".$distrolist["order_date"];
      } while ($distrolist=mysql_fetch_array($result));
      };
     ?>
     </select>
     
     </td></tr>


     <tr><td>
     
     Cost</td>
     <td>
     <input type="Text" id="cost" name="cost" value="<? echo $myrow["cost"] ?>">
     </td>
     </tr>

     <tr><td>
     
     Quantity</td>
     <td>
     <input type="Text" name="quantity" value="<? echo $myrow["quantity"] ?>">
     </td>
     </tr>

     <tr><td>
     
     Retail</td>
     <td>
     <input type="Text" name="retail" value="<? echo $myrow["retail"] ?>">
     </td>
     </tr>

     <tr><td>
     
     Folder</td>
     <td>
     <input type="Text" name="folder" value="<? echo $myrow["folder"] ?>">
     </td>
     </tr>

     <tr><td>
     
     Keywords</td>
     <td>
     <input type="Text" name="keywords" value="<? echo ShowKeyword($itemid); ?>">
     </td>
     </tr>
     
     <tr>
     	<td>
	    <input type="hidden" name="module" value="<? echo $module;?>">
        <input type="hidden" name="sort" value="<? echo $sort ?>">
        <input type="hidden" name="lower" value="<? echo $lower ?>">
        <input type="hidden" name="number" value="<? echo $number ?>">
        <input type="hidden" name="desc" value="<? echo $desc ?>">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="review_date" value="<? echo date("M d y",time()) ?>">
        <input type="Submit" name="submit" value="Enter information" class=button1>
     	</td>
     </tr>    
     </table>
     </form>    

     <?

      $sql = "SELECT * FROM tracks WHERE itemid='$itemid'";
      $result = mysql_query($sql);

      if ($tracklist=mysql_fetch_array($result))
      {   

       echo "<table>\n";
   
       echo "<tr><td class='title1' colspan='12'><b>Audio</b></td></tr>\n";
       echo "<tr class=title2>
             <td>TrackID</td>
             <td>ItemID</td>
             <td>Track #</td>
             <td>Artist</td>
             <td>Title</td>
             <td>URL</td></tr>";

      do
      {
 		echo "<tr><td>".$tracklist["trackid"]."</td><td>".$tracklist["itemid"]."</td><td>".$tracklist["tracknumber"]."</td> <td>".$tracklist["artist"]."</td><td><a href='".$tracklist["url"]."'>".$tracklist["title"]."</a></td><td>".$tracklist["url"]."</td></tr>";
      } while ($tracklist=mysql_fetch_array($result));

      echo "</table>";

      };


     }

     
?>
<P>