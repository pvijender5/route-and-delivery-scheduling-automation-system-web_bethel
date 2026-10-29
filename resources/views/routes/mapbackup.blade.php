<?php
 $arr = ($routes[0]->item_address);
 $start = $routes[0]->start;
//  echo $arr;
//  die;
?>
<x-app-layout>
<!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCbAq1UtzoUlVx4djUkuETaRur7X4TEel4"></script> -->
<div id="map" style="width:100%;height:600px;"></div>

<script>
   const items = <?php echo json_encode($arr); ?>;
  

  function initMap() {
            const map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 37.7749, lng: -122.4194 }, // Change this to your desired starting point
                zoom: 12, // Adjust the initial zoom level as needed
            });

            // Define the directions service
            const directionsService = new google.maps.DirectionsService();
            const directionsDisplay = new google.maps.DirectionsRenderer({ map: map });


           // var items = ["it park panchkula, HR", "sector 25, panchkula", "sector 21 panchkula"];
         
            var waypoints = [];
              for (var i = 0; i < items.length; i++) {
                  var address = items[i];
                  if (address !== "") {
                      waypoints.push({
                          location: address,
                          stopover: true
                      });
                  }
              }


            // Specify your origin and destination
            const origin = '<?php echo $start; ?>';//'tricity plaza, peermuchala, PB';  Change this to your starting location
            const destination =  '<?php echo $start; ?>'; //'tricity plaza, peermuchala, PB'  Change this to your destination

            // Create a request object for the Directions API

            var request = {
            origin: origin,
            destination: destination,
            waypoints: waypoints, //an array of waypoints
            optimizeWaypoints: true, //set to true if you want google to determine the shortest route or false to use the order specified.
            travelMode: google.maps.DirectionsTravelMode.DRIVING // You can change this to other modes like 'WALKING', 'BICYCLING', or 'TRANSIT'
        };
            // Use the Directions service to generate a route
            directionsService.route(request, function (response, status) {
                if (status === 'OK') {
                    // Display the route on the map
                    directionsDisplay.setDirections(response);
                } else {
                    window.alert('Directions request failed due to ' + status);
                }
            });
        }

        // Initialize the map when the page loads
        google.maps.event.addDomListener(window, 'load', initMap);
</script>

</x-app-layout>