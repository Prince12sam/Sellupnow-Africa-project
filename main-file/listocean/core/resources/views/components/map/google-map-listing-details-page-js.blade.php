@if(!empty(get_static_option("google_map_settings_on_off")))
    @php
        $google_map_maker_icon = 'https://fonts.gstatic.com/s/i/materialicons/location_on/v14/24px.svg' ?? '';
     @endphp
    <script src="https://maps.googleapis.com/maps/api/js?key={{get_static_option("google_map_api_key")}}&libraries=places">
    <script defer src="//cdn.jsdelivr.net/npm/markerclustererplus/dist/markerclusterer.min.js"> </script>
    <script>
        var map;
        var markers = [];
        var infowindow = new google.maps.InfoWindow();
        var places;
        var latitude = '{{$lat}}';
        var longitude = '{{$lon}}';

        var centerLatLng = new google.maps.LatLng(latitude, longitude);
        function initialize() {
            var mapOptions = {
                zoom: 6,
                minZoom: 2,
                maxZoom: 14,
                zoomControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.DEFAULT
                },
                center: centerLatLng,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                scrollwheel: true,
                panControl: true,
                mapTypeControl: true,
                scaleControl: true,
                overviewMapControl: true,
                rotateControl: true,
            };
            map = new google.maps.Map(document.getElementById('single-map-canvas'), mapOptions);
            addMarkers();
        }

        google.maps.event.addDomListener(window, 'load', initialize);
        function addMarkers() {
            var google_maker_icon = "{{ isset($google_map_maker_icon) ? $google_map_maker_icon : 'https://maps.gstatic.com/mapfiles/ridefinder-images/mm_20_red.png' }}";
            var image = new google.maps.MarkerImage(google_maker_icon, null, null, null, new google.maps.Size(40, 52));

            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(
                    latitude,
                    longitude
                ),
                map: map,
                icon: image,
            });

            markers.push(marker);
            google.maps.event.addListener(marker, 'click', (function (marker) {
                return function () {
                    toggleMarkerAnimation(marker);
                    smoothZoomIn(map, marker, 12, 1000);
                };

            })(marker));
        }

        // Smooth zoom-in animation centered on a marker
        function smoothZoomIn(map, marker, zoomLevel, duration) {
            var currentZoom = map.getZoom();
            var targetZoom = Math.min(zoomLevel, map.maxZoom);
            var step = 1;
            var delay = Math.round(duration / (targetZoom - currentZoom));

            // Recursively increase the zoom level
            function zoom() {
                if (currentZoom < targetZoom) {
                    currentZoom += step;
                    map.setZoom(currentZoom);
                    setTimeout(zoom, delay);
                } else {
                    map.panTo(marker.getPosition());
                }
            }
            zoom();
        }

        // Function to toggle marker animation
        function toggleMarkerAnimation(marker) {
            if (marker.getAnimation() !== null) {
                marker.setAnimation(null);
            } else {
                marker.setAnimation(google.maps.Animation.BOUNCE);
                setTimeout(function() {
                    marker.setAnimation(null);
                }, 3000); // Stop animation after 3 seconds
            }
        }

    </script>
@endif
