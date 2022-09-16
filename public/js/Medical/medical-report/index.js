(function ($) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var $search = $('#search');
        var $table = $('#table');
        var columns = [
            {field: "EMPLOYEE_CODE", title: "Code", width: 80},
            {field: "FULL_NAME", title: "Employee", width: 120},
            {field: "DEPARTMENT_NAME", title: "Department", width: 100},
            {field: "FUNCTIONAL_TYPE_EDESC", title: "Functional Type", width: 130},
            {field: "SELF", title: "Self", width: 90},
            {field: "DEPENDENT", title: "Dependent", width: 80},
            {field: "OPERATION", title: "Operation", width: 80},
        ];
        var map = {
            'EMPLOYEE_CODE': 'Code',
            'FULL_NAME': 'Employee',
            'DEPARTMENT_NAME': 'Department',
            'FUNCTIONAL_TYPE_EDESC': 'Functional Type',
            'SELF': 'Self',
            'DEPENDENT': 'Dependent',
            'OPERATION': 'Operation'
        }
        app.initializeKendoGrid($table, columns, "Advance List.xlsx");

        app.searchTable($table, ['EMPLOYEE_CODE', 'FULL_NAME', 'DEPARTMENT_NAME', 'FUNCTIONAL_TYPE_EDESC', 'SELF', 'DEPENDENT', 'OPERATION']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'MedicalBalance.xlsx');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'MedicalBalance.pdf');
        });


        $search.on('click', function () {
            var q = document.searchManager.getSearchValues();
            app.serverRequest(document.pullBalanceLink, q).then(function (response) {
                if (response.success) {
                    console.log(response);
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

//        $("#reset").on("click", function () {
//            $(".form-control").val("");
//        });




    });
})(window.jQuery);