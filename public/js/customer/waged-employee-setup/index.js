(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var actiontemplateConfig = {
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
        var columns = [
            {field: "EMPLOYEE_ID", title: "EMPLOYEE ID", width: 150},
            {field: "FULL_NAME", title: "Name", width: 150},
            {field: "GENDER_NAME", title: "Gender", width: 150},
            {field: "BLOOD_GROUP_CODE", title: "Blood Group", width: 150},
            {field: "MOBILE_NO", title: "Mobile", width: 150},
            {field: "TELEPHONE_NO", title: "Telephone", width: 150},
            {field: ["EMPLOYEE_ID"], title: "Action", width: 120, template: app.genKendoActionTemplate(actiontemplateConfig)}
        ];
        var map = {
            'EMPLOYEE ID': 'EMPLOYEE_ID',
            'Employee': 'FULL_NAME',
            'Gender': 'GENDER_NAME',
            'Blood Group': 'BLOOD_GROUP_CODE',
            'Mobile': 'MOBILE_NO',
            'Telephone': 'TELEPHONE_NO',
        }
        app.initializeKendoGrid($table, columns);

        app.pullDataById("", {}).then(function (response) {
            console.log(response.data);
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });



    });
})(window.jQuery, window.app);