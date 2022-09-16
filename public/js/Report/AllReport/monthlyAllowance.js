
//function setTemplate(temp) {
//    var returnvalue = '';
//    if (temp == 'PR') {
//        returnvalue = 'blue';
//    } 
//    else if (temp == 'AB') {
//        returnvalue = 'red';
//    } else if (temp == 'LV') {
//        returnvalue = 'green';
//    } else if (temp == 'DO') {
//        returnvalue = 'yellow';
//    } else if (temp == 'HD') {
//        returnvalue = 'purple';
//    } else if (temp == 'WD') {
//        returnvalue = 'purple-soft';
//    } else if (temp == 'WH') {
//        returnvalue = 'yellow-soft';
//    }
//    return returnvalue;
//}


(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
        var $table = $("#report");
        var $fromDate = $('#fromDate');
        var $toDate = $('#toDate');
        var $nepaliFromDate = $('#nepaliFromDate');
        var $nepaliToDate = $('#nepaliToDate');
        var $search = $('#search');


        var exportVals={
//            'FULL_NAME': 'Employee Name',
            'FULL_NAME': ' Name',
            'EMPLOYEE_CODE': 'Code',
            'COMPANY_NAME': 'Company',
            'BRANCH_NAME': 'Branch',
            'DEPARTMENT_NAME': 'Department',
            'DESIGNATION_TITLE': 'Designation',
            'POSITION_NAME': 'Position',
            'SYSTEM_OVERTIME': 'System Overtime',
            'MANUAL_OVERTIME': 'Overtime',
            'HOLIDAY_COUNT': 'Holiday Count',
            'FOOD_ALLOWANCE': 'Food Allowance Count',
            'SHIFT_ALLOWANCE': 'Shift Allowance Count',
            'NIGHT_SHIFT_ALLOWANCE': 'Night shift Allowance Count',
        };


        var months = null;
        var $year = $('#fiscalYearId');
        var $month = $('#monthId');
//        app.setFiscalMonth($year, $month, function (yearList, monthList, currentMonth) {
//            months = monthList;
//        });
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


        
        app.searchTable('report', [
            'FULL_NAME'
                    , 'EMPLOYEE_CODE'
                    , 'COMPANY_NAME'
                    , 'BRANCH_NAME'
                    , 'DEPARTMENT_NAME'
                    , 'DESIGNATION_TITLE'
                    , 'POSITION_NAME']);

        app.initializeKendoGrid($table, [
            {field: 'FULL_NAME', title: "Name", width: 120, locked: true},
            {field: 'EMPLOYEE_CODE', title: "Code", width: 90, locked: true},
//            {field: 'COMPANY_NAME',title: "Company", width: 100, locked: true},
//            {field: 'BRANCH_NAME',title: "Branch", width: 100, locked: true},
            {field: 'DEPARTMENT_NAME', title: "Department", width: 120, locked: true},
//            {field: 'DESIGNATION_TITLE',title: "Designation", width: 100, locked: true},
            {field: 'POSITION_NAME', title: "position", width: 120, locked: true},
            {field: 'SYSTEM_OVERTIME', title: "System Overtime", width: 100},
            {field: 'MANUAL_OVERTIME', title: "Overtime", width: 100},
            {field: 'HOLIDAY_COUNT', title: "Holiday Count", width: 100},
            {field: 'FOOD_ALLOWANCE', title: "Food Allowance Count", width: 100},
            {field: 'SHIFT_ALLOWANCE', title: "Shift Allowance Count", width: 100},
            {field: 'NIGHT_SHIFT_ALLOWANCE', title: "Night Shift Allowance Count", width: 100},
        ],null,null, 'Employee_Wise_Allowance_Report.xlsx');


        $search.on('click', function () {
            var data = document.searchManager.getSearchValues();
            data['monthCodeId'] = $month.val();
            data['fromDate'] = $fromDate.val();
            data['toDate'] = $toDate.val();
            
            if(data['fromDate']==''){
            app.showMessage('From date is mandatory', 'error');
            return;
            }
            if(data['toDate']==''){
            app.showMessage('To date is mandatory', 'error');
            return;
            }
            
            
            app.serverRequest('', data).then(function (response) {

                if (response.success) {
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }

            }, function (error) {

            });
        });





        $('#excelExport').on('click', function () {
            app.excelExport($table, exportVals, 'Employee Wise Allowance Report');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportVals, 'Employee Wise Allowance Report');
        });


//        $("#reset").on("click", function () {
//            $(".form-control").val("");
//        });


    });
})(window.jQuery, window.app);