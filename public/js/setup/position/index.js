(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#positionTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:POSITION_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:POSITION_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "LEVEL_NO", title: "Level", width: 100},
            {field: "POSITION_CODE", title: "Code", width: 50},
            {field: "POSITION_NAME", title: "Position", width: 300},
            {field: "WOH_FLAG", title: "Work On Holiday", width: 150},
            {field: "COMPANY_NAME", title: "Company", width: 250},
            {field: "REMARKS", title: "Remarks", hidden: true},
            {field: "POSITION_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'Position List');

        app.searchTable('positionTable', ['LEVEL_NO', 'POSITION_NAME', 'COMPANY_NAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'LEVEL_NO': 'Level',
                'POSITION_NAME': 'Position',
                'POSITION_CODE': 'Code',
                'COMPANY_NAME': 'Company',
                'WOH_FLAG': 'Work On Holiday',
                'REMARKS': 'Remarks'
            }, 'Position List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'LEVEL_NO': 'Level',
                'POSITION_NAME': 'Position',
                'COMPANY_NAME': 'Company',
                'WOH_FLAG': 'Work On Holiday',
                'REMARKS': 'Remarks'
            }, 'Position List');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);