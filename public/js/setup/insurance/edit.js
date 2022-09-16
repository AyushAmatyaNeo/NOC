(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali("eligibleAfter", "nepaliEligibleAfter");

        if ($("#type").val() == 'SW') {
            $("#month").show().prop('required', true);
            $("#flat").hide().prop('required', false);
        }
        if ($("#type").val() == 'FW') {
            $("#month").hide().prop('required', false);
            $("#flat").show().prop('required', true);
        }


        $("#type").change(function () {
            console.log(type);

            if ($("#type").val() == 'SW') {
                $("#month").show().prop('required', true);
                $("#flat").hide().prop('required', false);
            }
            if ($("#type").val() == 'FW') {
                $("#month").hide().prop('required', false);
                $("#flat").show().prop('required', true);
            }

        });
    });
})(window.jQuery, window.app);