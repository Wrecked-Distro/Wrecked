<?
$time = date("F jS Y, h:iA"); //using the date() function
$ip = $REMOTE_ADDR;  
//$remote_addr is PHP variable to get ip address
$referer = $HTTP_REFERER;  
//$http_referer is PHP variable to get referer
$browser = $HTTP_USER_AGENT;  
//$http_user_agent is PHP variable for browser

//echo $ip;

$fp = fopen("log.html",  "a");  
//use the fopen() function
fputs($fp, "Time: $time IP: $ip Referer: $refererBrowser: $browser \n<br>");  
//using the fputs() function
fclose($fp);  
//closing the function
?>

