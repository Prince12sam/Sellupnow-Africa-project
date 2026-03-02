<script>
    (function($){
        "use strict";
        $(document).ready(function(){
            $(document).on('click','#update, #submitBtn',function () {
                $(this).attr("disabled", "disabled");
                $(this).html('<i class="fas fa-spinner fa-spin mr-1"></i> {{__("Submitting")}}');
                // Submit the form
                $(this).closest('form').trigger('submit');
            });
        });
    })(jQuery);
</script>
