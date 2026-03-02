<script>
    (function($){
        "use strict";
        $(document).ready(function(){

            $(document).on('click','.view-description',function(){
                var description = $(this).data('description');
                $('#descriptionContent').text(description);
                $('#descriptionModal').modal('show');
            });

            // pagination
            $(document).on('click', '.pagination a', function(e){
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                let string_search = $('#string_search').val();
                types(page,string_search);
            });
            function types(page,string_search){
                $.ajax({
                     url:"{{ route('admin.listing.report.paginate.data').'?page='}}" + page,
                    data:{string_search:string_search},
                    success:function(res){
                        $('.search_result').html(res);
                    }
                });
            }

            // search
            $(document).on('keyup','#string_search',function(){
                let string_search = $(this).val();
                $.ajax({
                    url:"{{ route('admin.listing.report.search') }}",
                    method:'GET',
                    data:{string_search:string_search},
                    success:function(res){
                        if(res.status=='nothing'){
                            $('.search_result').html('<h3 class="text-center text-danger">'+"{{ __('Nothing Found') }}"+'</h3>');
                        }else{
                            $('.search_result').html(res);
                        }
                    }
                });
            });
        });
    }(jQuery));
</script>
