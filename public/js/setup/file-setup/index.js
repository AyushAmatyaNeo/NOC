(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#fileSetupTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:FILE_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:FILE_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "FILE_NAME", title: "File Name", width: 100},
            {field: "NAME", title: "File Type", width: 100},
            {field: "FILE_ID", title: "Action", width: 100, template: action}
        ], null, null, null, 'File Master Setup List');

        app.searchTable('fileSetupTable', ['FILE_NAME']);
        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'FILE_NAME': 'File name'
            }, 'File Master Setup List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'FILE_NAME': 'File Name'
            }, 'File Master Setup List');
        });

        app.pullDataById("", {}).then(function (response) {
            console.log(response.data);
            app.renderKendoGrid($table, response.data);
        }, function (error) {
        });
    });
})(window.jQuery);

