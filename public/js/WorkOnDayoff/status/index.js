(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        var $tableContainer = $("#dayoffWorkRequestStatusTable");
        var $search = $('#search');
        var $bulkActionDiv = $('#bulkActionDiv');
        var $bulkBtns = $(".btnApproveReject");
        var $superpower = $("#super_power");
        
        var columns = [
            {field: "EMPLOYEE_CODE", title: "Code"},
            {field: "FULL_NAME", title: "Employee"},
            {title: "Requested Date",
                columns: [{
                        field: "REQUESTED_DATE_AD",
                        title: "AD",
                        template: "<span>#: (REQUESTED_DATE_AD == null) ? '-' : REQUESTED_DATE_AD #</span>"},
                    {field: "REQUESTED_DATE_BS",
                        title: "BS",
                        template: "<span>#: (REQUESTED_DATE_BS == null) ? '-' : REQUESTED_DATE_BS #</span>"}]},
            {title: "From Date",
                columns: [{
                        field: "FROM_DATE_AD",
                        title: "AD",
                        template: "<span>#: (FROM_DATE_AD == null) ? '-' : FROM_DATE_AD #</span>"},
                    {field: "FROM_DATE_BS",
                        title: "BS",
                        template: "<span>#: (FROM_DATE_BS == null) ? '-' : FROM_DATE_BS #</span>"}]},
            {title: "To Date",
                columns: [{
                        field: "TO_DATE_AD",
                        title: "AD",
                        template: "<span>#: (TO_DATE_AD == null) ? '-' : TO_DATE_AD #</span>"},
                    {field: "TO_DATE_BS",
                        title: "BS",
                        template: "<span>#: (TO_DATE_BS == null) ? '-' : TO_DATE_BS #</span>"}]},
            {field: "DURATION", title: "Duration"},
            {field: "STATUS", title: "Status"},
            {field: "ID", title: "Action", template: `
            <span>  
            <a class="btn  btn-icon-only btn-success" href="${document.viewLink}/#: ID #" style="height:17px;" title="view">
            <i class="fa fa-search-plus"></i></a>
            </span>`}];

        var map = {
            'EMPLOYEE_CODE': 'Code',
            'FULL_NAME': 'Name',
            'REQUESTED_DATE_AD': 'Request Date(AD)',
            'REQUESTED_DATE_BS': 'Request Date(BS)',
            'FROM_DATE_AD': 'From Date(AD)',
            'FROM_DATE_BS': 'From Date(BS)',
            'TO_DATE_AD': 'To Date(AD)',
            'TO_DATE_BS': 'To Date(BS)',
            'DURATION': 'Duration',
            'STATUS': 'Status',
            'REMARKS': 'Remarks',
            'RECOMMENDED_REMARKS': 'Recommended Remarks',
            'RECOMMENDED_DATE': 'Recommended Date',
            'APPROVED_REMARKS': 'Approved Remarks',
            'APPROVED_DATE': 'Approved Date'

        };
        columns=app.prependPrefColumns(columns);
        map=app.prependPrefExportMap(map);
        var pk = 'ID';
        var grid = app.initializeKendoGrid($tableContainer, columns, null, {id: pk, atLast: false, fn: function (selected) {
                if (selected) {
                    $bulkActionDiv.show();
                } else {
                    $bulkActionDiv.hide();
                }
            }}, null, 'Work On DayOff Request List.xlsx');
        app.searchTable('dayoffWorkRequestStatusTable', ['FULL_NAME', 'EMPLOYEE_CODE', 'REQUESTED_DATE_AD', 'FROM_DATE_AD', 'TO_DATE_AD', 'REQUESTED_DATE_BS', 'FROM_DATE_BS', 'TO_DATE_BS', 'DURATION', 'STATUS']);

        $search.on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['requestStatusId'] = $('#requestStatusId').val();
            q['fromDate'] = $('#fromDate').val();
            q['toDate'] = $('#toDate').val();
            App.blockUI({target: "#hris-page-content"});
            window.app.pullDataById(document.pullDayoffWorkRequestStatusListLink, q).then(function (success) {
                App.unblockUI("#hris-page-content");
                app.renderKendoGrid($tableContainer, success.data);
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });
        });
        $('#excelExport').on('click', function () {
            app.excelExport($tableContainer, map, "Work On DayOff Request List.xlsx");
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($tableContainer, map, "Work On DayOff Request List.pdf");
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
    });
})(window.jQuery, window.app);
