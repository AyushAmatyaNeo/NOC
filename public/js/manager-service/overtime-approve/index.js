(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.viewLink}/#:OVERTIME_ID#/#:ROLE#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
            </div>
        `;
        app.initializeKendoGrid($table, [
            {
                title: 'Select All',
                headerTemplate: "<input type='checkbox' id='header-chb' class='k-checkbox header-checkbox'><label class='k-checkbox-label' for='header-chb'></label>",
                template: "<input type='checkbox' id='#:OVERTIME_ID#' role-id='#:ROLE#'  class='k-checkbox row-checkbox'><label class='k-checkbox-label' for='#:OVERTIME_ID#'></label>",
                width: 40
            },
            {field: "EMPLOYEE_CODE", title: "Code"},
            {field: "FULL_NAME", title: "Employee"},
            {title: "Requested Date",
                columns: [{
                        field: "REQUESTED_DATE",
                        title: "English",
                        template: "<span>#: (REQUESTED_DATE == null) ? '-' : REQUESTED_DATE #</span>"},
                    {field: "REQUESTED_DATE_N",
                        title: "Nepali",
                        template: "<span>#: (REQUESTED_DATE_N == null) ? '-' : REQUESTED_DATE_N #</span>"}]},
            {title: "Overtime Date",
                columns: [{
                        field: "OVERTIME_DATE",
                        title: "English",
                        template: "<span>#: (OVERTIME_DATE == null) ? '-' : OVERTIME_DATE #</span>"},
                    {field: "OVERTIME_DATE_N",
                        title: "Nepali",
                        template: "<span>#: (OVERTIME_DATE_N == null) ? '-' : OVERTIME_DATE_N #</span>"}]},
            {
                field: "DETAILS",
                title: "Time (From-To)",
                template: `<ul id="branchList">  
                                #  ln=DETAILS.length #
                                #for(var i=0; i<ln; i++) { #
                                    <li>
                                    #=i+1 #) #=DETAILS[i].START_TIME # - #=DETAILS[i].END_TIME #
                                </li>
                                #}#
                            </ul>`},
            {field: "TOTAL_HOUR", title: "Total Hour"},
            {field: "YOUR_ROLE", title: "Your Role"},
            {field: ["OVERTIME_ID", "ROLE"], title: "Action", template: action}
        ], null, null, null, 'Overtime Request List');


        app.pullDataById('', {}).then(function (response) {
            if (response.success) {
                app.renderKendoGrid($table, response.data);
                selectItems = {};
                var data = response.data;
                for (var i in data) {
                    selectItems[data[i]['OVERTIME_ID']] = {'checked': false, 'role': data[i]['ROLE']};
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
            'REQUESTED_DATE': 'Request Date(AD)',
            'REQUESTED_DATE_N': 'Request Date(BS)',
            'OVERTIME_DATE': 'Overtime Date(AD)',
            'OVERTIME_DATE_N': 'Overtime Date(BS)',
            'TOTAL_HOUR': 'Total Hour',
            'DESCRIPTION': 'Description',
            'YOUR_ROLE': 'Role',
            'STATUS': 'Status',
            'REMARKS': 'Remarks',
            'RECOMMENDED_REMARKS': 'Recommended Remarks',
            'RECOMMENDED_DATE': 'Recommended Date',
            'APPROVED_REMARKS': 'Approved Remarks',
            'APPROVED_DATE': 'Approved Date'

        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Overtime Request List');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Overtime Request List');
        });
        var selectItems = {};
        var $bulkBtnContainer = $('#acceptRejectDiv');
        var $bulkBtns = $(".btnApproveReject");
        $table.on('click', '.k-checkbox', function () {
            var checked = this.checked;
            var row = $(this).closest("tr");
            var grid = $table.data("kendoGrid");
            var dataItem = grid.dataItem(row);
            if (selectItems[dataItem['OVERTIME_ID']] === undefined) {
                selectItems[dataItem['OVERTIME_ID']] = {'checked': checked, 'role': dataItem['ROLE']};
            } else {
                selectItems[dataItem['OVERTIME_ID']]['checked'] = checked;
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
