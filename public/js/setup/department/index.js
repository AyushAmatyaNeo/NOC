(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#departmentTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:DEPARTMENT_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:DEPARTMENT_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var jvAction = document.acl.JV_FLAG == 'Y' ? '<a class="btn-edit" title="JV" href="' + document.jvLink + '/#:DEPARTMENT_ID#" style="height:17px;"><i class="fa fa-star-o"></i></a>' : '';
        var action = editAction + deleteAction + jvAction;
        app.initializeKendoGrid($table, [
            {field: "DEPARTMENT_NAME", title: "Name", width: 150},
            {field: "DEPARTMENT_NDESC", title: "Name (Nepali)", width: 150},
            {field: "BRANCH_NAME", title: "Branch", width: 150},
            {field: "DEPARTMENT_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'Department List');

        app.searchTable('departmentTable', ['DEPARTMENT_NAME', 'DEPARTMENT_NDESC', 'BRANCH_NAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'DEPARTMENT_NAME': 'Name',
                'DEPARTMENT_NDESC': 'Name (Nepali)',
                'BRANCH_NAME': 'Branch',
            }, 'Department List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'DEPARTMENT_NAME': 'Name',
                'DEPARTMENT_NDESC': 'Name (Nepali)',
                'BRANCH_NAME': 'Branch',
            }, 'Department List');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);