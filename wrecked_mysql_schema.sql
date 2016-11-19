/*
Navicat MySQL Data Transfer
Schema necessary for the Wrecked distro v1.0 site.
Date: 2015-11-19 13:54:35
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for billing_method
-- ----------------------------
DROP TABLE IF EXISTS `billing_method`;
CREATE TABLE `billing_method` (
  `billing_methodid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(24) NOT NULL DEFAULT '',
  `description` tinytext NOT NULL,
  `access` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`billing_methodid`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for category
-- ----------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `catid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(48) NOT NULL DEFAULT '',
  `description` tinytext,
  `parent` varchar(48) DEFAULT NULL,
  PRIMARY KEY (`catid`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for catlink
-- ----------------------------
DROP TABLE IF EXISTS `catlink`;
CREATE TABLE `catlink` (
  `catlinkID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `catid` int(10) unsigned NOT NULL DEFAULT '0',
  `itemid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`catlinkID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for comments
-- ----------------------------
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `commentID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `itemID` int(11) NOT NULL DEFAULT '0',
  `username` varchar(64) NOT NULL DEFAULT '',
  `message` tinytext NOT NULL,
  `keywords` varchar(128) NOT NULL DEFAULT '',
  `rank` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `expire` tinyint(4) NOT NULL DEFAULT '0',
  `expiredate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`commentID`)
) ENGINE=MyISAM AUTO_INCREMENT=182 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for conditions
-- ----------------------------
DROP TABLE IF EXISTS `conditions`;
CREATE TABLE `conditions` (
  `condid` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(12) NOT NULL DEFAULT '',
  PRIMARY KEY (`condid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for discount
-- ----------------------------
DROP TABLE IF EXISTS `discount`;
CREATE TABLE `discount` (
  `discountID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `discountNAME` varchar(48) NOT NULL DEFAULT '',
  `discountVALUE` decimal(3,2) NOT NULL DEFAULT '0.00',
  `discountDESCRIPTION` tinytext NOT NULL,
  PRIMARY KEY (`discountID`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for distributors
-- ----------------------------
DROP TABLE IF EXISTS `distributors`;
CREATE TABLE `distributors` (
  `distroid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(24) NOT NULL DEFAULT '',
  `contact` varchar(24) NOT NULL DEFAULT '',
  `email` varchar(48) NOT NULL DEFAULT '',
  `phone` varchar(14) NOT NULL DEFAULT '',
  `fax` varchar(14) NOT NULL DEFAULT '',
  `address` varchar(24) NOT NULL DEFAULT '',
  `city` varchar(24) NOT NULL DEFAULT '',
  `state` varchar(24) NOT NULL DEFAULT '',
  `zip` varchar(10) NOT NULL DEFAULT '',
  `country` varchar(24) NOT NULL DEFAULT '',
  `site` varchar(64) NOT NULL DEFAULT '',
  `description` tinytext NOT NULL,
  PRIMARY KEY (`distroid`)
) ENGINE=MyISAM AUTO_INCREMENT=118 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for distro_items
-- ----------------------------
DROP TABLE IF EXISTS `distro_items`;
CREATE TABLE `distro_items` (
  `distro_itemid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `distro_orderid` int(10) unsigned NOT NULL DEFAULT '0',
  `itemid` int(10) unsigned NOT NULL DEFAULT '0',
  `cost` decimal(4,2) NOT NULL DEFAULT '0.00',
  `quantity` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`distro_itemid`)
) ENGINE=MyISAM AUTO_INCREMENT=205 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for distro_orders
-- ----------------------------
DROP TABLE IF EXISTS `distro_orders`;
CREATE TABLE `distro_orders` (
  `distro_orderid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `distroid` int(10) unsigned NOT NULL DEFAULT '0',
  `order_cost` decimal(6,2) NOT NULL DEFAULT '0.00',
  `shipping_cost` decimal(4,2) NOT NULL DEFAULT '0.00',
  `shippingMethod` varchar(32) NOT NULL DEFAULT '',
  `order_date` date NOT NULL DEFAULT '0000-00-00',
  `paid_date` date DEFAULT NULL,
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `paymentMethod` varchar(64) NOT NULL DEFAULT '',
  `received_date` date DEFAULT NULL,
  `received` tinyint(1) NOT NULL DEFAULT '0',
  `description` tinytext,
  PRIMARY KEY (`distro_orderid`)
) ENGINE=MyISAM AUTO_INCREMENT=419 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for events
-- ----------------------------
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `eventID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `venue` varchar(64) NOT NULL DEFAULT '',
  `city` varchar(64) NOT NULL DEFAULT '',
  `ages` varchar(10) NOT NULL DEFAULT '',
  `cost` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `start` tinytext,
  `length` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `details` text NOT NULL,
  `brief` tinytext NOT NULL,
  `link` varchar(128) NOT NULL DEFAULT '',
  `contact` varchar(128) NOT NULL DEFAULT '',
  `poster` int(11) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `owner` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`eventID`)
) ENGINE=MyISAM AUTO_INCREMENT=476 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for expenses
-- ----------------------------
DROP TABLE IF EXISTS `expenses`;
CREATE TABLE `expenses` (
  `expenseID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vendor` varchar(32) NOT NULL DEFAULT '',
  `order_cost` decimal(6,2) NOT NULL DEFAULT '0.00',
  `shipping_cost` decimal(4,2) NOT NULL DEFAULT '0.00',
  `tax_cost` decimal(4,2) NOT NULL DEFAULT '0.00',
  `tax` tinyint(1) NOT NULL DEFAULT '1',
  `wholesale` tinyint(1) NOT NULL DEFAULT '0',
  `order_date` date NOT NULL DEFAULT '0000-00-00',
  `recieved_date` date NOT NULL DEFAULT '0000-00-00',
  `description` text NOT NULL,
  PRIMARY KEY (`expenseID`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for foldermap
-- ----------------------------
DROP TABLE IF EXISTS `foldermap`;
CREATE TABLE `foldermap` (
  `foldermapID` int(11) NOT NULL AUTO_INCREMENT,
  `foldermapName` varchar(64) NOT NULL DEFAULT '',
  `foldermapItemID` int(10) unsigned NOT NULL DEFAULT '0',
  `foldermapType` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `foldermapOffset` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`foldermapID`)
) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for format
-- ----------------------------
DROP TABLE IF EXISTS `format`;
CREATE TABLE `format` (
  `formatid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(24) NOT NULL DEFAULT '',
  `description` tinytext NOT NULL,
  `weight` decimal(3,2) NOT NULL DEFAULT '0.00',
  `parent` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`formatid`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for forums
-- ----------------------------
DROP TABLE IF EXISTS `forums`;
CREATE TABLE `forums` (
  `forum_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(48) NOT NULL DEFAULT '',
  `status` varchar(12) NOT NULL DEFAULT '',
  `moderator` varchar(24) NOT NULL DEFAULT '0',
  `description` tinytext NOT NULL,
  `privatepost` int(1) unsigned NOT NULL DEFAULT '0',
  `guestpost` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`forum_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for images
-- ----------------------------
DROP TABLE IF EXISTS `images`;
CREATE TABLE `images` (
  `imageid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `itemid` int(10) unsigned NOT NULL DEFAULT '0',
  `caption` varchar(64) DEFAULT NULL,
  `url` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`imageid`)
) ENGINE=MyISAM AUTO_INCREMENT=1079 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for items
-- ----------------------------
DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `itemid` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(48) NOT NULL DEFAULT '',
  `format` varchar(24) NOT NULL DEFAULT '',
  `artist` varchar(64) NOT NULL DEFAULT '',
  `title` varchar(64) NOT NULL DEFAULT '',
  `label` varchar(64) NOT NULL DEFAULT '',
  `catalog` varchar(12) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `condition` varchar(12) NOT NULL DEFAULT '',
  `released` date NOT NULL DEFAULT '0000-00-00',
  `cost` decimal(4,2) NOT NULL DEFAULT '0.00',
  `quantity` tinyint(4) NOT NULL DEFAULT '0',
  `retail` decimal(4,2) unsigned DEFAULT NULL,
  `restocked` date NOT NULL DEFAULT '0000-00-00',
  `folder` varchar(64) DEFAULT NULL,
  `visible` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`itemid`)
) ENGINE=MyISAM AUTO_INCREMENT=8326 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for keywords
-- ----------------------------
DROP TABLE IF EXISTS `keywords`;
CREATE TABLE `keywords` (
  `keywordID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `itemID` int(10) NOT NULL DEFAULT '0',
  `keyword` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`keywordID`)
) ENGINE=MyISAM AUTO_INCREMENT=12374 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for list
-- ----------------------------
DROP TABLE IF EXISTS `list`;
CREATE TABLE `list` (
  `listID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `listnamesID` int(10) unsigned NOT NULL DEFAULT '0',
  `listitemID` int(10) unsigned NOT NULL DEFAULT '0',
  `listrank` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `listcomment` tinytext NOT NULL,
  PRIMARY KEY (`listID`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for listnames
-- ----------------------------
DROP TABLE IF EXISTS `listnames`;
CREATE TABLE `listnames` (
  `listnamesID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `listnamesName` varchar(64) NOT NULL DEFAULT '',
  `listnamesText` text NOT NULL,
  `listnamesURL` varchar(128) NOT NULL DEFAULT '',
  `listnamesActive` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`listnamesID`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for news
-- ----------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `newsid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `text` text NOT NULL,
  `showstock` tinyint(1) NOT NULL DEFAULT '0',
  `start` date DEFAULT NULL,
  PRIMARY KEY (`newsid`)
) ENGINE=MyISAM AUTO_INCREMENT=302 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for order_items
-- ----------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `order_itemid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `distro_orderid` int(10) unsigned NOT NULL DEFAULT '0',
  `itemid` int(10) unsigned NOT NULL DEFAULT '0',
  `cost` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `quantity` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`order_itemid`)
) ENGINE=MyISAM AUTO_INCREMENT=98 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for posts
-- ----------------------------
DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `post_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` int(10) unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `author` varchar(24) NOT NULL DEFAULT '',
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usesig` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `useIP` varchar(40) DEFAULT NULL,
  `emailreply` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `firstpost` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`post_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1490 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for recommends
-- ----------------------------
DROP TABLE IF EXISTS `recommends`;
CREATE TABLE `recommends` (
  `recommendID` int(11) NOT NULL AUTO_INCREMENT,
  `itemID` int(11) NOT NULL DEFAULT '0',
  `username` varchar(24) NOT NULL DEFAULT '',
  `note` tinytext NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `votes` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`recommendID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for request
-- ----------------------------
DROP TABLE IF EXISTS `request`;
CREATE TABLE `request` (
  `requestID` int(10) NOT NULL AUTO_INCREMENT,
  `requestTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `requestUsername` varchar(32) NOT NULL DEFAULT '0',
  `requestIP` varchar(16) NOT NULL DEFAULT '0',
  `requestItem` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`requestID`)
) ENGINE=MyISAM AUTO_INCREMENT=608 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sales_items
-- ----------------------------
DROP TABLE IF EXISTS `sales_items`;
CREATE TABLE `sales_items` (
  `sales_itemid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sales_orderid` int(10) unsigned NOT NULL DEFAULT '0',
  `itemid` int(10) unsigned NOT NULL DEFAULT '0',
  `quantity` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `confirm` tinyint(1) NOT NULL DEFAULT '0',
  `discount` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`sales_itemid`)
) ENGINE=MyISAM AUTO_INCREMENT=16783 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sales_orders
-- ----------------------------
DROP TABLE IF EXISTS `sales_orders`;
CREATE TABLE `sales_orders` (
  `sales_orderid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `order_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `confirm` tinyint(1) NOT NULL DEFAULT '0',
  `paid_date` date NOT NULL DEFAULT '0000-00-00',
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `sent_date` date NOT NULL DEFAULT '0000-00-00',
  `sent` tinyint(1) NOT NULL DEFAULT '0',
  `order_cost` decimal(6,2) NOT NULL DEFAULT '0000.00',
  `tax_cost` decimal(4,2) NOT NULL DEFAULT '0.00',
  `shipping_cost` decimal(4,2) NOT NULL DEFAULT '0.00',
  `shipping_method` tinyint(4) NOT NULL DEFAULT '0',
  `insurance` decimal(4,2) NOT NULL DEFAULT '0.00',
  `billing_method` tinyint(4) NOT NULL DEFAULT '0',
  `note` mediumtext NOT NULL,
  `ship_name` varchar(48) DEFAULT NULL,
  `ship_address` tinytext,
  `ship_city` varchar(24) DEFAULT NULL,
  `ship_state` varchar(24) DEFAULT NULL,
  `ship_zip` varchar(10) DEFAULT NULL,
  `ship_country` varchar(24) DEFAULT NULL,
  `cancel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`sales_orderid`)
) ENGINE=MyISAM AUTO_INCREMENT=4023 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for search
-- ----------------------------
DROP TABLE IF EXISTS `search`;
CREATE TABLE `search` (
  `searchID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `searchTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `searchUsername` varchar(32) NOT NULL DEFAULT '',
  `searchIP` varchar(16) NOT NULL DEFAULT '',
  `searchType` varchar(16) NOT NULL DEFAULT '',
  `searchKeyword` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`searchID`)
) ENGINE=MyISAM AUTO_INCREMENT=55459 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `sessionID` int(11) NOT NULL AUTO_INCREMENT,
  `sessionValue` varchar(128) NOT NULL DEFAULT '',
  `sessionTimestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sessionUser` varchar(64) NOT NULL DEFAULT '',
  `sessionIP` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`sessionID`)
) ENGINE=MyISAM AUTO_INCREMENT=4997 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for shipping
-- ----------------------------
DROP TABLE IF EXISTS `shipping`;
CREATE TABLE `shipping` (
  `shippingid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(48) NOT NULL DEFAULT '',
  `url` varchar(128) NOT NULL DEFAULT '',
  `note` tinytext NOT NULL,
  `zone` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`shippingid`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for temp_orders
-- ----------------------------
DROP TABLE IF EXISTS `temp_orders`;
CREATE TABLE `temp_orders` (
  `temp_orderid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `itemid` int(10) unsigned NOT NULL DEFAULT '0',
  `quantity` tinyint(3) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`temp_orderid`)
) ENGINE=MyISAM AUTO_INCREMENT=24331 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for threads
-- ----------------------------
DROP TABLE IF EXISTS `threads`;
CREATE TABLE `threads` (
  `thread_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `forum_id` int(10) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(100) NOT NULL DEFAULT '',
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  `replies` int(10) unsigned NOT NULL DEFAULT '0',
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `topped` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `author` varchar(24) NOT NULL DEFAULT '0',
  `usesig` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `useIP` varchar(40) DEFAULT '0',
  `emailreply` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `private` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`thread_id`)
) ENGINE=MyISAM AUTO_INCREMENT=647 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for tracks
-- ----------------------------
DROP TABLE IF EXISTS `tracks`;
CREATE TABLE `tracks` (
  `trackid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `itemid` int(10) unsigned NOT NULL DEFAULT '0',
  `tracknumber` char(3) NOT NULL DEFAULT '',
  `artist` varchar(24) DEFAULT NULL,
  `title` varchar(24) DEFAULT NULL,
  `url` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`trackid`)
) ENGINE=MyISAM AUTO_INCREMENT=3980 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `userid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(24) NOT NULL DEFAULT '',
  `password` varchar(24) NOT NULL DEFAULT '',
  `hint` varchar(24) NOT NULL DEFAULT '',
  `first_name` varchar(24) NOT NULL DEFAULT '',
  `last_name` varchar(24) NOT NULL DEFAULT '',
  `phone` varchar(12) NOT NULL DEFAULT '555-555-5555',
  `address` tinytext NOT NULL,
  `address2` varchar(128) DEFAULT NULL,
  `city` varchar(24) NOT NULL DEFAULT '',
  `state` varchar(24) NOT NULL DEFAULT '',
  `zip` varchar(10) NOT NULL DEFAULT '',
  `country` varchar(24) NOT NULL DEFAULT '',
  `email` varchar(48) NOT NULL DEFAULT '',
  `billing_method` tinyint(4) DEFAULT '1',
  `shipping` tinyint(4) DEFAULT '1',
  `cc_type` varchar(10) DEFAULT NULL,
  `cc_number` varchar(20) DEFAULT NULL,
  `cc_expire` varchar(7) DEFAULT NULL,
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `note` tinytext,
  `mailinglist` tinyint(1) NOT NULL DEFAULT '0',
  `usertype` tinyint(4) NOT NULL DEFAULT '0',
  `sessions` int(11) NOT NULL DEFAULT '0',
  `approved` tinyint(4) NOT NULL DEFAULT '0',
  `confirmation` varchar(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM AUTO_INCREMENT=2711 DEFAULT CHARSET=latin1;
