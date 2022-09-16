(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        var $search = $('#search');
        var $status = $('#status');
        var $fromDate = $('#fromDate');
        var $toDate = $('#toDate');

        var $table = $('#table');
        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.viewLink}/#:ADVANCE_REQUEST_ID#/#:ROLE#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
            </div>
        `;

        var columns = [
            {field: "EMPLOYEE_CODE", title: "Code"},
            {field: "EMPLOYEE_NAME", title: "Employee"},
            {field: "ADVANCE_ENAME", title: "Advance"},
            {title: "Request Date",
                columns: [{
                        field: "REQUESTED_DATE_AD",
                        title: "English",
                    },
                    {
                        field: "REQUESTED_DATE_BS",
                        title: "Nepali",
                    }]},
            {title: "Date Of Advance",
                columns: [{
                        field: "DATE_OF_ADVANCE",
                        title: "English",
                    },
                    {field: "DATE_OF_ADVANCE",
                        title: "Nepali",
                    }]},
            {field: "REQUESTED_AMOUNT", title: "Request Amt."},
            {field: "STATUS_DETAIL", title: "Status"},
            {field: "YOUR_ROLE", title: "Role"},
            {field: ["ADVANCE_REQUEST_ID", "ROLE"], title: "Action", template: action}
        ];

        app.initializeKendoGrid($table, columns, "Advance To Approve List.xlsx");


        $search.on('click', function () {
            app.pullDataById('', {
                'status': $status.val(),
                'fromDate': $fromDate.val(),
                'toDate': $toDate.val()
            }).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });



        app.searchTable($table, ['EMPLOYEE_NAME', 'EMPLOYEE_CODE']);
        var exportMap = {
            'EMPLOYEE_CODE': 'Code',
            'EMPLOYEE_NAME': 'Employee Name',
            'ADVANCE_ENAME': 'Advance',
            'REQUESTED_DATE_AD': 'Request Date(AD)',
            'REQUESTED_DATE_BS': 'Request Date(BS)',
            'DATE_OF_ADVANCE_AD': 'Advance Date(AD)',
            'DATE_OF_ADVANCE_BS': 'Advance Date(BS)',
            'STATUS_DETAIL': 'Status',
            'YOUR_ROLE': 'Role',
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Travel Request List.xlsx');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Travel Request List.pdf');
        });

        $("#reset").on("click", function () {
            if (typeof document.ids !== "undefined") {
                $.each(document.ids, function (key, value) {
                    $("#" + key).val(value).change();
                });
            }
        });



    });
})(window.jQuery, window.app);
