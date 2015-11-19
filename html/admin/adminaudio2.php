<?
//
// adminaudio.php
// script for adding and editing audio for inclusion on webpages
//

// CSS document describing formatting 
   include("header.php"); 

// database handling scripts
   include("db.php");

// variable declarations

   if (!$dbName) {$dbName="cutup";};
   if (!$lower) {$lower=0;};
   if (!$number) {$number=20;};
   if (!$desc) {$desc="DESC";};
   if (!$sort) {$sort="audioID";};
   $pagetitle="AUDIO admin";
   $audioRows = array("audioID","author","title","description","tracklist","type","length","format","url" 
,"released","updated","keywords","owner");

// header and body

    echo "<html><head><title>$pagetitle</title></head>";
    echo "<body name=body>";
    echo "<b>$pagetitle</b><p>";

   dbConnect($dbName);

// count items in database
   $result=mysql_query("SELECT COUNT(audioID) FROM audio");
   $total=mysql_fetch_array($result);
   $count=$tower[0];

   if ($lower<0) {$lower=$count;};
   if ($lower>$count) {$lower=0;};

   if ($submit)
   {
    // here if no ID then adding, else we're editing

     if ($audioID)
     {
      // if there is an audioID, we're editing an existing audio entry 
   
     $timetemp=time();
      $sql = "UPDATE audio SET title='$title', author='$author',description='$description', tracklist='$tracklist',
type='$type', length='$length', format='$format', url='$url', released='$year+$month+$day', 
updated=now(),keywords='$keywords',owner='$owner' WHERE audioID='$audioID'";
      echo "Update of ".$audioID."\n";

     }
     else
     {

  $timetemp=time();
  $sql = "INSERT INTO audio 
(audioID,author,title,description,tracklist,type,length,format,url,released,updated,keywords,owner) 
VALUES  (0, '$author','$title', '$description', '$tracklist', '$type', '$length', '$format','$url','$year+$month+$day', 
now(),'keywords','$owner')";

      echo "Inserting ".$title."\n";


     };

     // run SQL against the DB

      $result = mysql_query($sql);

      echo "Record updated.<p>";
    
      echo "<a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=$desc\">back</a>";  

     } elseif ($delete) {
      
       // delete a record

       $sql = "DELETE FROM audio WHERE audioID='$audioID'";
       $result = mysql_query($sql);
       echo "$sql Record deleted!<p>";
      echo "<a href=\"$PHP_SELF?sort=$sort&lower=$lower&number=$number&desc=$desc\" target=\"body\">back</a>";
      
     } else {


      // this part happens if we don't press submit

     if (!$audioID) {
    // print the list if there is not editing


     $result = mysql_query("SELECT *,DATE_FORMAT(released,'%m/%d/%y') AS released, 
DATE_FORMAT(updated,'%m/%d/%y') AS updated , DATE_FORMAT(released,'%y') AS year, DATE_FORMAT(released,'%m') AS 
month,DATE_FORMAT(released,'%d') AS day FROM audio ORDER BY $sort $desc LIMIT $lower, $number");

     if ($myrow = mysql_fetch_array($result))
     {
      
       echo "<table border=0 cellspacing=0 cellpadding=3>\n";
     
       echo "<tr><td class=\"title1\" colspan=11><b>Audio Items</b></font></td></tr>\n";
       echo "<tr class=\"title2\">
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?sort=audioID&lower=$lower&number=$number&desc=$desc\">AudioID</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?sort=author&lower=$lower&number=$number&desc=$desc\">Author</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?sort=title&lower=$lower&number=$number&desc=$desc\">Title</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?sort=type&lower=$lower&number=$number&desc=$desc\">Type</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?sort=length&lower=$lower&number=$number&desc=$desc\">Length</a></td> 
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?sort=format&lower=$lower&number=$number&desc=$desc\">Format</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?sort=url&lower=$lower&number=$number&desc=$desc\">URL</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?sort=released&lower=$lower&number=$number&desc=$desc\">Released</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?sort=updated&lower=$lower&number=$number&desc=$desc\">Updated</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?sort=kaywords&lower=$lower&number=$number&desc=$desc\">Keywords</a></td>
             <td><font color=\"ffffff\">
<a href=\"$PHP_SELF?sort=owner&lower=$lower&number=$number&desc=$desc\">Owner</a></td>
             </tr>\n";
      
       do
       {
        printf("<tr><td>%s</td><td>%s</td> <td>%s</td> <td>%s</td> <td>%s</td> <td>%s</td> <td><a 
href=\"%s\">%s</a></td> <td>%s</td><td> 
%s</td> <td>%s</td> <td>%s</td>",$myrow["audioID"], $myrow["author"],$myrow["title"], $myrow["type"], $myrow["length"], 
$myrow["format"], 
$myrow["url"], $myrow["url"],$myrow["released"], $myrow["updated"],$myrow["keywords"],$myrow["owner"]); 

        printf("<td> <a href=\"%s?audioID=%s&delete=yes&sort=$sort&lower=$lower&number=$number&desc=$desc\">(DELETE)</a>
</td>
<td><a href=\"%s?audioID=%s&sort=$sort&lower=$lower&number=$number&desc=$desc\">(EDIT)</a></td>
</tr>", $PHPSELF, $myrow["audioID"],$PHP_SELF,$myrow["audioID"]);

       } while ($myrow=mysql_fetch_array($result));
       echo "</table>\n";
      }
       
     echo "<p>";
       
     }
             
    ?>

<table>
<tr><td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="sort" value="<? echo $sort;?>">
<input type="submit" name="show" value="Show&nbsp;:" class=button1>
<input type="text" name="number" size="3" value="<? echo $number; ?>">
rows beginning with number
<input type="text" name="lower" size="3" value="<? echo $lower; ?>">
in
<select name="desc">  
<option value="&nbsp;" <? if ($desc!="DESC") echo " SELECTED ";?> >ASCENDING
<option value="DESC" <? if ($desc=="DESC") echo " SELECTED ";?> >DESCENDING
</select>
order.
</form>
</td>
<td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower-$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Previous <? echo $number;?>" class=button1>
</form>
</td>
<td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $number; ?>">
<input type="hidden" name="lower" size="3" value="<? echo ($lower+$number);?>">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Next <? echo $number;?>" class=button1>
</form>
</td>

<td>
<form action="<? echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="number" size="3" value="<? echo $total[0]; ?>">
<input type="hidden" name="lower" size="3" value="0">
<input type="hidden" name="desc" value="<? echo $desc; ?>">
<input type="hidden" name="sort" value="<? echo $sort; ?>">
<input type="submit" name="show" value="Show All" class=button1>
</form>
</td>
</tr>
</table>
     <p>
             
     <a href="<?php echo $PHP_SELF?>">ADD audio</a>
             
     <p>
             
     <form method="post" name="addform" action="<?php echo $PHP_SELF?>" >
       
     <?
      
     if ($audioID)
     {  
        
     // editing so select a record
        

     $sql = "SELECT *,DATE_FORMAT(released,'%m/%d/%y') AS released_format, DATE_FORMAT(updated,'%m/%d/%y') AS updated , 
DATE_FORMAT(released,'%Y') AS year, DATE_FORMAT(released,'%m') AS month,DATE_FORMAT(released,'%d') AS day  FROM 
audio WHERE audioID='$audioID'";
    
     $result = mysql_query($sql);
        
     $myrow = mysql_fetch_array($result);
       
     $author = $myrow["author"];	
	
     $title = $myrow["title"];

     $year = $myrow["year"];
     $month = $myrow["month"];
     $day = $myrow["day"];	
      
     $description = $myrow["description"];
     
     $tracklist = $myrow["tracklist"];
      
     $type = $myrow["type"];

     $length = $myrow["length"];
        
     $format = $myrow["format"];
     
     $url = $myrow["url"];
        
     $released_format = $myrow["released_format"];

     $released  = $myrow["released"];
     
     $updated = $myrow["updated"];
       
	$keywords = $myrow["keywords"];

     $owner = $myrow["owner"];
     
      
     // print the id for editing
     
     ?>
     
     <input type=hidden name="audioID" value="<?php echo $audioID ?>">
     
     <?
     }

     ?>

     Fill in all fields to add new audio<br>     *'d fields are optional.<p>
     <table>
     
     <tr><td>
        <font class=text3>
        Author
        </td><td><input type="Text" name="author" value="<? echo $myrow["author"] ?>">
     </td></tr>

     <tr><td>
        <font class=text3>
        Title
        </td><td><input type="Text" name="title" value="<? echo $myrow["title"] ?>">
     </td></tr>

     <tr><td>
        <font class=text3>
        URL
        </td><td><input type="Text" name="url" width="128" value="<? echo $myrow["url"] ?>">
     </td></tr>
       
     
     <tr><td>
     <font class=text3> 
     Type
     </td><td>
	<select name="type" size="1">
	<option value="track" <? if ($type=="track") echo "selected"; ?> >track
	<option value="mix" <? if ($type == "mix") echo "selected"; ?> >mix
	<option value="liveset" <? if ($type == "liveset") echo "selected"; ?> >liveset
	<option value="sample" <? if ($type == "sample") echo "selected"; ?> >sample
	</select>
	</td>
     </tr>

     <tr><td>
     <font class=text3> 
     Format
     </td><td>
	<select name="format" size="1">
	<option value="mp3" <? if ($format=="mp3") echo "selected"; ?> >MP3
	<option value="ra" <? if ($format == "ra") echo "selected"; ?> >RealAudio
	<option value="ogg" <? if ($format  == "ogg") echo "selected"; ?> >OGG
	</select>
	</td>
     </tr>

     <tr><td>
     <font class=text3>
     Length</td>
     <td>
     <input type="Text" name="length" size=2 value="<? echo $myrow["length"] ?>">
     </td>
     </tr>

     <tr><td>
     <font class=text3>
     Released</td>
  

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
     <? for ($i=1990;$i<=date("Y",time());$i++)
     { echo "<option ";
       if ($year==$i) echo "selected";
       echo ">".$i;
     };
     ?>
     </select>
     </td></tr>

     <tr><td>
     <font class=text3>
     Tracklist</td>
     <td>
     <textarea name="tracklist" rows="7" cols="40" wrap="virtual"><? echo $myrow["tracklist"] ?></textarea>
     </td>
     </tr>

     <tr><td>
     <font class=text3>
     Description</td>
     <td>
     <textarea name="description" rows="7" cols="40" wrap="virtual"><? echo $myrow["description"] ?></textarea>
     </td>
     </tr>

     <tr><td>
        <font class=text3>
        Keywords
        </td><td><input type="Text" name="keywords" width="128" value="<? echo $myrow["keywords"] ?>">
     </td></tr>


     <tr><td>
     <font class=text3>
     Owner</td>
     <td>
     <input type="Text" name="owner" value="<? echo $myrow["owner"] ?>">
     </td>
     </tr>

     <tr><td>
        <input type="hidden" name="sort" value="<? echo $sort ?>">
        <input type="hidden" name="lower" value="<? echo $lower ?>">
        <input type="hidden" name="number" value="<? echo $number ?>">
        <input type="hidden" name="desc" value="<? echo $desc ?>">
        <input type="Submit" name="submit" value="Enter information" class=button1></td></tr>
     
     </table>
     </form>       

<?
     };

     
?>
<P>
     
     
     
</body>

     
</html>
