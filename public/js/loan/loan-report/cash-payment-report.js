(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        var $tableContainer = $("#loanRequestStatusTable");
        var $search = $('#search');

        var columns = [
            {field: "EMPLOYEE_CODE", title: "Emp. Code", width: 100},
            {field: "FULL_NAME", title: "Employee", width: 150},
            {field: "LOAN_NAME", title: "Loan", width: 120},
            {title: "Paid Date",
                columns: [{
                        field: "PAID_DATE_AD",
                        title: "AD",
                        width: 120},
                    {field: "PAID_DATE_BS",
                        title: "BS",
                        width: 120
                    }
                ]
            },
            {field: "TOTAL_AMOUNT", title: "Total Amount", width: 150},
            {field: "PAID_AMOUNT", title: "Paid Amount", width: 150},
            {field: "BALANCE", title: "Balance Amount", width: 150},
            {field: "REMARKS", title: "Remarks", width: 200}
        ];
 
        var map = {
            'EMPLOYEE_CODE': 'Emp. Code',
            'FULL_NAME': 'Name',
            'LOAN_NAME': 'Loan',
            'PAID_DATE_AD': 'Paid Date(AD)',
            'PAID_DATE_BS': 'Paid Date(BS)',
            'TOTAL_AMOUNT': 'Total Amt',
            'PAID_AMOUNT': 'Paid Amount',
            'BALANCE': 'Balance',
            'REMARKS': 'Remarks'
        }
        app.initializeKendoGrid($tableContainer, columns, null, null, null, 'Loan Cash Payment Report.xlsx');
        app.searchTable($tableContainer, ['FULL_NAME']);

        $search.on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['loanId'] = $('#loanId').val();
            q['loanRequestStatusId'] = $('#loanRequestStatusId').val();
            q['fromDate'] = $('#fromDate').val();
            q['toDate'] = $('#toDate').val();
            q['recomApproveId'] = $('#recomApproveId').val();
            App.blockUI({target: "#hris-page-content"});
            window.app.pullDataById(document.pullCashPaymentListLink, q).then(function (success) {
                App.unblockUI("#hris-page-content");
                app.renderKendoGrid($tableContainer, success.data);
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });
        });
        $('#excelExport').on('click', function () {
            app.excelExport($tableContainer, map, "Loan Request List.xlsx");
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($tableContainer, map, "Loan Request List.pdf");
        });
    });
})(window.jQuery, window.app);
