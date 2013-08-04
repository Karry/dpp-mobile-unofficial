<?php
require_once "./db.php";

Header("Content-Type: text/html; charset=utf-8");
//tohle zverstvo proti predpisum delam kuli IE, ktery xthml mime typ odmita zobrazit
//Header("Content-Type: application/xhtml+xml; charset=utf-8");

header("Expires: ".GMDate("D, d M Y H:i:s")." GMT");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs">
<head>
    <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0"/>

    <title>Spoj</title>

    <style>
    div, span{
        margin-bottom:-3px;
        padding:0px;
    }
    </style>

    <link href="./main.css"  rel="StyleSheet" type="text/css" media="only screen" title="Default" />
</head>
<body>

  <div id="content">

<?php
require_once "./functions.php";

function updateStation($userid, $station){
	$res = mysql_query("SELECT * FROM idos_station WHERE name = '".mysql_real_escape_string($station)."'");
	if (mysql_num_rows($res) <= 0){
		mysql_query("INSERT INTO idos_station (name) VALUES ('".mysql_real_escape_string($station)."');");
		$stationid = mysql_insert_id();
	}else{
		$row = mysql_fetch_assoc($res);
		$stationid = $row['id'];
	}
	mysql_query("INSERT INTO idos_access (user_id, station_id, time) VALUES ($userid, $stationid, NOW());");
	//echo "INSERT INTO idos_access (user_id, station_id, time) VALUES ($userid, $stationid, NOW());";
}


flush();

$time = time() - (5*60);

$from=array_key_exists('from',$_GET)? $_GET['from']: 'stadion strahov';
$to=array_key_exists('to',$_GET)? $_GET['to']: 'karlovo náměstí';
$via=array_key_exists('via',$_GET)? $_GET['via']: '';

updateStation($userid, $from);
updateStation($userid, $to);


$url = "http://spojeni.dpp.cz/ConnForm.aspx?".
        "date=".urlencode(date('j.n.Y',$time)).
        "&time=".urlencode(date('H:i',$time)).
        "&f=".urlencode($from).
        "&tvlc=".
        "&t=".urlencode($to).
        "&tvlc=".
        "&v=".urlencode($via).
        "&vvlc=".
        "&cl=C&res=1&tom=0&cmdSearch=vyhledat&isdep=1&alg=1&chn=7";

$content = getUrl($url, "");

$page = 1;
do{
  $res = preg_match_all('|<a href="([^"]*)"[^>]*>následující|U', $content, $nextPage, PREG_SET_ORDER);
  if ((is_bool($res) && $res === FALSE) || $res == 0)
    break;
  $url = "http://spojeni.dpp.cz". preg_replace('/&amp;/', '&', $nextPage[0][1]);
  $content = getUrl($url, NULL);
  $page ++;
}while ($page <=3);
//echo "$url <hr />$content";
//exit();


//$content = getUrl($url);
//$content = str_replace ("\n","",$content);
if (array_key_exists('action',$_GET) && $_GET['action'] == "print"){
    echo "<pre>".htmlspecialchars($content)."</pre>";
}
//echo $content;
//exit(0);

preg_match_all('|<h1>([^<]*)</h1>|U', $content, $title, PREG_SET_ORDER);
echo "<h3>".$title[0][1]."</h3>";

//preg_match_all('|(<div class="spojeni">.*<hr/>)|U', $content, $items, PREG_SET_ORDER);
//$items = split('<div class="spojeni',$content);
$items = preg_split('/<div class="spojeni[^>]*>/',$content);

if ((is_bool($items) && !$items) || count($items) <= 1 ){
  echo "Spoj nenalezen. <a href=\"$url\">Zombrazit originální stránku DPP.</a>";
  break;
}else{
  // skip first line with start of original document
  for ($i= 1; $i < count($items); $i++){
      echo "<hr />";
      $lines = split ( "\n", strip_tags( $items[$i] ));
      for ($line = 3; $line < count($lines); $line++){
          $lines[$line] = trim($lines[$line]);
          if ($lines[$line] == "" || $lines[$line] == "&nbsp;")
              continue;
          if (startsWith("cena",$lines[$line]))
              break;

          if (startsWith("ze stanice", $lines[$line]))
              $lines[$line] = "<span class=\"from\">".trim(substr($lines[$line],strlen("ze stanice")))."</span>";

          if (startsWith("do stanice", $lines[$line]))
              $lines[$line] = "<span class=\"to\">".trim(substr($lines[$line],strlen("do stanice")))."</span>";

          echo "<div>".$lines[$line]."</div>\n";
      }
  }
}

?>

  </div>

</body>
</html>