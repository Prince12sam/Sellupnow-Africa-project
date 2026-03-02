<script>
    (function ($) {
        "use strict";
        $(document).ready(function () {
            @if($featuredenable === false)
                $("#is_featured").prop("disabled", true);
                $("#is_featured").addClass('feature_disable_color');
            @else
            $("#is_featured").removeClass('feature_disable_color');
                $(document).on('click', '#is_featured', function (e) {
                    if ($(this).is(':disabled')) {
                       return false;
                     }
                  $('#is_featured').val($(this).is(':checked') ? '1' : '');
                });
            @endif
        });
    })(jQuery);
</script>
