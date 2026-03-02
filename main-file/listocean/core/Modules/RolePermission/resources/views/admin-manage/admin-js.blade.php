<script>
    (function($){
        "use strict";
        $(document).ready(function(){
            $(document).on('click','.change_admin_password',function(){
                let admin_id = $(this).data('admin-id')
                $('input[name=admin_id_for_change_password]').val(admin_id)
            });
            $(document).on('click','.update_admin_password',function(){
                let password = $('input[name=password]').val()
                if (password == '') {
                    toastr_warning_js("{{ __('Password field is required.') }}")
                    return false
                }
            });
        });
    }(jQuery));
</script>
