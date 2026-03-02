<script>
    (function($){
        "use strict";
        $(document).ready(function(){

            // Initialize Select2 for add modal
            $('.select2-country, .select2-state').select2({
                dropdownParent: $('#addModal')
            });

            // Initialize Select2 for edit modal
            $('.select22-country, .select22-state').select2({
                dropdownParent: $('#editCityModal')
            });


            // add country
            $(document).on('click','.add_city',function(e){
                let city = $('#city').val();
                let state = $('#state').val();
                let country = $('#country').val();
                if(city == '' || state == '' || country == ''){
                    toastr_warning_js("{{ __('Please fill all fields !') }}");
                    return false;
                }

            });

            //show city in edit modal
            $(document).on('click','.edit_city_modal',function(){
                let city = $(this).data('city');
                let city_id = $(this).data('city_id');
                let state_id = $(this).data('state_id');
                let country_id = $(this).data('country_id');

                $('#city_name').val(city).trigger("change");
                $('#city_id').val(city_id).trigger("change");
                $('#state_id').val(state_id).trigger('change');
                $('#country_id').val(country_id).trigger("change");
            });

            // update city
            $(document).on('click','.edit_city',function(e){
                let city = $('#city_name').val();
                let state = $('#state_id').val();
                let country = $('#country_id').val();
                if(city == '' || state == '' || country == ''){
                    toastr_warning_js("{{ __('Please fill all fields !') }}");
                    return false;
                }
            });

            //change country and get state
            $('#country_id').on('change', function() {
                let country = $(this).val();
                $.ajax({
                    method: 'post',
                    url: "{{ route('au.state.all') }}",
                    data: {
                        country: country
                    },
                    success: function(res) {
                        if (res.status == 'success') {
                            let all_options = "<option value=''>{{__('Select State')}}</option>";
                            let all_state = res.states;
                            $.each(all_state, function(index, value) {
                                all_options += "<option value='" + value.id +
                                    "'>" + value.state + "</option>";
                            });
                            $(".get_country_state").html(all_options);
                            $(".info_msg").html('');
                            if(all_state.length <= 0){
                                $(".info_msg").html('<span class="text-danger"> {{ __('No state found for selected country!') }} <span>');
                            }
                        }
                    }
                });
            });

            // change country and get state
            $('#country').on('change', function() {
                let country = $(this).val();
                $.ajax({
                    method: 'post',
                    url: "{{ route('au.state.all') }}",
                    data: {
                        country: country
                    },
                    success: function(res) {
                        if (res.status == 'success') {
                            let all_options = "<option value=''>{{__('Select State')}}</option>";
                            let all_state = res.states;
                            $.each(all_state, function(index, value) {
                                all_options += "<option value='" + value.id +
                                    "'>" + value.state + "</option>";
                            });
                            $(".get_country_state").html(all_options);
                            if(all_state.length <= 0){
                                $(".info_msg").html('<span class="text-danger"> {{ __('No state found for selected country!') }} <span>');
                            }
                        }
                    }
                })
            });

            // pagination
            $(document).on('click', '.pagination a', function(e){
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                countries(page);
            });
            function countries(page){
                $.ajax({
                    url:"{{ route('admin.city.paginate.data').'?page='}}" + page,
                    success:function(res){
                        $('.search_result').html(res);
                    }
                });
            }

            // search state
            $(document).on('keyup','#string_search',function(){
                let string_search = $(this).val();
                $.ajax({
                    url:"{{ route('admin.city.search') }}",
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
        });
    }(jQuery));
</script>
