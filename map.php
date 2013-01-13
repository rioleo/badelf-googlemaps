<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Export BadElf GPS to Google Maps</title>
    <link href="default.css" rel="stylesheet">
    
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
      <script type="text/javascript" src="geoxml3.js"></script>

    <script>
      function initialize() {
        var chicago = new google.maps.LatLng(35.6833, 139.7667);
        var mapOptions = {
          zoom: 14,
          center: chicago,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        var map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

        var ctaLayer = new google.maps.KmlLayer('http://rioleo.org/dev/gps/data/<?=$_GET["f"];?>');
        ctaLayer.setMap(map);
      }
    </script>
  </head>
  <body onload="initialize()">
  <h2>BadElf GPS Pro to Google Maps</h2>
  <p>Allows users of BadElf's GPS Pro to send their GPS paths to a server by way of Dropbox.</p>
  <div class="menu">
  <?php
  //path to directory to scan
$directory = "data/";
 
//get all image files with a .jpg extension.
$images = glob($directory . "*.kml");
 
//print each file name
foreach($images as $image)
{
echo "<li><a href='http://rioleo.org/dev/gps/map.php?f=".str_replace($directory, "", $image)."'>".str_replace($directory, "", $image)."</a></li>";
}
?>
  </div>
    <div id="map_canvas"></div>
  </body>
</html>
