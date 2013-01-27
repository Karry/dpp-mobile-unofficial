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


/*
$url = "http://idos.dpp.cz/idos/ConnRes.aspx?".
    "date=".urlencode(date('j.n.Y',$time)).
    "&time=".urlencode(date('H:i',$time)).
    "&from=".urlencode($from."|1|28|0").
    "&to=".urlencode($to."|1|28|0").
    "&isdep=1&sp=0&alg=1&chn=7&dev=0&deval=250&mask=-116|-1&std=0|1&min=1|1&max=60|60&tt=pid";
*/
$content = getUrl("http://spojeni.dpp.cz","");
preg_match_all('|id="__EVENTVALIDATION" value="([^"]*)"|U', $content, $data1, PREG_SET_ORDER);
preg_match_all('|id="__VIEWSTATE" value="([^"]*)"|U', $content, $data2, PREG_SET_ORDER);
//$content = getUrl($url);
//$content = str_replace ("\n","",$content);
if (array_key_exists('action',$_GET) && $_GET['action'] == "print"){
    //echo "<pre>".htmlspecialchars($content)."</pre>";
    //echo $content;
    print_r($data2);
}
//exit(0);

$validKey = $data1[0][1];
$viewState = $data2[0][1];
//$viewState = '/wEPDwUJMTgwMTYxMzQ4DxYEHgJ0dAUDcGlkHgJjbAUBQxYCAgIPZBYKZg9kFgxmDw8WAh4EVGV4dAUFT2RrdWRkZAIEDw8WAh8CBQlvbWV6aXQgbmFkZAIGDw8WAh8CBRZyb3rFocOtxZllbsOpIHphZMOhbsOtZGQCDA8WAh4HVmlzaWJsZWgWBAIBDw8WAh8CBRlPZGt1ZCwgcHJ2bsOtIGFsdGVybmF0aXZhZGQCCQ8WAh4FdmFsdWVkZAINDxYCHwNoFgQCAQ8PFgIfAgUZT2RrdWQsIGRydWjDoSBhbHRlcm5hdGl2YWRkAgkPFgIfBGRkAg4PFgIfA2gWBmYPFgQeCFRhYkluZGV4BQE0HgdjaGVja2VkZGQCAQ8PFgIfAgUhTWF4LiBwxJvFocOtIHDFmWVzdW4gbmEgemHEjcOhdGt1ZGQCAg8QDxYCHwUBBQBkEBUIByhuZW7DrSkFMCBtaW4FNSBtaW4GMTAgbWluBjIwIG1pbgYzMCBtaW4GNDUgbWluBjYwIG1pbhUIAi0xATABNQIxMAIyMAIzMAI0NQI2MBQrAwhnZ2dnZ2dnZxYBZmQCAQ9kFgxmDw8WAh8CBQNLYW1kZAIEDw8WAh8CBQlvbWV6aXQgbmFkZAIGDw8WAh8CBRZyb3rFocOtxZllbsOpIHphZMOhbsOtZGQCDA8WAh8DaBYEAgEPDxYCHwIFF0thbSwgcHJ2bsOtIGFsdGVybmF0aXZhZGQCCQ8WAh8EZGQCDQ8WAh8DaBYEAgEPDxYCHwIFF0thbSwgZHJ1aMOhIGFsdGVybmF0aXZhZGQCCQ8WAh8EZGQCDg8WAh8DaBYGZg8WBB8FBQE5HwZkZAIBDw8WAh8CBR1NYXguIHDEm8Whw60gcMWZZXN1biBuYSBrb25jaWRkAgIPEA8WAh8FAQoAZBAVCAcobmVuw60pBTAgbWluBTUgbWluBjEwIG1pbgYyMCBtaW4GMzAgbWluBjQ1IG1pbgY2MCBtaW4VCAItMQEwATUCMTACMjACMzACNDUCNjAUKwMIZ2dnZ2dnZ2cWAWZkAgIPZBYMZg8PFgIfAgUFUMWZZXNkZAIEDw8WAh8CBQlvbWV6aXQgbmFkZAIGDw8WBB8CBRZyb3rFocOtxZllbsOpIHphZMOhbsOtHwNoZGQCDA8WAh8DaBYEAgEPDxYCHwIFGVDFmWVzLCBwcnZuw60gYWx0ZXJuYXRpdmFkZAIJDxYCHwRkZAINDxYCHwNoFgQCAQ8PFgIfAgUZUMWZZXMsIGRydWjDoSBhbHRlcm5hdGl2YWRkAgkPFgIfBGRkAg4PFgIfA2gWBGYPFgIfBQUCMTRkAgIPEA8WAh8FAQ8AZBAVCAcobmVuw60pBTAgbWluBTUgbWluBjEwIG1pbgYyMCBtaW4GMzAgbWluBjQ1IG1pbgY2MCBtaW4VCAItMQEwATUCMTACMjACMzACNDUCNjAUKwMIZ2dnZ2dnZ2cWAWZkAgoPEA8WAh8CBRsgamVuIG7DrXprb3BvZGxhxb5uw60gc3BvamVkZGRkAgsPFgIfA2gWDGYPEA8WBB8CBTcgcMSbxaHDrSBwxZllc3VueSBqZW4gbWV6aSB6YXN0w6F2a2FtaSBzdGVqbsOpaG8gam3DqW5hHgdDaGVja2VkaGRkZGQCAQ8QDxYEHwIFNCBwcmVmZXJvdsOhbsOtIHNwb2plbsOtIHMgdsSbdMWhw60gZnJla3ZlbmPDrSBzcG9qxa8fB2hkZGRkAgIPFgIfBgUHY2hlY2tlZGQCAw8WAh8GZGQCBA9kFgJmDxBkZBYBAgRkAgUPZBYEZg9kFgICAQ9kFgYCAg9kFgZmD2QWCGYPZBYCZg8QDxYCHwdnZGRkZAICD2QWAmYPEA8WAh8HZ2RkZGQCBA9kFgJmDxAPFgIfB2dkZGRkAgYPZBYCZg8QDxYCHwdnZGRkZAICD2QWCGYPZBYCZg8QDxYCHwdnZGRkZAICD2QWAmYPEA8WAh8HZ2RkZGQCBA9kFgJmDxAPFgIfB2dkZGRkAgYPZBYCZg8QDxYCHwdnZGRkZAIED2QWCGYPZBYCZg8QDxYCHwdnZGRkZAICD2QWAmYPEA8WAh8HZ2RkZGQCBA9kFgJmDxAPFgIfB2dkZGRkAgYPZBYCZg8QDxYCHwdnZGRkZAIDD2QWBgIBDxYCHwYFB2NoZWNrZWRkAgMPFgIfBmRkAgYPEGRkFgECAWQCBA9kFgICAQ8QZGQWAQIEZAIBD2QWAgIBD2QWBgICD2QWAmYPZBYEZg9kFgJmDxAPFgIfB2dkZGRkAgIPZBYCZg8QDxYCHwdnZGRkZAIDD2QWBgIBDxYCHwZkZAIDDxYCHwYFB2NoZWNrZWRkAgYPEGRkFgECAWQCBA9kFgICAQ8QZGQWAQIEZBgBBR5fX0NvbnRyb2xzUmVxdWlyZVBvc3RCYWNrS2V5X18WBwUMb3B0RGVwYXJ0dXJlBQpvcHRBcnJpdmFsBQpvcHRBcnJpdmFsBRBvcHRDaGFuZ2VzRGlyZWN0BRBvcHRDaGFuZ2VzRGlyZWN0BQpvcHRDaGFuZ2VzBRRjaGtMb3dEZWNrQ29ubmVjdGlvbkpHjkygc8385LKIynsF/SPtoIXM';
//$validKey = '/wEWgwECpuKIoAgCp8b/yA4Cl5CCygwC74OpkgUCre3MhAsC4YOtkgUC4IOtkgUC64OtkgUC6oOtkgUC7YOtkgUC7IOtkgUC54OtkgUC5oOtkgUC74PZ/gwC7oPZ/gwC4YPZ/gwC4IPZ/gwC64PZ/gwC6oPZ/gwC7YPZ/gwC7IPZ/gwC54PZ/gwC5oPZ/gwC74P12wMC7oP12wMC4YP12wMC4IP12wMC64P12wMC6oP12wMC7YP12wMC7IP12wMC54P12wMC5oP12wMCtMWHdgKbiv/dBAKJpIr/AgK/isOECALzn/7OCgLaxs+rAwLutO9vApanxLcJAtTJoaEHApinwLcJApmnwLcJApKnwLcJApOnwLcJApSnwLcJApWnwLcJAp6nwLcJAp+nwLcJApantFsCl6e0WwKYp7RbApmntFsCkqe0WwKTp7RbApSntFsClae0WwKep7RbAp+ntFsClqeY/g8Cl6eY/g8CmKeY/g8CmaeY/g8CkqeY/g8Ck6eY/g8ClKeY/g8ClaeY/g8CnqeY/g8Cn6eY/g8C9fDl6wgC59CrgAkC1qjtlwoC45jy7wYC1dOA5wcChZiCoAMCsb3v1woCya7EjwMCi8ChmQ0Cx67AjwMCxq7AjwMCza7AjwMCzK7AjwMCy67AjwMCyq7AjwMCwa7AjwMCwK7AjwMCya604woCyK604woCx6604woCxq604woCza604woCzK604woCy6604woCyq604woCwa604woCwK604woCya6YxgUCyK6YxgUCx66YxgUCxq6YxgUCza6YxgUCzK6YxgUCy66YxgUCyq6YxgUCwa6YxgUCwK6YxgUCv7+BVwKa4tT1BwLb3JKnCAKXvK2YCgLEhKiDDgK8hMyGBgLiy9aBBgLoq52zCQK/k4qEBwLsw/DxDQKFrcGaCAKErcGaCAKHrcGaCAKGrcGaCAKBrcGaCAKArcGaCAKDrcGaCAKSrcGaCAKdrcGaCAKFrYGZCAKRjvLFCALbxvf1BwL+jfNoAsGyha4Hh97eQni95AiGkf/oL53tWG2fU30=';
//            '/wEWgwECoI3IsQwCp8b/yA4Cl5CCygwC74OpkgUCre3MhAsC4YOtkgUC4IOtkgUC64OtkgUC6oOtkgUC7YOtkgUC7IOtkgUC54OtkgUC5oOtkgUC74PZ/gwC7oPZ/gwC4YPZ/gwC4IPZ/gwC64PZ/gwC6oPZ/gwC7YPZ/gwC7IPZ/gwC54PZ/gwC5oPZ/gwC74P12wMC7oP12wMC4YP12wMC4IP12wMC64P12wMC6oP12wMC7YP12wMC7IP12wMC54P12wMC5oP12wMCtMWHdgKbiv/dBAKJpIr/AgK/isOECALzn/7OCgLaxs+rAwLutO9vApanxLcJAtTJoaEHApinwLcJApmnwLcJApKnwLcJApOnwLcJApSnwLcJApWnwLcJAp6nwLcJAp+nwLcJApantFsCl6e0WwKYp7RbApmntFsCkqe0WwKTp7RbApSntFsClae0WwKep7RbAp+ntFsClqeY/g8Cl6eY/g8CmKeY/g8CmaeY/g8CkqeY/g8Ck6eY/g8ClKeY/g8ClaeY/g8CnqeY/g8Cn6eY/g8C9fDl6wgC59CrgAkC1qjtlwoC45jy7wYC1dOA5wcChZiCoAMCsb3v1woCya7EjwMCi8ChmQ0Cx67AjwMCxq7AjwMCza7AjwMCzK7AjwMCy67AjwMCyq7AjwMCwa7AjwMCwK7AjwMCya604woCyK604woCx6604woCxq604woCza604woCzK604woCy6604woCyq604woCwa604woCwK604woCya6YxgUCyK6YxgUCx66YxgUCxq6YxgUCza6YxgUCzK6YxgUCy66YxgUCyq6YxgUCwa6YxgUCwK6YxgUCv7+BVwKa4tT1BwLb3JKnCAKXvK2YCgLEhKiDDgK8hMyGBgLiy9aBBgLoq52zCQK/k4qEBwLsw/DxDQKFrcGaCAKErcGaCAKHrcGaCAKGrcGaCAKBrcGaCAKArcGaCAKDrcGaCAKSrcGaCAKdrcGaCAKFrYGZCAKRjvLFCALbxvf1BwL+jfNoAsGyha4HdCmSdaOucEA7KzRAdEo5ZhCI+VE=';

