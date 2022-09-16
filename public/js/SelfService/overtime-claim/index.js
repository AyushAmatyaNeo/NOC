(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        // <a class="btn btn-icon-only yellow" href="${document.editLink}/#:OVERTIME_CLAIM_ID#" style="height:17px;" title="Edit">
        //             <i class="fa fa-edit"></i>
        //         </a>
        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.viewLink}/#:OVERTIME_CLAIM_ID#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
                #if(ALLOW_DELETE=='Y'){#
                
                <a  class="btn btn-icon-only red confirmation" href="${document.deleteLink}/#:OVERTIME_CLAIM_ID#" style="height:17px;" title="Cancel">
                    <i class="fa fa-times"></i>
                </a>
                #}#
            </div>
        `;
        var columns = [
            {title: "Month Detail", field: "MONTH_DESC"},
            {field: "TOTAL_REQ_OT_HOURS", title: "Requested OT Hours"},
            {field: "TOTAL_REQ_GRAND_TOTAL_LEAVE", title: "Requested Substitute Leave"},
            {field: "TOTAL_APP_OT_HOURS", title: "Approved OT Hours"},
            {field: "TOTAL_APP_GRAND_TOTAL_LEAVE", title: "Approved Substitute Leave"},
            {field: "STATUS", title: "Status"},
            {field: ["OVERTIME_CLAIM_ID", "ALLOW_DELETE"], title: "Action", width: 140, template: action}
        ];
        // var map = {
        //     'OVERTIME_DATE_AD': ' Overtime Date(AD)',
        //     'OVERTIME_DATE_BS': ' Overtime Date(BS)',
        //     'REQUESTED_DATE_AD': 'Request Date(AD)',
        //     'REQUESTED_DATE_BS': 'Request Date(BS)',
        //     'TOTAL_HOUR': 'Total Hour',
        //     'STATUS': 'Status',
        //     'IN_DESCRIPTION': 'Description',
        //     'REMARKS': 'Remarks',
        //     'RECOMMENDER_NAME': 'Recommender',
        //     'APPROVER_NAME': 'Approver',
        //     'RECOMMENDED_REMARKS': 'Recommended Remarks',
        //     'RECOMMENDED_DATE': 'Recommended Date',
        //     'APPROVED_REMARKS': 'Approved Remarks',
        //     'APPROVED_DATE': 'Approved Date'
        // };
        app.initializeKendoGrid($table, columns, null, null, null, 'Overtime Request List List');

        app.searchTable($table, ['OVERTIME_DATE_AD', 'OVERTIME_DATE_BS', 'REQUESTED_DATE_AD', 'REQUESTED_DATE_BS', 'STATUS']);

        $('.k-grid-pdf').on('click', function(){
            var fc = app.filterExportColumns(null, map);
            app.exportToPDFSmallFontSize($table, fc, 'Overtime Request List.pdf');
            return false;
        });

        $('.k-grid-excel').on('click', function(){
            var fc = app.filterExportColumns(null, map);
            app.excelExport($table, fc, 'Overtime Request List.xlsx');
            return false;
        });
        
        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Overtime Request List.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDFSmallFontSize($table, map, 'Overtime Request List.pdf');
        });

        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery, window.app);