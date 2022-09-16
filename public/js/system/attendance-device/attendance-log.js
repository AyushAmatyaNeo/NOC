(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var $deviceIP = $('#deviceIP');
        $deviceIP.select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', function ($from, $to, from, to) {
            deviceIPChange($deviceIP.val(), from, to);
        }, true);
        var columns = [
            {field: "IP_ADDRESS", title: "IP Address", width: 100},
            {field: "THUMB_ID", title: "Thumb IP", width: 100},
            {field: "ATTENDANCE_DATE", title: "Date", width: 100},
            {field: "ATTENDANCE_TIME", title: "Time", width: 100},
            {field: "EMPLOYEE_ID", title: "Employee Id", width: 100},
            {field: "EMPLOYEE_NAME", title: "Name", width: 150},
        ];


        app.initializeKendoGrid($table, columns);

        var deviceIPChange = function (ipList, fromDate, toDate) {
            if (ipList === undefined || ipList === null || ipList === "") {
                return;
            }
            app.serverRequest("", {ipList: ipList, fromDate: fromDate, toDate: toDate}).then(function (response) {
                app.renderKendoGrid($table, response.data);
            }, function (error) {
                console.log(error);
            });
        };
        $deviceIP.on('change', function () {
            var $this = $(this);
            deviceIPChange($this.val(), $('#fromDate').val(), $('#toDate').val());
        });


        var map = {
            'IP_ADDRESS': 'IP Address',
            'THUMB_ID': 'Thumb IP',
            'ATTENDANCE_DATE': 'Attendance Date',
            'ATTENDANCE_TIME': 'Attendance Time',
            'EMPLOYEE_ID': 'Employee Id',
            'EMPLOYEE_NAME': 'Employee Name'
        };

        app.searchTable($table, ['EMPLOYEE_NAME']);


        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Attendance.xlsx');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Attendance.pdf');
        });
        var status = null;
        var $deviceStatusColor = $('#device-status-color');
        var $deviceStatusText = $('#device-status-text');
        var checkExeStatus = function ($action) {
            app.serverRequest(document.checkExeStatusLink, {'action': $action}).then(function (response) {
                if (response.success) {
                    status = response.data.isRunning;
                    if (response.data.isRunning) {
                        $deviceStatusColor.removeClass('red').addClass('green');
                        $deviceStatusText.html('Online');
                    } else {
                        $deviceStatusColor.removeClass('green').addClass('red');
                        $deviceStatusText.html('Offline');
                    }
                }
            });
        };
        $deviceStatusColor.on('click', function (e) {
            e.preventDefault();
            if (status != null && !status) {
                checkExeStatus('start-service');
            }

        });

        checkExeStatus('check-exe-status');


    });
})(window.jQuery, window.app);
