<script>
    $(document).ready(function () {
        // empty check
        $(document).on('click', '.setLocation_btn', function() {
            var get_home_search_value = $('#home_search').val();
            var errorMessage = '{{ __('Please enter a search term.') }}';
            if (get_home_search_value === null || get_home_search_value === "") {
                toastr.warning(errorMessage);
                $(this).prop("disabled", true);
            }
        });

        // empty check
        $("#home_search").on('keyup click change', function() {
            var home_search_value = $(this).val();
            if (home_search_value === null || home_search_value === "") {
                $('.setLocation_btn').attr('disabled', 'disabled');
            } else {
                $('.setLocation_btn').removeAttr('disabled');
            }
        });
    });
</script>

<script>
    (function($){
        "use strict";

        $(document).ready(function(){
            // top listings filter to listings page
            $(document).on('click', '#submit_form_listing_filter_top', function(event) {
                event.preventDefault();
                $("#filter_with_listing_page_top").trigger('submit');
            });
            $(document).on('click', '.submit_form_listing_filter_category_wise_listing', function(event) {
                event.preventDefault();
                var formId = $(this).closest('form').attr('id');
                var count = $(".some-class").length;
                if (count > 0) {
                    $(".other-elements").attr('name', 'new_name');
                }
                $(this).closest('form').trigger('submit');
            });

            // Recent listings filter to listings page
            $(document).on('click', '#submit_form_listing_filter_recent', function(event) {
                event.preventDefault();
                $("#filter_with_listing_page_recent").trigger('submit');
            });

            // Attach the click event to
            $(document).on('click', '.submit_form_listing_filter_tag', function(event) {
                event.preventDefault();
                let tagId = $(this).data('tag-id');
                $("#tag_id").val(tagId);
                $("#filter_with_listing_page_tag").trigger('submit');
            });

            // Category wise listings filter to listings page
            $(document).on('click', '#submit_form_listing_filter_category', function(event) {
                event.preventDefault();
                $("#filter_with_listing_page_category").trigger('submit');
            });

            // Category wise listings filter to listings page
            $(document).on('click', '#submit_form_listing_filter_subcategory', function(event) {
                event.preventDefault();
                $("#filter_with_listing_page_subcategory").trigger('submit');
            });


            // search by country, state and city
            $(document).on('keyup','#home_search',function(e){
                e.preventDefault();

                let search_text = $(this).val();
                let city_id = $('#city_id').val();
                let state_id = $('#state_id').val();
                let country_id = $("#service_country_id").val();

                if(search_text.length > 0){
                    $('#home_search').parent().find('button[type="submit"] i').addClass('la-spin la-spinner').removeClass('la-search');
                    $.ajax({
                        url:"{{ route('frontend.home.search') }}",
                        method:"get",
                        data:{
                            search_text:search_text,
                            country_id:country_id,
                            state_id:state_id,
                            city_id:city_id,
                        },
                        success:function(res){
                            $('#home_search').parent().find('button[type="submit"] i').removeClass('la-spin la-spinner').addClass('la-search');
                            if (res.status == 'success') {
                                $('#all_search_result').html(res.result);
                            }else{
                                $('#all_search_result').html(res.result);
                            }
                            $('#all_search_result').show();
                        }

                    });
                }else{
                    $('#all_search_result').hide();
                }
            });
        });
    })(jQuery);
</script>
