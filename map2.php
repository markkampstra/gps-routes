<?php
  # test
  require_once 'config.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Noordkaap 2019</title>
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
  </head>
  <body>
    <div id="map"></div>
    <script>

      // This example creates a 2-pixel-wide red polyline showing the path of
      // the first trans-Pacific flight between Oakland, CA, and Brisbane,
      // Australia which was made by Charles Kingsford Smith.

      function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 4,
          center: {lat: 59.334591, lng: 18.063240},
          mapTypeId: 'terrain'
        });

        var flightPlanCoordinates = [
        <?php

          $conn = new mysqli($db_host, $db_username, $db_password, $db_database);
          // Check connection
          if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
          }

          if (mysqli_connect_errno())
          {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
          }

          $result = mysqli_query($conn,"SELECT * FROM location_entries where created_at > '2019-07-07' order by created_at asc");

          $locations = array();
          while($row = mysqli_fetch_array($result)) {
            $lat = $row['lat'];
            $lon = $row['lon'];

            array_push($locations, "{lat: $lat, lng: $lon}");
          }
          $result = implode(',', $locations);

          mysqli_close($conn);
          echo $result;
        ?>
          /* {lat: 37.772, lng: -122.214}, */
          /* {lat: 21.291, lng: -157.821}, */
          /* {lat: -18.142, lng: 178.431}, */
          /* {lat: -27.467, lng: 153.027} */
        ];
        var flightPath = new google.maps.Polyline({
          path: flightPlanCoordinates,
          geodesic: true,
          strokeColor: '#FF0000',
          strokeOpacity: 1.0,
          strokeWeight: 2
        });

        flightPath.setMap(map);
      }
    </script>
    <script async defer
          src="https://maps.googleapis.com/maps/api/js?key=<?php echo $api_key ?>&callback=initMap">
    </script>
  </body>
</html>
