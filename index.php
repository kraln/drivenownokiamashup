<?php 
/*
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 */
  
 /* attempt to disable compression and caching so we can get the 
  * top of the page sent asap */
  @apache_setenv('no-gzip', 1);
  @ini_set('zlib.output_compression', 0);
  @ini_set('implicit_flush', 1);
?>
<!doctype html>
<html style="width:100%; height:100%; margin:0;">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <link rel="stylesheet" href="http://api.maps.nokia.com/mobile/1.0.0/lib/mh5.css">
  <link rel="stylesheet" href="http://api.maps.nokia.com/mobile/1.0.0/lib/colors.css">
 <title>DriveNow Berlin API Mashup</title>
</head>
<body class="mh5_hwacc_body" style="width:100%; height:100%; margin:0">
  <script src="http://api.maps.nokia.com/mobile/1.0.0/lib/mh5.js"></script>
  <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
  <header id="app header" style="position: absolute; z-index: 5; border-radius: 5px; background-color: rgba(255,255,255,0.4); opacity 0.4; padding-left: 1em; padding-right: 1em;"><h2>DriveNow Berlin Available Cars<br/>An API Mashup by kraln.com </header>
<a href="https://github.com/kraln/drivenownokiamashup"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_red_aa0000.png" alt="Fork me on GitHub"></a>
  <div id="app_location" style="width: 100%; height: 100%; position: relative;"></div>
  <script>
  nokia.mh5.assetsPath = "http://api.maps.nokia.com/mobile/1.0.0/lib/";
var map = new nokia.mh5.components.Map({
   appId: "n_OTQ_eqiQYGc38YjWrX",
   appCode: "2pfZOa_PSicIrFH_ooLH3g",
   schema: "normal.day.traffic",
   center: {
     latitude: 52.5233,
     longitude: 13.4127
   },
   zoom: 12,
   listeners: {
      "poiclick": function(e) {
        var car = e.data[0].data;
        var params = {
              content: ["title","description","more"],
              listeners:{
                click: function() { map.hideInfoBubble(this); },
                leftclick: function(){ 
                    location.href='https://de.drive-now.com/php/metropolis/vehicle_details?vin=' + car.carVIN;
                  },
              },
              left: "carbook.png",
              title:car.name,
              description:car.address, 
              more: car.carColor + " " + car.carLicense,
              maxWidth: 340
        }
      
      if(car.carVIN) {
      e.preventDefault();
      this.showInfoBubble(e.data[0],params,function(b){console.log(b)});
    } 
    },
   }
 });
 $("#app_location")[0].appendChild(map.root);
 
nokia.mh5.require("nokia.mh5.geolocation");
var firstLocation = 0
  nokia.mh5.event.add(nokia.mh5.geolocation, "positionchange", function (evt) {
  if(map.zoom < 14) { map.zoom = 14; }
  if(firstLocation == 0) {
    map.zoom = 16;
    map.center = nokia.mh5.geolocation;
    map.center = nokia.mh5.geolocation.coords;
    firstLocation = 1;
  }
});

 if (!nokia.mh5.geolocation.available) {
   nokia.mh5.event.add(nokia.mh5.geolocation, "positionerror", function () {
     alert("Sorry but position is not available");
   });
   nokia.mh5.geolocation.activate({timeout: 10000});
 } else {
   map.moveTo(nokia.mh5.geolocation);
   map.zoom = 16;
 }      
map.tracking = true;
</script>
<script type="text/javascript">
/* This data is a JSON file served by drive-now. no JSONP is available, so it is retrieved server side */
<?php 
/* Flush what's already on the page while we grab the JSON */
for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
    ob_implicit_flush(1);
 ?>
var mydata = <?php 
$remote = fopen("https://www.drive-now.com/php/metropolis/json.vehicle_filter?cit=6099", "r");
fpassthru($remote);
?>;
$.each(mydata.rec.vehicles.vehicles, function (i, car) {
    var latitude = parseFloat(car.position.latitude),
    longitude = parseFloat(car.position.longitude);
var newpoi = map.createPoi("carico.png", 
{carId:              car.int,
carName:            car.carName,
hasOwnerContent:    false,
address:            car.address,
latitude:           latitude,
longitude:          longitude,
name:               car.personalName,
carColor:           car.color,
carLicense:         car.licensePlate,
carTrans:           car.auto,
carVIN:             car.vin
});
});
</script>
</body>
</html>
