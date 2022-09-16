(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var ruleList = document.ruleList;
        var salarySheetList = document.salarySheetList;
        var $year = $('#fiscalYearId');
        var $month = $('#monthId');
        var $fromDate = $('#fromDate');
        var $nepaliFromDate = $('#nepaliFromDate');
        var $toDate = $('#toDate');
        var $nepaliToDate = $('#nepaliToDate');
        var $table = $('#table');
        var $search = $('#search');

        app.setFiscalMonth($year, $month);
        $month.on('change', function () {
            var value = $(this).val();
            for (var i in salarySheetList) {
                if (salarySheetList[i]['MONTH_ID'] == value) {
                    $fromDate.val(salarySheetList[i]['START_DATE']);
                    $nepaliFromDate.val(nepaliDatePickerExt.fromEnglishToNepali(salarySheetList[i]['START_DATE']));
                    $toDate.val(salarySheetList[i]['END_DATE']);
                    $nepaliToDate.val(nepaliDatePickerExt.fromEnglishToNepali(salarySheetList[i]['END_DATE']));
                    break;
                }
            }
        });

        var exportMap = {"EMPLOYEE_NAME": "Employee"};
        var employeeNameColumn = {field: "EMPLOYEE_NAME", title: "Employee", width: 150};
        if (ruleList.length > 0) {
            employeeNameColumn.locked = true;
        }
        var columns = [
            employeeNameColumn
        ];

        $.each(ruleList, function (key, value) {
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
        app.initializeKendoGrid($table, columns);

        $search.on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['monthId'] = $month.val();
            app.serverRequest('', q).then(function (response) {
                app.renderKendoGrid($table, response.data);
            });
        });

        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Salary Sheet');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Salary Sheet');
        });

    });
})(window.jQuery, window.app);