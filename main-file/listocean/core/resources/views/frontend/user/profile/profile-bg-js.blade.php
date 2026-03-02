<script>
    (function($){
        "use strict";
        $(document).ready(function(){
            //update profile
            $(document).on('submit','#edit_profile_form',function(e){
                e.preventDefault();
                let profile_background = $('#profile_background').val();
                let profile_bg_image_request = $('#profile_bg_image_request').val();
                if(profile_background == ''){
                    toastr_warning_js('Please fill all fields !');
                    return false;
                }else{
                    $.ajax({
                        url: "{{ route('user.profile.edit') }}",
                        type: 'post',
                        data: {
                            profile_background:profile_background,
                            profile_bg_image_request:profile_bg_image_request,
                        },
                        success: function(res){
                            if(res.status == 'ok'){
                                window.location.reload();
                                toastr_success_js("{{ __('Profile Info Successfully Updated') }}");
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

        });
    }(jQuery));
</script>
