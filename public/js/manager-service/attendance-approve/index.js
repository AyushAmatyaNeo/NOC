(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.viewLink}/#:ID#/#:ROLE#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
            </div>
        `;

        var rowTemplateString = "<tr  class='#: (PRI_SEC=='SECONDARY')  ? 'bg-primary' : '' #' data-uid='#: uid #'>" +
          "<td><input type='checkbox' id='#:ID#' role-id='#:ROLE#'  class='k-checkbox row-checkbox'><label class='k-checkbox-label' for='#:ID#'></label></td>" +
          "<td>#: EMPLOYEE_CODE #</td>" +
          "<td>#: FULL_NAME #</td>" +
          "<td>#: ATTENDANCE_DT #</td>" +
          "<td>#: ATTENDANCE_DT_BS #</td>" +
          "<td>#: REQUESTED_DT #</td>" +
          "<td>#: REQUESTED_DT_BS #</td>" +
          "<td>#: IN_TIME #</td>" +
          "<td>#: IN_REMARKS #</td>" +
          "<td>#: OUT_TIME #</td>" +
          "<td>#: OUT_REMARKS #</td>" +
          "<td>#: TOTAL_HOUR #</td>" +
          "<td>#: STATUS_DETAIL #</td>" +
          "<td>#: YOUR_ROLE #</td>" +
          "<td>"+action+"</td>" +
          "</tr>";

        app.initializeKendoGrid($table, [
            {
                title: 'Select All',
                headerTemplate: "<input type='checkbox' id='header-chb' class='k-checkbox header-checkbox'><label class='k-checkbox-label' for='header-chb'></label>",
                template: "<input type='checkbox' id='#:ID#' role-id='#:ROLE#'  class='k-checkbox row-checkbox'><label class='k-checkbox-label' for='#:ID#'></label>",
                width: 40
            },
            {field: "EMPLOYEE_CODE", title: "Code"},
            {field: "FULL_NAME", title: "Employee"},
            {title: "Date of Attendance",
                columns: [{
                        field: "ATTENDANCE_DT",
                        title: "AD",
                    },
                    {
                        field: "ATTENDANCE_DT_BS",
                        title: "BS",
                    }]},
            {title: "Requested Date",
                columns: [
                    {
                        field: "REQUESTED_DT",
                        title: "AD",
                    },
                    {
                        field: "REQUESTED_DT_BS",
                        title: "BS",
                    }]
            },
            {field: "IN_TIME", title: "In Time"},
            {field: "IN_REMARKS", title: "In Remarks"},
            {field: "OUT_TIME", title: "Out Time"},
            {field: "OUT_REMARKS", title: "Out Remarks"},
            {field: "TOTAL_HOUR", title: "Total Hour"},
            {field: "STATUS_DETAIL", title: "Status"},
            {field: "YOUR_ROLE", title: "Role"},
            {field: ["ID", "ROLE"], title: "Action", template: action}
        ], null, null, {rowTemplate : rowTemplateString}, 'Attendance Request List');


        app.pullDataById('', {}).then(function (response) {
            if (response.success) {
                app.renderKendoGrid($table, response.data);
                selectItems = {};
                var data = response.data;
                for (var i in data) {
                    selectItems[data[i]['ID']] = {'checked': false, 'role': data[i]['ROLE']};
                }
            } else {
                app.showMessage(response.error, 'error');
            }
        }, function (error) {
            app.showMessage(error, 'error');
        });

        app.searchTable($table, ['FULL_NAME', 'EMPLOYEE_CODE']);
        var exportMap = {
            'EMPLOYEE_CODE': 'Code',
            'FULL_NAME': 'Employee Name',
            'REQUESTED_DT': 'Requested Date(AD)',
            'REQUESTED_DT_BS': 'Requested Date(BS)',
            'ATTENDANCE_DT': 'Attendance Date(AD)',
            'ATTENDANCE_DT_BS': 'Attendance Date(BS)',
            'IN_TIME': 'In Time',
            'OUT_TIME': 'Out Time',
            'IN_REMARKS': 'In Remarks',
            'OUT_REMARKS': 'Out Remarks',
            'TOTAL_HOUR': 'Total Hour',
            'STATUS_DETAIL': 'Status',
            'RECOMMENDER_NAME': 'Recommender',
            'RECOMMENDED_DT': 'Recommended Date',
            'RECOMMENDED_REMARKS': 'Recommender Remarks',
            'APPROVER_NAME': 'Approver',
            'APPROVED_DT': 'Aprroved Date',
            'APPROVED_REMARKS': 'Approver Remarks'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Attendance Request List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Attendance Request List');
        });
        var selectItems = {};
        var $bulkBtnContainer = $('#acceptRejectDiv');
        var $bulkBtns = $(".btnApproveReject");
        $table.on('click', '.k-checkbox', function () {
            var checked = this.checked;
            var row = $(this).closest("tr");
            var grid = $table.data("kendoGrid");
            var dataItem = grid.dataItem(row);
            if (selectItems[dataItem['ID']] === undefined) {
                selectItems[dataItem['ID']] = {'checked': checked, 'role': dataItem['ROLE']};
            } else {
                selectItems[dataItem['ID']]['checked'] = checked;
            }
            if (checked) {
                row.addClass("k-state-selected");
                $bulkBtnContainer.show();
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
                    $bulkBtnContainer.show();
                } else {
                    $bulkBtnContainer.hide();
                }

            }
        });
        $bulkBtns.bind("click", function () {
            var btnId = $(this).attr('id');
            var selectedValues = [];
            for (var i in selectItems) {
                if (selectItems[i].checked) {
                    selectedValues.push({id: i, role: selectItems[i]['role'], btnAction: btnId, status: selectItems[i]['STATUS_DETAIL']});
                }
            }
            app.bulkServerRequest(document.approveRejectUrl, selectedValues, function () {
                window.location.reload(true);
            }, function (data, error) {

            });
        });


    });
})(window.jQuery, window.app);
