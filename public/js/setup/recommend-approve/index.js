(function ($) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var $table = $('#table');
        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["EMPLOYEE_ID"],
                'url': document.editLink
            },
            delete: {
//                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'ALLOW_DELETE': 'N',
                'params': ["EMPLOYEE_ID"],
                'url': document.deleteLink
            }
        };
        var columns = [
            {field: "COMPANY_NAME", title: "Company"},
            {field: "EMPLOYEE_CODE", title: "Code"},
            {field: "EMPLOYEE_NAME", title: "Name"},
            {field: "RECOMMENDER_NAME", title: "Recommender"},
            {field: "APPROVER_NAME", title: "Approver"},
            {field: "A_R_NAME", title: "Alt Recm"},
            {field: "A_A_NAME", title: "Alt App"},
            {field: ["EMPLOYEE_ID"], title: "Action", template: app.genKendoActionTemplate(actiontemplateConfig)}
        ];
        var map = {
            'COMPANY_NAME': 'Company',
            'EMPLOYEE_CODE': 'Code',
            'EMPLOYEE_NAME': 'Name',
            'RECOMMENDER_NAME': 'Recommender',
            'APPROVER_NAME': 'Approver',
            'A_R_NAME': 'Alternate Recommender',
            'A_A_NAME': 'Alternate Approver',
        }
        app.initializeKendoGrid($table, columns, null, null, null, 'Recommender Approver List.xlsx');

        app.searchTable($table, ['EMPLOYEE_NAME', 'EMPLOYEE_CODE']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Recommender Approver List.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Recommender Approver List.pdf');
        });
        $('#search').on('click', function () {
            var search = document.searchManager.getSearchValues();
            search['recommenderId'] = $('#recommenderId').val();
            search['approverId'] = $('#approverId').val();
            app.pullDataById("", search).then(function (response) {
                app.renderKendoGrid($table, response.data);
            }, function (error) {

            });
        });
        
//        $('#reset').on('click',function (){
//            $('.form-control').val("");
//        });
    });
})(window.jQuery);