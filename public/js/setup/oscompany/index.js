(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#companyTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:COMPANY_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:COMPANY_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "COMPANY_NAME", title: "Name"},
            {field: "ADDRESS", title: "Address"},
            {field: "COMPANY_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'Company List');

        app.searchTable('companyTable', ["COMPANY_NAME", "ADDRESS"]);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'COMPANY_NAME': 'Name',
                'ADDRESS': 'Address',
            }, 'Company List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'COMPANY_NAME': 'Name',
                'ADDRESS': 'Address',
            }, 'Company List');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);