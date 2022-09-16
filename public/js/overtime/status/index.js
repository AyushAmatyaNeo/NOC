(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        var $tableContainer = $("#overtimeRequestStatusTable");
        var $search = $('#search');
        var $bulkActionDiv = $('#bulkActionDiv');
        var $bulkBtns = $(".btnApproveReject");
        var columns = [
            {field: "EMPLOYEE_CODE", title: "Code"},
            {field: "FULL_NAME", title: "Employee"},
            {title: "Requested Date",
                columns: [
                    {
                        field: "REQUESTED_DATE_AD",
                        title: "AD",
                    },
                    {
                        field: "REQUESTED_DATE_BS",
                        title: "BS",
                    }]
            },
            {title: "Overtime Date",
                columns: [
                    {
                        field: "OVERTIME_DATE_AD",
                        title: "AD",
                    },
                    {field: "OVERTIME_DATE_BS",
                        title: "BS",
                    }]},
            {field: "DETAILS", title: "Time (From-To)", template: `
                <ul id="branchList"> #  ln=DETAILS.length # #for(var i=0; i<ln; i++) { #
                    <li>
                       #=i+1 #) #=DETAILS[i].START_TIME # - #=DETAILS[i].END_TIME #
                    </li> #}#
                </ul>`},
            {field: "TOTAL_HOUR", title: "Total Hour", type: "number"},
            {field: "STATUS", title: "Status"},
            {field: ["OVERTIME_ID"], title: "Action", template: `
            <span> 
                <a class="btn  btn-icon-only btn-success" href="${document.viewLink}/#: OVERTIME_ID #" style="height:17px;" title="view">
                    <i class="fa fa-search-plus"></i>
                </a>
            </span>`}
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
        columns=app.prependPrefColumns(columns);
        map=app.prependPrefExportMap(map);
        var pk = 'OVERTIME_ID';
        var grid = app.initializeKendoGrid($tableContainer, columns, null, {id: pk, atLast: false, fn: function (selected) {
                if (selected) {
                    $bulkActionDiv.show();
                } else {
                    $bulkActionDiv.hide();
                }
            }});
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
            var q = document.searchManager.getSearchValues();
            q['requestStatusId'] = $('#requestStatusId').val();
            q['fromDate'] = $('#fromDate').val();
            q['toDate'] = $('#toDate').val();
            q['recomApproveId'] = $('#recomApproveId').val();
            app.serverRequest("", q).then(function (success) {
                app.renderKendoGrid($tableContainer, success.data);
                data = success.data;
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
        
//        $("#reset").on("click", function () {
//            $(".form-control").val("");
//        });

    });
})(window.jQuery, window.app);
