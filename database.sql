# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.5-10.2.11-MariaDB)
# Database: sentimap2
# Generation Time: 2018-03-30 19:33:08 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table citiesdata
# ------------------------------------------------------------

DROP TABLE IF EXISTS `citiesdata`;

CREATE TABLE `citiesdata` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `Lat` varchar(15) NOT NULL,
  `lng` varchar(15) NOT NULL,
  `pos` int(11) NOT NULL,
  `neg` int(11) NOT NULL,
  `neu` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `weather` text DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table citiesdata2
# ------------------------------------------------------------

DROP TABLE IF EXISTS `citiesdata2`;

CREATE TABLE `citiesdata2` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `pos` int(11) NOT NULL,
  `neg` int(11) NOT NULL,
  `neu` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `set_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table citiesdata3
# ------------------------------------------------------------

DROP TABLE IF EXISTS `citiesdata3`;

CREATE TABLE `citiesdata3` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `pos` int(11) DEFAULT NULL,
  `neg` int(11) DEFAULT NULL,
  `neu` int(11) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `set_id` int(11) DEFAULT NULL,
  `label` varchar(100) DEFAULT NULL,
  `score` float(20,10) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table climate_data_cities
# ------------------------------------------------------------

DROP TABLE IF EXISTS `climate_data_cities`;

CREATE TABLE `climate_data_cities` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `city` varchar(100) DEFAULT NULL,
  `month_number` int(11) DEFAULT NULL,
  `temp_max` int(11) DEFAULT NULL,
  `temp_min` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table initial_cities
# ------------------------------------------------------------

DROP TABLE IF EXISTS `initial_cities`;

CREATE TABLE `initial_cities` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `City` text NOT NULL,
  `Lat` varchar(20) NOT NULL,
  `Lng` varchar(20) NOT NULL,
  `pos` int(11) NOT NULL,
  `neu` int(11) NOT NULL,
  `neg` int(11) NOT NULL,
  `weather` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table sets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sets`;

CREATE TABLE `sets` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `lat` float(10,6) NOT NULL,
  `lng` float(10,6) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table tweets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tweets`;

CREATE TABLE `tweets` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tweet` text NOT NULL,
  `set_id` int(10) NOT NULL,
  `twitter_id` bigint(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table weather_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `weather_data`;

CREATE TABLE `weather_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `temp` float(10,2) DEFAULT NULL,
  `pressure` int(10) DEFAULT NULL,
  `humidity` int(10) DEFAULT NULL,
  `visibility` int(10) DEFAULT NULL,
  `wind_speed` float(10,2) DEFAULT NULL,
  `clouds` int(10) DEFAULT NULL,
  `weather_main` varchar(10) DEFAULT NULL,
  `weather_icon` varchar(10) DEFAULT NULL,
  `weather_description` varchar(100) DEFAULT NULL,
  `lat` float(10,5) DEFAULT NULL,
  `lng` float(10,5) DEFAULT NULL,
  `city_name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `raw_json` text DEFAULT NULL,
  `set_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
