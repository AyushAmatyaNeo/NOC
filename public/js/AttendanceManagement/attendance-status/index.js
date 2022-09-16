(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        var $tableContainer = $("#attendanceRequestStatusTable");
        var $status = $('#attendanceRequestStatusId');
        var $search = $("#search");
        var $excelExport = $("#excelExport");
        var $pdfExport = $("#pdfExport");
        var $bulkActionDiv = $('#bulkActionDiv');
        var $bulkBtns = $(".btnApproveReject");

        $.each(document.searchManager.getIds(), function (key, value) {
            $('#' + value).select2();
        });
        $status.select2();

        var columns = [
            {field: "EMPLOYEE_CODE", title: "Code"},
            {field: "FULL_NAME", title: "Employee", template: "<span>#: (FULL_NAME == null) ? '-' : FULL_NAME #</span>"},
            {title: "Requested Date",
                columns: [{
                        field: "REQUESTED_DT_AD",
                        title: "AD",
                        template: "<span>#: (REQUESTED_DT_AD == null) ? '-' : REQUESTED_DT_AD #</span>"},
                    {field: "REQUESTED_DT_BS",
                        title: "BS",
                        template: "<span>#: (REQUESTED_DT_BS == null) ? '-' : REQUESTED_DT_BS #</span>"}]},
            {title: "Attendance Date",
                columns: [{
                        field: "ATTENDANCE_DT_AD",
                        title: "AD",
                        template: "<span>#: (ATTENDANCE_DT_AD == null) ? '-' : ATTENDANCE_DT_AD #</span>"},
                    {field: "ATTENDANCE_DT_BS",
                        title: "BS",
                        template: "<span>#: (ATTENDANCE_DT_BS == null) ? '-' : ATTENDANCE_DT_BS #</span>"}]},
            {field: "IN_TIME", title: "Check In", template: "<span>#: (IN_TIME == null) ? '-' : IN_TIME #</span>"},
            {field: "OUT_TIME", title: "Check Out", template: "<span>#: (OUT_TIME == null) ? '-' : OUT_TIME #</span>"},
            {field: "IN_REMARKS", title: "In Remarks"},
            {field: "OUT_REMARKS", title: "Out Remarks"},
            //{field: "STATUS", title: "Status", template: "<span>#: (STATUS == null) ? '-' : STATUS #</span>"},
            {field: "ID", title: "Action", template: `
                <span>
                    <a class="btn  btn-icon-only btn-success" href="${document.viewLink}/#: ID #" style="height:17px;" title="view">
                        <i class="fa fa-search-plus"></i>
                    </a>
                </span>`}
                ];
        columns = app.prependPrefColumns(columns);
        var map = {
            'EMPLOYEE_CODE': 'Code',
            'FULL_NAME': 'Name',
            'REQUESTED_DT_AD': 'Req.Date(AD)',
            'REQUESTED_DT_BS': 'Req.Date(BS)',
            'ATTENDANCE_DT_AD': 'AttenDate(AD)',
            'ATTENDANCE_DT_BS': 'AttenDate(BS)',
            'IN_TIME': 'In Time',
            'OUT_TIME': 'Out Time',
            'TOTAL_HOUR': 'Total Hrs',
            'IN_REMARKS': 'In Remarks',
            'OUT_REMARKS': 'Out Remarks',
            //'STATUS': 'Status',
            'APPROVED_DT': 'Approved Date',
            'APPROVED_REMARKS': 'Approved Remarks',

        };
        map = app.prependPrefExportMap(map);
        var pk = 'ID';
        var grid = app.initializeKendoGrid($tableContainer, columns, null, {id: pk, atLast: false, fn: function (selected) {
                if (selected) {
                    $bulkActionDiv.show();
                } else {
                    $bulkActionDiv.hide();
                }
            }}, null, 'Attendance Request List.xlsx');
        app.searchTable('attendanceRequestStatusTable', ['FULL_NAME', 'EMPLOYEE_CODE', 'REQUESTED_DT_AD', 'ATTENDANCE_DT_AD', 'REQUESTED_DT_BS', 'ATTENDANCE_DT_BS', 'IN_TIME', 'OUT_TIME', 'YOUR_ROLE', 'STATUS']);


        $search.on("click", function () {
            var q = document.searchManager.getSearchValues();
            q['attendanceRequestStatusId'] = $status.val();
            q['fromDate'] = $('#fromDate').val();
            q['toDate'] = $('#toDate').val();
            q['approverId'] = $('#approverId').val();

            window.app.serverRequest(document.pullAttendanceRequestStatusListLink, q).then(function (success) {
                app.renderKendoGrid($tableContainer, success.data);
            }, function (failure) {
            });
        });

        $excelExport.on('click', function () {
            app.excelExport($tableContainer, map, "AttendanceRequestList.xlsx");
        });
        $pdfExport.on('click', function () {
            app.exportToPDF($tableContainer, map, "AttendanceRequestList.pdf");
        });
        $bulkBtns.bind("click", function () {
            var list = grid.getSelected();
            var action = $(this).attr('action');

            var selectedValues = [];
            for (var i in list) {
                selectedValues.push({id: list[i][pk], action: action, status: list[i]['STATUS'] });
            }
            app.bulkServerRequest(document.bulkLink, selectedValues, function () {
                $search.trigger('click');
            }, function (data, error) {

            });
        });

    });
})(window.jQuery, window.app);

      