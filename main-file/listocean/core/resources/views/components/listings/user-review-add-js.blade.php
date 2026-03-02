<script>
    (function($){
        "use strict";
        $(document).ready(function(){

            // review
            $(document).on('click', '.review_add_modal', function () {
                let el = $(this);
                let user_id = el.data('user_id');
                let form = $('#reviewModal');
                form.find('#user_id').val(user_id);
            });

            $("#review").rating({
                "value": 5,
                "click": function (e) {
                    $("#rating").val(e.stars);
                }
            });

            // Review modal form submission
            $(document).on('submit', '#reviewModal form', function(event) {
                event.preventDefault();
                let formData = $(this).serialize();
                let form = $(this);
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: formData,
                    success: function(res){
                        if(res.status == 'same_user_review_error'){
                            toastr.error(res.message);
                        }else if(res.status == 'not_auth_error'){
                            toastr.error(res.message);
                        }else if(res.status == 'already_add_error'){
                            toastr.error(res.message);
                        }else if(res.status == 'error'){
                            $.each(res.message, function(key, value) {
                                toastr.error(value);
                            });
                        } else if(res.status == 'add_success'){
                            toastr.success(res.message);
                            $('#reviewModal').modal('hide');
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
