(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#branchTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:BRANCH_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:BRANCH_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "BRANCH_NAME", title: "Name", width: 150},
            {field: "STREET_ADDRESS", title: "Address", width: 180},
            {field: "TELEPHONE", title: "Telephone", width: 120},
            {field: "EMAIL", title: "Email", width: 140},
            {field: "COMPANY_NAME", title: "Company", width: 120},
            {field: "FULL_NAME", title: "Branch Manager", width: 120},
            {field: "BRANCH_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'Branch List');

        app.searchTable('branchTable', ['BRANCH_NAME', 'STREET_ADDRESS', 'TELEPHONE', 'EMAIL', 'COMPANY_NAME']);
        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'BRANCH_NAME': 'Name',
                'STREET_ADDRESS': 'Address',
                'TELEPHONE': 'Telephone',
                'EMAIL': 'Email',
                'COMPANY_NAME': 'Company',
            }, 'Branch List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'BRANCH_NAME': 'Name',
                'STREET_ADDRESS': 'Address',
                'TELEPHONE': 'Telephone',
                'EMAIL': 'Email',
                'COMPANY_NAME': 'Company',
            }, 'Branch List');
        });

        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {
        });

        $("#sync").click(function(){
            app.serverRequest(document.syncLink, {}).then(function (response) {
                response.success == true ? app.showMessage(response.message, 'success') : 
                app.showMessage(response.message, 'error');
                app.pullDataById("", {}).then(function (response) {
                    app.renderKendoGrid($table, response.data);
                }, function (error) {
                });
            }, function (error) {
            });
        });
    });
})(window.jQuery);

