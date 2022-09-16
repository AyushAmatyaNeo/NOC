(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate', null, false);
        app.datePickerWithNepali("form-loanDate","nepaliDate");

        var $shifts = $("#shifts");

        var shifts = [];
        
        $.each(document.selectedShift, function(k,v){
            shifts.push(v.SHIFT_ID);
        });

        $shifts.val(shifts).trigger('change.select2');

    });
})(window.jQuery, window.app);
