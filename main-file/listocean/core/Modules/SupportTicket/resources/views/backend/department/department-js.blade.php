<script>
    (function($){
        "use strict";
        $(document).ready(function(){

            // change status
            $(document).on('click','.swal_status_change',function(e){
                e.preventDefault();
                Swal.fire({
                    title: '{{__("Are you sure to change status complete? Once you done you can not revert this !!")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('Yes, change it!') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).next().find('.swal_form_submit_btn').trigger('click');
                    }
                });
            });

            // add country
            $(document).on('click','.add_department',function(e){
                let name = $('#name').val();
                if(name == ''){
                    toastr_warning_js("{{ __('Please enter a department name !') }}");
                    return false;
                }
            });

            // show country in edit modal
            $(document).on('click','.edit_department_modal',function(){
                let department = $(this).data('department');
                let department_id = $(this).data('department_id');
                $('#edit_name').val(department);
                $('#department_id').val(department_id);
            });

            // update country
            $(document).on('click','.update_department',function(){
                let department = $('#edit_department').val();
                if(department == ''){
                    toastr_warning_js("{{ __('Please enter a department name !') }}");
                    return false;
                }
            });
        });
    }(jQuery));
</script>
