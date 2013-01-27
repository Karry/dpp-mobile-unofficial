<?php

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