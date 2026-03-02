<script>
    (function($){
        "use strict";

        $(document).ready(function(){
            $(document).on('change','#search_by_country,#search_by_state,#search_by_city,#search_by_category,#search_by_subcategory, #search_by_child_category, #search_by_rating,#search_by_sorting',function(e){
                e.preventDefault();
                // get price and set value
                let left_value = $('.input-min').val();
                let right_value = $('.input-max').val();
                $('#price_range_value').val(left_value + ',' + right_value);

                // google map km set
                let distance_km_value = $('#slider-value').text();
                $('#distance_kilometers_value').val(distance_km_value);
                let get_autocomplete_value = $('#autocomplete').val();
                $('#autocomplete_address').val(get_autocomplete_value);

                $('#search_listings_form').trigger('submit');
            });

            $(document).on('click','#yesterday,#last_week,#today',function(e){
                e.preventDefault();

                // get price and set value
                let left_value = $('.input-min').val();
                let right_value = $('.input-max').val();
                $('#price_range_value').val(left_value + ',' + right_value);

                // google map km set
                let distance_km_value = $('#slider-value').text();
                $('#distance_kilometers_value').val(distance_km_value);
                let get_autocomplete_value = $('#autocomplete').val();
                $('#autocomplete_address').val(get_autocomplete_value);



                // Determine the value based on the clicked element
                let date_posted_value;
                if ($(this).is('#yesterday')) {
                    date_posted_value = 'yesterday';
                } else if ($(this).is('#last_week')) {
                    date_posted_value = 'last_week';
                } else if ($(this).is('#today')) {
                    date_posted_value = 'today';
                }
                // Set the value to the hidden input field
                $('#date_posted_listing').val(date_posted_value);

                $('#search_listings_form').trigger('submit');
            });

            $(document).on('click','#card_grid,#card_list',function(e){
                e.preventDefault();

                // get price and set value
                let left_value = $('.input-min').val();
                let right_value = $('.input-max').val();
                $('#price_range_value').val(left_value + ',' + right_value);
                // google map km set
                $('#distance_kilometers_value').val(0);
                $('#autocomplete_address').val('');
                $('#autocomplete').val('');
                $('#location_city_name').val('');
                $('#longitude').val(0);
                $('#latitude').val(0);
                $('#price_range_value').val(0);

                // Determine the value based on the clicked element
                let listing_card_view_value;
                if ($(this).is('#card_grid')) {
                    listing_card_view_value = 'grid';
                } else if ($(this).is('#card_list')) {
                    listing_card_view_value = 'list';
                }
                // Set the value to the hidden input field
                $('#listing_grid_and_list_view').val(listing_card_view_value);

                showLoader();
                handleFormSubmission();

                $('#search_listings_form').trigger('submit');
            });

            $(document).on('click','#featured, #top_listing',function(e){
                e.preventDefault();

                // get price and set value
                let left_value = $('.input-min').val();
                let right_value = $('.input-max').val();
                $('#price_range_value').val(left_value + ',' + right_value);

                // google map km set
                let distance_km_value = $('#slider-value').text();
                $('#distance_kilometers_value').val(distance_km_value);
                let get_autocomplete_value = $('#autocomplete').val();
                $('#autocomplete_address').val(get_autocomplete_value);

                let listing_type_preferences_value;
                if ($(this).is('#featured')) {
                    listing_type_preferences_value = 'featured';
                } else if ($(this).is('#top_listing')) {
                    listing_type_preferences_value = 'top_listing';
                }
                // Set the value to the hidden input field
                $('#listing_type_preferences').val(listing_type_preferences_value);
                $('#search_listings_form').trigger('submit');
            });

            $(document).on('click','#new, #used',function(e){
                e.preventDefault();
                // get price and set value
                let left_value = $('.input-min').val();
                let right_value = $('.input-max').val();
                $('#price_range_value').val(left_value + ',' + right_value);

                // google map km set
                let distance_km_value = $('#slider-value').text();
                $('#distance_kilometers_value').val(distance_km_value);
                let get_autocomplete_value = $('#autocomplete').val();
                $('#autocomplete_address').val(get_autocomplete_value);

                let condition_listing_value;
                if ($(this).is('#new')) {
                    condition_listing_value = 'new';
                } else if ($(this).is('#used')) {
                    condition_listing_value = 'used';
                }
                // Set the value to the hidden input field
                $('#listing_condition').val(condition_listing_value);
                $('#search_listings_form').trigger('submit');
            });

            // Service search by text
            var oldSearchQ = '';
            $(document).on('keyup','#search_by_query',function(e){
                e.preventDefault();

                // get price and set value
                let left_value = $('.input-min').val();
                let right_value = $('.input-max').val();
                $('#price_range_value').val(left_value + ',' + right_value);

                // google map km set
                let distance_km_value = $('#slider-value').text();
                $('#distance_kilometers_value').val(distance_km_value);
                let get_autocomplete_value = $('#autocomplete').val();
                $('#autocomplete_address').val(get_autocomplete_value);

                let qVal = $(this).val().trim();

                if(oldSearchQ !== qVal){
                    setTimeout(function (){
                        oldSearchQ = qVal.trim();
                        if(qVal.length > 2){
                            $('#search_listings_form').trigger('submit');
                        }
                    },2000);
                }
            });

            // Function to show the loader
            function showLoader() {
                $('#loader').show();
                $('.customTab-content-1, .googleWraper, .custom-pagination').hide();
            }

            // Function to handle form submission
            function handleFormSubmission() {
                setTimeout(function() {
                    $('.customTab-content-1, .googleWraper, .custom-pagination').show();
                    $('#loader').hide();
                }, 2000);
            }

            // Hide the loader initially
            $('#loader').hide();

        });
    })(jQuery);
</script>
