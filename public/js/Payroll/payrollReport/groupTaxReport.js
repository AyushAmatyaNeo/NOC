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
        var $groupVariable = $('#groupVariable');
        var $table = $('#table');
        var $salaryTypeId = $('#salaryTypeId');
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
            {ID: "JOIN_DATE", VALUE: "Join Date"},
             {ID: "ID_PAN_NO", VALUE: "Pan No"},
             {ID: "BRANCH_NAME", VALUE: "Branch Name"},
             {ID: "ID_ACCOUNT_NO", VALUE: "Account No"}
        ];

        app.setFiscalMonth($fiscalYear, $month, function (years, months, currentMonth) {
            monthList = months;
        });

//        console.log(document.groupVariables);

        app.populateSelect($otVariable, document.nonDefaultList, 'VARIANCE_ID', 'VARIANCE_NAME', '---', '');
        app.populateSelect($groupVariable, document.groupVariables, 'VARIANCE_ID', 'VARIANCE_NAME', '---', '');
        app.populateSelect($extraFields, extraFieldsList, 'ID', 'VALUE', '---', '');
        
         app.populateSelect($salaryTypeId, document.salaryType, 'SALARY_TYPE_ID', 'SALARY_TYPE_NAME', 'All',-1,-1);

        var initKendoGrid = function (defaultColumns, otVariables, extraVariable, data) {
            let dataSchemaCols = {};
            let aggredCols = [];
            $table.empty();
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
                    footerTemplate: "#=sum||0#"
                });
                map[value['VARIANCE']] = value['VARIANCE_NAME'];
                dataSchemaCols[value['VARIANCE']] = {type: "number"};
                aggredCols.push({field: value['VARIANCE'], aggregate: "sum"});
            });

            $.each(otVariables, function (index, value) {
                for (var i in document.nonDefaultList) {
                    if (document.nonDefaultList[i]['VARIANCE_ID'] == value) {
                        columns.push({
                            field: 'V' + value,
                            title: document.nonDefaultList[i]['VARIANCE_NAME'],
                            width: 100,
                            aggregates: ["sum"],
                            footerTemplate: "#=sum||0#"
                        });
                        map['V' + value] = document.nonDefaultList[i]['VARIANCE_NAME'];
                        dataSchemaCols['V' + value] = {type: "number"};
                        aggredCols.push({field: 'V' + value, aggregate: "sum"});
                    }
                }
            });

            $table.kendoGrid({
                toolbar: ["excel"],
                excel: {
                    fileName: "Group Tax Report.xlsx",
                    filterable: false,
                    allPages: true
                },
                dataSource: {
                    data: data,
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
            q['groupVariable'] = $groupVariable.val();
            q['salaryTypeId'] = $salaryTypeId.val();

            app.serverRequest(document.pullGroupTaxReportLink, q).then(function (response) {
                if (response.success) {
                    console.log(response);
                    if(q['reportType']=='GS'){
                    initKendoGrid(response.columns, $otVariable.val(), $extraFields.val(), response.data);
                }else if(q['reportType']=='GD'){
                    initKendoGrid(response.columns, [], $extraFields.val(), response.data);
                }
                    //app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'GroupTax.xlsx',exportType);
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'GroupTax.pdf');
        });






    });
})(window.jQuery, window.app);


