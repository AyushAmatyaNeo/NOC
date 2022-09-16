(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
        var $table = $('#table');
        var $search = $('#search');
        var $fromDate = $('#fromDate');
        var $toDate = $('#toDate');

        var columns = [];
        var map = {};

        app.searchTable('table', ['EMPLOYEE_ID', 'FULL_NAME'], false);

        $search.on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['fromDate'] = $fromDate.val();
            q['toDate'] = $toDate.val();

            app.serverRequest(document.getOvertimeReportLink, q).then(function (response) {
                $table.empty();
                map = {
                    'Code' : 'EMPLOYEE_CODE',
                    'Employee Name' : 'FULL_NAME'
                };
                columns = [{field: "EMPLOYEE_CODE", title: "Code", locked: true, width: 100},
                        {field: "FULL_NAME", title: "Employee Name", locked: true, width: 100},
                        {field: "DEPARTMENT_NAME", title: "Department", locked: true, width: 100}
                    ];
                var dates = response.dates; 
                for(var i = 0; i < dates.length; i++){
                    let temp = dates[i].replace(/-/g, '_');
                    columns.push({title: dates[i], columns: [
                            {field: "DATE_"+ temp.toUpperCase()+ "_R", title: "R", width: 50},
                            {field: "DATE_"+ temp.toUpperCase()+ "_M", title: "M", width: 50},
                            {field: "DATE_"+ temp.toUpperCase()+ "_A", title: "A", width: 50}
                    ]});
                    
                }
                app.initializeKendoGrid($table, columns,null,null,null,'Overtime Report.xlsx');
                app.renderKendoGrid($table, response.data);
            }, function (error) {
                
            });
        });

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Overtime Report.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.excelExport($table, map, 'Query result.xlsx');
        });

//        $('#reset').on('click', function () {
//            $('.form-control').val("");
//        });
    });
})(window.jQuery, window.app);