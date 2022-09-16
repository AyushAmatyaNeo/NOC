(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        $("#reset").on("click", function () {
            if (typeof document.ids !== "undefined") {
                $.each(document.ids, function (key, value) {
                    $("#" + key).val(value).change();
                });
            }
        });
        var $table = $('#attendanceTable');
        var actionTemplate = `
                <a class="btn-edit" title="Attendance Request" href="${document.applyLink}/#:ID#" style="height:17px;display:#:(LATE_STATUS == 'X' || LATE_STATUS == 'Y')?'block':'none'#;">
                    <i class="fa fa-edit"></i>
                </a>
        `;

        $('#myAttendance').on('click', function () {
            viewAttendance();
        });

    });



})(window.jQuery, window.app);