(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["CUSTOMER_ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["CUSTOMER_ID"],
                'url': document.deleteLink
            }
        };
        
        
        var actionTemplate=app.genKendoActionTemplate(actiontemplateConfig);
        var locationLinkTemplate=`
                <a class="btn-edit" title="View" href="${document.locationSetupLink}/#:CUSTOMER_ID #" style="height:17px;">
                    <i class="fa fa-search-plus"></i>
                </a>`;
        var allTemplate=actionTemplate+locationLinkTemplate;
        
        var columns = [
            {field: "CUSTOMER_ENAME", title: "Name", width: 150},
            {field: "ADDRESS", title: "Address", width: 150},
            {field: "PHONE_NO", title: "Phone No", width: 150},
            {field: ["CUSTOMER_ID"], title: "Action", width: 120, template:allTemplate }
        ];
        var map = {
            'CUSTOMER_ENAME': 'Name',
            'ADDRESS': 'Address',
            'PHONE_NO': 'Phone no',
        }
        app.initializeKendoGrid($table, columns);

        app.searchTable($table, ['CUSTOMER_ENAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Customer List.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Customer List.pdf');
        });

        app.serverRequest(document.listLink, {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);