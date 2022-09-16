(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
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



        var columns = [
            {field: "LOCATION_NAME", title: "Location Name", width: 150},
            {field: "ADDRESS", title: "Address", width: 150},
            {field: ["LOCATION_ID"], title: "Action", width: 120, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ];
//        var map = {
//            'CUSTOMER_ENAME': 'Name',
//            'ADDRESS': 'Address',
//            'PHONE_NO': 'Phone no',
//        }
        app.initializeKendoGrid($table, columns);
//
//        app.searchTable($table, ['CUSTOMER_ENAME']);
//
//        $('#excelExport').on('click', function () {
//            app.excelExport($table, map, 'Customer Location List.xlsx');
//        });
//        $('#pdfExport').on('click', function () {
//            app.exportToPDF($table, map, 'Customer Location List.pdf');
//        });

        app.pullDataById(document.pullLocationDetails, {}).then(function (response) {
            console.log(response.data);
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);