(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.viewLink}/#:ADVANCE_REQUEST_ID#/#:ROLE#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
            </div>
        `;

        var columns = [
            {
                title: 'Select All',
                headerTemplate: "<input type='checkbox' id='header-chb' class='k-checkbox header-checkbox'><label class='k-checkbox-label' for='header-chb'></label>",
                template: "<input type='checkbox' id='#:ADVANCE_REQUEST_ID#' role-id='#:ROLE#'  class='k-checkbox row-checkbox'><label class='k-checkbox-label' for='#:ADVANCE_REQUEST_ID#'></label>",
                width: 40
            },
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

//

        app.pullDataById('', {}).then(function (response) {
            if (response.success) {
//                console.log(response.data);
                app.renderKendoGrid($table, response.data);
                selectItems = {};
                var data = response.data;
                for (var i in data) {
                    selectItems[data[i]['ADVANCE_REQUEST_ID']] = {'checked': false, 'role': data[i]['ROLE']};
                }
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

        var selectItems = {};
        var $bulkBtnContainer = $('#acceptRejectDiv');
        var $bulkBtns = $(".btnApproveReject");
        $table.on('click', '.k-checkbox', function () {
            var checked = this.checked;
            var row = $(this).closest("tr");
            var grid = $table.data("kendoGrid");
            var dataItem = grid.dataItem(row);
            selectItems[dataItem['ADVANCE_REQUEST_ID']].checked = checked;
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
                    selectedValues.push({id: i, role: selectItems[i]['role']});
                }
            }
            App.blockUI({target: "#hris-page-content"});
            app.pullDataById(
                    document.approveRejectUrl,
                    {data: selectedValues, btnAction: btnId}
            ).then(function (success) {
                App.unblockUI("#hris-page-content");
                window.location.reload(true);
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });


        });

    });
})(window.jQuery, window.app);
