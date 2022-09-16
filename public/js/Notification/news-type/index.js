(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["NEWS_TYPE_ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["NEWS_TYPE_ID"],
                'url': document.deleteLink
            }
        };
        var columns = [
            {field: "NEWS_TYPE_DESC", title: "News Type", width: 150},
            {field: "UPLOAD_FLAG", title: "Upload Flag", width: 150},
            {field: ["NEWS_TYPE_ID"], title: "Action", width: 120, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ];
        var map = {
            'NEWS_TYPE_DESC': 'News Type',
            'UPLOAD_FLAG': 'Upload Flag'
        }
        app.initializeKendoGrid($table, columns, "News Type  List.xlsx");

        app.searchTable($table, ['ADVANCE_ENAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'News Type List.xlsx');
        });
        
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'News Type List.pdf');
        });

        app.pullDataById("", {}).then(function (response) {
            console.log(response.data);
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);