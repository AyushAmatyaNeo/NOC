(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#advanceTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:ADVANCE_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:ADVANCE_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "ADVANCE_NAME", title: "Advance", width: 130},
            {field: "COMPANY_NAME", title: "Company", width: 110},
            {field: "MIN_SALARY_AMT", title: "Min. Salary Amount", width: 130},
            {field: "AMOUNT_TO_ALLOW", title: "Amount To Allow", width: 120},
            {field: "MONTH_TO_ALLOW", title: "Month To Allow", width: 110},
            {field: "ADVANCE_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'Advance List');

        app.searchTable('advanceTable', ['ADVANCE_NAME', 'COMPANY_NAME', 'MIN_SALARY_AMT', 'AMOUNT_TO_ALLOW', 'MONTH_TO_ALLOW']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'ADVANCE_NAME': 'Advance Name',
                'COMPANY_NAME': 'Company',
                'MIN_SALARY_AMT': 'Min Salary Amt',
                'MAX_SALARY_AMT': 'Max Salary Amt',
                'AMOUNT_TO_ALLOW': 'Amount To Allow',
                'MONTH_TO_ALLOW': 'Month T0 Allow',
                'REMARKS': 'Remarks'
            }, 'AdvanceList');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'ADVANCE_NAME': 'Advance Name',
                'COMPANY_NAME': 'Company',
                'MIN_SALARY_AMT': 'Min Salary Amt',
                'MAX_SALARY_AMT': 'Max Salary Amt',
                'AMOUNT_TO_ALLOW': 'Amount To Allow',
                'MONTH_TO_ALLOW': 'Month T0 Allow',
                'REMARKS': 'Remarks'
            }, 'AdvanceList');
        });

        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);