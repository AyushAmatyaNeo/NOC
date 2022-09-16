(function ($) {
    'use strict';
    $(document).ready(function () {
        var advanceGrid = $("#advanceApproveTable").kendoGrid({
            excel: {
                fileName: "AdvanceRequestList.xlsx",
                filterable: true,
                allPages: true
            },
            dataSource: {
                data: document.advanceApprove,
                pageSize: 20
            },
            height: 450,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: {
                input: true,
                numeric: false
            },
            dataBound: gridDataBound,
            columns: [
                {
                    title: 'Select All',
                    headerTemplate: "<input type='checkbox' id='header-chb' class='k-checkbox header-checkbox'><label class='k-checkbox-label' for='header-chb'></label>",
                    template: "<input type='checkbox' id='#:ADVANCE_REQUEST_ID#' role-id='#:ROLE#'  class='k-checkbox row-checkbox'><label class='k-checkbox-label' for='#:ADVANCE_REQUEST_ID#'></label>"
                },
                {field: "EMPLOEE_CODE", title: "Code"},
                {field: "FULL_NAME", title: "Employee"},
                {field: "ADVANCE_NAME", title: "Advance"},
                {title: "Requested Date",
                    columns: [{
                            field: "REQUESTED_DATE",
                            title: "English",
                            template: "<span>#: (REQUESTED_DATE == null) ? '-' : REQUESTED_DATE #</span>"},
                        {field: "REQUESTED_DATE_N",
                            title: "Nepali",
                            template: "<span>#: (REQUESTED_DATE_N == null) ? '-' : REQUESTED_DATE_N #</span>"}]},
                {title: "Advance Date",
                    columns: [{
                            field: "ADVANCE_DATE",
                            title: "English",
                            template: "<span>#: (ADVANCE_DATE == null) ? '-' : ADVANCE_DATE #</span>"},
                        {field: "ADVANCE_DATE_N",
                            title: "Nepali",
                            template: "<span>#: (ADVANCE_DATE_N == null) ? '-' : ADVANCE_DATE_N #</span>"}]},
                {field: "REQUESTED_AMOUNT", title: "Requested Amt."},
                {field: "TERMS", title: "Terms"},
                {field: "YOUR_ROLE", title: "Your Role"},
                {field: ["ADVANCE_REQUEST_ID", "ROLE"], title: "Action", template: `<span><a class="btn-edit" href="` + document.viewLink + `/#: ADVANCE_REQUEST_ID #/#: ROLE #" style="height:17px;" title="view detail">
                            <i class="fa fa-search-plus"></i>
                            </a></span>`}
            ]
        });


        var checkedIds = {};

        advanceGrid.on("click", ".k-checkbox", selectRow);

        function selectRow() {
            var checked = this.checked,
                    row = $(this).closest("tr"),
                    grid = $("#advanceApproveTable").data("kendoGrid"),
                    dataItem = grid.dataItem(row);
            checkedIds[dataItem.ADVANCE_REQUEST_ID] = {
                'checked': checked,
                data: {
                    'id': dataItem.ADVANCE_REQUEST_ID,
                    'role': dataItem.ROLE
                }

            }
            if (checked) {
                row.addClass("k-state-selected");
            } else {
                row.removeClass("k-state-selected");
            }

            var checkedNo = $('.k-state-selected').length;
            if (checkedNo > 0) {
                $('#acceptRejectDiv').show();
                if ($('#header-chb').prop('checked') == 1 && checkedNo == 1) {
                    $('#acceptRejectDiv').hide();
                }
            } else {
                $('#acceptRejectDiv').hide();
            }
        }


        $('#header-chb').change(function (ev) {
            var checked = ev.target.checked;
            $('.row-checkbox').each(function (idx, item) {
                if (checked) {
                    if (!($(item).closest('tr').is('.k-state-selected'))) {
                        $(item).click();
                    }
                } else {
                    if ($(item).closest('tr').is('.k-state-selected')) {
                        $(item).click();
                    }
                }
            });
        });


        $(".btnApproveReject").bind("click", function () {
            var btnId = $(this).attr('id');
            var selectedValues = [];
            for (var i in checkedIds) {
                if (checkedIds[i].checked) {
                    selectedValues.push(checkedIds[i].data);
                }
            }

//            console.log(selectedValues);
            App.blockUI({target: "#hris-page-content"});
            app.pullDataById(
                    document.approveRejectUrl,
                    {data: selectedValues, btnAction: btnId}
            ).then(function (success) {
                App.unblockUI("#hris-page-content");
                console.log(success);
                if (success.success == true) {
                    var dataSource = new kendo.data.DataSource({data: success.data, pageSize: 20});
                    var grid = $('#advanceApproveTable').data("kendoGrid");
                    dataSource.read();
                    grid.setDataSource(dataSource);
                    checkedIds = {};
                    $('#acceptRejectDiv').hide();
                }

            }, function (failure) {
                App.unblockUI("#hris-page-content");
                console.log(failure);
            });
        });



        app.searchTable('advanceApproveTable', ['FULL_NAME', 'EMPLOEE_CODE', 'ADVANCE_NAME', 'REQUESTED_DATE', 'REQUESTED_DATE_N', 'ADVANCE_DATE', 'ADVANCE_DATE_N', 'REQUESTED_AMOUNT', 'TERMS', 'YOUR_ROLE']);

        app.pdfExport(
                'advanceApproveTable',
                {
                    'EMPLOEE_CODE': 'Code',
                    'FULL_NAME': 'Name',
                    'ADVANCE_NAME': 'Advance',
                    'REQUESTED_DATE': 'Request Date(AD)',
                    'REQUESTED_DATE_N': 'Request Date(BS)',
                    'ADVANCE_DATE': 'Advance Date(AD)',
                    'ADVANCE_DATE_N': 'Advance Date(BS)',
                    'REQUESTED_AMOUNT': 'Request Amt',
                    'TERMS': 'Terms',
                    'YOUR_ROLE': 'Role',
                    'STATUS': 'status',
                    'REASON': 'Reason',
                    'RECOMMENDED_REMARKS': 'Recommeded Remarks',
                    'RECOMMENDED_DATE': 'Recommended Date',
                    'APPROVED_REMARKS': 'Approved Remarks',
                    'APPROVED_DATE': 'Approved Date'
                });


        function gridDataBound(e) {
            var grid = e.sender;
            if (grid.dataSource.total() == 0) {
                var colCount = grid.columns.length;
                $(e.sender.wrapper)
                        .find('tbody')
                        .append('<tr class="kendo-data-row"><td colspan="' + colCount + '" class="no-data">There is no data to show in the grid.</td></tr>');
            }
        }
        ;
        $("#export").click(function (e) {
            var rows = [{
                    cells: [
                        {value: "Employee Name"},
                        {value: "Advance Name"},
                        {value: "Requested Date(AD)"},
                        {value: "Requested Date(BS)"},
                        {value: "Advance Date(AD)"},
                        {value: "Advance Date(BS)"},
                        {value: "Requested Amount"},
                        {value: "Terms"},
                        {value: "Your Role"},
                        {value: "Status"},
                        {value: "Reason"},
                        {value: "Remarks By Recommender"},
                        {value: "Recommended Date"},
                        {value: "Remarks By Approver"},
                        {value: "Approved Date"}
                    ]
                }];
            var dataSource = $("#advanceApproveTable").data("kendoGrid").dataSource;
            var filteredDataSource = new kendo.data.DataSource({
                data: dataSource.data(),
                filter: dataSource.filter()
            });

            filteredDataSource.read();
            var data = filteredDataSource.view();

            for (var i = 0; i < data.length; i++) {
                var dataItem = data[i];

                rows.push({
                    cells: [
                        {value: dataItem.FULL_NAME},
                        {value: dataItem.ADVANCE_NAME},
                        {value: dataItem.REQUESTED_DATE},
                        {value: dataItem.REQUESTED_DATE_N},
                        {value: dataItem.ADVANCE_DATE},
                        {value: dataItem.ADVANCE_DATE_N},
                        {value: dataItem.REQUESTED_AMOUNT},
                        {value: dataItem.TERMS},
                        {value: dataItem.YOUR_ROLE},
                        {value: dataItem.STATUS},
                        {value: dataItem.REASON},
                        {value: dataItem.RECOMMENDED_REMARKS},
                        {value: dataItem.RECOMMENDED_DATE},
                        {value: dataItem.APPROVED_REMARKS},
                        {value: dataItem.APPROVED_DATE},
                    ]
                });
            }
            excelExport(rows);
            e.preventDefault();
        });

        function excelExport(rows) {
            var workbook = new kendo.ooxml.Workbook({
                sheets: [
                    {
                        columns: [
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true},
                            {autoWidth: true}
                        ],
                        title: "Advance Request List",
                        rows: rows
                    }
                ]
            });
            kendo.saveAs({dataURI: workbook.toDataURL(), fileName: "AdvanceRequestList.xlsx"});
        }
    });
})(window.jQuery, window.app);
