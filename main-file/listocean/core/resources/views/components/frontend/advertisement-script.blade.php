<script>
    (function($){
        "use strict";

        $(document).ready(function(){

        //Home Advertisement Click Store
        $(document).on('click','#home_advertisement_store',function(){
            let id = $('#add_id').val();
            $.ajax({
            url : "{{route('frontend.home.advertisement.click.store')}}",
            type: "GET",
            data:{
            'id':id
            },
            success:function (data){
             }
            });
        });

        //Home Advertisement Click Store
        $(document).on('mouseover','#home_advertisement_store',function(){
            let id = $('#add_id').val();
            $.ajax({
            url : "{{route('frontend.home.advertisement.impression.store')}}",
            type: "GET",
            data:{
            'id':id
            },
            success:function (data){
                     }
                });
            });

        });
    })(jQuery);
</script>
