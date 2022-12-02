(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#jobResponsibilityTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "JOB_RES_ENG_NAME", title: "Name (English)", width: 200},
            {field: "JOB_RES_NEP_NAME", title: "Name (Nepali)", width: 120},
            {field: "ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'Designation List');

        app.searchTable('jobResponsibilityTable', ['JOB_RES_ENG_NAME', 'JOB_RES_NEP_NAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'DESIGNATION_TITLE': 'Name',
                'COMPANY_NAME': 'Company',
                'BASIC_SALARY': 'Basic Salary',
            }, 'Designation List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'DESIGNATION_TITLE': 'Name',
                'COMPANY_NAME': 'Company',
                'BASIC_SALARY': 'Basic Salary',
            }, 'Designation List');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);
