<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
<link href="../css/admin.css" media="screen" type="text/css" rel="stylesheet">

<script src="../javascript/jquery-1.11.2.min.js" type="text/javascript"></script>
<link rel="shortcut icon" href="../content/favicon.ico">
<title>Wrecked-Distro - Administration
<?php
if ($_REQUEST["module"]) echo " - ".$_REQUEST["module"];
?>
</title>
</head>
<body>
<?php
  // include external functions for database connection, email and sales business logic?

   include_once("db.php");
   include_once("../authSendEmail.php");
   include_once("saleincludes.php");
   include_once("keywords.php");

   // connect to the database
   dbConnect(DEFAULT_DATABASE);

   // initialize constants
   $sortArray = array(1=>"DESC",0=>"ASC");
   $accountTypeArray = array(0=>"RETAIL ACCOUNT", 1=>"WHOLESALE LOGIN");

   // get some request values
   $module = isset($_REQUEST['module']) ? $_REQUEST['module'] : "adminsales_orders_pending.php";
   $command = isset($_REQUEST['command']) ? $_REQUEST['command'] : "ALL";
   $state = isset($_REQUEST['state']) ? $_REQUEST['state'] : "OPEN";
   $lower = isset($_REQUEST['lower']) ? $_REQUEST['lower'] : 0;
   $number = isset($_REQUEST['number']) ? $_REQUEST['number'] : 20;
   $desc = isset($_REQUEST['desc']) ? $_REQUEST['desc'] : 1;
   $sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : "released";
   $mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : 1;
   $search = isset($_REQUEST['search']) ? $_REQUEST['search'] : "itemid";
   $keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : "";
   $scope = isset($_REQUEST['scope']) ? $_REQUEST['scope'] : 30;
   $itemid = isset($_REQUEST['itemid']) ? $_REQUEST['itemid'] : null;
   $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
?>

<div id="container">
	<div id="menu">
		<?php include("menu.php");?>
	</div>

	<div id="content">
		<?php include($module);?>
	</div>
</div>
</body>
</html>
