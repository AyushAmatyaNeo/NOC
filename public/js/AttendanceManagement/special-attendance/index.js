(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            { field: "SP_CODE", title: "SP Code" },
            { field: "SP_EDESC", title: "SP Name" },
            { field: "REMARKS", title: "Remarks" },
            { field: "ID", title: "Action", width: 120, template: action }
        ], null, null, null, 'SP List');

        app.searchTable('table', ['SP_CODE', 'SP_EDESC']);

        var map = {
            'SP_CODE': 'SP Code',
            'SP_EDESC': 'SP Name',
            'REMARKS': 'Remarks'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'SP List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'SP List');
        });
        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);
