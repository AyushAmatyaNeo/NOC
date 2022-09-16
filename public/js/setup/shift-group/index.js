(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#bestShiftTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:CASE_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:CASE_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "CASE_NAME", title: "Group Name", width: 100},
            {field: "START_DATE", title: "Start Date", width: 100},
            {field: "END_DATE", title: "End Date", width: 100},
            {field: "CASE_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'Shift Group List');

        app.searchTable('bestShiftTable', ['CASE_NAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'CASE_NAME': 'Group Name',
                'START_DATE': 'Start Date',
                'END_DATE': 'End Date'
            }, 'Shift Group List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
               'CASE_NAME': 'Group Name',
                'START_DATE': 'Start Date',
                'END_DATE': 'End Date'
            }, 'Shift Group List');
        });


        app.pullDataById("", {}).then(function (response) {
            console.log(response.data);
            app.renderKendoGrid($table, response.data);
            
        }, function (error) {

        });
    });
})(window.jQuery);