(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#fileTypeTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:FILETYPE_CODE#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:FILETYPE_CODE#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "NAME", title: "File Type Name", width: 150},
            {field: "BRANCH_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'FileType List');

        app.searchTable('fileTypeTable', ['NAME']);
        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'NAME': 'File Type'
            }, 'FileType List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'NAME': 'File Type'
            }, 'FileType List');
        });


        
        app.pullDataById("", {}).then(function (response) {
            console.log(response);
            app.renderKendoGrid($table, response.data);
        }, function (error) {
        });
    });
})(window.jQuery);

