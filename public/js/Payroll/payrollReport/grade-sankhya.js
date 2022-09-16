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
            {field: "FULL_NAME", title: "FULL NAME"},
            {field: "FUNCTIONAL_LEVEL_EDESC", title: "Functional Level"},
            {field: "POSITION_NAME", title: "Position Name"},
            {field: "GRADE_SANKHYA", title: "Grade Sankhya"},
            {field: "TECHNICAL_GRADE_SANKHYA", title: "Technical Grade Sankhya"},
            {field: "EMPLOYEE_TYPE_NAME", title: "EMPLOYEE TYPE"},
        ], null, null, null, 'Grade Sankhya REPORT LIST');
        

        

        app.setFiscalMonth($fiscalYear, $month, function (years, months, currentMonth) {
            monthList = months;
        });

        




 
       $('#searchEmployeesBtn').on('click', function () {
            var q = {};
            q['fiscalId'] = $fiscalYear.val();
            q['monthId'] = $month.val();
            q['employeeType']=$employeeType.val();

            app.serverRequest(document.pullGradeSankhyaReportLink, q).then(function (response) {
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
            'FULL_NAME':'FULL NAME',
            'FUNCTIONAL_LEVEL_EDESC':'Functional Level',
            'POSITION_NAME':'Position Name',
            'GRADE_SANKHYA':'Grade Sankhya',
            'TECHNICAL_GRADE_SANKHYA': 'Technical Grade Sankhya',
            'EMPLOYEE_TYPE_NAME':'EMPLOYEE TYPE'
        };

        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Grade Sankhya REPORT LIST.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Grade Sankhya REPORT LIST.pdf','A4');
        });
        
        



        app.searchTable('table', ['EMPLOYEE_CODE', 'FULL_NAME'], false);


        $month.select2();
    
    
        
        
    });
})(window.jQuery, window.app);


