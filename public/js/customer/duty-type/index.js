(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["DUTY_TYPE_ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["DUTY_TYPE_ID"],
                'url': document.deleteLink
            }
        };



        var columns = [
            {field: "DUTY_TYPE_NAME", title: "Duty Type Name", width: 150},
            {field: "NORMAL_HOUR", title: "Normal Hour", width: 150},
            {field: "OT_HOUR", title: "OT Hour", width: 150},
            {field: ["DUTY_TYPE_ID"], title: "Action", width: 120, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ];
        var map = {
            'DUTY_TYPE_NAME': 'Duty Type Name',
            'NORMAL_HOUR': 'Normal Hour',
            'OT_HOUR': 'OT Hour',
        }
        app.initializeKendoGrid($table, columns);

        app.searchTable($table, ['DUTY_TYPE_NAME', 'NORMAL_HOUR', 'OT_HOUR']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Duty Type List.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Duty Type List.pdf');
        });

        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);