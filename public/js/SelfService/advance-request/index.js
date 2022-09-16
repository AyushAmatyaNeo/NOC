(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#advanceTable');
        var viewAction = '<span><a class="btn-edit" href="' + document.viewLink + '/#: ADVANCE_REQUEST_ID #" style="height:17px;" title="view detail">'
                + '<i class="fa fa-search-plus"></i>'
                + '</a>';
        var deleteAction = '#if(ALLOW_TO_EDIT == 1){#'
                + '<a class="confirmation btn-delete" href="' + document.deleteLink + '/#: ADVANCE_REQUEST_ID #" id="bs_#:ADVANCE_REQUEST_ID #" style="height:17px;">'
                + '<i class="fa fa-trash-o"></i>'
                + '</a> #}#'
                + '</span>';
        var action = viewAction + deleteAction;

        app.initializeKendoGrid($table, [
            {field: "ADVANCE_NAME", title: "Advance Name"},
            {title: "Applied Date",
                columns: [{
                        field: "REQUESTED_DATE_AD",
                        title: "AD",
                        template: "<span>#: (REQUESTED_DATE_AD == null) ? '-' : REQUESTED_DATE_AD #</span>"},
                    {field: "REQUESTED_DATE_BS",
                        title: "BS",
                        template: "<span>#: (REQUESTED_DATE_BS == null) ? '-' : REQUESTED_DATE_BS #</span>"}]},
            {title: "Advance Date",
                columns: [{
                        field: "ADVANCE_DATE_AD",
                        title: "AD",
                        template: "<span>#: (ADVANCE_DATE_AD == null) ? '-' : ADVANCE_DATE_AD #</span>"},
                    {field: "ADVANCE_DATE_BS",
                        title: "BS",
                        template: "<span>#: (ADVANCE_DATE_BS == null) ? '-' : ADVANCE_DATE_BS #</span>"}]},
            {field: "REQUESTED_AMOUNT", title: "Request Amount"},
            {field: "TERMS", title: "Terms(in month)"},
            {field: "STATUS", title: "Status"},
            {field: ["ADVANCE_REQUEST_ID", "ALLOW_TO_EDIT"], title: "Action", template: action}
        ], null, null, null, 'Advance Request List');

        app.searchTable('advanceTable', ['ADVANCE_NAME', 'REQUESTED_DATE_AD', 'REQUESTED_DATE_BS', 'ADVANCE_DATE_AD', 'ADVANCE_DATE_BS', 'REQUESTED_AMOUNT', 'TERMS', 'STATUS']);


        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'ADVANCE_NAME': 'Advance',
                'REQUESTED_DATE_AD': 'Request Dt(AD)',
                'REQUESTED_DATE_BS': 'Request Dt(BS)',
                'ADVANCE_DATE_AD': 'Advance Dt(AD)',
                'ADVANCE_DATE_BS': 'Advance Dt(BS)',
                'REQUESTED_AMOUNT': 'Request Amt',
                'TERMS': 'Terms',
                'STATUS': 'Status',
                'REASON': 'Reason',
                'RECOMMENDER_NAME': 'Recommender',
                'APPROVER_NAME': 'Approver',
                'RECOMMENDED_REMARKS': 'Reccommended Remarks',
                'RECOMMENDED_DATE': 'Recommended Dt',
                'APPROVED_REMARKS': 'Approved Remarks',
                'APPROVED_DATE': 'Approved Dt'
            }, 'Advance Request List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'ADVANCE_NAME': 'Advance',
                'REQUESTED_DATE_AD': 'Request Dt(AD)',
                'REQUESTED_DATE_BS': 'Request Dt(BS)',
                'ADVANCE_DATE_AD': 'Advance Dt(AD)',
                'ADVANCE_DATE_BS': 'Advance Dt(BS)',
                'REQUESTED_AMOUNT': 'Request Amt',
                'TERMS': 'Terms',
                'STATUS': 'Status',
                'REASON': 'Reason',
                'RECOMMENDER_NAME': 'Recommender',
                'APPROVER_NAME': 'Approver',
                'RECOMMENDED_REMARKS': 'Reccommended Remarks',
                'RECOMMENDED_DATE': 'Recommended Dt',
                'APPROVED_REMARKS': 'Approved Remarks',
                'APPROVED_DATE': 'Approved Dt'
            }, 'Advance Request List');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {
            console.log(error);
        });

    });
})(window.jQuery, window.app);