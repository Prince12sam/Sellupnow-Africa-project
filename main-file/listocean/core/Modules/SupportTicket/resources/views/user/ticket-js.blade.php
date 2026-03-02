<script>
    (function($){
        "use strict";
        $(document).ready(function(){

            //for modal
            $('.select2_activation').select2({
                dropdownParent: $('#addModal')
            });

            // add ticket
            $(document).on('click','.add_ticket',function(e){
                let title = $('#title').val();
                let department = $('#department').val();
                let priority = $('#priority').val();
                let description = $('#description').val();

                if(title == '' || department == '' || priority == '' || description == ''){
                    toastr_warning_js("{{ __('All fields are required !') }}");
                    return false;
                }
            });

            // pagination
            $(document).on('click', '.pagination a', function(e){
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                let string_search = $('#string_search').val();
                $.ajax({
                    url:"{{ route('user.ticket.paginate.data').'?page='}}" + page,
                    data:{string_search:string_search},
                    success:function(res){
                        $('.search_result').html(res);
                    }
                });
            });


            // search ticket
            $(document).on('keyup','#string_search',function(){
                let string_search = $(this).val();
                $.ajax({
                    url:"{{ route('user.ticket.search') }}",
                    method:'POST',
                    data:{string_search:string_search},
                    success:function(res){
                        if(res.status=='nothing'){
                            $('.search_result').html('<h5 class="text-center text-danger mb-5 mt-5">'+"{{ __('Nothing Found') }}"+'</h5>');
                        }else{
                            $('.search_result').html(res);
                        }
                    }
                });
            })

        });
    }(jQuery));
</script>
