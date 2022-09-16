(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('Select').select2();

        var monthList = null;
        var $fiscalYear = $('#fiscalYearId');
        var $month = $('#monthId');
        var $employeeType=$('#employeeTypeId');
        
        
        var $table = $('#table');
        var exportMap = {};
 
        app.initializeKendoGrid($table, [
            {field: "EMPLOYEE_CODE", title: "EMPLOYEE CODE"},
            {field: "ID_PAN_NO", title: "PAN NO"},
            {field: "FULL_NAME", title: "FULL NAME"},
            {field: "TAXABLE_INCOME", title: "TAXABLE INCOME"},
            {field: "TDS_AMOUNT", title: "TDS AMOUNT"},
            {field: "REVENUE_CODE", title: "REVENUE CODE"},
            {field: "EMPLOYEE_TYPE_NAME", title: "EMPLOYEE TYPE"},
        ], null, null, null, 'TDS REPORT LIST');
        

        

        app.setFiscalMonth($fiscalYear, $month, function (years, months, currentMonth) {
            monthList = months;
        });

        




 
       $('#searchEmployeesBtn').on('click', function () {
            var q = {};
            q['fiscalId'] = $fiscalYear.val();
            q['monthId'] = $month.val();
            q['payId'] = $('#payhead').val();
            q['employeeType']=$employeeType.val();

            app.serverRequest(document.pullTdsReportLink, q).then(function (response) {
                if (response.success) {
                    console.log(response.data);
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });


        var exportMap = {
            'EMPLOYEE_CODE': 'EMPLOYEE CODE',
            'ID_PAN_NO': 'PAN NO',
            'FULL_NAME':'FULL NAME',
            'TAXABLE_INCOME':'TAXABLE INCOME',
            'TDS_AMOUNT':'TDS AMOUNT',
            'REVENUE_CODE':'REVENUE CODE',
            'EMPLOYEE_TYPE_NAME':'EMPLOYEE TYPE'
        };

        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'TDS REPORT LIST.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'TDS REPORT LIST.pdf','A4');
        });
        
        






        $month.select2();
    
    
        
        
    });
})(window.jQuery, window.app);


