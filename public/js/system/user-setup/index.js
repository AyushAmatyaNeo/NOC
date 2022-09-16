(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["USER_ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["USER_ID"],
                'url': document.deleteLink
            }
        };
        var columns = [
            {field: "COMPANY_NAME", title: "Company", width: 150},
            {field: "ROLE_NAME", title: "Role", width: 150},
            {field: "EMPLOYEE_CODE", title: "Code", width: 150},
            {field: "FULL_NAME", title: "Employee Name", width: 150},
            {field: "USER_NAME", title: "User Name", width: 150},
            {field: "STATUS", title: "Status", width: 150},
            {field: "USER_ID", title: "Action", width: 120, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ];
        var map = {
            'COMPANY_NAME': 'Company',
            'ROLE_NAME': 'Role',
            'EMPLOYEE_CODE': 'Code',
            'FULL_NAME': 'Employee Name',
            'USER_NAME': 'User Name',
            'STATUS': 'Status',
        }
        app.initializeKendoGrid($table, columns);

        app.searchTable($table, ['COMPANY_NAME', 'ROLE_NAME', 'FULL_NAME','USER_NAME','EMPLOYEE_CODE']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'User List.xlsx');
        });
        $('#excelExportWithPassword').on('click', function () {
            map['PASSWORD'] = "Password";
            app.excelExport($table, map, 'User List.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'User List.pdf');
        });

        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);