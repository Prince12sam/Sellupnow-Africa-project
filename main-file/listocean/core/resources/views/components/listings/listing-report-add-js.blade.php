<script>
    (function($){
        "use strict";
        $(document).ready(function(){
            // Report modal form submission
            $(document).on('submit', '#reportModal form', function(event) {
                event.preventDefault();
                var formData = $(this).serialize();
                var form = $(this);
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: formData,
                    success: function(res){
                        if(res.status == 'not_auth_error'){
                            toastr.error(res.message);
                        }else if(res.status == 'already_add_error'){
                            toastr.error(res.message);
                        }else if(res.status == 'error'){
                            $.each(res.message, function(key, value) {
                                toastr.error(value);
                            });
                        } else if(res.status == 'add_success'){
                            toastr.success(res.message);
                            $('#reportModal').modal('hide');
                            form[0].reset();
                        }
                    },  error: function(xhr, status, error) {
                        // Handle other types of errors, if necessary
                    }

                });
            });
        });
    })(jQuery);
</script>
