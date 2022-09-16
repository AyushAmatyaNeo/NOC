(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');

        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["VARIANCE_ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["VARIANCE_ID"],
                'url': document.deleteLink
            }
        };
        app.initializeKendoGrid($table, [
            {field: "VARIANCE_NAME", title: "Name"},
            {field: "VARIABLE_TYPE_NAME", title: "Variable Type"},
            {field: "PAY_EDESC", title: "Heads"},
            {field: "REMARKS", title: "Remarks"},
            {field: "VARIANCE_ID", title: "Action", width: 120, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ]);

        app.searchTable($table, ["VARIANCE_NAME", "VARIABLE_TYPE_NAME", "PAY_EDESC","REMARKS"]);
//        var map = {
//            "FLAT_ID": "Flat Id",
//            "FLAT_CODE": "Flat Code",
//            "FLAT_EDESC": "Flat Title",
//            "REMARKS": "Remarks",
//        };

//        $('#excelExport').on('click', function () {
//            app.excelExport($table, map, 'Flat Value List');
//        });
//        $('#pdfExport').on('click', function () {
//            app.exportToPDF($table, map, 'Flat Value List');
//        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);