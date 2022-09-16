(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate');
        if (document.searchSelectedValues !== undefined) {
            document.searchManager.setSearchValues(document.searchSelectedValues);
        }
    });
})(window.jQuery, window.app);




