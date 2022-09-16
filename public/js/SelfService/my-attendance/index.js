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
        app.initializeKendoGrid($table, [
            {title: "Attendance Date",
                columns: [{
                        field: "ATTENDANCE_DT_AD",
                        title: "AD",
                        template: "<span>#: (ATTENDANCE_DT_AD == null) ? '-' : ATTENDANCE_DT_AD #</span>"},
                    {field: "ATTENDANCE_DT_BS",
                        title: "BS",
                        template: "<span>#: (ATTENDANCE_DT_BS == null) ? '-' : ATTENDANCE_DT_BS #</span>"}]},
            {field: "IN_TIME", title: "Check In"},
            {field: "OUT_TIME", title: "Check Out"},
            {field: "START_TIME", title: "Start Time"},
            {field: "END_TIME", title: "End Time"},
            {field: "TOTAL_HOUR", title: "Total Hour"},
            {field: "STATUS", title: "Status"},
            //{field: "IN_REMARKS", title: "Late In Reason"},
          //  {field: "OUT_REMARKS", title: "Early Out Reason"},
            //{field: ["ID", "LATE_STATUS"], title: "Action", template: actionTemplate}
        ], null, null, null, 'Attendance List');



        $('#myAttendance').on('click', function () {
            viewAttendance();
        });


        var viewAttendance = function () {
            console.log($('#fromDate').val());
            console.log($('#toDate').val());
            
            
            app.pullDataById(document.attendancelistUrl, {data: {
                    'fromDate': $('#fromDate').val(),
                    'toDate': $('#toDate').val(),
                    'employeeId': $('#employeeId').val(),
                    'status': $('#statusId').val(),
                    'presentStatus': $('#presentStatusId').val()
                }}).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });

        }



        app.searchTable('attendanceTable', ['ATTENDANCE_DT_AD', 'ATTENDANCE_DT_BS', 'IN_TIME', 'OUT_TIME', 'TOTAL_HOUR', 'STATUS', 'IN_REMARKS', 'OUT_REMARKS']);

        var map = {
            'ATTENDANCE_DT_AD': ' Attendance Date(AD)',
            'ATTENDANCE_DT_BS': ' Attendance Date(BS)',
            'IN_TIME': 'In Time',
            'OUT_TIME': 'Out Time',
            'START_TIME': 'Start Time',
            'END_TIME': 'End Time',
            'TOTAL_HOUR': 'Total Hour',
            'STATUS': 'Status',
            'IN_REMARKS': 'In Remarks',
            'OUT_REMARKS': 'Out Remarks'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Attendance List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Attendance List', 'A4');
        });

        var paramMap = {1: 'P', 2: 'L', 3: 'T', 4: 'TVL', 5: 'WOH', 6: 'LI', 7: 'EO', 8: 'MP'};
        setTimeout(function () {
            var pageUrl = window.location.href;
            var idFromParameter = pageUrl.substring(pageUrl.lastIndexOf('/') + 1);
            var fiscalYear = jQuery.parseJSON(document.fiscalYear);

            if (parseInt(idFromParameter) > 0) {
                var $status = $('#statusId');
                var $fromDate = $('#fromDate');
                var $toDate = $('#toDate');
                var $presentStatus = $('#presentStatusId');
                
                

                $fromDate.val(getDateFormat(fiscalYear.FROM_DATE));
                $toDate.val(getDateFormat(fiscalYear.TO_DATE));

                if (idFromParameter >= 6) {
                    $presentStatus.val(paramMap[idFromParameter]).change();
                } else {
                    $status.val(paramMap[idFromParameter]).change();
                }

                viewAttendance();
            }
        }, 6000);
        
        
        
        function getDateFormat(date) {
            var m_names = new Array("Jan", "Feb", "Mar",
                    "Apr", "May", "Jun", "Jul", "Aug", "Sep",
                    "Oct", "Nov", "Dec");


            var d = new Date(date);
            var curr_date = d.getDate();
            var curr_month = d.getMonth();
            var curr_year = d.getFullYear();
            return curr_date + "-" + m_names[curr_month]
                    + "-" + curr_year;

        }



    });



})(window.jQuery, window.app);