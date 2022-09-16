(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#trainingTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:EXPENSE_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:EXPENSE_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "TRAINING_NAME", title: "Training"},
            {field: "EXPENSE_NAME", title: "Expense"},
            {field: "AMOUNT", title: "Amount"},
            {field: "DESCRIPTION", title: "Description"},
            {field: "EXPENSE_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'TrainingList');

        app.searchTable('trainingTable', ['TRAINING_NAME', 'EXPENSE_NAME', 'AMOUNT', 'DESCRIPTION']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'TRAINING_NAME': 'Training',
                'EXPENSE_NAME': 'Expenses',
                'AMOUNT': 'Amount',
                'DESCRIPTION': 'Description'
            }, 'TrainingExpenseList');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'TRAINING_NAME': 'Training',
                'EXPENSE_NAME': 'Expenses',
                'AMOUNT': 'Amount',
                'DESCRIPTION': 'Description'
            }, 'TrainingExpenseList');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);
