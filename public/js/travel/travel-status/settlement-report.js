(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        var $table = $('#table');
        var action = `
            <div class="clearfix">
                #if(REASON =='Not Applied'){#
                    <a class="btn btn-icon-only green" href="${document.viewLink}/#:TRAVEL_ID#" style="height:17px;" title="View Detail">
                        <i class="fa fa-search"></i>
                    </a>
                    <a  class="btn btn-icon-only blue" href="${document.expenseAddLink}/#:TRAVEL_ID#" style="height:17px;" title="Apply For Expense">
                        <i class="fa fa-arrow-right"></i>
                    </a>
                #}#
                #if(REASON =='Not Approved'){#
                    <a class="btn btn-icon-only green" href="${document.expenseDetailLink}/#:REFERENCE_TRAVEL_ID#" style="height:17px;" title="View Detail">
                        <i class="fa fa-search"></i>
                    </a>
                    <a class="btn btn-icon-only yellow" href="${document.expenseEditLink}/#:REFERENCE_TRAVEL_ID#" style="height:17px;" title="Edit">
                        <i class="fa fa-edit"></i>
                    </a>
                #}#

            </div>
        `;
        app.initializeKendoGrid($table, [
            {field: "EMPLOYEE_CODE", title: "Code"},
            {field: "EMPLOYEE_NAME", title: "Employee"},
            {title: "Start Date",
                columns: [{
                        field: "FROM_DATE_AD",
                        title: "English",
                    },
                    {
                        field: "FROM_DATE_BS",
                        title: "Nepali",
                    }]},
            {title: "To Date",
                columns: [{
                        field: "TO_DATE_AD",
                        title: "English",
                    },
                    {field: "TO_DATE_BS",
                        title: "Nepali",
                    }]},
            {title: "Applied Date",
                columns: [{
                        field: "REQUESTED_DATE_AD",
                        title: "English",
                    },
                    {field: "REQUESTED_DATE_BS",
                        title: "Nepali",
                    }]},
            {field: "DESTINATION", title: "Destination"},
            {field: "PURPOSE", title: "Purpose"},
            {field: "REQUESTED_AMOUNT", title: "Request Amt."},
            {field: "REASON", title: "Reason"},
            {field: "TRAVEL_ID", title: "Action", template: action}
        ]);
        app.serverRequest('', {}).then(function (response) {
            if (response.success) {
                app.renderKendoGrid($table, response.data);
            } else {
                app.showMessage(response.error, 'error');
            }
        }, function (error) {
            app.showMessage(error, 'error');
        });
        app.searchTable($table, ['EMPLOYEE_NAME', 'EMPLOYEE_CODE']);
        var exportMap = {
            'EMPLOYEE_CODE': 'Code',
            'EMPLOYEE_NAME': 'Employee Name',
            'REQUESTED_DATE_AD': 'Request Date(AD)',
            'REQUESTED_DATE_BS': 'Request Date(BS)',
            'FROM_DATE_AD': 'From Date(AD)',
            'FROM_DATE_BS': 'From Date(BS)',
            'TO_DATE_AD': 'To Date(AD)',
            'TO_DATE_BS': 'To Date(BS)',
            'DESTINATION': 'Destination',
            'PURPOSE': 'Purpose',
            'REQUESTED_AMOUNT': 'Request Amt',
            'VOUCHER_NO': 'Voucher No',
            'REASON': 'Reason',
            'REMARKS': 'Remarks',
            'RECOMMENDER_NAME': 'Recommender',
            'APPROVER_NAME': 'Approver',
            'RECOMMENDED_BY_NAME': 'Recommended By',
            'APPROVED_BY_NAME': 'Approved By',
            'RECOMMENDED_REMARKS': 'Recommended Remarks',
            'RECOMMENDED_DATE': 'Recommended Date',
            'APPROVED_REMARKS': 'Approved Remarks',
            'APPROVED_DATE': 'Approved Date'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Travel Not Settled List.xlsx');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Travel Not Settled List.pdf');
        });
    });
})(window.jQuery, window.app);
