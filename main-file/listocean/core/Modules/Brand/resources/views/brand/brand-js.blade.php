<script>
    (function($){
        "use strict";
        $(document).ready(function(){

            $(document).on("click", ".media_upload_form_btn", function (){
                let prevModal = $(this).closest(".modal");
                if(prevModal.length > 0){
                    $(document).on("click", ".media_upload_modal_submit_btn , .modal .close-select-button", function (){
                        $(".media_upload_modal_submit_btn").closest('.modal').hide();
                        prevModal.modal("show");
                    });
                }
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

            // add brand
            $(document).on('click','.add_brand',function(e){
                let brand = $('#brand').val();
                if(brand == ''){
                    toastr_warning_js("{{ __('Please enter a brand !') }}");
                    return false;
                }
            });

            // show brand in edit modal
            $(document).on('click','.edit_brand_modal',function(){
                let brand = $(this).data('brand');
                let url = $(this).data('url');
                let brand_id = $(this).data('brand_id');

                let image = $(this).data('img_url');
                let image_id = $(this).data('img_id');

                $('#edit_brand').val(brand);
                $('#edit_url').val(url);
                $('#brand_id').val(brand_id);

                $('#editBrandModal').find('.media-upload-btn-wrapper .img-wrap').html('');
                $('#editBrandModal').find('.media-upload-btn-wrapper input').val('');
                if (image_id != '') {
                    $('#editBrandModal').find('.media-upload-btn-wrapper .img-wrap').html('<div class="attachment-preview"><div class="thumbnail"><div class="centered"><img class="avatar user-thumb" src="' + image + '" > </div></div></div>');
                    $('#editBrandModal').find('.media-upload-btn-wrapper input').val(image_id);
                    $('#editBrandModal').find('.media-upload-btn-wrapper .media_upload_form_btn').text('Change Image');
                }
            });

            // update brand
            $(document).on('click','.update_brand',function(){
                let brand = $('#edit_brand').val();
                if(brand == ''){
                    toastr_warning_js("{{ __('Please enter a brand !') }}");
                    return false;
                }
            });

            // pagination
            $(document).on('click', '.pagination a', function(e){
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                brands(page);
            });
            function brands(page){
                $.ajax({
                    url:"{{ route('admin.brand.paginate.data').'?page='}}" + page,
                    success:function(res){
                        $('.search_result').html(res);
                    }
                });
            }

            // search brand
            $(document).on('keyup','#string_search',function(){
                let string_search = $(this).val();
                $.ajax({
                    url:"{{ route('admin.brand.search') }}",
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
