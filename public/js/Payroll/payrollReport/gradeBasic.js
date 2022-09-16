(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('Select').select2();

        var monthList = null;
        var $fiscalYear = $('#fiscalYearId');
        var $month = $('#monthId');
        var $reportType = $('#reportType');
        var $otVariable = $('#otVariable');
        var $extraFields = $('#extraFields');
        var $extraMonth = $('#extraMonth');
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

//        console.log(document.datas.otVariables);

        app.populateSelect($otVariable, document.datas.otVariables, 'VARIANCE_ID', 'VARIANCE_NAME', '---', '');
        app.populateSelect($extraMonth, document.datas.monthList, 'MONTH_ID', 'MONTH_EDESC', '---', '');
        app.populateSelect($extraFields, extraFieldsList, 'ID', 'VALUE', '---', '');


        var initKendoGrid = function (defaultColumns, otVariables, extraVariable, reportType, reportData) {
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
                            width: 100
                        });
                        map[value] = extraFieldsList[i]['VALUE'];
                    }
                }
//                columns.push({
//                    field: value['VARIANCE'],
//                    title: value['VARIANCE_NAME'],
//                    width: 100
//                });
            });

            $.each(defaultColumns, function (index, value) {
                columns.push({
                    field: value['VARIANCE'],
                    title: value['VARIANCE_NAME'],
                    width: 100,
                    aggregates: ["sum"],
                    footerTemplate: "#=sum#"

                });
                map[value['VARIANCE']] = value['VARIANCE_NAME'];
                dataSchemaCols[value['VARIANCE']] = {type: "number"};
                aggredCols.push({field: value['VARIANCE'], aggregate: "sum"});
            });
            aggredCols.push({field: "ProductName", aggregate: "count"});


            $.each(otVariables, function (index, value) {
                for (var i in document.datas.otVariables) {
                    if (document.datas.otVariables[i]['VARIANCE_ID'] == value) {
                        columns.push({
                            field: 'V' + value,
                            title: document.datas.otVariables[i]['VARIANCE_NAME'],
                            width: 100,
                            aggregates: ["sum"],
                            footerTemplate: "#=sum#"
                        });
                        map['V' + value] = document.datas.otVariables[i]['VARIANCE_NAME'];
                        dataSchemaCols['V' + value] = {type: "number"};
                        aggredCols.push({field: 'V' + value, aggregate: "sum"});
                    }
                }
            });
//            console.log(dataSchemaCols);

            columns.push({field: 'TOTAL', title: 'TOTAL', width: 100, aggregates: ["sum"], footerTemplate: "#=sum#"});
            aggredCols.push({field: 'TOTAL', aggregate: "sum"});
            if (reportType == 'D') {
                columns.push({field: 'DAY1', title: '28', width: 100});
                columns.push({field: 'DAY2', title: '29', width: 100});
                columns.push({field: 'DAY3', title: '30', width: 100});
                columns.push({field: 'DAY4', title: '31', width: 100});
            }

//            console.log(map);
//            app.initializeKendoGrid($table, columns);


            $table.kendoGrid({
                toolbar: ["excel"],
                excel: {
                    fileName: "Grade Basic Report.xlsx",
                    filterable: false,
                    allPages: true
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
            q['monthId'] = $month.val();
            q['extVar'] = $otVariable.val();
            q['extField'] = $extraFields.val();
            q['reportType'] = $reportType.val();
            q['extraMonth'] = $extraMonth.val();
//            console.log(q);

            app.serverRequest(document.pullGradeBasicLink, q).then(function (response) {
                if (response.success) {
                    let otVariables = $otVariable.val();
//                    console.log(response);


                    var renderData = []
                    $.each(response.data, function (index, value) {

                        let total = 0;
                        $.each(response.columns, function (di, dv) {
                            let tempName = dv['VARIANCE'];
                            total += parseFloat(value[tempName]);
                        });

                        $.each(otVariables, function (oi, ov) {
                            for (var i in document.datas.otVariables) {
                                if (document.datas.otVariables[i]['VARIANCE_ID'] == ov) {
                                    let tempOtVal = 'V' + ov;
                                    total += parseFloat(value[tempOtVal]);
                                }
                            }
                        });

                        value['TOTAL'] = total;
                        value['DAY1'] = parseFloat(((total / 8) / 28) * 1.5).toFixed(2);
                        value['DAY2'] = parseFloat(((total / 8) / 29) * 1.5).toFixed(2);
                        value['DAY3'] = parseFloat(((total / 8) / 30) * 1.5).toFixed(2);
                        value['DAY4'] = parseFloat(((total / 8) / 31) * 1.5).toFixed(2);
//                        console.log(value)
                        renderData.push(value);
                    });
                    initKendoGrid(response.columns, otVariables, $extraFields.val(), $reportType.val(), renderData);

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


