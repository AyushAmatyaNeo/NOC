(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#instituteTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:INSTITUTE_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:INSTITUTE_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "INSTITUTE_NAME", title: "Institute Name", width: 130},
            {field: "LOCATION", title: "Location Detail", width: 110},
            {field: "TELEPHONE", title: "Telephone", width: 120},
            {field: "EMAIL", title: "Email", width: 150},
            {field: "INSTITUTE_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'Institute List');

        app.searchTable('instituteTable', ['INSTITUTE_NAME', 'LOCATION', 'TELEPHONE', 'EMAIL']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'INSTITUTE_NAME': 'Institute',
                'LOCATION': 'Location',
                'TELEPHONE': 'telephone',
                'EMAIL': 'Email'
            }, 'InstituteList');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'INSTITUTE_NAME': 'Institute',
                'LOCATION': 'Location',
                'TELEPHONE': 'telephone',
                'EMAIL': 'Email'
            }, 'InstituteList');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);