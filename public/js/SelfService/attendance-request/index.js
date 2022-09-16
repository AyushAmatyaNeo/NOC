(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);

        $("#reset").on("click", function () {
            if (typeof document.ids !== "undefined") {
                $.each(document.ids, function (key, value) {
                    $("#" + key).val(value).change();
                });
            }
        });


        var $table = $('#attendanceRequestTable');
        var $search = $('#viewAttendanceRequestStatus');
        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.viewLink}/#:ID#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
                #if(ALLOW_EDIT=='Y'){#
                <a class="btn btn-icon-only yellow" href="${document.editLink}/#:ID#" style="height:17px;" title="Edit">
                    <i class="fa fa-edit"></i>
                </a>
                #}#
                #if(ALLOW_DELETE=='Y'){#
                <a  class="btn btn-icon-only red confirmation" href="${document.deleteLink}/#:ID#" style="height:17px;" title="Cancel">
                    <i class="fa fa-times"></i>
                </a>
                #}#
            </div>
        `;
        var columns = [
            {title: "Applied Date",
                columns: [{
                        field: "REQUESTED_DT_AD",
                        title: "AD",
                    },
                    {field: "REQUESTED_DT_BS",
                        title: "BS",
                    }]},
            {title: "Attendance Date",
                columns: [{
                        field: "ATTENDANCE_DT_AD",
                        title: "AD",
                    },
                    {field: "ATTENDANCE_DT_BS",
                        title: "BS",
                    }]},

            {field: "IN_TIME", title: "Check In", template: "<span>#: (IN_TIME == null) ? '-' : IN_TIME #</span>"},
            {field: "OUT_TIME", title: "Check Out", template: "<span>#: (OUT_TIME == null) ? '-' : OUT_TIME #</span>"},
            {field: "STATUS", title: "Status"},
            {field: ["ID", "ALLOW_EDIT", "ALLOW_DELETE"], title: "Action", template: action}
        ];
        app.initializeKendoGrid($table, columns, null, null, null, 'Attendance Request List');

        $search.on('click', function () {
            var employeeId = $('#employeeId').val();
            var attendanceRequestStatusId = $('#attendanceRequestStatusId').val();
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();

            app.pullDataById(document.pullAttendanceUrl, {
                'employeeId': employeeId,
                'attendanceRequestStatusId': attendanceRequestStatusId,
                'fromDate': fromDate,
                'toDate': toDate
            }).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });


        });

        app.searchTable('attendanceRequestTable', ['REQUESTED_DT_AD', 'REQUESTED_DT_BS', 'ATTENDANCE_DT_AD', 'ATTENDANCE_DT_BS', 'IN_TIME', 'OUT_TIME', 'A_STATUS']);

        var exportMap = {
            'REQUESTED_DT_AD': 'Request Date(AD)',
            'REQUESTED_DT_BS': 'Request Date(BS)',
            'ATTENDANCE_DT_AD': 'Attendance Date(AD)',
            'ATTENDANCE_DT_BS': 'Attendance Date(BS)',
            'IN_TIME': 'In Time',
            'OUT_TIME': 'Out Time',
            'IN_REMARKS': 'In Remarks',
            'OUT_REMARKS': 'Out Remarks',
            'TOTAL_HOUR': 'Total Hour',
            'A_STATUS': 'STATUS',
            'APPROVER_NAME': 'Approver',
            'APPROVED_REMARKS': 'Approved Remarks',
            'APPROVED_DT': 'Approved Date',
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Attendance Request List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Attendance Request List');
        });

    });
})(window.jQuery, window.app);

