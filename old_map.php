    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
    <script>
function initialize() {
  var myLatlng = new google.maps.LatLng(51.494782, -0.127242);
  var mapOptions = {mapTypeID: 'HYBRID',
minZoom:17, maxZoom:17, panControl: false,
    zoom: 17,
    center: myLatlng,
draggable: false, scrollWheel: false, tilt: 45
  }
          var contentString = '<div id="content">'+
            '<div id="siteNotice">'+
            '</div>'+
            '</div>';
  var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

  var marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      title: 'Hello World!'
  });
          var infowindow = new google.maps.InfoWindow({
          content: contentString
        });
        var marker = new google.maps.Marker({
          position: myLatlng,
          map: map,
          title: 'The Runway'
        });
         infowindow.open(map, marker);
        marker.addListener('click', function() {
         
        });
}

google.maps.event.addDomListener(window, 'load', initialize);

    </script>

<div id="map-canvas"></div>