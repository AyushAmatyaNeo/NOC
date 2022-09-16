(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $fromDate = $('#fromDate');
        var $toDate = $('#toDate');
        var $presentStatusId = $("#presentStatusId");
        var $status = $('#statusId');
        var $table = $('#table');
        var $search = $('#search');

        $('select').select2();
        $('#inTime').combodate({
            minuteStep: 1
        });
        $('#outTime').combodate({
            minuteStep: 1
        });

        $.each(document.searchManager.getIds(), function (key, value) {
            $('#' + value).select2();
        });
        $presentStatusId.select2();
        $status.select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
        app.getServerDate().then(function (response) {
            $fromDate.val(response.data.serverDate);
            $('#nepaliFromDate').val(nepaliDatePickerExt.fromEnglishToNepali(response.data.serverDate));
        });


        app.initializeKendoGrid($table, [
            // {field: "COMPANY_NAME", title: "Company"},
            {field: "EMPLOYEE_CODE", title: "Code", width: 50},
            {field: "EMPLOYEE_NAME", title: "Employee", width: 130, template: "<span>#: (EMPLOYEE_NAME == null) ? '-' : EMPLOYEE_NAME # </span>"},
            {field: "DEPARTMENT_NAME", title: "Department", width: 100},
            {field: "ATTENDANCE_DT", title: "Date", width: 90},
            {field: "FUNCTIONAL_TYPE", title: "Functional Type", width: 70},
            // {field: "IN_TIME", title: "Check In", template: "<span>#: (IN_TIME == null) ? '-' : IN_TIME # </span>"},
            // {field: "OUT_TIME", title: "Check Out", template: "<span>#: (OUT_TIME == null) ? '-' : OUT_TIME # </span>"},
            // {field: "IN_REMARKS", title: "In Remarks"},
            // {field: "OUT_REMARKS", title: "Out Remarks"},
//            {field: "SYSTEM_OVERTIME", title: "OT", template: "<span>#: (SYSTEM_OVERTIME == null) ? '-' : SYSTEM_OVERTIME # </span>"},
//            {field: "MANUAL_OVERTIME", title: "MOT", template: "<span>#: (MANUAL_OVERTIME == null) ? '-' : MANUAL_OVERTIME # </span>"},
            {field: "STATUS", title: "Status", width: 70 ,template: "<span>#: (STATUS == null) ? '-' : STATUS # </span>"},
            // {title: 'Shift Details', columns: [
            //         {field: "SHIFT_ENAME", title: "Name"},
            //         {field: "START_TIME", title: "From"},
            //         {field: "END_TIME", title: "To"},
            //     ]}
        ], null, null, null, 'Attendance Report.xlsx');

        var group = [
            {field: "DEPARTMENT_NAME"},
            {field: "FUNCTIONAL_TYPE"},
            ];

        $search.on("click", function () {

            var q = document.searchManager.getSearchValues();
            q['fromDate'] = $fromDate.val();
            q['toDate'] = $toDate.val();
            q['status'] = $status.val();
            q['presentStatus'] = $presentStatusId.val();
            app.serverRequest(document.pullAttendanceWS, q).then(function (response) {
                if (response.success) {
                    renderKendoGrid($table, response.data, group);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

        var renderKendoGrid = function ($table, data, group) {
            var dataSource = new kendo.data.DataSource({data: data, group: group});
            var grid = $table.data("kendoGrid");
            dataSource.read();
            grid.setDataSource(dataSource);
        }

        app.searchTable($table, ['EMPLOYEE_NAME', 'EMPLOYEE_CODE']);
        var exportMap = {
            // 'COMPANY_NAME': ' Company',
            // 'BRANCH_NAME': 'Branch',
            'DEPARTMENT_NAME': ' Department',
            'EMPLOYEE_CODE': 'Code',
            'EMPLOYEE_NAME': ' Name',
            'ATTENDANCE_DT': 'Attendance Date(AD)',
            'ATTENDANCE_DT_N': 'Attendance Date(BS)',
            // 'IN_TIME': 'In Time',
            // 'OUT_TIME': 'Out Time',
            // 'IN_REMARKS': 'In Remarks',
            // 'OUT_REMARKS': 'Out Remarks',
            // 'TOTAL_HOUR': 'Total Hour',
//            'SYSTEM_OVERTIME': 'System OT',
//            'MANUAL_OVERTIME': 'Manual OT',
            'STATUS': 'Status',
            // 'SHIFT_ENAME': 'Shift Name',
            // 'START_TIME': 'Start Time',
            // 'END_TIME': 'End Time',
            'FUNCTIONAL_TYPE_EDESC': 'Functional Type'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, "AttendanceList.xlsx");
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, "AttendanceList.pdf",'A3');
        });

        $('#pdfExportDaily').on('click', function () {
            app.exportToPDFPotrait($table, {
                'SN':'Sn',
                'EMPLOYEE_CODE': 'Code',
                'EMPLOYEE_NAME': ' Name',
                'DEPARTMENT_NAME': ' Department',
                'FUNCTIONAL_TYPE_EDESC': 'Functional Type',
                'ATTENDANCE_DT': 'Date',
                'STATUS': 'Status'
                // 'IN_TIME': 'In Time',
                // 'OUT_TIME': 'Out Time'
            }, "Modified Report.pdf");
        });

        $('#excelExportDaily').on('click',function(){
            app.excelExport($table, {
                'SN':'Sn',
                'EMPLOYEE_CODE': 'Code',
                'EMPLOYEE_NAME': ' Name',
                'DEPARTMENT_NAME': ' Department',
                'FUNCTIONAL_TYPE_EDESC': 'Functional Type',
                'ATTENDANCE_DT': 'Date',
                'STATUS': 'Status'
                // 'IN_TIME': 'In Time',
                // 'OUT_TIME': 'Out Time'
            }, "Modified Report.xlsx");
        });

        let $branch = $('#branchId');
        let $province= $('#province');
        let populateBranch ;

        $province.on("change", function () {
            populateBranch = [];
            $.each(document.braProv, function(k,v){
                if(v == $province.val()){
                    populateBranch.push(k);
                }
            });
            $branch.val(populateBranch).change();
        });

    });
})(window.jQuery, window.app);
