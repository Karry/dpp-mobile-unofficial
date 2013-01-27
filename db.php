<?php
/**

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


*/
include_once 'config.php';

$link = mysql_connect($config['db_host'], $config['db_user'], $config['db_password']);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
MySQL_Select_db($config['db']);
mysql_query("SET CHARACTER SET utf8;");
mysql_query("SET NAMES utf8;");


if (array_key_exists('userhash', $_COOKIE)){
	$userhash = $_COOKIE['userhash'];
	$first = false;
}else{
	$userhash = md5(microtime());
	$first = true;
}

setcookie("userhash", $userhash,  time()+(365*24*3600));

$res = mysql_query("SELECT * FROM idos_user AS u WHERE u.hash = '".mysql_real_escape_string($userhash)."';");
if (mysql_num_rows($res) <= 0){
	mysql_query("INSERT INTO idos_user (hash, last_access) VALUES ('".mysql_real_escape_string($userhash)."', NOW());");
	$userid = mysql_insert_id();
}else{
	$row = mysql_fetch_assoc($res);
	$userid = $row['id'];
}

?>