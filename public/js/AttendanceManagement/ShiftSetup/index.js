(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#shiftTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:SHIFT_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:SHIFT_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var copyAction = '<a class="btn-edit" title="Copy" href="' + document.addLink + '/#:SHIFT_ID#" style="height:17px;"><i class="fa fa-copy"></i></a>';
        var action = copyAction + editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "SHIFT_ENAME", title: "Shift", width: 120}, delete
                    {field: "COMPANY_NAME", title: "COMPANY", width: 130}
            ,
            {field: "START_TIME", title: "Start Time", width: 120},
            {field: "END_TIME", title: "End Time", width: 120},
            {field: "SHIFT_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'Shift List');

        app.searchTable('shiftTable', ['SHIFT_ENAME', 'COMPANY_NAME', 'START_TIME', 'END_TIME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'SHIFT_ENAME': ' Shift',
                'COMPANY_NAME': 'Company',
                'START_TIME': 'Start Time',
                'START_DATE': 'End Time',
                'HALF_TIME': 'Half Time',
                'HALF_DAY_END_TIME': 'Half End Time',
                'LATE_IN': 'late In',
                'EARLY_OUT': 'Early Out'
            }, 'Shift List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'SHIFT_ENAME': ' Shift',
                'COMPANY_NAME': 'Company',
                'START_TIME': 'Start Time',
                'START_DATE': 'End Time',
                'HALF_TIME': 'Half Time',
                'HALF_DAY_END_TIME': 'Half End Time',
                'LATE_IN': 'late In',
                'EARLY_OUT': 'Early Out'
            }, 'Shift List');
        });


        app.serverRequest("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);
