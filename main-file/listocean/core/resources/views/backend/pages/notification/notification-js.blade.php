<script>
    (function($){
        "use strict";
        $(document).ready(function(){

            // pagination
            $(document).on('click', '.pagination li a', function(e){
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                listing(page);
            });
            function listing(page){
                $.ajax({
                    url:"{{ route('admin.notification.paginate.data').'?page='}}" + page,
                    success:function(res){
                        $('.search_notification_result').html(res);
                    }
                });
            }

            // search state
            $(document).on('keyup','#string_search',function(){
                let string_search = $(this).val();
                $.ajax({
                    url:"{{ route('admin.notification.search') }}",
                    method:'GET',
                    data:{string_search:string_search},
                    success:function(res){
                        if(res.status=='nothing'){
                            $('.search_notification_result').html('<h3 class="text-center text-danger">'+"{{ __('Nothing Found') }}"+'</h3>');
                        }else{
                            $('.search_notification_result').html(res);
                        }
                    }
                });
            });


        });
    }(jQuery));

</script>
