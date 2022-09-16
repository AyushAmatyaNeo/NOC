(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate');
        app.setLoadingOnSubmit("leaveApply");

        $('#approve').on('click', function () {
            var subRemarks = $("#form-subRemarks");

            if (typeof subRemarks !== "undefined") {
                subRemarks.removeAttr("required");
            }
        });
    });
})(window.jQuery, window.app);