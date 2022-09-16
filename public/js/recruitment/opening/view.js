(function ($,app) {
    'use strict';
    $(document).ready(function () {
        var employeeId = $('#employeeId').val();
        window.app.floatingProfile.setDataFromRemote(employeeId);

        app.datePickerWithNepali('Start_dt', 'nepaliStartDate');
        app.datePickerWithNepali('End_dt', 'nepaliEndDate');
        app.datePickerWithNepali('Extended_dt', 'nepaliExtendedDate');

        var $print = $('#print');
        $print.on('click', function () {
            app.exportDomToPdf('printableArea', document.urlCss);
        });
               
    });
})(window.jQuery, window.app);
