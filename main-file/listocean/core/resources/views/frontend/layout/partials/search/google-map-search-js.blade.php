@if(!empty(get_static_option('google_map_settings_on_off')))
    @php
        $root_url = url('/');
        $listing_page_url = $root_url . '/' . get_static_option('listing_filter_page_url');
        $request_url = URL::current();
        $check_google_map_for_page = !empty($root_url) || !empty($listing_page_url);
    @endphp

   <!-- google map for live location -->
    @if($check_google_map_for_page)
        <script src="https://maps.googleapis.com/maps/api/js?key={{get_static_option('google_map_api_key')}}&libraries=places&v=3.46.0"></script>
    @endif

    @if($check_google_map_for_page)
        <script>
            // Function to get a cookie
            function getCookie(name) {
                var nameEQ = name + "=";
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
                }
                return null;
            }
        </script>
    @endif

    @if($check_google_map_for_page)
        <script>
            // Cookie add set of time
            function setCookie(name, value, days) {
                var expires = "";
                if (days) {
                    var date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = "; expires=" + date.toUTCString();
                }
                document.cookie = name + "=" + (value || "") + expires + "; path=/";
            }

            // if  Cookie id null
            function getCookie(name) {
                var nameEQ = name + "=";
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
                }
                return null;
            }

            // Function to get the visitor's country and coordinates
            function getVisitorLocation() {
                // Check if the Geolocation API is supported
                if (navigator.geolocation) {
                    // Get the visitor's current position
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var latitude = position.coords.latitude;
                        var longitude = position.coords.longitude;

                        // Store the latitude and longitude in localStorage
                        localStorage.setItem('latitude', latitude);
                        localStorage.setItem('longitude', longitude);

                        // Create a new Geocoder object
                        var geocoder = new google.maps.Geocoder();
                        // Prepare the latitude and longitude values as a LatLng object
                        var latLng = new google.maps.LatLng(latitude, longitude);

                        // Reverse geocode the coordinates to get the address
                        geocoder.geocode({ 'location': latLng }, function(results, status) {
                            if (status === 'OK') {
                                if (results[0]) {
                                    // Get the country component from the address
                                    var address = results[0].formatted_address;
                                    var placeId = results[0].place_id;
                                    var country = '';
                                    for (var i = 0; i < results[0].address_components.length; i++) {
                                        var component = results[0].address_components[i];
                                        if (component.types.includes('country')) {
                                            country = component.long_name;
                                            break;
                                        }
                                    }


                                    $('#myLocationGetAddress').on('click', function() {
                                        if ('permissions' in navigator) {
                                            // Request location permission using the Permissions API
                                            navigator.permissions.query({ name: 'geolocation' }).then(function(permissionStatus) {
                                                if (permissionStatus.state === 'granted') {
                                                    // Location permission granted
                                                    $("#autocomplete").val(address);
                                                } else if (permissionStatus.state === 'prompt') {
                                                    // Location permission prompt
                                                    permissionStatus.onchange = function() {
                                                        if (permissionStatus.state === 'granted') {
                                                            // Location permission granted
                                                            $("#autocomplete").val(address);
                                                        }
                                                    };
                                                    permissionStatus.prompt();
                                                }
                                            });
                                        } else if ('geolocation' in navigator) {
                                            // Request location permission using the Geolocation API
                                            navigator.geolocation.getCurrentPosition(function(position) {
                                                // Location permission granted
                                                $("#autocomplete").val(address);
                                            }, function(error) {
                                            });
                                        }
                                    });

                                    // Display the stored place ID
                                    var storedPlaceId = getCookie('placeId');
                                }
                            }
                        });
                    }, function() {
                    });
                }
            }
            getVisitorLocation();
        </script>


        <!-- autocomplete address js start -->
        <script>
            $(document).ready(function () {
                $("#latitudeArea").addClass("d-none");
                $("#longtitudeArea").addClass("d-none");
            });
        </script>

        <!-- search user address wise-->
        <script>
            $(document).ready(function () {
                $('#autocomplete').on('keyup change click', function () {
                    var input = document.getElementById("pac-input");
                    var autocomplete = new google.maps.places.Autocomplete(input);
                    autocomplete.setFields(["address_components", "geometry"]);
                    autocomplete.addListener("place_changed", function () {
                        var place = autocomplete.getPlace();
                        if (!place.geometry || !place.geometry.location) {
                            return;
                        }
                        // Get the latitude and longitude of the selected place
                        let selectedLatitude = place.geometry.location.lat();
                    });
                });
            });
        </script>

        <script>
            google.maps.event.addDomListener(window, 'load', initialize);

            // Function to set a cookie
            function setCookie(name, value, days) {
                var expires = "";
                if (days) {
                    var date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = "; expires=" + date.toUTCString();
                }
                document.cookie = name + "=" + (value || "") + expires + "; path=/";
            }

            // Function to get a cookie
            function getCookie(name) {
                var nameEQ = name + "=";
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
                }
                return null;
            }

            function initialize() {
                var input = document.getElementById('autocomplete');
                var autocomplete = new google.maps.places.Autocomplete(input);

                autocomplete.addListener('place_changed', function () {
                    var place = autocomplete.getPlace();
                    $('#latitude').val(place.geometry.location.lat());
                    $('#longitude').val(place.geometry.location.lng());
                    $("#latitudeArea").removeClass("d-none");
                    $("#longitudeArea").removeClass("d-none");
                    $("#change_address_new").val(1);

                    // Check if stored placeId exists
                    var storedPlaceId = getCookie('placeId');
                    var placeId = place.place_id;
                    var currentPlaceId;
                    if (storedPlaceId) {
                        currentPlaceId = placeId;
                        if (currentPlaceId !== storedPlaceId) {
                            // Remove old cookies
                            document.cookie = "placeId=;expires=" + new Date(0).toUTCString();
                            document.cookie = "address=;expires=" + new Date(0).toUTCString();
                        }
                    }
                    setCookie('placeId', placeId, 7);
                    setCookie('address', place.formatted_address, 7);
                });


                $('.setLocation_btn').on('click', function () {
                    var changeAddress = $('#change_address_new').val();
                    if (changeAddress === '') {
                        // User didn't change the address, use current location-wise service
                        var place = autocomplete.getPlace();

                        if (typeof place !== 'undefined' && place.hasOwnProperty('place_id') && place.hasOwnProperty('formatted_address')) {
                            var placeId = place.place_id;
                            var address = place.formatted_address;
                            var id_add = setCookie('placeId', placeId, 7);
                            var add_add = setCookie('address', address, 7);
                        }

                    }
                });
            }
        </script>

        <!-- location address validation -->
        <script>
            $(document).ready(function () {
                // remove  disabled
                var autocompleteInput = $('#autocomplete');
                autocompleteInput.on('keyup click change', function() {
                    var getAutocompleteInputValue = $('#autocomplete').val();
                    if (getAutocompleteInputValue !== null) {
                        $('.setLocation_btn').removeAttr('disabled');
                    }else {
                        $(this).prop("disabled", false);
                    }
                });

            });
        </script>
        <!-- autocomplete address js end -->
    @endif
@endif
