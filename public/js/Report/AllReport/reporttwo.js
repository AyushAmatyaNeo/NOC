(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#report');
        var months = {
            'ONE': 'Shrawan',
            'TWO': 'Bhadra',
            'THREE': 'Ashwin',
            'FOUR': 'Kartik',
            'FIVE': 'Mangsir',
            'SIX': 'Paush',
            'SEVEN': 'Magh',
            'EIGHT': 'Falgun',
            'NINE': 'Chaitra',
            'TEN': 'Baishakh',
            'ELEVEN': 'Jestha',
            'TWELVE': 'Ashadh',
        };
        
        if(document.calenderType=='E'){
            months = {
            'ONE': 'July',
            'TWO': 'August',
            'THREE': 'September',
            'FOUR': 'October',
            'FIVE': 'November',
            'SIX': 'December',
            'SEVEN': 'January',
            'EIGHT': 'February',
            'NINE': 'March',
            'TEN': 'April',
            'ELEVEN': 'May',
            'TWELVE': 'June',
        };
        }
        
        var reportHeadList = {
            'PR': {field: 'PRESENT', class: 'blue'},
            'AB': {field: 'ABSENT', class: 'red'},
            'LV': {field: 'LEAVE', class: 'green'},
            'DO': {field: 'DAYOFF', class: 'yellow'},
            'HD': {field: 'HOLIDAY', class: 'purple'},
            'WH': {field: 'WOH', class: 'yellow-soft'},
            'WD': {field: 'WOD', class: 'purple-soft'}
        };
        var exportMap = [{"EMPLOYEE_CODE": "Code"}, {"FULL_NAME": "Employee"}];
        var columns = [
            {field: "EMPLOYEE_CODE", title: "Code", width: 150, locked: true},
            {field: "FULL_NAME", title: "Employee", width: 150, locked: true}
        ];
        for (var i in months) {
            var column = {title: months[i]};
            var innerColumns = [];
            for (var j in reportHeadList) {
                var field = i + "_" + reportHeadList[j]['field'];
                var exportColumnName = months[i] + "_" + reportHeadList[j]['field'];
                innerColumns.push({
                    field: field,
                    title: j,
                    width: 70,
                    template: `<button type="button" class="btn btn-block ${reportHeadList[j]['class']}"> #: ${field} # </button>`
                });
                exportMap[field] = exportColumnName;
            }
            column['columns'] = innerColumns;
            columns.push(column);
        }
        app.initializeKendoGrid($table, columns, null, null, 'Department wise monthly attendance report');

        app.searchTable($table, ['FULL_NAME', 'EMPLOYEE_CODE']);
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Department wise monthly attendance report');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Department wise monthly attendance report');
        });

        $('.hris-filter-container select').select2();
        var $search = $('#search');

        $search.on('click', function () {
            var data = document.searchManager.getSearchValues();
            data['fiscalYearId'] = $fiscalYearId.val();
            app.serverRequest('', data).then(function (response) {
                app.renderKendoGrid($table, response.data);
            }, function (error) {

            });
        });

        var $fiscalYearId = $('#fiscalYearId');
    });
})(window.jQuery, window.app);