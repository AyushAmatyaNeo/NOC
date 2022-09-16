(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        var $tableContainer = $("#leaveDeductionStatusTable");
        var $search = $('#search');
        var $bulkActionDiv = $('#bulkActionDiv');
        var $bulkBtns = $(".btnApproveReject");
        var $superpower = $("#super_power");
        
        var $leaveId = $("#leaveId");
        var $leaveYear = $('#leaveYear');

        $.each(document.searchManager.getIds(), function (key, value) {
            $('#' + value).select2();
        });

        var columns = [
            {field: "EMPLOYEE_CODE", title: "Code"},
            {field: "FULL_NAME", title: "Employee"},
            {field: "LEAVE_ENAME", title: "Leave"},
            {title: "Deduction Date",
                columns: [{
                        field: "DEDUCTION_DT_AD",
                        title: "AD",
                        template: "<span>#: (DEDUCTION_DT_AD == null) ? '-' : DEDUCTION_DT_AD #</span>"},
                    {field: "DEDUCTION_DT_BS",
                        title: "BS",
                        template: "<span>#: (DEDUCTION_DT_BS == null) ? '-' : DEDUCTION_DT_BS #</span>"}]},
            {field: "NO_OF_DAYS", title: "No of Days"},
            {field: "REMARKS", title: "Remarks"},
            {field: "STATUS", title: "Status"},
            // {field: "ID", title: "Action", template: `
            // <span>
            //     <a class="btn  btn-icon-only btn-success" href="${document.viewLink}/#: ID #" style="height:17px; width:13px" title="view">
            //     <i class="fa fa-search-plus"></i>
            //     </a>
            // </span>`}
        ];
        var pk = 'ID';
        var grid = app.initializeKendoGrid($tableContainer, columns, null, {id: pk, atLast: false, fn: function (selected) {
                if (selected) {
                    $bulkActionDiv.show();
                } else {
                    $bulkActionDiv.hide();
                }
            }}, null, 'Leave Deduction Report.xlsx');
        app.searchTable($tableContainer, ["FULL_NAME", "EMPLOYEE_CODE"]);
  
        var map = {
            'EMPLOYEE_CODE': 'Code',
            'FULL_NAME': 'Name',
            // 'FUNCTIONAL_TYPE_EDESC': 'Functional Type',
            'LEAVE_ENAME': 'Leave',
            'DEDUCTION_DT_AD': 'Deduction Date(AD)',
            'DEDUCTION_DT_BS': 'Deduction Date(BS)',
            'NO_OF_DAYS': 'No Of Days',
            'REMARKS': 'Remarks',
            'STATUS': 'Status',
        };
        // map = app.prependPrefExportMap(map);

        $search.on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['fromDate'] = $('#fromDate').val();
            q['toDate'] = $('#toDate').val();
            q['leaveId'] = $leaveId.val();
            q['leaveYear'] = $leaveYear.val();
            App.blockUI({target: "#hris-page-content"});
            app.pullDataById(document.pullLeaveDeductionStatus, q).then(function (success) {
                App.unblockUI("#hris-page-content");
                app.renderKendoGrid($tableContainer, success.data);
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });
        });

        $('#excelExport').on('click', function () {
            app.excelExport($tableContainer, map, "Leave Deduction List.xlsx");
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($tableContainer, map, "Leave Deduction List.pdf", 'A2');
        });

        $bulkBtns.bind("click", function () {
            var list = grid.getSelected();
            var action = $(this).attr('action');
            var superPower = $superpower.prop('checked');

            var selectedValues = [];
            for (var i in list) {
                selectedValues.push({id: list[i][pk], action: action, status: list[i]['STATUS'], super_power: superPower});
            }
            app.bulkServerRequest(document.bulkLink, selectedValues, function () {
                $search.trigger('click');
            }, function (data, error) {

            });
        });
        
        
         function leaveYearChange(leaveYear){
            let leaveList = document.allLeaveForReport[leaveYear];
            app.populateSelect($leaveId, leaveList, 'LEAVE_ID', 'LEAVE_ENAME', 'All Leaves', -1, -1);
        }
        leaveYearChange($leaveYear.val());
        
        $leaveYear.on('change', function () {
            let selectedLeaveYear = $(this).val();
            leaveYearChange(selectedLeaveYear);
        });
        

    });
})(window.jQuery, window.app);
