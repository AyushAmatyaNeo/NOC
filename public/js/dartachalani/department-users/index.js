(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#departmentTable');
        var editAction ='<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:DEPARTMENT_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>';
    

        var action = editAction;
        app.initializeKendoGrid($table, [
            {field: "DEPARTMENT_NAME", title: "Name", width: 150},
            {field: "COMPANY_NAME", title: "Company", width: 150},
            {field: "BRANCH_NAME", title: "Branch", width: 150},
            {field: "DEPARTMENT_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'Department List');

        app.searchTable('departmentTable', ['DEPARTMENT_NAME', 'COMPANY_NAME', 'BRANCH_NAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'DEPARTMENT_NAME': 'Name',
                'COMPANY_NAME': 'Company',
                'BRANCH_NAME': 'Branch',
            }, 'Department List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'DEPARTMENT_NAME': 'Name',
                'COMPANY_NAME': 'Company',
                'BRANCH_NAME': 'Branch',
            }, 'Department List');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);