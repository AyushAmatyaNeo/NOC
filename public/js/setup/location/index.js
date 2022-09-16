(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#locationTable');
        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["LOCATION_ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["LOCATION_ID"],
                'url': document.deleteLink
            }
        };
        app.initializeKendoGrid($table, [
            {field: "LOCATION_CODE", title: "Location Code", width: 200},
            {field: "LOCATION_EDESC", title: "Location Name", width: 200},
            {field: "PARENT_LOCATION_EDESC", title: "Parent Location Name", width: 200},
            {field: "LOCATION_ID", title: "Action", width: 120, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ], null, null, null, 'Location List');
        app.searchTable('locationTable', ['LOCATION_EDESC']);
        var map = {
          LOCATION_CODE:'Location Code',  LOCATION_EDESC: 'Location Name', PARENT_LOCATION_EDESC: 'Parent Location Name'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Location List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Location List');
        });
        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery, window.app);
