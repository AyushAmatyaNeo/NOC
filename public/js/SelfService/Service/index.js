(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);

        $("#reset").on("click", function () {
            if (typeof document.ids !== "undefined") {
                $.each(document.ids, function (key, value) {
                    $("#" + key).val(value).change();
                });
            }
        });


        var $table = $('#serviceHistoryTable');
        var viewAction = '<a class="btn  btn-icon-only btn-success" href="' + document.viewLink + '/#: JOB_HISTORY_ID #" style="height:17px;" title="view">'
                + '<i class="fa fa-search-plus"></i>'
                + '</a>';
        var action = viewAction;
        app.initializeKendoGrid($table, [
            {field: "START_DATE", title: "Start Date", width: 120, template: "#: (START_DATE == null) ? '-' : START_DATE #"},
            {field: "END_DATE", title: "End Date", width: 120, template: "#: (END_DATE == null) ? '-' : END_DATE #"},
            {field: "SERVICE_EVENT_TYPE_NAME", title: "Service Event Type", width: 180, template: "#: (SERVICE_EVENT_TYPE_NAME == null) ? ' ' : SERVICE_EVENT_TYPE_NAME #"},
            {field: "FROM_SERVICE_TYPE_NAME", title: "Service Type (From-To)", width: 220, template: "#: (FROM_SERVICE_TYPE_NAME == null) ? ' ' : FROM_SERVICE_TYPE_NAME # - #: (TO_SERVICE_TYPE_NAME == null) ? ' ' : TO_SERVICE_TYPE_NAME #"},
            {field: "FROM_BRANCH_NAME", title: "Branch (From-To)", width: 250, template: "#: (FROM_BRANCH_NAME == null) ? ' ' : FROM_BRANCH_NAME # - #: (TO_BRANCH_NAME == null) ? ' ' : TO_BRANCH_NAME #"},
            {field: "FROM_DEPARTMENT_NAME", title: "Department (From-To)", width: 300, template: "#: (FROM_DEPARTMENT_NAME == null) ? ' ' : FROM_DEPARTMENT_NAME # - #: (TO_DEPARTMENT_NAME == null) ? ' ' : TO_DEPARTMENT_NAME #"},
            {field: "FROM_DESIGNATION_TITLE", title: "Designation (From-To)", width: 300, template: "#: (FROM_DESIGNATION_TITLE == null) ? ' ' : FROM_DESIGNATION_TITLE # - #: (TO_DESIGNATION_TITLE == null) ? ' ' : TO_DESIGNATION_TITLE #"},
            {field: "FROM_POSITION_NAME", title: "Position (From-To)", width: 300, template: "#: (FROM_POSITION_NAME == null) ? ' ' : FROM_POSITION_NAME # - #: (TO_POSITION_NAME == null) ? ' ' : TO_POSITION_NAME #"},
            {field: ["JOB_HISTORY_ID"], title: "Action", width: 100, template: action}
        ], null, null, null, 'Service List');


        $('#myServiceHistory').on('click', function () {
            var employeeId = $('#employeeId').val();
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();

            app.pullDataById(document.pullSeriveListWS, {
                data: {
                    'fromDate': fromDate,
                    'toDate': toDate,
                    'employeeId': employeeId
                }}).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });


        app.searchTable('serviceHistoryTable', ['START_DATE', 'END_DATE', 'SERVICE_EVENT_TYPE_NAME', 'FROM_SERVICE_TYPE_NAME', 'FROM_BRANCH_NAME', 'FROM_DEPARTMENT_NAME', 'FROM_DESIGNATION_TITLE', 'FROM_POSITION_NAME']);


        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'START_DATE': 'Start Date',
                'END_DATE': 'End Date',
                'SERVICE_EVENT_TYPE_NAME': 'Type',
                'FROM_SERVICE_TYPE_NAME': 'From service',
                'TO_SERVICE_TYPE_NAME': 'To Service',
                'FROM_BRANCH_NAME': 'From Branch',
                'TO_BRANCH_NAME': 'To Branch',
                'FROM_DEPARTMENT_NAME': 'From Department',
                'TO_DEPARTMENT_NAME': 'To department',
                'FROM_DESIGNATION_TITLE': 'From Designation',
                'TO_DESIGNATION_TITLE': 'To Designation',
                'FROM_POSITION_NAME': 'From Position',
                'TO_POSITION_NAME': 'To Position'
            }, 'Service List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'START_DATE': 'Start Date',
                'END_DATE': 'End Date',
                'SERVICE_EVENT_TYPE_NAME': 'Type',
                'FROM_SERVICE_TYPE_NAME': 'From service',
                'TO_SERVICE_TYPE_NAME': 'To Service',
                'FROM_BRANCH_NAME': 'From Branch',
                'TO_BRANCH_NAME': 'To Branch',
                'FROM_DEPARTMENT_NAME': 'From Department',
                'TO_DEPARTMENT_NAME': 'To department',
                'FROM_DESIGNATION_TITLE': 'From Designation',
                'TO_DESIGNATION_TITLE': 'To Designation',
                'FROM_POSITION_NAME': 'From Position',
                'TO_POSITION_NAME': 'To Position'
            }, 'Service List');
        });



    });
})(window.jQuery, window.app);
