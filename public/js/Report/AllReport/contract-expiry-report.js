(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
        var $table = $('#table');
        var $search = $('#search');
        var $fromDate = $('#fromDate');
        var $toDate = $('#toDate');

        var columns = [
            {field: "EMPLOYEE_CODE", title: "Code", locked: true, width: 100},
            {field: "FULL_NAME", title: "Employee Name", locked: true, width: 180},
            {field: "BRANCH_NAME", title: "Branch Name", width: 140},
            {field: "DEPARTMENT_NAME", title: "Department Name", width: 160},
            {field: "START_DATE", title: "Start Date", width: 140},
            {field: "END_DATE", title: "End Date", width: 140},
            {field: "CONTRACT_STATUS", title: "Status", width: 140}];

        var map = {
            'EMPLOYEE_CODE': 'Code',
            'FULL_NAME': 'Name',
            'BRANCH_NAME': 'Branch Name',
            'DEPARTMENT_NAME': 'Department Name',
            'START_DATE': 'Start Date',
            'END_DATE': 'End Date',
        };

        app.searchTable('table', ['EMPLOYEE_CODE', 'FULL_NAME'], false);

        app.initializeKendoGrid($table, columns, null, null, null, 'Contract Expiry Report');

        $search.on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['fromDate'] = $fromDate.val();
            q['toDate'] = $toDate.val();

            app.serverRequest(document.getContractExpiry, q).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {

            });
        });

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Contract Expiry Report.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.pdfExport($table, map, 'Contract Expiry Report.pdf');
        });

    });
})(window.jQuery, window.app);