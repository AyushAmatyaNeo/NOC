(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#employeeList');

        var actiontemplateConfig = {
            view: {
                'params': ["EMPLOYEE_ID"],
                'url': document.viewLink
            },
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["EMPLOYEE_ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["EMPLOYEE_ID"],
                'url': document.deleteLink
            }
        };
        app.initializeKendoGrid($table, [
            {field: "EMPLOYEE_CODE", title: "Employee Code", template: "<span>#: (EMPLOYEE_CODE == null) ? '-' : EMPLOYEE_CODE #</span>"},
            {field: "FULL_NAME", title: "Full Name", template: "<span>#: (FULL_NAME == null) ? '-' : FULL_NAME #</span>"},
            {field: "MOBILE_NO", title: "Mobile No", template: "<span>#: (MOBILE_NO == null) ? '-' : MOBILE_NO #</span>"},
            {title: "Birth Date", columns: [{field: "BIRTH_DATE_AD", title: "AD"}, {field: "BIRTH_DATE_BS", title: "BS"}]},
            {field: "COMPANY_NAME", title: "Company", template: "<span>#: (COMPANY_NAME == null) ? '-' : COMPANY_NAME #</span>"},
            {field: "BRANCH_NAME", title: "Branch", template: "<span>#: (BRANCH_NAME == null) ? '-' : BRANCH_NAME #</span>"},
            {field: "DEPARTMENT_NAME", title: "Department", template: "<span>#: (DEPARTMENT_NAME == null) ? '-' : DEPARTMENT_NAME #</span>"},
            {field: "POSITION_NAME", title: "Position", template: "<span>#: (POSITION_NAME == null) ? '-' : POSITION_NAME #</span>"},
            {field: "DESIGNATION_TITLE", title: "Designation", template: "<span>#: (DESIGNATION_TITLE == null) ? '-' : DESIGNATION_TITLE #</span>"},
            {field: "SERVICE_TYPE_NAME", title: "Service Type", template: "<span>#: (SERVICE_TYPE_NAME == null) ? '-' : SERVICE_TYPE_NAME    #</span>"},
            {field: "EMPLOYEE_ID", title: "Action", width: 120, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ]);
        app.searchTable('employeeList', ['EMPLOYEE_CODE', 'FULL_NAME', 'MOBILE_NO', 'BIRTH_DATE', 'COMPANY_NAME', 'BRANCH_NAME', 'DEPARTMENT_NAME', "DESIGNATION_TITLE"]);
        app.pdfExport(
                'Subordinate List',
                {
                    'EMPLOYEE_CODE': 'Employee Code',
                    'FULL_NAME': 'Full Name',
                    'MOBILE_NO': 'Mobile No',
                    'BIRTH_DATE_AD': 'Birth Date(AD)',
                    'BIRTH_DATE_BS': 'Birth Date(BS)',
                    'COMPANY_NAME': 'Company',
                    'BRANCH_NAME': 'Branch',
                    'DEPARTMENT_NAME': 'Department',
                    'POSITION_NAME': 'Position',
                    'DESIGNATION_TITLE': 'Designation',
                    'SERVICE_TYPE_NAME': 'Service Type',
                }
        );
        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
        $('#export').on('click', function () {
            app.excelExport($table, {
                'EMPLOYEE_CODE': 'Employee Code',
                'FULL_NAME': 'Full Name',
                'MOBILE_NO': 'Mobile No',
                'BIRTH_DATE_AD': 'Birth Date(AD)',
                'BIRTH_DATE_BS': 'Birth Date(BS)',
                'COMPANY_NAME': 'Company',
                'BRANCH_NAME': 'Branch',
                'DEPARTMENT_NAME': 'Department',
                'POSITION_NAME': 'Position',
                'DESIGNATION_TITLE': 'Designation',
                'SERVICE_TYPE_NAME': 'Service Type',
            }, 'Subordinate List');
        });
    });
})(window.jQuery, window.app);
