(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');

        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["FLAT_ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["FLAT_ID"],
                'url': document.deleteLink
            }
        };
        app.initializeKendoGrid($table, [
            {field: "FLAT_CODE", title: "Flat Code"},
            {field: "FLAT_EDESC", title: "Flat Title"},
            {field: "REMARKS", title: "Remarks"},
            {field: "FLAT_ID", title: "Action", width: 120, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ]);

        app.searchTable($table, ["FLAT_CODE", "FLAT_EDESC", "REMARKS"]);
        var map = {
            "FLAT_ID": "Flat Id",
            "FLAT_CODE": "Flat Code",
            "FLAT_EDESC": "Flat Title",
            "REMARKS": "Remarks",
        };

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Flat Value List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Flat Value List');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);