(function ($, app) {
    'use strict';
    $(document).ready(function () {

        $('select').select2();

        $('#outTime').combodate({
            minuteStep: 1
        });

        app.datePickerWithNepali("attendanceDt", "nepaliDate");

        let $employeeId = $('#employeeId');
        app.floatingProfile.setDataFromRemote($employeeId.val());

        app.setLoadingOnSubmit("attendanceByHr", function () {
            if ($('#outTime').val() == '') {
                app.showMessage('Out time is not set.', 'error');
                return false;
            }
            return true;
        });
    });
})(window.jQuery, window.app);


