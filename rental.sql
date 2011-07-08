# ************************************************************
# Sequel Pro SQL dump
# Version 3348
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.1.44)
# Datenbank: ausleihe_neu
# Erstellungsdauer: 2011-07-08 15:14:24 +0200
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Export von Tabelle bookings
# ------------------------------------------------------------

CREATE TABLE `bookings` (
  `booking_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `booking_status` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `booking_room` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `booking_time` datetime DEFAULT NULL,
  `booking_start` datetime DEFAULT NULL,
  `booking_end` datetime DEFAULT NULL,
  `booking_desc` text,
  PRIMARY KEY (`booking_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;



# Export von Tabelle bookings_inventory
# ------------------------------------------------------------

CREATE TABLE `bookings_inventory` (
  `booking_id` int(11) unsigned DEFAULT NULL,
  `inventory_id` int(11) unsigned DEFAULT NULL,
  KEY `booking_id` (`booking_id`),
  KEY `inventory_id` (`inventory_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Export von Tabelle bookings_updates
# ------------------------------------------------------------

CREATE TABLE `bookings_updates` (
  `booking_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `booking_status` tinyint(2) unsigned DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `update_desc` text,
  KEY `booking_id` (`booking_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Export von Tabelle inventory
# ------------------------------------------------------------

CREATE TABLE `inventory` (
  `inventory_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `inventory_status` tinyint(2) unsigned DEFAULT NULL,
  `inventory_room` tinyint(1) unsigned DEFAULT '0',
  `inventory_time` datetime DEFAULT NULL,
  `inventory_title` varchar(50) DEFAULT NULL,
  `inventory_desc` text,
  PRIMARY KEY (`inventory_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;



# Export von Tabelle packages
# ------------------------------------------------------------

CREATE TABLE `packages` (
  `package_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `package_status` tinyint(2) unsigned DEFAULT NULL,
  `package_time` datetime DEFAULT NULL,
  `package_title` varchar(50) DEFAULT NULL,
  `package_desc` text,
  PRIMARY KEY (`package_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;



# Export von Tabelle packages_inventory
# ------------------------------------------------------------

CREATE TABLE `packages_inventory` (
  `package_id` int(11) unsigned DEFAULT NULL,
  `inventory_id` int(11) unsigned DEFAULT NULL,
  KEY `package_id` (`package_id`),
  KEY `inventory_id` (`inventory_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Export von Tabelle semesters
# ------------------------------------------------------------

CREATE TABLE `semesters` (
  `semester_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `semester_title` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`semester_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;



# Export von Tabelle sessions
# ------------------------------------------------------------

CREATE TABLE `sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(50) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Export von Tabelle users
# ------------------------------------------------------------

CREATE TABLE `users` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `semester_id` int(11) DEFAULT NULL,
  `user_status` tinyint(2) unsigned DEFAULT NULL,
  `user_role` tinyint(2) unsigned DEFAULT NULL,
  `user_name` varchar(50) DEFAULT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `user_password` varchar(40) DEFAULT NULL,
  `user_last_visit` datetime DEFAULT NULL,
  `user_reg_date` datetime DEFAULT NULL,
  `user_token` varchar(40) DEFAULT NULL,
  `user_token_expire` datetime DEFAULT NULL,
  `user_student_id` int(6) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
