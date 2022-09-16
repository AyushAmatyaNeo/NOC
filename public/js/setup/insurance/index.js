(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#insuranceTable');
        
        var viewAction = '<a class="btn-edit" title="Edit" href="' + document.viewLink + '/#:INSURANCE_ID#" style="height:17px;"> <i class="fa fa-search-plus"></i></a>';
        var editAction = '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:INSURANCE_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>';
        var deleteAction = '<a   class=" confirmation btn-edit" title="Delete" href="' + document.deleteLink + '/#:INSURANCE_ID#" style="height:17px;"> <i class="fa fa-trash"></i></a>';
        //var editAction = '<a class="btn-edit" title="Edit" href="${document.editLink}/#:REG_DRAFT_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>';
        //var deleteAction = '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:BRANCH_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>';
        var action = viewAction + editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "INSURANCE_CODE", title: "Insurance Code", width: 150},
            {field: "INSURANCE_ENAME", title: "Insurance Name", width: 150},
            {field: "V_TYPE", title: "Type", width: 100},
            {field: "IS_OPEN", title: "Is Open", width: 80},
            {field: "SERVICE_TYPE_NAME", title: "Service Type", width: 80},
            {field: "REMARKS", title: "Remarks", width: 80},
            {field: "INSURANCE_ID", title: "Action", width: 50, template: action},
        ], null, null, null, 'Insurance List');

        app.serverRequest(document.getTableData,'').then(function(success){
            app.renderKendoGrid($table,success.data);
        }, function (failure){
            ApplicationCache.unblockUI("#hris-page-content");
        });

        app.searchTable('insuranceTable', ['INSURANCE_CODE', 'INSURANCE_ENAME']);
        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'INSURANCE_CODE': 'Insurance Code',
                'INSURANCE_ENAME': 'Insurance Name',
                'V_TYPE': 'Type',
                'IS_OPEN': 'Is Open',
                'SERVICE_TYPE_NAME': 'Service Type',
                'REMARKS': 'Remarks',
            }, 'Insurance List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'INSURANCE_CODE': 'Insurance Code',
                'INSURANCE_ENAME': 'Insurance Name',
                'V_TYPE': 'Type',
                'IS_OPEN': 'Is Open',
                'SERVICE_TYPE_NAME': 'Service Type',
                'REMARKS': 'Remarks',
            }, 'Insurance List');
        });
        // app.pullDataById("", {}).then(function (response) {
        //     app.renderKendoGrid($table, response.data);
        // }, function (error) {
        // });
    });
})(window.jQuery);

