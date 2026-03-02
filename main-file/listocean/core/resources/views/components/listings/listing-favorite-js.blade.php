<script>
    (function($){
        "use strict";
        $(document).ready(function(){

            //bookmarks
            $(document).on('click','.click_to_favorite_add_remove',function(){
                let $this = $(this);
                let listing_id = $this.data('listing_id');
                $.ajax({
                    url: "{{ route('listing.favorite.add.remove') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        listing_id: listing_id
                    },
                    success: function(res){
                        if(res.status === 'add_success'){
                            // Change the heart icon to filled heart
                            $this.find('i').removeClass('favorite_remove_icon').addClass('favorite_add_icon');
                            $this.closest('.favourite-icon').addClass('favourite');
                            toastr_success_js(res.message);
                        }else if(res.status === 'remove_success'){
                            $this.find('i').removeClass('favorite_add_icon').addClass('favorite_remove_icon');
                            $this.closest('.favourite-icon').removeClass('favourite');
                            toastr_warning_js(res.message);
                        }else{
                            toastr_warning_js(res.message);
                        }
                    }
                });
            });

        });
    })(jQuery);
</script>
