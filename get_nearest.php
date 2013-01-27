<?php

require_once 'db.php';

echo "<h4>Nejbližší:</h4>";

$lat = $_GET['lat'] / 1;
$lon = $_GET['lon'] / 1;

// 50°5'31.201"N, 14°30'26.525"E
// 50°5'30.266"N, 14°29'5.405"E
$magicConstant = 0.022533; // 0.022533;

$lat1 = $lat - $magicConstant;
$lon1 = $lon - $magicConstant;
$lat2 = $lat + $magicConstant;
$lon2 = $lon + $magicConstant;

$sql = ("select *, pow(lat - $lat,2) + pow(lon - $lon,2) as distance from idos_geo_station "
        . "where `lat` >= $lat1 and `lon` >= $lon1 and `lat` <= $lat2 and `lon` <= $lon2 group by name order by distance limit 5;");
//echo $sql;
$res = mysql_query($sql);

while ($station = mysql_fetch_assoc($res)) {
  echo "<p><a href=\"javascript:hide('fromwrapper');show('towrapper');setValue('from', '" . $station['name'] . "');\">" . $station['name'] . "</a></p>";
}


//echo $_GET['lat']." ".$_GET['lon']."";
echo "<hr />";
echo "<h4>Oblíbené:</h4>";
?>
