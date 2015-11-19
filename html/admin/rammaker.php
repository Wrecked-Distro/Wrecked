<?
       if (strtoupper(substr($url,-3))=="RAM")
        {$url=substr($url,0,-3)."rm";};
 
        Header("Content-Type: audio/x-pn-realaudio");
        echo $url;
?>

