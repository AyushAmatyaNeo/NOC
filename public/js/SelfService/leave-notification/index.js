(function ($, app) {
    'use strict';
    $(document).ready(function () {

        var $table = $('#leaveNotificationTable');
        var viewAction = '<span><a class="btn-edit" href="' + document.viewLink + '/#: ID #" style="height:17px;" title="view detail">'
                + '<i class="fa fa-search-plus"></i>'
                + '</a>'
                + '</span>';
        var action = viewAction;
        app.initializeKendoGrid($table, [
            {field: "FULL_NAME", title: "Employee"},
            {field: "LEAVE_ENAME", title: "Leave"},
            {title: "Applied Date",
                columns: [{
                        field: "REQUESTED_DT_AD",
                        title: "AD",
                        template: "<span>#: (REQUESTED_DT_AD == null) ? '-' : REQUESTED_DT_AD #</span>"},
                    {field: "REQUESTED_DT_BS",
                        title: "BS",
                        template: "<span>#: (REQUESTED_DT_BS == null) ? '-' : REQUESTED_DT_BS #</span>"}
                ]},
            {title: "From Date",
                columns: [{
                        field: "FROM_DATE_AD",
                        title: "AD",
                        template: "<span>#: (FROM_DATE_AD == null) ? '-' : FROM_DATE_AD #</span>"},
                    {field: "FROM_DATE_BS",
                        title: "BS",
                        template: "<span>#: (FROM_DATE_BS == null) ? '-' : FROM_DATE_BS #</span>"}
                ]},
            {title: "To Date",
                columns: [{
                        field: "TO_DATE_AD",
                        title: "AD",
                        template: "<span>#: (TO_DATE_AD == null) ? '-' : TO_DATE_AD #</span>"},
                    {field: "TO_DATE_BS",
                        title: "BS",
                        template: "<span>#: (TO_DATE_BS == null) ? '-' : TO_DATE_BS #</span>"}
                ]},
            {field: "NO_OF_DAYS", title: "Duration"},
            {field: "STATUS_DETAIL", title: "Overall Status"},
            {field: "SUB_APPROVED_FLAG", title: "Approved"},
            {field: ["ID"], title: "Action", template: action}
        ], null, null, null, 'Leave Notification List');

        app.searchTable('leaveNotificationTable', ['FULL_NAME', 'LEAVE_ENAME', 'REQUESTED_DT_AD', 'REQUESTED_DT_BS', 'FROM_DATE_AD', 'FROM_DATE_BS', 'TO_DATE_AD', 'TO_DATE_BS', 'NO_OF_DAYS', 'STATUS', 'APPROVED_FLAG']);

        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
        var exportMap = {
            'FULL_NAME': 'Name',
            'LEAVE_ENAME': 'Leave',
            'REQUESTED_DT_AD': 'Requested Date(AD)',
            'REQUESTED_DT_BS': 'Requested Date(BS)',
            'FROM_DATE_AD': 'From Date(AD)',
            'FROM_DATE_BS': 'From Date(BS)',
            'TO_DATE_AD': 'To Date(AD)',
            'TO_DATE_BS': 'To Date(BS)',
            'NO_OF_DAYS': 'No Days',
            'STATUS_DETAIL': 'Overall Status',
            'REMARKS': 'Remarks',
            'RECOMMENDER_NAME': 'Recommender',
            'APPROVER_NAME': 'Approver',
            'RECOMMENDED_REMARKS': 'Recommender Remarks',
            'RECOMMENDED_DT': 'Recommender Date',
            'APPROVED_REMARKS': 'Approved Remarks',
            'APPROVED_DT': 'Approved Date',
            'SUB_EMPLOYEE_NAME': 'Substitute Employee',
            'SUB_APPROVED_FLAG': 'Substitute App Flag',
            'SUB_APPROVED_DATE': 'Substitute Approved Date',
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Leave Notification List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Leave Notification List', 'A2');
        });
    });
})(window.jQuery, window.app);
