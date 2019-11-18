# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.7.26)
# Database: pos
# Generation Time: 2019-11-18 11:41:31 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table tbl_inventory
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_inventory`;

CREATE TABLE `tbl_inventory` (
  `inventory_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `starting_qty` int(11) NOT NULL,
  `qty_on_hand` int(11) NOT NULL,
  `expiry_date` date NOT NULL,
  `batch_no` int(11) NOT NULL,
  PRIMARY KEY (`inventory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table tbl_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_items`;

CREATE TABLE `tbl_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `barcode` bigint(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  `uom` varchar(100) NOT NULL,
  `qty_per_uom` int(11) NOT NULL,
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `barcode` (`barcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table tbl_price_code
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_price_code`;

CREATE TABLE `tbl_price_code` (
  `price_code_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `price_per_piece` float NOT NULL,
  `description` varchar(100) NOT NULL,
  `discount` float NOT NULL,
  PRIMARY KEY (`price_code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table tbl_transaction
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_transaction`;

CREATE TABLE `tbl_transaction` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `or_num` varchar(255) NOT NULL,
  PRIMARY KEY (`transaction_id`),
  UNIQUE KEY `or_num` (`or_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table tbl_transaction_details
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_transaction_details`;

CREATE TABLE `tbl_transaction_details` (
  `transaction_details_id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` int(11) NOT NULL,
  `price_code_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `amount` float NOT NULL COMMENT 'price_code_amount x qty',
  PRIMARY KEY (`transaction_details_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
