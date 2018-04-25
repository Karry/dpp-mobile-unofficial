<?php

include_once 'config.php';

$link = mysqli_connect($config['db_host'], $config['db_user'], $config['db_password']);
if (!$link) {
    die('Could not connect: ' . mysqli_error());
}
MySQLi_Select_db($link, $config['db']);
mysqli_query($link, "SET CHARACTER SET utf8;");
mysqli_query($link, "SET NAMES utf8;");


if (array_key_exists('userhash', $_COOKIE)){
	$userhash = $_COOKIE['userhash'];
	$first = false;
}else{
	$userhash = md5(microtime());
	$first = true;
}

setcookie("userhash", $userhash,  time()+(365*24*3600));

$res = mysqli_query($link, "SELECT * FROM idos_user AS u WHERE u.hash = '".mysqli_real_escape_string($link,$userhash)."';");
if (mysqli_num_rows($res) <= 0){
	mysqli_query($link, "INSERT INTO idos_user (hash, last_access) VALUES ('".mysqli_real_escape_string($link,$userhash)."', NOW());");
	$userid = mysqli_insert_id($link);
}else{
	$row = mysqli_fetch_assoc($res);
	$userid = $row['id'];
}

?>
