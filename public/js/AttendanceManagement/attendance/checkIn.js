(function ($, app) {
    'use strict';
    $(document).ready(function () {

        $('select').select2();
        $('#inTime').combodate({
            minuteStep: 1
        });

        var $attendanceDt = $("#attendanceDt");
        if (!($attendanceDt.is('[readonly]'))) {
            app.datePickerWithNepali("attendanceDt", "nepaliDate");
            app.getServerDate().then(function (response) {
                $attendanceDt.datepicker('setEndDate', app.getSystemDate(response.data.serverDate));
            }, function (error) {
                console.log("error=>getServerDate", error);
            });
        } else {
            app.datePickerWithNepali("attendanceDt", "nepaliDate");
        }

        let $employeeId = $('#employeeId');
        app.floatingProfile.setDataFromRemote($employeeId.val());

        $employeeId.on("change", function (e) {
            app.floatingProfile.setDataFromRemote($(e.target).val());
        });
        app.setLoadingOnSubmit("attendanceByHr", function () {
            if ($('#inTime').val() == '') {
                app.showMessage('In time is not set.', 'error');
                return false;
            }
            return true;
        });
    });
})(window.jQuery, window.app);


