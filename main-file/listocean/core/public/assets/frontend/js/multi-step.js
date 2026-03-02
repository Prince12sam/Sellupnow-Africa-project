(function ($) {
    "use strict";
    $(document).ready(function () {

        $("#submitBtn, #prevBtn").hide();
        // Click for the Continue button
       $(document).on('click', '#nextBtn', function () {
            $(this).hide();
            $("#submitBtn, #prevBtn").show();
        });


        // Service Add next previous tab js
        var totalTab = $('#add-listing-tab .nav-link').length;
        var tabNavList = $('#add-listing-tab .nav-link');
        let currentTabIndex = 1;

        $(document).on('click', '#add-listing-tab .nav-link', function () {
            if ((totalTab - 1) === tabNavList.index($(this))) {
                $('#nextBtn').hide();
                $('#submitBtn').show();
            } else {
                $('#nextBtn').show();
                $('#submitBtn').hide();
            }
        });



        //service next and previous js start
        $(document).on('click', '#nextBtn', function (e) {

            let currentState = $('#add-listing-tab .nav-link.active');
            let currentContent = $('#add-listing-tabContent .step.active');
            currentTabIndex = currentState.index() + 1;

            if (currentTabIndex === totalTab) {
                return false;
            }
            if (currentTabIndex === totalTab - 1) {
                // $(this).text("Submit").attr('type', 'submit');
                $('#nextBtn').hide();
                $('#submitBtn').show();
            } else {
                $('#nextBtn').show();
                $('#submitBtn').hide();
            }
            currentState.removeClass('active show').next().addClass('active show');
            currentState.next();

            currentContent.siblings().removeClass('active show')
            currentContent.removeClass('active show').next().addClass('active show');
            currentContent.next();

            currentTabIndex++;
        });


        $(document).on('click', '#prevBtn', function (e) {

            let currentState = $('#add-listing-tab .nav-link.active');
            let currentContent = $('#add-listing-tabContent .step.active');
            currentTabIndex = currentState.index() + 1;

            if (currentTabIndex === 1) return;

            if (currentTabIndex === totalTab) {
                $('#nextBtn').show();
                $('#submitBtn').hide();
            } else {
                $('#nextBtn').show();
                $('#submitBtn').hide();
            }

            currentState.removeClass('active show').prev().addClass('active show');
            currentState.prev();

            currentContent.siblings().removeClass('active show')
            currentContent.removeClass('active show').prev().addClass('active show');
            currentContent.prev();

            currentTabIndex--;
        });

    });
})(jQuery)
