(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('#employeeId').select2();
        var $employee = $('#employeeId');
        var $leave = $('#leaveId');
        var $search = $('#search');
        var $table = $('#leaveEncashmentTable');
        var viewAction = '<span><a class="btn-edit" href="' + document.viewLink + '/#: ID #" style="height:17px;" title="view detail">'
                + '<i class="fa fa-search-plus"></i>'
                + '</a>'
                + '</span>';
        var action = viewAction;
        app.initializeKendoGrid($table, [
            {field: "FULL_NAME", title: "Employee"},
            {field: "LEAVE_ENAME", title: "Leave"},
            {field: "REQUESTED_DATE", title: "Applied Date"},
            // {title: "From Date",
            //     columns: [{
            //             field: "FROM_DATE_AD",
            //             title: "AD",
            //             template: "<span>#: (FROM_DATE_AD == null) ? '-' : FROM_DATE_AD #</span>"},
            //         {field: "FROM_DATE_BS",
            //             title: "BS",
            //             template: "<span>#: (FROM_DATE_BS == null) ? '-' : FROM_DATE_BS #</span>"}
            //     ]},
            // {title: "To Date",
            //     columns: [{
            //             field: "TO_DATE_AD",
            //             title: "AD",
            //             template: "<span>#: (TO_DATE_AD == null) ? '-' : TO_DATE_AD #</span>"},
            //         {field: "TO_DATE_BS",
            //             title: "BS",
            //             template: "<span>#: (TO_DATE_BS == null) ? '-' : TO_DATE_BS #</span>"}
            //     ]},
            {field: "REQUESTED_DAYS_TO_ENCASH", title: "Requested Days"},
            {field: "TOTAL_ACCUMULATED_DAYS", title: "Total Accumulated Days"},
            {field: "REMAINING_BALANCE", title: "Remaining Balance"},
            // {field: "SUB_APPROVED_FLAG", title: "Approved"},
            // {field: ["ID"], title: "Action", template: action}
        ], null, null, null, 'Leave Notification List');

        app.searchTable($table, ['FULL_NAME', 'LEAVE_ENAME', 'REQUESTED_DATE','REQUESTED_DAYS_TO_ENCASH','TOTAL_ACCUMULATE_DAYS','REMAINING_BALANCE']);

        $search.on('click', function () {
            var q = { employeeId:$employee.val(),
            leaveId: $leave.val()};

            // console.log(q);

            app.pullDataById("", q).then(function (response) {
                app.renderKendoGrid($table, response.data);
            }, function (error) {
    
            });
        });



        var exportMap = {
            'FULL_NAME': 'Name',
            'LEAVE_ENAME': 'Leave',
            'REQUESTED_DAYS_TO_ENCASH': 'Requested Days',
            'TOTAL_ACCUMULATED_DAYS': 'Total Accumulated Days',
            'REMAINING_BALANCE':'Remaining Balance',
            'REQUESTED_DATE':'Applied Date'

        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Leave Notification List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Leave Notification List', 'A2');
        });
    });
})(window.jQuery, window.app);
