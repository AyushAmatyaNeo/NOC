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
        app.initializeKendoGrid($table, [
            {
                title: 'Select All',
                headerTemplate: "<input type='checkbox' id='header-chb' class='k-checkbox header-checkbox'><label class='k-checkbox-label' for='header-chb'></label>",
                template: "<input type='checkbox' id='#:ID#' role-id='#:ROLE#'  class='k-checkbox row-checkbox'><label class='k-checkbox-label' for='#:ID#'></label>",
                width: 40
            },
            {field: "EMPLOYEE_CODE", title: "Code"},
            {field: "FULL_NAME", title: "Employee"},
            {field: "HOLIDAY_ENAME", title: "Holiday"},
            {title: "Requested Date",
                columns: [{
                        field: "REQUESTED_DATE",
                        title: "English",
                        template: "<span>#: (REQUESTED_DATE == null) ? '-' : REQUESTED_DATE #</span>"},
                    {field: "REQUESTED_DATE_N",
                        title: "Nepali",
                        template: "<span>#: (REQUESTED_DATE_N == null) ? '-' : REQUESTED_DATE_N #</span>"}]},
            {title: "From Date",
                columns: [{
                        field: "FROM_DATE",
                        title: "English",
                        template: "<span>#: (FROM_DATE == null) ? '-' : FROM_DATE #</span>"},
                    {field: "FROM_DATE_N",
                        title: "Nepali",
                        template: "<span>#: (FROM_DATE_N == null) ? '-' : FROM_DATE_N #</span>"}]},
            {title: "To Date",
                columns: [{
                        field: "TO_DATE",
                        title: "English",
                        template: "<span>#: (TO_DATE == null) ? '-' : TO_DATE #</span>"},
                    {field: "TO_DATE_N",
                        title: "Nepali",
                        template: "<span>#: (TO_DATE_N == null) ? '-' : TO_DATE_N #</span>"}]},
            {field: "DURATION", title: "Duration"},
            {field: "YOUR_ROLE", title: "Your Role"},
            {field: ["ID", "ROLE"], title: "Action", template: `
                <span>  
                    <a class="btn-edit" href=" ` + document.viewLink + `/#:ID #/#:ROLE #" style="height:17px;" title="view">
                        <i class="fa fa-search-plus"></i>
                    </a>
                </span>
            `}
        ], null, null, null, 'Holiday Work Request');


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
            'FULL_NAME': 'Name',
            'HOLIDAY_ENAME': 'Holiday',
            'REQUESTED_DATE': 'Request Date(AD)',
            'REQUESTED_DATE_N': 'Request Date(BS',
            'FROM_DATE': 'From Date(AD)',
            'FROM_DATE_N': 'From Date(BS)',
            'TO_DATE': 'To Date(AD)',
            'TO_DATE_N': 'To Date(BS)',
            'DURATION': 'Duration',
            'YOUR_ROLE': 'Role',
            'STATUS': 'Status',
            'REMARKS': 'Remarks',
            'RECOMMENDED_REMARKS': 'Recommended Remarks',
            'RECOMMENDED_DATE': 'Recommended Date',
            'APPROVED_REMARKS': 'Approved Remarks',
            'APPROVED_DATE': 'Approved Date'

        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Holiday Work Request');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Holiday Work Request');
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
                    selectedValues.push({id: i, role: selectItems[i]['role'], btnAction: btnId});
                }
            }
            app.bulkServerRequest(document.approveRejectUrl, selectedValues, function () {
                window.location.reload(true);
            }, function (data, error) {

            });
        });


    });
})(window.jQuery, window.app);
