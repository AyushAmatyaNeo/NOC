(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
        var $tableContainer = $("#loanRequestStatusTable");
        var $search = $('#search');

        var columns = [
            {field: "EMPLOYEE_CODE", title: "Code", width: 100},
            {field: "FULL_NAME", title: "Employee", width: 150},
            {field: "LOAN_NAME", title: "Loan", width: 120},
            {field: "REQUESTED_AMOUNT", title: "Total Loan", width: 120},
            {title: "Taken/Payment Date",
                columns: [{
                        field: "DATE_AD",
                        title: "AD",
                        width: 120
                    },
                    {field: "DATE_BS",
                        title: "BS",
                        width: 120
                    }
                ]
            },
            {field: "PAID_AMOUNT", title: "Principle Amount", width: 120},
            {field: "INTEREST", title: "Interest Amount", width: 120},
            {field: "BALANCE", title: "Balance", width: 120},
            {field: "REMARKS", title: "Remarks", width: 120}
        ];
 
        var map = {
            'EMPLOYEE_CODE': 'Code',
            'FULL_NAME': 'Name',
            'LOAN_NAME': 'Loan',
            'REQUESTED_DATE_AD': 'Request Date(AD)',
            'REQUESTED_DATE_BS': 'Request Date(BS)',
            'LOAN_DATE_AD': 'Loan Date(AD)',
            'LOAN_DATE_BS': 'Loan Date(BS)',
            'REQUESTED_AMOUNT': 'Reqest Amt',
            'STATUS': 'Status',
            'PAID_AMOUNT': 'Paid Amount',
            'BALANCE': 'Balance'
        }
        app.initializeKendoGrid($tableContainer, columns, null, null, null, 'Loan Basic Report.xlsx');
        app.searchTable($tableContainer, ['FULL_NAME']);

        $search.on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['loanId'] = $('#loanId').val();
            q['loanRequestStatusId'] = $('#loanRequestStatusId').val();
            q['fromDate'] = $('#fromDate').val();
            q['toDate'] = $('#toDate').val();
            q['recomApproveId'] = $('#recomApproveId').val();
            q['loanStatus'] = $('#loanStatus').val();
            App.blockUI({target: "#hris-page-content"});
            window.app.pullDataById('', q).then(function (success) {
                App.unblockUI("#hris-page-content");
                if(success.data.length > 0){
                    let totalAmount = success.data[0].REQUESTED_AMOUNT;
                    success.data.forEach(function(item, i){
                        success.data[i].BALANCE = (totalAmount - item.PAID_AMOUNT).toFixed(2);
                        success.data[i].BALANCE = (success.data[i].BALANCE < 0) ? 0 : success.data[i].BALANCE;
                        totalAmount -= item.PAID_AMOUNT;
                    });
                }
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
