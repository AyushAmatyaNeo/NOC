(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#loanTable');
        var viewAction = '<span><a class="btn-edit" href="' + document.viewLink + '/#: LOAN_REQUEST_ID #" style="height:17px;" title="view detail">'
                + '<i class="fa fa-search-plus"></i></a>';
        var deleteAction = '#if(ALLOW_TO_EDIT == 1){#'
                + '<a class="confirmation btn-delete" href="' + document.deleteLink + '/#: LOAN_REQUEST_ID #" id="bs_#:LOAN_REQUEST_ID #" style="height:17px;">'
                + '<i class="fa fa-trash-o"></i></a>#}#'
                + '</span>';
        var action = viewAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "LOAN_NAME", title: "Loan Name"},
            {title: "Applied Date",
                columns: [{
                        field: "REQUESTED_DATE_AD",
                        title: "AD",
                        template: "<span>#: (REQUESTED_DATE_AD == null) ? '-' : REQUESTED_DATE_AD #</span>"},
                    {field: "REQUESTED_DATE_BS",
                        title: "BS",
                        template: "<span>#: (REQUESTED_DATE_BS == null) ? '-' : REQUESTED_DATE_BS #</span>"}]},
            {title: "Loan Date",
                columns: [{
                        field: "LOAN_DATE_AD",
                        title: "AD",
                        template: "<span>#: (LOAN_DATE_AD == null) ? '-' : LOAN_DATE_AD #</span>"},
                    {field: "LOAN_DATE_BS",
                        title: "BS",
                        template: "<span>#: (LOAN_DATE_BS == null) ? '-' : LOAN_DATE_BS #</span>"}]},
            {field: "REQUESTED_AMOUNT", title: "Request Amount"},
            {field: "STATUS", title: "Status"},
            {field: ["LOAN_REQUEST_ID", "ALLOW_TO_EDIT"], title: "Action", template: action}
        ], null, null, null, 'Loan Request');


        app.searchTable('loanTable', ['LOAN_NAME', 'REQUESTED_DATE_AD', 'REQUESTED_DATE_BS', 'LOAN_DATE_AD', 'LOAN_DATE_BS', 'REQUESTED_AMOUNT', 'STATUS']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'LOAN_NAME': 'Loan',
                'REQUESTED_DATE_AD': 'Request Date(AD)',
                'REQUESTED_DATE_BS': 'Request Date(BS)',
                'LOAN_DATE_AD': 'Loan Date(AD)',
                'LOAN_DATE_BS': 'Loan Date(BS)',
                'REQUESTED_AMOUNT': 'Request Amt',
                'STATUS': 'Status',
                'REASON': 'Reason',
                'RECOMMENDER_NAME': 'Recommender',
                'APPROVER_NAME': 'Approver',
                'RECOMMENDED_REMARKS': 'Recommended Remarks',
                'RECOMMENDED_DATE': 'Recommended Dt',
                'APPROVED_REMARKS': 'Approved Remarks',
                'APPROVED_DATE': 'Approved Date'
            }, 'Loan Request List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'LOAN_NAME': 'Loan',
                'REQUESTED_DATE_AD': 'Request Date(AD)',
                'REQUESTED_DATE_BS': 'Request Date(BS)',
                'LOAN_DATE_AD': 'Loan Date(AD)',
                'LOAN_DATE_BS': 'Loan Date(BS)',
                'REQUESTED_AMOUNT': 'Request Amt',
                'STATUS': 'Status',
                'REASON': 'Reason',
                'RECOMMENDER_NAME': 'Recommender',
                'APPROVER_NAME': 'Approver',
                'RECOMMENDED_REMARKS': 'Recommended Remarks',
                'RECOMMENDED_DATE': 'Recommended Dt',
                'APPROVED_REMARKS': 'Approved Remarks',
                'APPROVED_DATE': 'Approved Date'
            }, 'Loan Request List');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
        



    });
})(window.jQuery, window.app);