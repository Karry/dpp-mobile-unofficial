CREATE DATABASE `idos` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci;
use idos;

CREATE TABLE `idos_user` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`hash` VARCHAR( 32 ) NOT NULL ,
`last_access` DATETIME NOT NULL
) ENGINE = InnoDB;

CREATE TABLE `idos_station` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_czech_ci;

CREATE TABLE `idos_access` (
`user_id` INT NOT NULL ,
`station_id` INT NOT NULL ,
`time` DATETIME NOT NULL ,
PRIMARY KEY ( `user_id` , `station_id` , `time` )
) ENGINE = InnoDB;

ALTER TABLE `idos_access` ADD FOREIGN KEY ( `user_id` ) REFERENCES `idos_user` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `idos_access` ADD FOREIGN KEY ( `station_id` ) REFERENCES `idos_station` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

CREATE TABLE IF NOT EXISTS `idos_geo_station` (
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lat` double NOT NULL,
  `lon` double NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `osm_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
