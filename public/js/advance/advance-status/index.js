(function ($, app) {
    'use strict';
    $(document).ready(function () {
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        var $status = $('#status');
        var $search = $('#searchAdvance');
        var $table = $('#table');
        var $bulkActionDiv = $('#bulkActionDiv');
        var $bulkBtns = $(".btnApproveReject");
        $.each(document.searchManager.getIds(), function (key, value) {
            $('#' + value).select2();
        });
        $status.select2();
        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.viewLink}/#:ADVANCE_REQUEST_ID#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
            #if(STATUS=='AP'){#
                <a class="btn btn-icon-only green" href="${document.paymentViewLink}/#:ADVANCE_REQUEST_ID#" style="height:17px;" title="Payment Detail">
                    <i class="fa fa-money"></i>
                </a>
            #}#
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
            {field: "VOUCHER_NO", title: "Voucher"},
            {field: "ADVANCE_REQUEST_ID", title: "Action", template: action}
        ];

        var pk = 'ADVANCE_REQUEST_ID';
        var grid = app.initializeKendoGrid($table, app.prependPrefColumns(columns), null, {id: pk, atLast: false, fn: function (selected) {
                if (selected) {
                    $bulkActionDiv.show();
                } else {
                    $bulkActionDiv.hide();
                }
            }});

        app.searchTable($table, [
            'EMPLOYEE_CODE',
            'EMPLOYEE_NAME',
            'ADVANCE_ENAME',
            'REQUESTED_DATE_AD',
            'REQUESTED_DATE_BS',
            'DATE_OF_ADVANCE_AD',
            'DATE_OF_ADVANCE_BS',
            'STATUS_DETAIL',
            'REQUESTED_AMOUNT',
            'VOUCHER_NO'
        ]);
        var exportMap = app.prependPrefExportMap({
            'EMPLOYEE_CODE': 'Code',
            'EMPLOYEE_NAME': 'Employee Name',
            'ADVANCE_ENAME': 'Advance',
            'REQUESTED_DATE_AD': 'Request Date(AD)',
            'REQUESTED_DATE_BS': 'Request Date(BS)',
            'DATE_OF_ADVANCE_AD': 'Advance Date(AD)',
            'DATE_OF_ADVANCE_BS': 'Advance Date(BS)',
            'STATUS_DETAIL': 'Status',
            'REQUESTED_AMOUNT': 'Requested Amt',
            'VOUCHER_NO': 'Voucher',
        });

        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Advance Request List.xlsx');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Advance Request List.pdf');
        });



        $search.on('click', function () {
            var data = document.searchManager.getSearchValues();
            data['status'] = $status.val();
            data['fromDate'] = $('#fromDate').val();
            data['toDate'] = $('#toDate').val();
            app.serverRequest("", data).then(function (response) {
                app.renderKendoGrid($table, response.data, "AdvanceRequestList.xlsx");
            }, function (failure) {
            });
        });
        $bulkBtns.bind("click", function () {
            var list = grid.getSelected();
            var action = $(this).attr('action');

            var selectedValues = [];
            for (var i in list) {
                selectedValues.push({id: list[i][pk], action: action});
            }
            app.bulkServerRequest(document.bulkLink, selectedValues, function () {
                $search.trigger('click');
            }, function (data, error) {

            });
        });
        
//        $('#reset').on('click', function (){
//            $('.form-control').val("");
//        })

    });
})(window.jQuery, window.app);
