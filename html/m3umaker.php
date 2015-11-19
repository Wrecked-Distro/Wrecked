<?
       if (strtoupper(substr($url,-3))=="MP3")
        {$url=substr($url,0,-3)."M3U";};
 
        Header("Content-Type: audio/mpg");
        echo $url;
?>

