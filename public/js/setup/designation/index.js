(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#designationTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:DESIGNATION_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:DESIGNATION_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "DESIGNATION_TITLE", title: "Name", width: 200},
            {field: "COMPANY_NAME", title: "Company", width: 120},
            {field: "BASIC_SALARY", title: "Basic Salary", width: 120},
            {field: "DESIGNATION_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'Designation List');

        app.searchTable('designationTable', ['DESIGNATION_TITLE', 'COMPANY_NAME', 'BASIC_SALARY']);

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
