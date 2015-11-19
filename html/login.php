<?php

// login.php
// handles user login and account admin
// coded by geoffrey maddock; winter 2002

include_once ("authSendEmail.php");

// START MAIN PROGRAM SECTION

// if the user is logged in
include ("access.php");
include_once ("login_includes.php");

dbConnect();

$command = isset($_REQUEST['command']) ? $_REQUEST['command'] : "INFO";
$module = isset($_REQUEST['module']) ? $_REQUEST['module'] : "login.php";
$usertype = isset($_SESSION['usertype']) ? $_SESSION['usertype'] : 0;

// $username = isset($_SESSION['username']) ? $_SESSION['username'] : "Unknown";

$usertypearray = array(0 => "Standard", 1 => "Wholesale");

echo "<span id='search-header'>" . $username . " is ";
echo "<B>Logged in</B> with a " . $usertypearray[$usertype] . " Account</span>";

echo "<br>";
echo "<b>ACCOUNT OPTIONS: </b>";
echo "<a href=\"?module=$module&command=INFO\" ";
if ($command == "INFO") {
    echo "class=select2";
};
echo ">MyInfo</a> | ";
echo "<a href=\"?module=$module&command=CHECK\" ";
if ($command == "CHECK") {
    echo "class=select2";
};
echo ">Check Cart</a> | ";
echo "<a href=\"?module=$module&command=ORDERS\" ";
if ($command == "ORDERS") {
    echo "class=select2";
};
echo ">Current Orders</a> | ";
echo "<a href=\"?module=$module&command=PAST\" ";
if ($command == "PAST") {
    echo "class=select2";
};
echo ">Past Orders</a> | ";
echo "<a href=\"?module=$module&command=ITEM\" ";
if ($command == "ITEM") {
    echo "class=select2";
};
echo ">Past Items</a> | ";
echo "<a href=\"?module=logout.php&command=LOGOUT\">Log out</a>";
echo "<p>";
switch ($command) {
    case 'DELETE':
        
        // removes an item from the shopping cart
        deleteItem($_REQUEST['itemid']);
        showCart($username);
        break;

    case 'CHECK':
        
        // shows contents of current shopping cart
        showCart($username);
        break;

    case 'OUT':
        
        // starts check out for current shopping cart
        checkOut($username);
        break;

    case 'PROCESS':
        
        // processes cart & selections to a final sale
        processOrder($username, $_REQUEST['shipping_method'], $_REQUEST['billing_method'], $_REQUEST['total'], $_REQUEST['tax'], $_REQUEST['note']);
        break;

    case 'ORDERS':
        
        // shows current and completed orders
        showOrders($username);
        break;

    case 'PAST':
        
        // shows past completed orders
        showHistory($username, $_REQUEST['sales_orderid']);
        break;

    case 'ITEM':
        
        // shows past purchased items
        showItemHistory($username);
        break;

    case 'INFO':
        
        // displays current user information
        showUser($username);
        break;

    default:
        
        // displays current user information
        showUser($username);
        break;
};
?>
