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

        var columns = [
            {field: "S_NO", title: "S. NO", width: 100, locked: true},
            {field: "ACCOUNT_NO", title: "Account Number", width: 200, locked: true},
            {field: "FULL_NAME", title: "Employee", width: 200, locked: true},
            {field: "DR_AMOUNT", title: "Dr. Amount", width: 200, format: "{0:0.##}"},
            {field: "CR_AMOUNT", title: "Cr. Amount", width: 200, format: "{0:0.##}"}
        ];

        map = {
            'S_NO': 'S.NO',
            'ACCOUNT_NO': 'Account Number',
            'FULL_NAME': 'Employee',
            'DR_AMOUNT': 'Dr. Amount',
            'CR_AMOUNT': 'Cr. Amount'
        }

        app.populateSelect($extraFields, extraFieldsList, 'ID', 'VALUE', '---', '');

        var initKendoGrid = function (defaultColumns, otVariables, extraVariable) {
            $table.empty();
            
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
            });

            $.each(defaultColumns, function (index, value) {
                var widthVal = (value['TYPE'] == 'M') ? 110 : 150;
                columns.push({
                    field: value['DEFAULT_COL'],
                    title: value['MONTH_NAME'],
                    width: widthVal
                });
                map[value['DEFAULT_COL']] = value['MONTH_NAME'];
            });
        }

        $('#searchEmployeesBtn').on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['fiscalId'] = $fiscalYear.val();
            q['monthId'] = $month.val();
            q['extVar'] = $otVariable.val();
            q['extField'] = $extraFields.val();
            if (!($fiscalYear.val() > 0)) {
                app.showMessage('Please Select Fiscal Year', 'error');
                return;
            }

            app.serverRequest(document.specialMonthlyLink, q).then(function (response) {
                if (response.success) {
                    $table.empty();
                    response.data.push({
                        S_NO: parseInt(response.data[response.data.length-1]["S_NO"])+1,
                        ACCOUNT_NO: "0101011126901",
                        FULL_NAME: "SOALTEE CROWN PLAZA",
                        DR_AMOUNT: response.data.reduce((a, b) => +a + +b.CR_AMOUNT, 0),
                        CR_AMOUNT: ''
                    });
                    response.data.push({
                        S_NO: '',
                        ACCOUNT_NO: "TOTAL",
                        FULL_NAME: "",
                        DR_AMOUNT: response.data[response.data.length-1]["DR_AMOUNT"],
                        CR_AMOUNT: response.data[response.data.length-1]["DR_AMOUNT"]
                    });
                    app.initializeKendoGrid($table, columns);
                    app.renderKendoGrid($table, response.data);
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


