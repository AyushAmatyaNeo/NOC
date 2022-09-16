(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('Select').select2();

        var monthList = null;
        var $fiscalYear = $('#fiscalYearId');
        var $month = $('#monthId');
        var $employeeType=$('#employeeTypeId');
        var $employeeId = $('#employeeId');
        
        
        var $table = $('#table');
        var exportMap = {};
 
        app.initializeKendoGrid($table, [
            {field: "SERIAL", title: "S.N"},
            {field: "POSITION_NAME", title: "Position"},
            {field: "FULL_NAME", title: "Employee Name"},
            {field: "CIT_DEDUCTION", title: "CIT Deduction"},
            {field: "CIT_NO", title: "CIT Number"},
            {field: "ID_ACCOUNT_NO", title: "Account Number"},
        ], null, null, null, 'CIT REPORT LIST');
        

        

        app.setFiscalMonth($fiscalYear, $month, function (years, months, currentMonth) {
            monthList = months;
        });

        




 
       $('#searchEmployeesBtn').on('click', function () {
            var q = {};
            q['fiscalId'] = $fiscalYear.val();
            q['monthId'] = $month.val();
            q['employeeType']=$employeeType.val();
            q['employeeId'] = $employeeId.val();

            app.serverRequest(document.pullCitReportLink, q).then(function (response) {
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
            'SERIAL': 'S.NO',
            'POSITION_NAME': 'Position Name',
            'FULL_NAME':'Employee Name',
            'CIT_DEDUCTION':'CIT Deducted',
            'CIT_NO':'CIT Number',
            'ID_ACCOUNT_NO':'Account Number',
        };

        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'CIT REPORT LIST.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'CIT REPORT LIST.pdf','A4');
        });
        
        






        $month.select2();
    
    
        
        
    });
})(window.jQuery, window.app);


