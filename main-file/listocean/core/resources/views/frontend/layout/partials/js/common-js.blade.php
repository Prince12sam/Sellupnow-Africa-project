<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/{{current(explode('_',\App\Helpers\LanguageHelper::user_lang_slug()))}}.js"></script>
<script>
    $('.nav-new-menu-style li').addClass('list');
    $('select').select2({
        language: "{{current(explode('_',\App\Helpers\LanguageHelper::user_lang_slug()))}}"
    });
</script>
<!-- global ajax setup -->
<script> $.ajaxSetup({headers: {'X-CSRF-TOKEN': '{{csrf_token()}}'} }) </script>
<script>
    (function($){
        "use strict";
        $(document).ready(function(){
            // password check
            $(document).on('click', '.toggle-password', function() {
                $(this).toggleClass('show');
                let input = $(this).siblings('input');
                let icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('la-eye-slash').addClass('la-eye');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('la-eye').addClass('la-eye-slash');
                }
            });

            // modal close
            $('.close').on('click', function (){
                $('#media_upload_modal').modal('hide');
            });
            $(document).on('mouseup', function (e) {
                if ($(e.target).closest('.navbar-right-notification').find('.navbar-right-notification-wrapper').length === 0) {
                    $('.navbar-right-notification-wrapper').removeClass('active');
                }
            });
        });
    }(jQuery));
</script>
