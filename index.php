<?php
require_once "./db.php";


$sql = "SELECT s.name, COUNT(*) AS count
FROM idos_user AS u
JOIN idos_access AS a ON a.user_id = u.id
JOIN idos_station AS s ON s.id = a.station_id
WHERE u.id = $userid
  AND UNIX_TIMESTAMP(a.`time`) > (UNIX_TIMESTAMP(NOW()) - (30*24*3600))
GROUP BY s.id ORDER BY COUNT(*) DESC LIMIT 30;";

$stations = array();
$res = mysqli_query($link, $sql);
while ($row = mysqli_fetch_assoc($res)) {
  $stations[] = $row;
}



Header("Content-Type: text/html; charset=utf-8");
//tohle zverstvo proti predpisum delam kuli IE, ktery xthml mime typ odmita zobrazit
//Header("Content-Type: application/xhtml+xml; charset=utf-8");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs">
  <head>
    <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0"/>

    <title>Mobilní rozcestník</title>

    <link href="./main.css"  rel="StyleSheet" type="text/css" media="Screen" title="Default" />


    <script type="text/javascript" charset="utf-8" src="./js/jquery-1.7.2.js"></script>

    <script type="text/javascript">
      function hide(id){
        document.getElementById(id).style.display = "none";
      }
      function hideAll(){
        hide('fromwrapper');
        hide('towrapper');
        hide('viawrapper');
      }
      function show(id){
        document.getElementById(id).style.display = "block";
      }
      function setValue(id, value){
        document.getElementById(id).value = value;
      }
      function getValue(id){
        return document.getElementById(id).value;
      }
      function send(id){
        document.getElementById(id).submit();
      }
    
      $(document).ready(function () {
        var x=$("#nearest");              
        if (navigator.geolocation){
          navigator.geolocation.getCurrentPosition(function (position){
            
            $.get("./get_nearest.php", 
            {lat: position.coords.latitude, lon: position.coords.longitude},
            function(data) {
              x.html(data);
            } );
            
            /*
            x.html("Latitude: " + position.coords.latitude + 
              "<br>Longitude: " + position.coords.longitude);	
             */
            x.html("<small>Loading nearest stations...</small>");
          });
        }else{
          x.html("Geolocation is not supported by this browser.");
        }
      });
    
    </script>

  </head>
  <body>

    <div id="content">
      <h3>Mobilní rozcestník</h3>
      <?php if ($first) { ?><p><strong>Pravděpodobně jste zde poprvé.</strong> Tato stránka si pamatuje Vaše často hledané spoje (cookies) a při příštích návštěvách Vám je rovnou nabídne.</p><?php } ?>

      <div style="float:right; margin-right:6px;"><img src="./dpplogo.png" alt="DPP logo" /></div>

      <form action="./spoj.php" method="get" id="form">
        <div>z:     <input type="text" value="" name="from" id="from" onclick="hideAll();show('fromwrapper')" /> <a href="javascript:hideAll();show('fromwrapper')">&lt;--</a></div>
        <div>do:    <input type="text" value="" name="to"   id="to"   onclick="hideAll();show('towrapper')"   /> <a href="javascript:hideAll();show('towrapper')">&lt;--</a></div>
        <div>přes:  <input type="text" value="" name="via"  id="via"  onclick="hideAll();show('viawrapper')"  /> <a href="javascript:hideAll();show('viawrapper')">&lt;--</a></div>
        <input type="hidden" value="<?php echo $userhash; ?>" name="userhash" />
        <div><input type="submit" value="HLEDEJ" /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript: var from=getValue('from'); setValue('from', getValue('to')); setValue('to', from); ">&lt;- PROHOĎ -&gt;</a></div>
      </form>

      <hr />

      <div id="fromwrapper" >
        <h3>Ze stanice</h3>

        <div id="nearest"></div>

        <?php
        foreach ($stations as $station) {
          echo "<p><a href=\"javascript:hideAll();show('towrapper');setValue('from', '" . $station['name'] . "');\">" . $station['name'] . "</a></p>";
        }
        ?>

      </div>

      <div id="towrapper" style="display: none" >
        <h3>Do stanice</h3>
        <?php
        foreach ($stations as $station) {
          echo "<p><a href=\"javascript:setValue('to', '" . $station['name'] . "');send('form')\">" . $station['name'] . "</a></p>"; //  (".$station['count'].")
        }
        ?>

      </div>

      <div id="viawrapper" style="display: none" >
        <h3>Přes stanici</h3>
        <?php
        foreach ($stations as $station) {
          echo "<p><a href=\"javascript:setValue('via', '" . $station['name'] . "');\">" . $station['name'] . "</a></p>"; //  (".$station['count'].")
        }
        ?>

      </div>

      <hr />

      <p>Napraseno v PHP jen aby to fungovalo. Zdrojáky (pod GPLv2) jsou dostupné na <a href="http://gitorious.org/dpp-mobile-web/">Gitorious</a>.</p>

    </div>

  </body>
</html>
