<script src="{{asset('assets/backend/js/summernote.js')}}"></script>
<script>
    (function ($) {
        "use strict";
        $(document).ready(function () {

            // Disable the codeview button
            $.extend($.summernote.options.buttons, {
                codeview: function () {
                    return false;
                }
            });

            $('.summernote').summernote({
                height: 150,
                codemirror: {
                    theme: 'monokai'
                },
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['link', 'hr']],
                    ['view', ['fullscreen', 'codeview']],
                ],
                callbacks: {
                    onChange: function (contents, $editable) {
                        $(this).prev('input').val(contents);
                    }
                }
            });

            // Remove the codeview button from the DOM
            $('.note-view .note-codeview-keep').remove();

            if ($('.summernote').length) {
                $('.summernote').each(function (index, value) {
                    $(this).summernote('code', $(this).data('content'));
                });
            }
        });
    })(jQuery);
</script>


