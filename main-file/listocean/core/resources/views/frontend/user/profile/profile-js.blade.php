<script>
    (function($){
        "use strict";
        $(document).ready(function(){
            //update profile
            $(document).on('submit','#update_profile_form',function(e){
                e.preventDefault();
                let first_name = $('#first_name').val();
                let last_name = $('#last_name').val();
                let email = $('#email').val();
                let country = $('#country_id').val();
                let state = $('#state_id').val();
                let city = $('#city_id').val();
                let image = $('#image').val();
                let phone = $('#phone').val();
                let address = $('#address').val();
                let about = $('#about').val();
                let is_notifications_allowed = $('#is_notifications_allowed').is(':checked') ? 1 : 0;
                let is_contact_info_visible = $('#is_contact_info_visible').is(':checked') ? 1 : 0;

                if(first_name == '' || last_name == '' || email == '' || phone == '' || country == '' || state == ''){
                    toastr_warning_js('Please fill all fields !');
                    return false;
                }else{

                    $('#user_profile_info_update').attr("disabled", "disabled");
                    $('#user_profile_info_update').html('<i class="fas fa-spinner fa-spin mr-1"></i> {{__("Submitting")}}');
                    // Submit the form

                    $.ajax({
                        url: "{{ route('user.profile.edit') }}",
                        type: 'post',
                        data: {
                            first_name: first_name,
                            last_name:last_name,
                            email:email,
                            country:country,
                            state:state,
                            city:city,
                            image:image,
                            phone:phone,
                            address:address,
                            about:about,
                            is_notifications_allowed:is_notifications_allowed,
                            is_contact_info_visible:is_contact_info_visible
                        },
                        success: function(res){
                            if(res.status == 'ok'){
                                window.location.reload();
                                toastr_success_js("{{ __('Profile Info Successfully Updated') }}");

                                // Re-enable the submit button and reset its text
                                $('#user_profile_info_update').removeAttr("disabled");
                                $('#user_profile_info_update').html('Save changes');

                            }else if(res.status == 'demo_route_on'){
                                toastr_warning_js("{{ __('This is demonstration purpose only, you may not able to change few settings, once your purchase this script you will get access to all settings.') }}");
                            }

                        },
                        error: function (err) {
                            let error = err.responseJSON;
                            $('.error_msg_container').html('');
                            $.each(error.errors, function (index, value) {
                                $('.error_msg_container').append('<p class="text-danger">'+value+'<p>');
                            });
                        }
                    });
                }
            });


            // change country and get state
            $(document).on('change','#country_id', function() {
                let country = $(this).val();
                $.ajax({
                    method: 'post',
                    url: "{{ route('au.state.all') }}",
                    data: {
                        country: country
                    },
                    success: function(res) {
                        if (res.status == 'success') {
                            let all_options = "<option value=''>{{__('Select State')}}</option>";
                            let all_state = res.states;
                            $.each(all_state, function(index, value) {
                                all_options += "<option value='" + value.id +
                                    "'>" + value.state + "</option>";
                            });
                            $(".get_country_state").html(all_options);
                            $(".state_info").html('');
                            if(all_state.length <= 0){
                                $(".state_info").html('<span class="text-danger"> {{ __('No state found for selected country!') }} <span>');
                            }
                        }
                    }
                })
            })

            // change state and get city
            $('#state_id').on('change', function() {
                let state = $(this).val();
                $.ajax({
                    method: 'post',
                    url: "{{ route('au.city.all') }}",
                    data: {
                        state: state
                    },
                    success: function(res) {
                        if (res.status == 'success') {
                            let all_options = "<option value=''>{{__('Select City')}}</option>";
                            let all_city = res.cities;
                            $.each(all_city, function(index, value) {
                                all_options += "<option value='" + value.id +
                                    "'>" + value.city + "</option>";
                            });
                            $(".get_state_city").html(all_options);

                            $(".city_info").html('');
                            if(all_city.length <= 0){
                                $(".city_info").html('<span class="text-danger"> {{ __('No city found for selected state!') }} <span>');
                            }
                        }
                    }
                });
            });

        });
    }(jQuery));

    //  toastr warning
    function toastr_warning_js(msg){
        Command: toastr["warning"](msg, "Warning !")
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    }
    //toastr success
    function toastr_success_js(msg){
        Command: toastr["success"](msg, "Success !")
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    }
</script>
