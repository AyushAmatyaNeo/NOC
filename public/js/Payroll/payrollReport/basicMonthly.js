(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('Select').select2();

        var monthList = null;
        var $fiscalYear = $('#fiscalYearId');
        var $month = $('#monthId');
        var $otVariable = $('#otVariable');
        var $extraFields = $('#extraFields');
        var previousColumns = [];
        var currentColumns = [];
        var $table = $('#table');
        var map = {};
        var exportType = {
            "ACCOUNT_NO": "STRING",
        };

        var extraFieldsList = [
            {ID: "DESIGNATION_TITLE", VALUE: "Designation"},
            {ID: "DEPARTMENT_NAME", VALUE: "Department"},
            {ID: "FUNCTIONAL_TYPE_EDESC", VALUE: "Functional Type"},
            {ID: "ACCOUNT_NO", VALUE: "Account No"},
            {ID: "BIRTH_DATE", VALUE: "Birth Date"},
            {ID: "JOIN_DATE", VALUE: "Join Date"}
        ];

        app.setFiscalMonth($fiscalYear, $month, function (years, months, currentMonth) {
            monthList = months;
        });


//        app.populateSelect($otVariable, document.otVariables, 'VARIANCE_ID', 'VARIANCE_NAME', '---', '');
        app.populateSelect($extraFields, extraFieldsList, 'ID', 'VALUE', '---', '');


        var initKendoGrid = function (defaultColumns, otVariables, extraVariable, reportData) {
            let dataSchemaCols = {};
            let aggredCols = [];
            $table.empty();
//            console.log(defaultColumns);
            map = {
                'EMPLOYEE_CODE': 'Employee Code',
                'FULL_NAME': 'Employee',
                'POSITION_NAME': 'Position',
                'SERVICE_TYPE_NAME': 'Service',
            }

            var columns = [
                {field: "EMPLOYEE_CODE", title: "Code", width: 80, locked: true},
                {field: "FULL_NAME", title: "Employee", width: 120, locked: true},
                {field: "POSITION_NAME", title: "Position", width: 120, locked: true},
                {field: "SERVICE_TYPE_NAME", title: "Service", width: 120, locked: true},
            ];

            $.each(extraVariable, function (index, value) {
                for (var i in extraFieldsList) {
                    if (extraFieldsList[i]['ID'] == value) {
                        columns.push({
                            field: value,
                            title: extraFieldsList[i]['VALUE'],
                            width: 100,
                            
                        });
                        map[value] = extraFieldsList[i]['VALUE'];
                    }
                }
            });

            $.each(defaultColumns, function (index, value) {
                var widthVal = (value['TYPE'] == 'M') ? 110 : 150;
                columns.push({
                    field: value['DEFAULT_COL'],
                    title: value['MONTH_NAME'],
                    width: widthVal,
                    aggregates: ["sum"],
                    footerTemplate: "#=sum||''#"
                });
                map[value['DEFAULT_COL']] = value['MONTH_NAME'];
                dataSchemaCols[value['DEFAULT_COL']] = {type: "number"};
                aggredCols.push({field: value['DEFAULT_COL'], aggregate: "sum"});
            });

//            $.each(otVariables, function (index, value) {
//                for (var i in document.datas.otVariables) {
//                    if (document.datas.otVariables[i]['VARIANCE_ID'] == value) {
//                        columns.push({
//                            field: 'V' + value,
//                            title: document.datas.otVariables[i]['VARIANCE_NAME'],
//                            width: 100
//                        });
//                        map['V' + value] = document.datas.otVariables[i]['VARIANCE_NAME'];
//                    }
//                }
//            });
//            console.log(map);
//            app.initializeKendoGrid($table, columns);

            $table.kendoGrid({
                toolbar: ["excel"],
                excel: {
                    fileName: "Basic Monthly Report.xlsx",
                    filterable: false,
                    allPages: true
                },
                excelExport: function(e) {
                    var rows = e.workbook.sheets[0].rows;
                    var columns = e.workbook.sheets[0].columns;
                    
                    rows.unshift({
                        cells: [
                        {value: "Basic Monthly Report", colSpan: columns.length, textAlign: "left"}
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
                    data: reportData,
                    schema: {
                        model: {
                            fields: dataSchemaCols
                        }
                    },
                    pageSize: 20,
                    aggregate: aggredCols
                },
                height: 550,
                scrollable: true,
                sortable: true,
                groupable: true,
                filterable: true,
                pageable: {
                    input: true,
                    numeric: false
                },
                columns: columns
            });
        }






        $('#searchEmployeesBtn').on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['fiscalId'] = $fiscalYear.val();
            q['extVar'] = $otVariable.val();
            q['extField'] = $extraFields.val();
//            console.log(q);
            if (!($fiscalYear.val() > 0)) {
                app.showMessage('Please Select Fiscal Year', 'error');
                return;
            }

            app.serverRequest(document.basicMonthlyLink, q).then(function (response) {
                if (response.success) {
//                    console.log(response);
                    initKendoGrid(response.columns, $otVariable.val(), $extraFields.val(), response.data);
                    //app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });


        });



        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'GradeBasicReport.xlsx', exportType);
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'GradeBasicReport.pdf');
        });






    });
})(window.jQuery, window.app);


