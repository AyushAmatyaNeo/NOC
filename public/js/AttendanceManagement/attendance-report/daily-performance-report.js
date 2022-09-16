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
            {field: "EMPLOYEE_CODE", title: "Code", width: '75px'},
            {field: "FULL_NAME", title: "Employee", width: '100px'},
            {field: "DEPARTMENT_NAME", title: "Department", width: '100px'},
            {field: "ATTENDANCE_DT", title: "Date", width: '80px'},
            {title: "Shift Time",
                columns: [
                    {
                        field: "SHIFT_START_TIME",
                        title: "In",
                        template: "<span>#: (SHIFT_START_TIME == null) ? '-' : SHIFT_START_TIME # </span>",
                        width: '75px'
                    },
                    {
                        field: "SHIFT_END_TIME",
                        title: "Out",
                        template: "<span>#: (SHIFT_END_TIME == null) ? '-' : SHIFT_END_TIME # </span>",
                        width: '75px'
                    },
                    {
                        field: "TOTAL_WORKING_HR",
                        title: "Working Hour",
                        width: '100px',
                        headerAttributes: { style: "white-space: normal, overflow: visible"}
                    }
                ]
            },
            {title: "Time",
                columns: [
                    {
                        field: "IN_TIME",
                        title: "In",
                        template: "<span>#: (IN_TIME == null) ? '-' : IN_TIME # </span>",
                        width: '75px'
                    }
                ]
            },
            {title: "Break",
                columns: [
                    {
                        field: "LUNCH_OUT_TIME",
                        title: "Out",
                        template: "<span>#: (LUNCH_OUT_TIME == null) ? '-' : LUNCH_OUT_TIME # </span>",
                        width: '75px'
                    },
                    {
                        field: "LUNCH_IN_TIME",
                        title: "In",
                        template: "<span>#: (LUNCH_IN_TIME == null) ? '-' : LUNCH_IN_TIME # </span>",
                        width: '75px'
                    }
                ]
            },
            {title: "Time",
                columns: [
                    {
                        field: "OUT_TIME",
                        title: "Out",
                        template: "<span>#: (OUT_TIME == null) ? '-' : OUT_TIME # </span>",
                        width: '75px'
                    }
                ]
            },
            {field: "ACTUAL_WORKING_HR", title: "Actual Working", width: '100px'},
            {field: "OT", title: "OT Hours", width: '100px', headerAttributes: { style: "white-space: normal, overflow: visible"}},
            {field: "LATE_IN", title: "Late In", width: '100px', headerAttributes: { style: "white-space: normal, overflow: visible"}},
            //{field: "LATE_OUT", title: "Late Out", width: '100px', headerAttributes: { style: "white-space: normal, overflow: visible"}},
            //{field: "EARLY_IN", title: "Early In", width: '100px', headerAttributes: { style: "white-space: normal, overflow: visible"}},
            {field: "EARLY_OUT", title: "Early Out", width: '100px', headerAttributes: { style: "white-space: normal, overflow: visible"}},
            {field: "REMARKS", title: "Remarks", width: '100px', headerAttributes: { style: "white-space: normal, overflow: visible"}}
        ]);

        $search.on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['fromDate'] = $fromDate.val();
            q['toDate'] = $toDate.val();
            q['status'] = $status.val();
            q['presentStatus'] = $presentStatusId.val();
            app.serverRequest(document.pullAttendanceWS, q).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

        app.searchTable($table, ['EMPLOYEE_NAME', 'EMPLOYEE_CODE']);
        var exportMap = {
            'COMPANY_NAME': ' Company',
            'DEPARTMENT_NAME': ' Department',
            'EMPLOYEE_CODE': 'Code',
            'EMPLOYEE_NAME': ' Name',
            'ATTENDANCE_DT': 'Attendance Date(AD)',
            'ATTENDANCE_DT_N': 'Attendance Date(BS)',
            'IN_TIME': 'In Time',
            'OUT_TIME': 'Out Time',
            'IN_REMARKS': 'In Remarks',
            'OUT_REMARKS': 'Out Remarks',
            'TOTAL_HOUR': 'Total Hour',
//            'SYSTEM_OVERTIME': 'System OT',
//            'MANUAL_OVERTIME': 'Manual OT',
            'STATUS': 'Status',
            'SHIFT_ENAME': 'Shift Name',
            'START_TIME': 'Start Time',
            'END_TIME': 'End Time',
            'FUNCTIONAL_TYPE_EDESC': 'Functional Type'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, "Daily Performance Report.xlsx");
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, "Daily Performance Report.pdf");

        });

        var selectItems = {};
        var $bulkBtnContainer = $('#acceptRejectDiv');
    });
})(window.jQuery, window.app);
