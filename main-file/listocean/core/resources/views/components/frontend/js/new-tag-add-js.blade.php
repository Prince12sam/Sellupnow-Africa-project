<script>
    (function ($) {
        "use strict";

        $(document).ready(function () {
            // Initialize select2
            $('#tags').select2({
                tags: true,
                tokenSeparators: [',', ' '],
                createTag: function (params) {
                    return {
                        id: '',
                        text: params.term,
                        newTag: true
                    };
                }
            });

            // max tag add start
            let maxTags = 20;
            $('#tags').on('select2:select', function (e) {
                checkMaxTags();
            });

            $('#tags').on('select2:unselect', function (e) {
                checkMaxTags();
            });

            $('#tags').on('select2:close', function (e) {
                checkMaxTags();
            });

            function checkMaxTags() {
                // Check if the maximum number of tags is reached
                if ($('#tags').val().length > maxTags) {
                    // Unselect the last selected tag
                    let removedTag = $('#tags').select2('data')[0];
                    $('#tags').val($('#tags').val().filter(tag => tag !== removedTag.id));
                    $('#tags').trigger('change');
                    // Show error message
                    toastr.error('Maximum of ' + maxTags + ' tags allowed.');
                }
            }
            // max tag add end

            $('#tags').on('select2:select', function (e) {
                let selectedTag = e.params.data;
                if (selectedTag.newTag) {
                    // Make an Ajax request to add the new tag
                    $.ajax({
                        type: 'POST',
                        url: '/add-new-tag',
                        data: { tag_name: selectedTag.text },
                        success: function (response) {
                            handleAddTagSuccess(response, selectedTag.text);
                        },
                        error: function (error) {
                            handleAddTagError(error);
                        }
                    });
                }
            });


            // Function to handle successful tag addition
            function handleAddTagSuccess(response, newTagName) {
                if (response.status === 'success') {
                    let new_tag_id = response.new_tag.id;
                    let new_tag_name = response.new_tag.name;

                    // Remove the existing option
                    // $('#tags option[value="' + newTagName + '"]').remove();

                    // Remove the existing option based on text
                    $('#tags option').filter(function() {
                        return $(this).text() === newTagName;
                    }).remove();

                    // Add the new option
                    $('#tags').append(new Option(new_tag_name, new_tag_id, true, true));

                    // Trigger change event to update Select2
                    $('#tags').trigger('change');

                    toastr.success('Tag added successfully: ' + newTagName);
                } else if (response.status === 'exists') {
                    toastr.warning('Tag already exists: ' + newTagName);
                } else {
                    // Handle other statuses if needed
                    toastr.error('Unexpected response status: ' + response.status);
                }
            }



            // Function to handle error during tag addition
            function handleAddTagError(error) {
                toastr.error('Error adding tag: ' + error.statusText);
            }
        });
    })(jQuery);
</script>
