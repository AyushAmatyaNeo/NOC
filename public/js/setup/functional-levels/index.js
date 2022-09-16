(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#functionalLevelsTable');
        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["FUNCTIONAL_LEVEL_ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["FUNCTIONAL_LEVEL_ID"],
                'url': document.deleteLink
            }
        };
        app.initializeKendoGrid($table, [
            {field: "FUNCTIONAL_LEVEL_NO", title: "Functional Levels No", width: 200},
            {field: "FUNCTIONAL_LEVEL_EDESC", title: "Functional Levels Name", width: 200},
            {field: "FUNCTIONAL_LEVEL_ID", title: "Action", width: 120, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ], null, null, null, 'Functional Levels List');
        app.searchTable('functionalLevelsTable', ['FUNCTIONAL_LEVEL_EDESC']);
        var map = {
            FUNCTIONAL_LEVEL_NO: 'Functional Levels No', FUNCTIONAL_LEVEL_EDESC: 'Functional Levels Name'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Functional Levels List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Functional Levels List');
        });
        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery, window.app);
