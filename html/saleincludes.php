<?php
// saleincludes.php
//
// functions for handling discounting of items

// SETTINGS for use in sales functions


// will return the appropriate quantity of items to display
// given an itemID, type of user, quantity in stock and age since restocked

function calcQuantity($usertype, $myrow)
{
  $minimum_stock = 4;
  $sell_all_age = 365;

	$quantity = $myrow["quantity"];
	$age = $myrow["current_day"]-$myrow["restocked_days"];

  switch ($usertype)
  {
          case 1:
  if ($quantity > $minimum_stock)
  {
                    if ($age < $sell_all_age)
  	                {$quantity = $quantity - $minimum_stock;};
  };
                  break;

          default:
                  break;
  };

  if ($quantity < 1) {$quantity = 0;};
  return $quantity;
};

// will return the displayed retail price given the inputs below
// for items that are not yet purchased or in a pending order

function calcPrice($myrow,$discount)
{
        GLOBAL $wholesaleMarkupArray;
        GLOBAL $retailMarkupArray;

        $wholesale = $myrow["cost"];
        $retail = $myrow["retail"];
	$format = $myrow["format"];

	switch ($discount)
 	{
	  case 1: $price = $retail; break; // retail
	  case 2: $price = $wholesale + $wholesaleMarkupArray[$format]; break; // wholesale basic
	  case 3: $price = ($wholesale + $wholesaleMarkupArray[$format] + .5); break; // wholesale limited
	  case 4: $price = ($wholesale + $wholesaleMarkupArray[$format] + 1); break; // wholesale rare
	  case 5: $price = $wholesale; break; // trade
	  case 6: $price = ($retail*.90); break; // 10% off
	  case 7: $price = ($retail*.85); break;  // 15% off
	  case 8: $price = ($retail*.80); break; // 20% off
	  case 9: $price = ($retail*.75); break; // 25% off
	  default: $price = $retail; // retail price
	 };

	// discounts an item based on the format
	if ($format == "CD")
	{
		$price = $retail * .5; // 50% off
	};

	 $price = number_format($price,2,'.','');
	 return $price;
//	 return $price." $discount $wholesale";
};


// returns the actual final cost given an itemID given current date and sales

function retailPrice($itemID)
{

 dbConnect();
 $sql = "SELECT * FROM items WHERE itemid='$itemID'";
 $myrow= mysql_fetch_array(mysql_query($sql));

 $format = $result["format"];

 if (onsale($myrow["itemid"]))
 {
  $retail = discountitem($myrow["itemid"],onsale($myrow["itemid"]));
 }
 else
 {
  $retail = $myrow["retail"];
 };



 $retail = number_format($retail,2,'.','');
 return $retail;
};


//returns the actual final cost given an itemID and a discountID

function discountItem($itemID, $discountID)
{

 GLOBAL $retailMarkupArray;
 GLOBAL $wholesaleMarkupArray;

 dbConnect();
 $sql = "SELECT * FROM items WHERE itemid='$itemID'";
 $result=mysql_fetch_array(mysql_query($sql));

 $wholesale = $result["cost"];
 $retail = $result["retail"];
 $format = $result["format"];

 switch ($discountID)
 {
  case 1: $cost = $retail; break; // retail
  case 2: $cost = $wholesale + $wholesaleMarkupArray[$format]; break; // wholesale basic
  case 3: $cost = ($wholesale + $wholesaleMarkupArray[$format] + .5); break; // wholesale limited
  case 4: $cost = ($wholesale + $wholesaleMarkupArray[$format] + 1); break; // wholesale rare
  case 5: $cost = $wholesale; break; // trade
  case 6: $cost = ($retail*.90); break; // 10% off
  case 7: $cost = ($retail*.85); break;  // 15% off
  case 8: $cost = ($retail*.80); break; // 20% off
  case 9: $cost = ($retail*.75); break; // 25% off
  default: $cost = $retail; // retail price
 };

 // discounts an item based on the format
 if ($format == "CD")
 {
	$cost = $retail * .5; // 50% off
 };

 $cost = number_format($cost,2,'.','');
 return $cost;

};


function discount($discountID)

{
// returns the text name for a discount given the discount id

 dbConnect();

 $result = mysql_query("SELECT discountNAME FROM discount WHERE discountID='$discountID'");
 $myrow = mysql_fetch_array($result);

 $discountNAME=$myrow["discountNAME"];

 return $discountNAME;
};


// returns a sale discount code based on an itemid; 0=not on sale

function onsale($itemid)
{
 global $_SESSION;
 dbConnect();

 $sale=0;

 $result = mysql_query("SELECT *, TO_DAYS(restocked) AS restocked_days, TO_DAYS(CURRENT_DATE) AS current_days FROM items WHERE itemid='$itemid'");
 $item = mysql_fetch_array($result);

//echo $item["restocked_days"]." ".$item["current_days"];
 if (($item["restocked_days"]+365)<($item["current_days"]) AND $item["quantity"]>0)
 {
	// current setting: items older than a year are 10% off

  	$sale=6;
 };

 if (($item["restocked_days"]+730)<($item["current_days"]) AND $item["quantity"]>0)
 {
	// current setting: items older than 2 years are 20% off

	$sale=8;
 };


 return $sale;
};



// returns a sale discount code based on an itemid

function getDiscount($usertype, $myrow)
{
        dbConnect();

        $discountAge1 = 365;
        $discountAge2 = 720;
	$minstock = 4;

	$quantity = $myrow["quantity"];
	$age = $myrow["current_day"]-$myrow["restocked_days"];

	$discount = 0;  // default value is retail

        switch ($usertype)
        {
                case 1:
			if ($age > $discountAge1)
			{
			  $discount = 2; 	// wholesale for old items, however many
			}
			else
			{
				if ($quantity < $minstock)
				{ $discount = 0; }   // retail
				else
				{ $discount = 2; };  // wholesale
			};
                        break;

                default:
                        if ($age > $discountAge2)
                        { $discount = 2; };  //  wholesale for very old items

                        if ($age > $discountAge1 AND $age <= $discountAge2)
                        { $discount = 8; };   // 20% off for old items

                        if ($age <= $discountAge1)
                        { $discount = 0; }; // normal retail price
                        break;
        };

	return $discount;
};


   $wholesaleMarkupArray = array("vinyl"=>".5", "7"=>".5", "10"=>".5", "12"=> ".5", "LP"=>".75", "2LP"=>"1",
"Boxset"=> "3", "CD" =>"1", "2CD" =>"1.5",
"DVD" => "1.5", "Zine" => ".5", "Book"=> "2", "T-shirt"=> "1", "tape"=>".5", "3LP"=>"1.5", "4LP"=>"3",
"CDzine"=>"1",
"2x10"=>"1", "CDR"=>".5", "3inCD"=>".5", "2x3in"=>".75", "2x7"=>".75");

   $retailMarkupArray = array("vinyl"=>"3", "7"=>"2", "10"=>"3", "12" => "3", "LP"=>"3.50", "2LP" =>"5", "Boxset" =>
"8", "CD" =>"4", "2CD"=>"5",
"DVD"=> "8", "Zine"=> "2", "Book"=> "5", "T-shirt"=> "5", "tape"=>"3", "3LP"=>"9", "4LP"=>"12", "CDzine"=>"4",
"2x10"=>"5",
"CDR"=>"2", "3inCD"=>"2.5", "2x3in"=>"4", "2x7"=>"4");

?>
