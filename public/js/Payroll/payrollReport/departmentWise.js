(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('Select').select2();
        let aggredCols = [];
        var monthList = null;
        var $fiscalYear = $('#fiscalYearId');
        var $month = $('#monthId');
        var $table = $('#table');
        var $salaryTypeId = $('#salaryTypeId');
//        var map = {};
//        var exportType = {
//            "ACCOUNT_NO": "STRING",
//        };
//      
        var exportMap = {
            "DEPARTMENT_NAME": "Department"
        };

        var columns = [
            {field: "DEPARTMENT_NAME", title: "Department", width: 200}
        ];

//
        app.setFiscalMonth($fiscalYear, $month, function (years, months, currentMonth) {
            monthList = months;
        });
        
          app.populateSelect($salaryTypeId, document.salaryType, 'SALARY_TYPE_ID', 'SALARY_TYPE_NAME', 'All',-1,-1);


        $.each(document.ruleList, function (key, value) {
            var signFn = function ($type) {
                var sign = "";
                switch ($type) {
                    case "A":
                        sign = "+";
                        break;
                    case "D":
                        sign = "-";
                        break;
                    case "V":
                        sign = ".";
                        break;
                }
                return sign;
            };
            columns.push({field: "P_" + value['PAY_ID'], title: value['PAY_EDESC'] + "(" + signFn(value['PAY_TYPE_FLAG']) + ")", width: 150});
            exportMap["P_" + value['PAY_ID']] = value['PAY_EDESC'] + "(" + signFn(value['PAY_TYPE_FLAG']) + ")";
        });

        columns.push({field: "TOTAL", title: "Total", width: 160});

        function loadKendo(treeData, columns) {
            $table.kendoTreeList({
                toolbar: ["excel"],
                excel: {
                    fileName: "Department Wise Report.xlsx",
                    filterable: false,
                    allPages:true
                },
                excelExport: function(e) {
                var rows = e.workbook.sheets[0].rows;
                var columns = e.workbook.sheets[0].columns;
                
                
                rows.unshift({
                    cells: [
                    {value: "Department Wise Report", colSpan: columns.length, textAlign: "left"}
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
                dataSource: {
                    data: treeData,
                    aggregate: aggredCols,
                    schema: {
                        model: {
                            id: "DEPARTMENT_ID",
                            parentId: "PARENT_DEPARTMENT",
                            fields: {
                                PARENT_DEPARTMENT: {field: "PARENT_DEPARTMENT", nullable: true},
                                DEPARTMENT_ID: {field: "DEPARTMENT_ID", type: "number" }
                            },
//                            expanded: true
                        }
                    }
                },
                height: 540,
                filterable: true,
                sortable: true,
                columns: columns,
                groupable: true,
                pageable: {
                    pageSize: 15,
                    pageSizes: true
                }
            });
        }
        loadKendo([], columns);



        $('#searchEmployeesBtn').on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['fiscalId'] = $fiscalYear.val();
            q['monthId'] = $month.val();
            q['salaryTypeId'] = $salaryTypeId.val();
            app.serverRequest(document.pulldepartmentWiseLink, q).then(function (response) {
                if (response.success) {
                    for(let i = 0; i < response.data.length; i++){
                        response.data[i].TOTAL = 0;
                        for(let j in response.data[0]){
                            if(j == 'DEPARTMENT_NAME' || j == 'DEPARTMENT_ID' || j == 'PARENT_DEPARTMENT' || j == 'TOTAL'){
                                continue;
                            }
                            response.data[i].TOTAL+=parseFloat(response.data[i][j]);
                        }
                    }
                    
                    $("#table").data("kendoTreeList").dataSource.data(response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

//        $('#excelExport').on('click', function () {
//            $table.table2excel({
//                exclude: ".noExl",
//                name: "leave-card",
//                filename: "leave-report-card"
//            });
//        });
//        
//        $('#pdfExport').on('click', function () {
//            app.exportDomToPdf2("summaryDiv");
//        });








    });
})(window.jQuery, window.app);


