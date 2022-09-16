(function ($, app) {
    'use strict';
    $(document).ready(function () {

        $('#startTime').combodate({
            minuteStep: 1
        });
        $('#endTime').combodate({
            minuteStep: 1
        });

        app.startEndDatePickerWithNepali('nepaliStartDate', 'adjustmentStartDate', 'nepaliEndDate', 'adjustmentEndDate', null, true);

    });
})(window.jQuery, window.app);
