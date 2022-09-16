(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('Select').select2();
        var monthList = null;
        var $fiscalYear = $('#fiscalYearId');
        var $month = $('#monthId');
        var $table = $('#table');
        var $salaryTypeId = $('#salaryTypeId');
        var $reportTypeId = $('#reportTypeId');

        var columns = [
            {field: "DEPARTMENT_NAME", title: "Department", width: 200},
            {field: "JV_NAME", title: "JV Name", width: 200},
            {field: "JV_VALUE", title: "JV Value", width: 200},
            {field: "PAY_TYPE_FLAG", title: "Debit/Credit", width: 200}
        ];

        app.setFiscalMonth($fiscalYear, $month, function (years, months, currentMonth) {
            monthList = months;
        });
        
        app.populateSelect($salaryTypeId, document.salaryType, 'SALARY_TYPE_ID', 'SALARY_TYPE_NAME', 'All',-1,-1);

        var exportMap = {
            "DEPARTMENT_NAME": "Department",
            "JV_NAME": "JV Name",
            "JV_VALUE": "JV Value",
            "PAY_TYPE_FLAG": "PAY_TYPE_FLAG"
        };

        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Jv-report.xlsx');
        });
                   
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Jv-report.pdf');
        });

        function loadKendo(treeData) {
            $table.kendoGrid({
                toolbar: ["excel"],
                excel: {
                    fileName: "JV.xlsx",
                    filterable: false,
                    allPages:true
                },
                dataSource: {
                    data: treeData,
                    pageSize: 20
                },
                excelExport: function(e) {
                var rows = e.workbook.sheets[0].rows;
                var columns = e.workbook.sheets[0].columns;
                
                rows.unshift({
                    cells: [
                    {value: "Grade Basic Report", colSpan: columns.length, textAlign: "left"}
                    ]
                });
                if(document.preference != undefined){
                    if(document.preference.companyAddress != null){
                        rows.unshift({
                            cells: [
                            {value: document.preference.companyAddress, colSpan: columns.length, textAlign: "left"}
                            ]
                        });
                    }
                }
                if(document.preference != undefined){
                    if(document.preference.companyName != null){
                        rows.unshift({
                            cells: [
                            {value: document.preference.companyName, colSpan: columns.length, textAlign: "left"}
                            ]
                        });
                    }
                }
            },
                height: 550,
                scrollable: true,
                sortable: true,
                filterable: true,
                groupable: true,
                pageable: {
                    input: true,
                    numeric: false
                },
                columns: columns
            });
        }
        app.searchTable($table, ['DEPARTMENT_NAME', 'JV_NAME', 'JV_VALUE'], false);
        
        $('#searchEmployeesBtn').on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['fiscalId'] = $fiscalYear.val();
            q['monthId'] = $month.val();
            q['salaryTypeId'] = $salaryTypeId.val();
            q['reportTypeId'] = $reportTypeId.val();
            app.serverRequest('', q).then(function (response) {
                if (response.success) {
                    $table.empty();
                    loadKendo(response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });
    });
})(window.jQuery, window.app);


