(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["ROLE_ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["ROLE_ID"],
                'url': document.deleteLink
            }
        };
        var columns = [
            {field: "ROLE_NAME", title: "Role", width: 150},
            {field: "CONTROL", title: "Control", width: 150},
            {field: "ALLOW_ADD", title: "Allow Add", width: 150},
            {field: "ALLOW_UPDATE", title: "Allow Update", width: 150},
            {field: "ALLOW_DELETE", title: "Allow Delete", width: 150},
            {field: "REMARKS", title: "Remarks", width: 150},
            {field: "ROLE_ID", title: "Action", width: 120, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ];
        var map = {
            'ROLE_NAME': 'Name',
            'CONTROL': 'Control',
            'ALLOW_ADD': 'Allow Add',
            'ALLOW_UPDATE': 'Allow Update',
            'ALLOW_DELETE': 'Allow Delete',
            'REMARKS': 'Remarks',
        }
        app.initializeKendoGrid($table, columns);

        app.searchTable($table, ['ROLE_NAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Role List.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Role List.pdf');
        });

        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);