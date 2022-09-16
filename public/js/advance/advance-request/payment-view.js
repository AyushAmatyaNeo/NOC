(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        
        
        var columns = [
            {field: "NEP_YEAR", title: "Year", width: 150},
            {field: "MONTH_EDESC", title: "Month", width: 150},
            {field: "AMOUNT", title: "Amount", width: 150},
            {field: "STATUS_DESC", title: "Status", width: 150},
            {field: "PAYAMENT_DATE", title: "Payment Date", width: 150},
            {field: "PAYMENT_MODE_DESC", title: "Payment Mode", width: 150},
        ];
        var map = {
            'NEP_YEAR': 'Year',
            'MONTH_EDESC': 'Month',
            'AMOUNT': 'Amount',
            'STATUS_DESC': 'Status',
            'PAYAMENT_DATE':'Payment Date',
            'PAYMENT_MODE_DESC':'Payment Mode'
        }
        app.initializeKendoGrid($table, columns, "Advance Paymnet List.xlsx");

        app.searchTable($table, ['NEP_YEAR','MONTH_EDESC','STATUS_EDESC','AMOUNT','PAYAMENT_DATE','PAYMENT_MODE_DESC']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Advance Paymnet List.xlsx');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Advance Paymnet List.pdf');
        });

        app.pullDataById("", {}).then(function (response) {
            console.log(response.data);
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });

    });
})(window.jQuery);

