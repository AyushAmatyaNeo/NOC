(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        var $tableContainer = $("#advanceRequestStatusTable");
        var $search = $('#search');
        var columns = [
            {field: "FULL_NAME", title: "Employee"},
            {field: "ADVANCE_NAME", title: "Advance"},
            {title: "Requested Date",
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
            {field: "REQUESTED_AMOUNT", title: "Requested Amt."},
            {field: "TERMS", title: "Terms"},
            {field: "YOUR_ROLE", title: "Your Role"},
            {field: "STATUS", title: "Status"},
            {field: ["ADVANCE_REQUEST_ID", "ROLE"], title: "Action", template: `
            <span>  
            <a class="btn  btn-icon-only btn-success" href="${document.viewLink}/#: ADVANCE_REQUEST_ID #/#: ROLE #" style="height:17px;" title="view">
            <i class="fa fa-search-plus"></i></a>
            </span>`}
        ];
        var map = {
            'FULL_NAME': 'Name',
            'ADVANE_NAME': 'Advance',
            'REQUESTED_DATE_AD': 'Request Date(AD)',
            'REQUESTED_DATE_BS': 'Request Date(BS)',
            'ADVANCE_DATE_AD': 'Advance Date(AD)',
            'ADVANCE_DATE_BS': 'Advance Date(BS)',
            'REQUESTED_AMOUNT': 'Request Amt',
            'TERMS': 'Terms',
            'YOUR_ROLE': 'Role',
            'STATUS': 'Status',
            'REASON': 'Reason',
            'RECOMMENDED_REMARKS': 'Recommender Remarks',
            'RECOMMENDED_DATE': 'Recommended Date',
            'APPROVED_REMARKS': 'Approver Remarks',
            'APPROVED_DATE': 'Approved Date'
        };
        app.initializeKendoGrid($tableContainer, columns, null, null, null, "AdvanceRequestList.xlsx");
        app.searchTable('advanceRequestStatusTable', ['FULL_NAME', 'ADVANCE_NAME', 'REQUESTED_DATE', 'ADVANCE_DATE', 'REQUESTED_DATE_N', 'ADVANCE_DATE_N', 'REQUESTED_AMOUNT', 'TERMS', 'YOUR_ROLE', 'STATUS']);

        $search.on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['advanceId'] = $('#advanceId').val();
            q['advanceRequestStatusId'] = $('#advanceRequestStatusId').val();
            q['fromDate'] = $('#fromDate').val();
            q['toDate'] = $('#toDate').val();
            q['recomApproveId'] = $('#recomApproveId').val();
            App.blockUI({target: "#hris-page-content"});
            app.pullDataById(document.pullAdvanceRequestStatusListLink, q).then(function (success) {
                App.unblockUI("#hris-page-content");
                app.renderKendoGrid($tableContainer, success.data);
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });
        });

        $('#excelExport').on('click', function () {
            app.excelExport($tableContainer, map, "AdvanceRequestList.xlsx");
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($tableContainer, map, "AdvanceRequestList.pdf");
        });
    });
})(window.jQuery, window.app);
