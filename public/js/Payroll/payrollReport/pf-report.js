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
            {field: "DESIGNATION_TITLE", title: "Designation"},
            {field: "FULL_NAME", title: "Employee Name"},
            {field: "ID_PROVIDENT_FUND_NO", title: "PF NUMBER"},
            {field: "TOTAL_FUND_DEDUCTION", title: "Total Fund Deducted"},
            {field: "PF_DEDUCTION_FROM_EMPLOYEE", title: "PF Deducted From Employee"},
            {field: "PF_CONTRIBUTION_BY_EMPLOYEE", title: "PF Contribution By Employeer"},
        ], null, null, null, 'PF REPORT LIST');
        

        

        app.setFiscalMonth($fiscalYear, $month, function (years, months, currentMonth) {
            monthList = months;
        });

        




 
       $('#searchEmployeesBtn').on('click', function () {
            var q = {};
            q['fiscalId'] = $fiscalYear.val();
            q['monthId'] = $month.val();
            q['employeeType']=$employeeType.val();
            q['employeeId'] = $employeeId.val();

            app.serverRequest(document.pullPfReportLink, q).then(function (response) {
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
            'DESIGNATION_TITLE': 'DESIGNATION',
            'FULL_NAME':'Employee Name',
            'ID_PROVIDENT_FUND_NO':'PF Number',
            'TOTAL_FUND_DEDUCTION':'Total Fund deducated',
            'PF_DEDUCTION_FROM_EMPLOYEE':'Pf Deducted From Employee',
            'PF_CONTRIBUTION_BY_EMPLOYEE':'PF contribution by employees'
        };

        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'PF REPORT LIST.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'PF REPORT LIST.pdf','A4');
        });
        
        






        $month.select2();
    
    
        
        
    });
})(window.jQuery, window.app);