$postData = "&__EVENTTARGET=".
    "&__EVENTARGUMENT=".
    "&__VIEWSTATE=".urlencode($viewState).
    "&ctlFrom%24txtObject=".urlencode($from).
    "&ctlFrom%24cboCategory=0".
    "&ctlFrom%24txtVirtListItemCode=".
    "&ctlFrom%24txtFormState=".
    "&ctlFrom%24txtFormAction=".
    "&ctlFrom%24txtSearchMode=0".
    "&ctlTo%24txtObject=".urlencode($to).
    "&ctlTo%24cboCategory=0".
    "&ctlTo%24txtVirtListItemCode=".
    "&ctlTo%24txtFormState=".
    "&ctlTo%24txtFormAction=".
    "&ctlTo%24txtSearchMode=0".
    "&ctlVia%24txtObject=".urlencode($via).
    "&ctlVia%24cboCategory=0".
    "&ctlVia%24txtVirtListItemCode=".
    "&ctlVia%24txtFormState=".
    "&ctlVia%24txtFormAction=".
    "&ctlVia%24txtSearchMode=0".
    "&txtDate=".urlencode(date('j.n.Y',$time)).
    "&txtTime=".urlencode(date('H:i',$time)).
    "&Direction=optDeparture".
    "&Changes=optChanges".
    "&cboChanges=7".
    "&cmdSearch=vyhledat".
    "&__EVENTVALIDATION=".urlencode($validKey);

//$content = iconv ( "latin2" , "utf8" , getUrl($url) );
$content = getUrl("http://spojeni.dpp.cz/ConnForm.aspx", $postData);
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
$items = split('<div class="spojeni">',$content);

for ($i= 1; $i< count($items); $i++){
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

?>

  </div>

</body>
</html>