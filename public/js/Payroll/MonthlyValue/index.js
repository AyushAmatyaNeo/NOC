(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');

        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["MTH_ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["MTH_ID"],
                'url': document.deleteLink
            }
        };
        app.initializeKendoGrid($table, [
            {field: "MTH_CODE", title: " Code"},
            {field: "MTH_EDESC", title: "Month Value"},
            {field: "REMARKS", title: "Remarks"},
            {field: "MTH_ID", title: "Action", width: 120, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ]);

        app.searchTable($table, ["MTH_CODE", "MTH_EDESC", "REMARKS"]);
        var map = {
            "MTH_ID": "Flat Id",
            "MTH_CODE": "Flat Code",
            "MTH_EDESC": "Flat Title",
            "REMARKS": "Remarks",
        };

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Monthly Value List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Monthly Value List');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);