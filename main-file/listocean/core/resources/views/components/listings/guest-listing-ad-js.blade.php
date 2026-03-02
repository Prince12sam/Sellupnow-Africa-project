<script>
    (function ($) {
        "use strict";
        $(document).ready(function () {

            $(document).on('click', '#guest_register_request', function (e) {
                if ($(this).is(':disabled')) {
                   return false;
                 }
              $('#guest_register_request').val($(this).is(':checked') ? '1' : '');
                let check_value_this_get = $('#guest_register_request').val();
                if (check_value_this_get !== '1') {
                    $('#guest_error_message').empty();
                }

            });

            $('#guest_first_name, #guest_last_name, #guest_email, #guest_phone, #guest_register_request').on('keyup change click', function() {
                if ($(this).is(':checked')) {
                    $.ajax({
                        url: "{{ route('guest.request.check') }}",
                        method: 'POST',
                        data: {
                            guest_first_name: $('#guest_first_name').val(),
                            guest_last_name: $('#guest_last_name').val(),
                            guest_email: $('#guest_email').val(),
                            guest_phone: $('#guest_phone').val(),
                            guest_register_request: $('#guest_register_request').val(),
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                $('.guest_error_message').empty();
                            } else if (response.status === 'error') {
                                if (response.errors) {
                                    // Display validation errors
                                    response.errors.forEach(function(error) {
                                        // Check if the error message already exists
                                        if ($('#guest_error_message').find('span:contains("' + error + '")').length === 0) {
                                            $('#guest_error_message').append('<span class="text-danger">' + error + '</span');
                                        }
                                    });
                                } else {
                                    $('#guest_error_message').append('<span>' + response.message + '</span>');
                                }
                            }
                        },
                        error: function(response) {
                            // Handle AJAX error
                        }
                    });
                }

            });


        });
    })(jQuery);
</script>
