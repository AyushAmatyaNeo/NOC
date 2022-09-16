(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'form-fromDate', 'nepaliEndDate1', 'form-toDate');
        app.setLoadingOnSubmit("travelRequest-form");

        $('#approve').on('click', function () {
            var subRemarks = $("#form-subRemarks");

            if (typeof subRemarks !== "undefined") {
                subRemarks.removeAttr("required");
            }
        });
    });
})(window.jQuery, window.app);