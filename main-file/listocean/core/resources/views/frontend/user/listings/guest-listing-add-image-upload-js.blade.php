<script>
    (function ($) {
        "use strict";
        $(document).ready(function () {
            // Set the maximum allowed images
            let maxPhotosAllowed = '{{ !empty(get_static_option('guest_listing_gallery_image_upload_limit')) ? get_static_option('guest_listing_gallery_image_upload_limit') : 5 }}';

            let errorMessageDisplayed = false;
            $(document).on("click", ".media-uploader-image-list li", function() {
                const selectedPhotos = $(".media-uploader-image-list li.selected");
                if (selectedPhotos.length >= maxPhotosAllowed) {
                    $(".image-list-wr5apper ul li:not(.selected)").each(function() {
                        $(this).css("opacity", "0.5");
                        $(this).prop("disabled", true);
                    });
                    // $("#error_message_images").show();
                    let error_message_for_images = "{{ __('You can only select up to') }}" + " " + maxPhotosAllowed + " " + "{{ __('photos') }}";
                    errorMessageDisplayed = true;
                    toastr.error(error_message_for_images);
                }else if(selectedPhotos.length <= maxPhotosAllowed){
                    $(".image-list-wr5apper ul li:not(.selected)").each(function() {
                        $(this).css("opacity", "1");
                        $(this).removeAttr("disabled");
                    });
                }  else if (selectedPhotos.length <= maxPhotosAllowed && errorMessageDisplayed) {
                    errorMessageDisplayed = false;
                    toastr.clear();
                }
            });
        });
    })(jQuery)
</script>
