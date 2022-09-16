(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
        var $search = $('#search');
        var $fromDate = $('#fromDate');
        var $toDate = $('#toDate');
        var $status = $('#statusId');
        var $table = $('#table');
        var $bulkActionDiv = $('#bulkActionDiv');
        var $bulkBtns = $(".btnApproveReject");
        var selectedStatus;
        var columns = [
            {field: "MEDICAL_ID", title: "Tran No", width: 80},
            {field: "EMPLOYEE_CODE", title: "Code", width: 80},
            {field: "FULL_NAME", title: "Employee", width: 120},
            {field: "DEPARTMENT_NAME", title: "Department", width: 100},
            {field: "FUNCTIONAL_TYPE_EDESC", title: "Functional Type", width: 130},
            {field: "CLAIM_OF_NAME", title: "Claim Of", width: 90},
            {field: "BILL_STATUS_NAME", title: "Status", width: 80},
            {field: "REQUESTED_AMT", title: "Request Amt", width: 80},
            {field: "APPROVED_AMT", title: "Approved Amt", width: 80},
            {field: "MEDICAL_ID", title: "Action", width: 80, template: `
            <span>                                  
                <a class="btn  btn-icon-only btn-success" href="${document.viewLink}/#: MEDICAL_ID #" style="height:17px;" title="view">
                <i class="fa fa-search-plus"></i>
                </a>
            </span>`}
        ];
        var map = {
            'ADVANCE_CODE': 'AdvanceCode',
            'ADVANCE_ENAME': 'Name'
        }

        var pk = 'MEDICAL_ID';
        var grid = app.initializeKendoGrid($table, columns, "Advance List.xlsx",
                {id: pk, atLast: false, fn: function (selected) {
                        if (selected && selectedStatus == 'AP') {
                            $bulkActionDiv.show();
                        } else {
                            $bulkActionDiv.hide();
                        }
                    }}

        );

        app.searchTable($table, ['ADVANCE_ENAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Advance List.xlsx');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Advance List.pdf');
        });


        $search.on('click', function () {
            $bulkActionDiv.hide();
            var q = document.searchManager.getSearchValues();
            q['fromDate'] = $fromDate.val();
            q['toDate'] = $toDate.val();
            q['status'] = $status.val();
            selectedStatus = q['status'];
            app.serverRequest(document.pullMedicalListLink, q).then(function (response) {
                if (response.success) {
                    console.log(response);
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });


        $bulkBtns.bind("click", function () {
            var list = grid.getSelected();
            var action = $(this).attr('action');

            var selectedValues = [];
            for (var i in list) {
                selectedValues.push({id: list[i][pk], action: action});
            }

            console.log(selectedValues);

            app.bulkServerRequest(document.bulkLink, selectedValues, function () {
                $search.trigger('click');
            }, function (data, error) {

            });
        });

//        $("#reset").on("click", function () {
//            $(".form-control").val("");
//        });




    });
})(window.jQuery, window.app);