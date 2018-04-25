<?php

require_once 'db.php';

function approximateDistance($lat1, $lon1, $lat2, $lon2) {
  $R = 6371 * 1000; // Earth radius (mean) in metres {6371, 6367}

  $lat1Rad = $lat1 * ( M_PI / 180);
  $lon1Rad = $lon1 * ( M_PI / 180);
  $lat2Rad = $lat2 * ( M_PI / 180);
  $lon2Rad = $lon2 * ( M_PI / 180);

  $dLat = $lat2Rad - $lat1Rad;
  $dLon = $lon2Rad - $lon1Rad;

  $a = sin($dLat / 2) * sin($dLat / 2) +
          cos($lat1Rad) * cos($lat2Rad) *
          sin($dLon / 2) * sin($dLon / 2);
  $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
  return $R * $c;
}

function icon($alt, $img) {
  return "<img src=\"icons/" . $img . "\" alt=\"" . $alt . "\" /> ";
}

function icons($typeArr) {
  $result = "";
  $duplicates = array();
  foreach ($typeArr as $type) {
    if (array_key_exists($type, $duplicates))
      continue;

    if ($type == "highway=bus_stop")
      $result .= icon($type, "bus_p.gif");
    if ($type == "railway=subway_entrance")
      $result .= icon($type, "metro_p.gif");
    if ($type == "railway=tram_stop" || $type == "railway=halt")
      $result .= icon($type, "tram_p.gif");

    $duplicates[$type] = 1;
  }
  return $result;
}

$lat = $_GET['lat'] / 1;
$lon = $_GET['lon'] / 1;

// 50°5'31.201"N, 14°30'26.525"E
// 50°5'30.266"N, 14°29'5.405"E
$magicConstant = 0.022533; // 0.022533;

$lat1 = $lat - $magicConstant;
$lon1 = $lon - $magicConstant;
$lat2 = $lat + $magicConstant;
$lon2 = $lon + $magicConstant;

$sql = ("SELECT \n"
        . "  sub.*, \n"
        . "  GROUP_CONCAT(sub.type) AS all_types, \n"
        . "  MIN(sub.distance) AS distance \n"
        . "FROM (\n"
        . "  SELECT \n"
        . "     geo.* \n"
        . "  FROM(\n"
        . "    SELECT\n"
        . "       *,\n"
        . "       POW(lat - $lat,2) + POW(lon - $lon,2) AS distance \n"
        . "    FROM idos_geo_station AS geo \n"
        . "    WHERE `lat` >= $lat1 AND `lon` >= $lon1 AND `lat` <= $lat2 AND `lon` <= $lon2\n"
        . "  ) AS geo\n"
        . "  ORDER BY geo.distance \n"
        . ") AS sub\n"
        . "GROUP BY sub.name \n"
        . "ORDER BY sub.distance \n"
        . "LIMIT 5;\n");

//echo "<pre>$sql</pre>";

$res = mysqli_query($link,$sql);
if ($res === FALSE)
  return;

echo "<h4>Nejbližší:</h4>";

while ($station = mysqli_fetch_assoc($res)) {


  echo "<p>" . icons(explode(",", $station['all_types']))
  . " <a href=\"javascript:hide('fromwrapper');show('towrapper');setValue('from', '" . $station['name'] . "');\">"
  . $station['name'] . "</a>"
  . " <small>(" . floor(approximateDistance($lat, $lon, $station['lat'], $station['lon'])) . " m)</small>"
  . "</p>";
}


//echo $_GET['lat']." ".$_GET['lon']."";
echo "<hr />";
echo "<h4>Oblíbené:</h4>";
?>
