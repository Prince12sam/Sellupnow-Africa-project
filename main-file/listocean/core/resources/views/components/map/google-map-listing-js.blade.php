<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#viewer').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).on('change', '#customFileEg1', function () {
        readURL(this);
    });

    $(document).ready(function () {
        function initAutocomplete() {
            var myLatLng = {
                lat: <?= $user->latitude ?? '41.850033' ?>,
                lng: <?= $user->longitude ?? '-87.6500523' ?>
            };

            const map = new google.maps.Map(document.getElementById("map_canvas"), {
                center: myLatLng,
                zoom: 13,
                mapTypeId: "roadmap",
            });

            var marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
            });

            marker.setMap(map);
            var geocoder = new google.maps.Geocoder();

            // new start
            google.maps.event.addListener(map, 'click', function (mapsMouseEvent) {
                var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2);
                var coordinates = JSON.parse(coordinates);
                var latlng = new google.maps.LatLng(coordinates['lat'], coordinates['lng']);
                marker.setPosition(latlng);
                map.panTo(latlng);
                document.getElementById('latitude').value = coordinates['lat'];
                document.getElementById('longitude').value = coordinates['lng'];

                // Perform reverse geocoding to get the address details
                geocoder.geocode({ 'location': latlng }, function (results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            var countryName = '';
                            var cityName = '';

                            for (var i = 0; i < results[0].address_components.length; i++) {
                                var addressComponent = results[0].address_components[i];

                                if (addressComponent.types.includes('country')) {
                                    countryName = addressComponent.long_name;
                                }
                                if (addressComponent.types.includes('locality') || addressComponent.types.includes('postal_town')) {
                                    cityName = addressComponent.long_name;
                                }
                            }
                            // Update #user_address element with the complete address
                            var final_address = cityName + ', ' + countryName;
                            $('#user_address').val(final_address);
                        } else {
                        }
                    } else {
                    }
                });

            });
            // new end

            // Search box create
            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
            // Google map Search current view
            map.addListener("bounds_changed", () => {
                searchBox.setBounds(map.getBounds());
            });

            let markers = [];
            // info place
            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();
                if (places.length == 0) { return; }
                // select old marker remove
                markers.forEach((marker) => {
                    marker.setMap(null);
                });
                markers = [];
                // icon, name, location each
                const bounds = new google.maps.LatLngBounds();
                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) {
                        return;
                    }

                    var mrkr = new google.maps.Marker({
                        map,
                        title: place.name,
                        position: place.geometry.location,
                    });


                    // if click the pick location
                    google.maps.event.addListener(mrkr, "click", function (event) {
                        // for full address title start
                        var coordinates = JSON.stringify(event.latLng.toJSON(), null, 2);
                        var coordinates = JSON.parse(coordinates);
                        var latlng = new google.maps.LatLng(coordinates['lat'], coordinates['lng']);
                        marker.setPosition(latlng);
                        map.panTo(latlng);
                        // for full address title end

                        document.getElementById('latitude').value = this.position.lat();
                        document.getElementById('longitude').value = this.position.lng();

                        // for full address title start
                        geocoder.geocode({ 'location': latlng }, function (results, status) {
                            if (status === google.maps.GeocoderStatus.OK) {
                                if (results[0]) {
                                    var countryName = '';
                                    var cityName = '';

                                    for (var i = 0; i < results[0].address_components.length; i++) {
                                        var addressComponent = results[0].address_components[i];

                                        if (addressComponent.types.includes('country')) {
                                            countryName = addressComponent.long_name;
                                        }
                                        if (addressComponent.types.includes('locality') || addressComponent.types.includes('postal_town')) {
                                            cityName = addressComponent.long_name;
                                        }
                                    }

                                    // Update  the complete address
                                    var final_address = cityName + ', ' + countryName;
                                    $('#user_address').val(final_address);

                                } else {
                                }
                            }
                        });

                    });
                    markers.push(mrkr);
                    if (place.geometry.viewport) { bounds.union(place.geometry.viewport); } else { bounds.extend(place.geometry.location); }
                });
                map.fitBounds(bounds);
            });
        }
        initAutocomplete();

        // clear all value
        $(document).on('click', '.clear_all_value', function() {
            $('#name').val(null);
            $('#pac-input').val(null);
        });
    });

</script>
