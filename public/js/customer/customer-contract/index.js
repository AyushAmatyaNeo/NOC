(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["CONTRACT_ID"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["CONTRACT_ID"],
                'url': document.deleteLink
            }
        };
        
        var actionTemplate=app.genKendoActionTemplate(actiontemplateConfig);
        var locationLinkTemplate=`
                <a class="btn-edit" title="View" href="${document.contractDetailLink}/#:CONTRACT_ID #" style="height:17px;">
                    <i class="fa fa-search-plus"></i>
                </a>
                <a class="btn-edit" title="View" href="${document.contractPrintLink}/#:CONTRACT_ID #" style="height:17px;">
                    <i class="fa fa-print"></i>
                </a>
                `;
        var allTemplate=actionTemplate+locationLinkTemplate;
        
        var columns = [
            {field: "CONTRACT_NAME", title: "Contract" ,width:"150px"},
            {field: "CUSTOMER_ENAME", title: "Customer" ,width:"150px"},
            {title: "From Date", columns: [
                    {field: "START_DATE_AD", title: "AD" ,width:"100px"},
                    {field: "START_DATE_BS", title: "BS" ,width:"100px"},
                ]},
            {title: "To Date", columns: [
                    {field: "END_DATE_AD", title: "AD" ,width:"100px"},
                    {field: "END_DATE_BS", title: "BS" ,width:"100px"},
                ]},
            {field: "REMARKS", title: "Remarks"},
            {field: ["CONTRACT_ID"], width:"90px" ,title: "Action", template: allTemplate}
        ];
        var map = {
            'CONTRACT_NAME': 'Contract',
            'CUSTOMER_ENAME': 'Customer Name',
            'START_DATE_AD': 'From (AD)',
            'START_DATE_BS': 'From (BS)',
            'END_DATE_AD': 'To (AD)',
            'END_DATE_BS': 'To (BS)',
            'REMARKS': 'Remarks',
        }
        app.initializeKendoGrid($table, columns);

        app.searchTable($table, ['CONTRACT_NAME','CUSTOMER_ENAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Customer Contract List.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Customer Contract List.pdf');
        });

        app.pullDataById("", {}).then(function (response) {
            console.log(response);
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);