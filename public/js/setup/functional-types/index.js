(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#functionalTypesTable');
        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["FUNCTIONAL_TYPE_ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["FUNCTIONAL_TYPE_ID"],
                'url': document.deleteLink
            }
        };
        app.initializeKendoGrid($table, [
            {field: "FUNCTIONAL_TYPE_CODE", title: "Functional Types Code", width: 200},
            {field: "FUNCTIONAL_TYPE_EDESC", title: "Functional Types Name", width: 200},
            {field: "FUNCTIONAL_TYPE_ID", title: "Action", width: 120, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ], null, null, null, 'Functional Types List');
        app.searchTable('functionalTypesTable', ['FUNCTIONAL_TYPE_EDESC']);
        var map = {
          FUNCTIONAL_TYPE_CODE:'Functional Types Code',  FUNCTIONAL_TYPE_EDESC: 'Functional Types Name'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Functional Types List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Functional Types List');
        });
        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery, window.app);
