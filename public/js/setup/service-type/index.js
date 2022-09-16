
(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#serviceTypeTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:SERVICE_TYPE_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:SERVICE_TYPE_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "SERVICE_TYPE_NAME", title: "Service Type Name", width: 400},
            {field: "TYPE", title: "Type", width: 400},
            {field: "SERVICE_TYPE_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'ServiceType List');

        app.searchTable('serviceTypeTable', ['SERVICE_TYPE_NAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'SERVICE_TYPE_NAME': 'Service Type',
                'TYPE': 'Type',
            }, 'ServiceType List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'SERVICE_TYPE_NAME': 'Service Type',
                'TYPE': 'Type',
            }, 'ServiceType List');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);