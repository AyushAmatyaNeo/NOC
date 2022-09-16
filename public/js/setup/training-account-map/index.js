(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#trainingTable');
        app.initializeKendoGrid($table, [
            {field: "EXPENSE_NAME", title: "Expense Name"},
            {field: "ACCOUNT_CODE", title: "Account Code"},
        ], null, null, null, 'TrainingList');

        app.searchTable('trainingTable', ['EXPENSE_NAME', 'ACCOUNT_CODE']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'EXPENSE_NAME': 'Expense',
                'ACCOUNT_CODE': 'Account'
            }, 'TrainingExpenseAccounts');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'EXPENSE_NAME': 'Expense',
                'ACCOUNT_CODE': 'Account'
            }, 'TrainingExpenseAccounts');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);
