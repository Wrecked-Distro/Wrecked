<?php // db.php

include_once('../config/db.config'); // database config

$pdo = new PDO('mysql:host='.DATABASE_HOST.';dbname='.DEFAULT_DATABASE, DATABASE_USERNAME, DATABASE_PASSWORD);

function dbConnect($db = DEFAULT_DATABASE)
{
     global $dbhost, $dbuser, $dbpass;

     $dbcnx = @mysql_connect(DATABASE_HOST, DATABASE_USERNAME, DATABASE_PASSWORD)
        or die("The site database appears to be down.");

    if ($db!="" and !@mysql_select_db($db))
        die("The site database is unavailable.");
        return $dbcnx;
}
?>
