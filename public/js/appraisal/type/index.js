(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["APPRAISAL_TYPE_ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["APPRAISAL_TYPE_ID"],
                'url': document.deleteLink
            }
        };
        var columns = [
            {field: "APPRAISAL_TYPE_CODE", title: "Code", width: 150},
            {field: "APPRAISAL_TYPE_EDESC", title: "Name", width: 150},
            {field: "DURATION_TYPE_DETAIL", title: "Duration Type", width: 150},
            {field: ["APPRAISAL_TYPE_ID"], title: "Action", width: 120, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ];
        var map = {
            'APPRAISAL_TYPE_CODE': 'Code',
            'APPRAISAL_TYPE_EDESC': 'Name',
            'DURATION_TYPE_DETAIL': 'Duration Type',
        }
        app.initializeKendoGrid($table, columns);

        app.searchTable($table, ['APPRAISAL_TYPE_EDESC']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Appraisal Type List.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Appraisal Type List.pdf');
        });

        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
        
//        $('#reset').on('click', function (){
//            $('.form-control').val("");
//        })
    });
})(window.jQuery);