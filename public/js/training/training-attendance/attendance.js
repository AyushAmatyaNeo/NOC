(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var employeeList = document.employeeList;
        var dates = document.dates;
        var remoteAttendance = document.attendance;
        var attendance = {};

        var $attendanceTable = $('#attendance-table');
        var $head = $attendanceTable.find('thead');
        var $headRow = $("<tr>");
        var $th = $("<th>");
        $th.html("Employee/Date");
        $headRow.append($th);
        for (var i = 0; i < employeeList.length; i++) {
            var $th = $("<th>");
            $th.html(employeeList[i]['FULL_NAME']);
            $headRow.append($th);
        }
        $head.html($headRow);


        var $body = $attendanceTable.find('tbody');
        for (var i = 0; i < dates.length; i++) {
            var $bodyRow = $("<tr>");
            var $td = $("<td>");
            $td.append(dates[i]['DATES']);
            $bodyRow.append($td);
            attendance[dates[i]['DATES']] = {};
            for (var j = 0; j < employeeList.length; j++) {
                attendance[dates[i]['DATES']][employeeList[j]['EMPLOYEE_ID']] = false;
                if (typeof remoteAttendance[dates[i]['DATES']] !== 'undefined' &&
                        typeof remoteAttendance[dates[i]['DATES']][employeeList[j]['EMPLOYEE_ID']] !== 'undefined') {
                    attendance[dates[i]['DATES']][employeeList[j]['EMPLOYEE_ID']] = remoteAttendance[dates[i]['DATES']][employeeList[j]['EMPLOYEE_ID']];
                }
                var $td = $("<td>");
                var $checkBox = $('<input class="attendance-cb" name="attendance" type="checkbox">');
                $checkBox.attr('data-date', dates[i]['DATES']);
                $checkBox.attr('data-employeeId', employeeList[j]['EMPLOYEE_ID']);
                $checkBox.prop('checked', attendance[dates[i]['DATES']][employeeList[j]['EMPLOYEE_ID']]);
                $checkBox.prop('disabled', dates[i]['STATUS'] == 0);
                $td.append($checkBox);
                $bodyRow.append($td);
            }
            $body.append($bodyRow);
        }

        $('.attendance-cb').on('change', function () {
            var $this = $(this);
            attendance[$this.attr('data-date')][$this.attr('data-employeeId')] = $this.prop('checked');
        });

        $('#updateAttendanceBtn').on('click', function () {
            app.pullDataById(document.updateUrl, {data: attendance, trainingId: document.trainingId}).then(function (response) {
                if (response.success) {
                    app.showMessage("Training attendance updated.");
                }
            }, function (error) {

            });
        });

    });
})(window.jQuery, window.app);