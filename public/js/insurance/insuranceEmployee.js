(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#insuranceTable');
        
        var editAction = '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:INSURANCE_EMP_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>';
        var deleteAction = '<a   class=" confirmation btn-edit" title="Delete" href="' + document.deleteLink + '/#:INSURANCE_EMP_ID#" style="height:17px;"> <i class="fa fa-trash"></i></a>';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "FULL_NAME", title: "Employee Name", width: 100},
            {field: "INSURANCE_ENAME", title: "Insurance Name", width: 100},
            {field: "APPROVED_AMT", title: "Approved Amount", width: 100},
            {field: "RELEASED_AMT", title: "Released Amount", width: 80},
            {field: "INSURANCE_DT", title: "Insurance Date", width: 80},
            {field: "IS_COMPLETED", title: "Completed", width: 80},
            {field: "MATURED_DT", title: "Matured Date", width: 80},
            {field: "REMARKS", title: "Remarks", width: 120},
            {field: "INSURANCE_EMP_ID", title: "Action", width: 50, template: action},
        ], null, null, null, 'Insurance List');

        app.serverRequest('','').then(function(success){
            app.renderKendoGrid($table,success.data);
        }, function (failure){
            ApplicationCache.unblockUI("#hris-page-content");
        });

        app.searchTable('insuranceTable', ['FULL_NAME', 'INSURANCE_ENAME']);
        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'FULL_NAME': 'Employee Name',
                'INSURANCE_ENAME': 'Insurance Name',
                'APPROVED_AMT': 'Approved Amount',
                'RELEASED_AMT': 'Released Amount',
                'INSURANCE_DT': 'Insurance Date',
                'IS_COMPLETED': 'Completed',
                'MATURED_DT': 'Matured Date',
                'REMARKS': 'Remarks',
            }, 'Insurance List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'FULL_NAME': 'Employee Name',
                'INSURANCE_ENAME': 'Insurance Name',
                'APPROVED_AMT': 'Approved Amount',
                'RELEASED_AMT': 'Released Amount',
                'INSURANCE_DT': 'Insurance Date',
                'IS_COMPLETED': 'Completed',
                'MATURED_DT': 'Matured Date',
                'REMARKS': 'Remarks',
            }, 'Insurance List');
        });
    });
})(window.jQuery);

