(function ($, app) {
    'use strict';
    $(document).ready(function () {

        var $loanTable = $("#loanTable");

        let template = `
        <span> 
        <a class="btn btn-edit btn-icon-only btn-success" href="${document.editLink}/#: PAYMENT_ID #" style="height:17px;" title="view">
            <i class="fa fa-forward"></i>
        </a>
        </span>`;

        app.initializeKendoGrid($loanTable, [
            {field: "SNO", title: "SNO", width: 70, locked: true},
            {field: "FULL_NAME", title: "Employee", width: 130, locked: true},
            {field: "INTEREST_RATE", title: "Rate (%)", width: 80, locked: true},
            {field: "LOAN_NAME", title: "Loan Name", width: 100, locked: true},
            {field: "FROM_DATE", title: "From Date", width: 100, locked: true},
            {field: "TO_DATE", title: "To Date", width: 100, locked: true},
            {field: "AMOUNT", title: "Installment", width: 110, locked: true},
            {field: "PRINCIPLE_AMOUNT", title: "Principle", width: 80},
            {field: "INTEREST_AMOUNT", title: "Interest", width: 80},
            {field: "PAID", title: "paid", width: 150}
        ]);
        
        var map = {
            'SNO': 'SNO',
            'FULL_NAME': 'FULL_NAME',
            'INTEREST_RATE': 'INTEREST_RATE',
            'LOAN_NAME': 'LOAN_NAME',
            'FROM_DATE': 'FROM_DATE',
            'TO_DATE': 'TO_DATE',
            'PAYMENT_ID': 'PAYMENT_ID',
            'AMOUNT': 'INSTALLMENT',
            'PAID': 'PAID',
            'PRINCIPLE_AMOUNT': 'PRINCIPLE_AMOUNT',
            'INTEREST_AMOUNT': 'INTEREST_AMOUNT'
        };

        $(document).on('click', '.btn-edit', function(){
            var val = $(this).parent().siblings(":nth-of-type(7)").text();
            if(val == 0){
                return confirm("Are you sure you want to revert the skip this month?") ? true : false;
            }
            else{
                return confirm("Are you sure to skip loan payment this month?") ? true : false;
            }
        });

        app.serverRequest('', '').then(function (response) {
            if (response.success) {
                app.renderKendoGrid($loanTable, response.data);
                $(".btn-edit").find("i").removeClass().addClass("fa fa-forward");
            } else {
                app.showMessage(response.error, 'error');
            }
        }, function (error) {
            app.showMessage(error, 'error');
        });

        app.searchTable($loanTable, ['FULL_NAME']);

       
        $('#excelExport').on('click', function () {
            app.excelExport($loanTable, map, "Loan Request List.xlsx");
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($loanTable, map, "Loan Request List.pdf");
        });
    });
})(window.jQuery, window.app);
