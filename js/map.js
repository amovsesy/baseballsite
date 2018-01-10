var geocoder = null;
var map = null;
var directionsDisplay;
var directionsService = new google.maps.DirectionsService();

function initialize(address)
{
  directionsDisplay = new google.maps.DirectionsRenderer();
  var latlng = new google.maps.LatLng(0, 0);
  var myOptions = {
      zoom: 15,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  map = new google.maps.Map(document.getElementById("map"), myOptions);
  geocoder = new google.maps.Geocoder();
  showAddress(address);
  directionsDisplay.setMap(map);
  directionsDisplay.setPanel(document.getElementById("directionsPanel"));

}

function showAddress(address)
{
  geocoder.geocode(
  {
    'address' : address
  },
      function(results, status)
      {
        {
          if (status == google.maps.DirectionsStatus.OK)
          {
            map.setCenter(results[0].geometry.location);
            var marker = new google.maps.Marker(
            {
              map : map,
              position : results[0].geometry.location
            });
            map.addOverlay(marker);
          } 
        }
      });
}

function calcRoute(street, city, end)
{
  var start = street + ", " + city + ", CA";
  var request =
  {
    origin : start,
    destination : end,
    travelMode : google.maps.DirectionsTravelMode.DRIVING
  };
  directionsService.route(request, function(result, status)
  {
    if (status == google.maps.DirectionsStatus.OK)
    {
      directionsDisplay.setDirections(result);
    }
  });
}