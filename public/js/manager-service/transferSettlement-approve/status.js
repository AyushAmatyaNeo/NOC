(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.expenseDetailLink}/#:JOB_HISTORY_ID#/#:SERIAL_NUMBER#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
                #if(STATUS=='RQ'){#
                <a class="btn btn-icon-only yellow" href="${document.editLink}/#:JOB_HISTORY_ID#/#:SERIAL_NUMBER#" style="height:17px;" title="Edit">
                    <i class="fa fa-edit"></i>
                </a>
                #}#
                #if(STATUS=='AP'){#
                    <a class="btn btn-icon-only blue" href="${document.addLink}/#:JOB_HISTORY_ID#/#:SERIAL_NUMBER#" style="height:17px;" title="Edit">
                        <i class="fa fa-plus"></i>
                    </a>
                    #}#
            </div>
        `;

        var rowTemplateString = "<tr>" +
          "<td>#: EMPLOYEE_CODE #</td>" +
          "<td>#: FULL_NAME #</td>" +
          "<td>#: START_DATE #</td>" +
          "<td>#: START_DATE_BS #</td>" +
          "<td>#: EVENT_DATE #</td>" +
          "<td>#: EVENT_DATE_BS #</td>" +
          "<td>#: FROM_BRANCH #</td>" +
          "<td>#: TO_BRANCH #</td>" +
          "<td>#: REQ_SUM #</td>" +
          "<td>#: AP_SUM #</td>" +
          "<td>#: STATUS_DETAIL #</td>" +
          "<td>"+action+"</td>" +
          "</tr>";

        app.initializeKendoGrid($table, [

            {field: "EMPLOYEE_CODE", title: "Code"},
            {field: "FULL_NAME", title: "Employee"},
            {title: "Start Date",
                columns: [{
                        field: "START_DATE",
                        title: "English",
                    },
                    {
                        field: "START_DATE_BS",
                        title: "Nepali",
                    }]},
            
            {title: "Event Date",
                columns: [{
                        field: "EVENT_DATE",
                        title: "English",
                    },
                    {field: "EVENT_DATE_BS",
                        title: "Nepali",
                    }]},
            {field: "FROM_BRANCH", title: "From Branch"},
            {field: "TO_BRANCH", title: "To Branch"},
            //{field: "REQUESTED_TYPE_DETAIL", title: "Request For"},
            {field: "REQ_SUM", title: "Requested Amount"},
            {field: "AP_SUM", title: "Approved Amount"},
            {field: "STATUS_DETAIL", title: "Status"},
            {field: ["JOB_HISTORY_ID"], title: "Action", template: action}
        ], null, null, {rowTemplate : rowTemplateString}, 'Travel Request List');

        app.datePickerWithNepali('startDate', 'nepaliFromDate');
        app.datePickerWithNepali('eventDate', 'nepaliToDate');

        $('#search').on('click', function () {
            app.serverRequest('', {
                'status': $('#status').val(),
                'fromDate': $('#startDate').val(),
                'toDate': $('#eventDate').val()
            }).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($table, response.data);
                    selectItems = {};
                    var data = response.data;
                    for (var i in data) {
                        selectItems[data[i]['JOB_HISTORY_ID']] = {'checked': false, 'role': data[i]['SERIAL_NUMBER']};
                    }
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

        

        app.searchTable($table, ['FULL_NAME', 'EMPLOYEE_CODE']);
        var exportMap = {
            'EMPLOYEE_CODE': 'Code',
            'FULL_NAME': 'Employee Name',
            'START_DATE': 'Start Date(AD)',
            'START_DATE_BS': 'Start Date(BS)',
            'EVENT_DATE': 'Event Date(AD)',
            'EVENT_DATE_BS': 'Event Date(BS)',
            'FROM_BRANCH': 'From Branch',
            'TO_BRANCH': 'To Branch',
            'REQ_SUM': 'Requested Amount',
            'AP_SUM': 'Approved Amount',
            'STATUS_DETAIL': 'Status'
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
            selectItems[dataItem['JOB_HISTORY_ID']].checked = checked;
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
            console.log(selectItems);
            for (var i in selectItems) {
                if (selectItems[i].checked) {
                    selectedValues.push({id: i, role: selectItems[i]['role'], btnAction: btnId});
                }
            }
            console.log(selectedValues);
            for (var j in selectedValues){
                console.log(selectedValues[j]);
                app.serverRequest(document.approveRejectUrl, selectedValues[j]).then(event => function (response) {
                    event.preventDefault();
                    if (response.success) {
                        app.showMessage(response.success, 'msg');
                        window.location.reload(true);
                    } else {
                        app.showMessage(response.error, 'error');
                    }
                }, ev => function (error) {
                    ev.preventDefault();
                    // app.showMessage(response.success, 'success');
                    window.location.reload(true);
                });
                // app.serverRequest(document.approveRejectUrl, selectedValues[j]).then(function (response) {
                //     window.location.reload(true);
                // });
            }

            window.location.reload(true);
            // app.bulkServerRequest(document.approveRejectUrl, selectedValues, function () {
            //     //  window.location.reload(true);
            // }, function (data, error) {

            // });
        });

        $("#reset").on("click", function () {
            $(".form-control").val("");
            $('#status').val(-1)
        });
    });
})(window.jQuery, window.app);