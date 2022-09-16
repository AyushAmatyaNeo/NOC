(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        var $monthId = $('#monthId');
        var $tableContainer = $("#overtimeRequestStatusTable");
        var $search = $('#search');
        var $bulkActionDiv = $('#bulkActionDiv');
        var $bulkBtns = $(".btnApproveReject");
        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.viewLink}/#:OVERTIME_CLAIM_ID#/#:ROLE#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
            </div>
        `;

        var selectItems = {};

        var columns = [
            {
                title: 'Select All',
                headerTemplate: "<input type='checkbox' id='header-chb' class='k-checkbox header-checkbox'><label class='k-checkbox-label' for='header-chb'></label>",
                template: "<input type='checkbox' id='#:OVERTIME_CLAIM_ID#' role-id='#:ROLE#'  class='k-checkbox row-checkbox'><label class='k-checkbox-label' for='#:OVERTIME_CLAIM_ID#'></label>",
                width: 40
            },
            {field: "EMPLOYEE_CODE", title: "Code"},
            {field: "FULL_NAME", title: "Employee"},
            {title: "Month Detail", field: "MONTH_DESC"},
            {field: "TOTAL_REQ_OT_DAYS", title: "Requested OT Days"},
            {field: "TOTAL_REQ_GRAND_TOTAL_LEAVE", title: "Requested Substitute Leave"},
            {field: "TOTAL_APP_OT_DAYS", title: "Approved OT Days"},
            {field: "TOTAL_APP_GRAND_TOTAL_LEAVE", title: "Approved Substitute Leave"},
            {field: "STATUS", title: "Status"},
            {field: ["OVERTIME_CLAIM_ID", "ROLE"], title: "Action", template: action}
        ];
        var map = {
            'EMPLOYEE_CODE': 'Code',
            'FULL_NAME': 'Name',
            'REQUESTED_DATE_AD': 'Request Date(AD)',
            'REQUESTED_DATE_BS': 'Request Date(BS)',
            'OVERTIME_DATE_AD': 'Overtime Date(AD)',
            'OVERTIME_DATE_BS': 'Overtime Date(BS)',
            'TOTAL_HOUR': 'Total Hour',
            'DESCRIPTION': 'Description',
            'STATUS': 'Status',
            'REMARKS': 'Remarks',
            'RECOMMENDED_REMARKS': 'Recommended Remarks',
            'RECOMMENDED_DATE': 'Recommended Date',
            'APPROVED_REMARKS': 'Approved Remarks',
            'APPROVED_DATE': 'Approved Date'

        };

        $tableContainer.on('click', '.k-checkbox', function () {
            var checked = this.checked;
            var row = $(this).closest("tr");
            var grid = $tableContainer.data("kendoGrid");
            var dataItem = grid.dataItem(row);
            selectItems[dataItem['OVERTIME_CLAIM_ID']].checked = checked;
            if (checked) {
                row.addClass("k-state-selected");
                $bulkActionDiv.show();
            } else {
                row.removeClass("k-state-selected");
                var atleastOne = false;
                for (var key in selectItems) {
                    if (selectItems[key]['checked']) {
                        atleastOne = true;
                        return;
                    }
                }
                if (atleastOne) {
                    $bulkActionDiv.show();
                } else {
                    $bulkActionDiv.hide();
                }

            }
        });

        map=app.prependPrefExportMap(map);
        var pk = 'OVERTIME_CLAIM_ID';
        var grid = app.initializeKendoGrid($tableContainer, columns, null, null);
        app.searchTable($tableContainer, ["FULL_NAME", "EMPLOYEE_CODE"]);

        $('#excelExport').on('click', function () {
            app.excelExport(processData(data), map, "OT Request List.xlsx");
        });
        $('#excelExportCalculated').on('click', function () {
            app.excelExport(processData(data, true), map, "OT Request List.xlsx");
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($tableContainer, map, "OT Request List.pdf");
        });
        var data = [];
        var processData = function (i, sum) {
            var o = [];
            var t = 0;
            for (var x = 0; x < i.length; x++) {
                i[x]['TOTAL_HOUR'] = parseFloat(i[x]['TOTAL_HOUR']);
                t = t + i[x]['TOTAL_HOUR'];
                o.push(i[x]);
            }
            if (sum) {
                o.push({'TOTAL_HOUR': t});
            }
            return o;
        };
        $search.on('click', function () {
            $bulkActionDiv.hide();
            var q = document.searchManager.getSearchValues();
            q['requestStatusId'] = $('#requestStatusId').val();
            q['monthId'] = $monthId.val();
            console.log(q);
            app.serverRequest("", q).then(function (success) {
                selectItems = {};
                var data = success.data;
                for (var i in data) {
                    selectItems[data[i]['OVERTIME_CLAIM_ID']] = {'checked': false, 'role': data[i]['ROLE']};
                }
                app.renderKendoGrid($tableContainer, success.data);
                data = success.data;
            }, function (failure) {
            });
        });

        $bulkBtns.bind("click", function () {
            var list = grid.getSelected();
            var action = $(this).attr('action');
            var selectedValues = [];
            for (var i in selectItems) {
                if (selectItems[i].checked) {
                    selectedValues.push({id: i, role: selectItems[i]['role']});
                }
            }
console.log(selectedValues);
console.log(action);

            app.bulkServerRequest(document.approveRejectUrl, {data: selectedValues, btnAction: btnId}, function () {
                $search.trigger('click');
            }, function (data, error) {

            });
        });
        
//        $("#reset").on("click", function () {
//            $(".form-control").val("");
//        });

        var monthList = null;
        var $fiscalYear = $('#fiscalYearId');
        var $month = $('#monthId');

        app.setFiscalMonth($fiscalYear, $month, function (years, months, currentMonth) {
            monthList = months;
        });

        
        $month.select2();
        $('#fiscalYearId').select2();

    });
})(window.jQuery, window.app);
