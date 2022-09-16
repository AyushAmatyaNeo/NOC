(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliStartDate', 'startDate', 'nepaliEndDate', 'endDate', null, false);

        var $shifts = $("#shifts");

        var shifts = [];

        $shifts.val(shifts).trigger('change.select2');

    });
})(window.jQuery, window.app);
