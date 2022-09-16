(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#loanTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:LOAN_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:LOAN_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "LOAN_NAME", title: "Loan", width: 130},
            {field: "ISSUED_BY", title: "Issued By", width: 110},
            {field: "MIN_AMOUNT", title: "Amount Range", width: 110},
            {field: "INTEREST_RATE", title: "Interest Rate", width: 110},
            {field: "REPAYMENT_AMOUNT", title: "Repayment Amount", width: 130},
            {field: "REPAYMENT_PERIOD", title: "Repayment(In Month)", width: 150},
            {field: "LOAN_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'Loan List');

        app.searchTable('loanTable', ['LOAN_NAME', 'COMPANY_NAME', 'MIN_AMOUNT', 'INTEREST_RATE', 'REPAYMENT_AMOUNT', 'REPAYMENT_PERIOD']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'LOAN_NAME': 'Loan',
                'ISSUED_BY': 'Issued By',
                'MIN_AMOUNT': 'Min Amount',
                'INTEREST_RATE': 'Intrest Rate',
                'REPAYMENT_AMOUNT': 'Repayment Amount',
                'REPAYMENT_PERIOD': 'Repayment Period'
            }, 'LoanList');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'LOAN_NAME': 'Loan',
                'COMPANY_NAME': 'Company',
                'MIN_AMOUNT': 'Min Amount',
                'INTEREST_RATE': 'Intrest Rate',
                'REPAYMENT_AMOUNT': 'Repayment Amount',
                'REPAYMENT_PERIOD': 'Repayment Period'
            }, 'LoanList');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);