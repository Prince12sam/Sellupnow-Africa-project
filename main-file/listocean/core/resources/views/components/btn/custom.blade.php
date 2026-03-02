<script>
    (function($){
        "use strict";
        $(document).ready(function () {
            $(document).on('click', '#update', function () {
                $(this).addClass("disabled");
                var buttonText = $(this).text().trim();
                var title = (buttonText === 'Update') ? '{{ __("Updating") }}' : '{{ __("Submitting") }}';
                $(this).html('<i class="fas fa-spinner fa-spin mr-1"></i> ' + title);
            });
        });
    })(jQuery);
</script>
