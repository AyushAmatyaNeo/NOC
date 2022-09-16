(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
        app.searchTable('withOTReport', ['COMPANY_NAME', 'DEPARTMENT_NAME', 'FULL_NAME'], true);
        app.pdfExport(
                'withOTReport',
                {
                    'COMPANY_NAME': 'Company',
                    'DEPARTMENT_NAME': 'Department',
                    'EMPLOYEE_ID': 'ID',
					'EMPLOYEE_CODE':'Code',
                    'FULL_NAME': 'Name',
                    'TOTAL_DAYS': 'Total Days',
                    'PRESENT': 'Present',
                    'ABSENT': 'Absent',
                    'DAYOFF': 'Dayoff',
                    'HOLIDAY': 'Holiday',
                    'LEAVE': 'Leave',
                    //'PAID_LEAVE': 'Paid Leave',
                    //'UNPAID_LEAVE': 'Unpaid Leave',
//                    'OVERTIME_HOUR': 'Overtime Hour',
                    'TRAVEL': 'Travel',
                    'TRAINING': 'Training',
                    'WORK_ON_HOLIDAY': 'Work on Holiday',
                    'WORK_ON_DAYOFF': 'Work on Dayoff',
                }
        );
        var months = null;
        var $year = $('#fiscalYearId');
        var $month = $('#monthId');
        app.setFiscalMonth($year, $month, function (yearList, monthList, currentMonth) {
            months = monthList;
            $fromDate.val(currentMonth['FROM_DATE_AD']);
            $toDate.val(currentMonth['TO_DATE_AD']);
            $nepaliFromDate.val(currentMonth['FROM_DATE_BS']);
            $nepaliToDate.val(currentMonth['TO_DATE_BS']);

        });
        var monthChange = function ($this) {
            var value = $this.val();
            if (value == null) {
                return;
            }
            var selectedMonthList = months.filter(function (item) {
                return item['MONTH_ID'] === value;
            });
            if (selectedMonthList.length <= 0) {
                return;
            }
            $fromDate.val(selectedMonthList[0]['FROM_DATE']);
            $toDate.val(selectedMonthList[0]['TO_DATE']);
            $nepaliFromDate.val(nepaliDatePickerExt.fromEnglishToNepali(selectedMonthList[0]['FROM_DATE']));
            $nepaliToDate.val(nepaliDatePickerExt.fromEnglishToNepali(selectedMonthList[0]['TO_DATE']));
        };
        $month.on('change', function () {
            monthChange($(this));
        });
        var $withOTReport = $('#withOTReport');
        var $fromDate = $('#fromDate');
        var $toDate = $('#toDate');
        var $nepaliFromDate = $('#nepaliFromDate');
        var $nepaliToDate = $('#nepaliToDate');
        var $search = $('#search');
        var $confirm = $('#confirm');
        app.initializeKendoGrid($withOTReport, [
            {field: "COMPANY_NAME", title: "Company", width: 120, locked: true},
            {field: "DEPARTMENT_NAME", title: "Department", width: 120, locked: true},
            {field: "EMPLOYEE_ID", title: "Id", width: 100, locked: true},
			{field: "EMPLOYEE_CODE", title: "Code", width: 100, locked: true},
            {field: "FULL_NAME", title: "Name", width: 130, locked: true},
            {field: "TOTAL_DAYS", title: "Total Days", width: 120},
            {field: "PRESENT", title: "Present", width: 120},
            {field: "ABSENT", title: "Absent", width: 120},
            {field: "DAYOFF", title: "Weekly off", width: 120},
            {field: "HOLIDAY", title: "Holiday", width: 120},
            {field: "LEAVE", title: "Leave", width: 150},
            //{field: "PAID_LEAVE", title: "Paid Leave", width: 150},
            //{field: "UNPAID_LEAVE", title: "Unpaid Leave", width: 150},
//            {field: "OVERTIME_HOUR", title: "Overtime Hour", width: 150},
            {field: "TRAVEL", title: "Travel", width: 150},
            {field: "TRAINING", title: "Training", width: 150},
            {field: "WORK_ON_HOLIDAY", title: "Work on Holiday", width: 150},
            {field: "WORK_ON_DAYOFF", title: "Work on Weeklyoff", width: 150},
        ]);
        $search.on('click', function () {
            var data = document.searchManager.getSearchValues();
            data['fromDate'] = $fromDate.val();
            data['toDate'] = $toDate.val();
            app.serverRequest(document.withOvertimeWs, data).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($withOTReport, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });
        $confirm.on('click', function () {
            var monthValue = $month.val();
            var fiscalYearId = $year.val();
            if (monthValue === null || monthValue === '') {
                $month.focus();
                app.showMessage('No Month selected.', 'error');
                return;
            }

            var filteredMonth = months.filter(function (item) {
                return item['MONTH_ID'] == monthValue;
            });
            if (filteredMonth.length !== 1) {
                throw "Internal Data error.";
                return;
            }

            app.serverRequest(document.toEmpowerLink, {fiscalYearId: fiscalYearId, fiscalYearMonthNo: filteredMonth[0]['FISCAL_YEAR_MONTH_NO']}).then(function (response) {
                if (response.success) {
                    app.showMessage(`Attendance Data of month: ${filteredMonth[0]['MONTH_EDESC']} is successfully transfered to Empower.`, 'success');
                }
            }, function (error) {});

        });

        $('#loadData').on('click', function () {
            var monthValue = $month.val();
            var fiscalYearId = $year.val();
            if (monthValue === null || monthValue === '') {
                $month.focus();
                app.showMessage('No Month selected.', 'error');
                return;
            }

            var filteredMonth = months.filter(function (item) {
                return item['MONTH_ID'] == monthValue;
            });
            if (filteredMonth.length !== 1) {
                throw "Internal Data error.";
                return;
            }
            app.serverRequest(document.loadDataLink, {fiscalYearId: fiscalYearId, fiscalYearMonthNo: filteredMonth[0]['FISCAL_YEAR_MONTH_NO']}).then(function (response) {
                if (response.success) {
                    app.showMessage(`Attendance Data of month: ${filteredMonth[0]['MONTH_EDESC']} is successfully loaded.`, 'success');
                }
            }, function (error) {});
        });

    });
})(window.jQuery, window.app);