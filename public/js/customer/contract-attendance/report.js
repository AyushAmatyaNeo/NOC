function setTemplate(temp) {
    var returnvalue = '';
    if (temp == 'Absent' || temp == 'Leave') {
        returnvalue = 'attendance-color-red';
    } else if (temp == 'Present') {
        returnvalue = 'attendance-color-green';
    } else if (temp == 'DayOff' || temp == 'PublicHoliday') {
        returnvalue = 'attendance-color-yellow';
    }

    return returnvalue;
}

function getStausString(status, normal, ot, subEmp) {
    var returnvalue = status + '</br>Normal:' + normal + '</br>Ot:' + ot;

//    if (subEmp != null) {
//        returnvalue += ' (' + subEmp + ')';
//    }
    return returnvalue;
}


(function ($) {
    'use strict';
    $(document).ready(function () {


        var $cutomerSelect = $('#customerSelect');
        var $locationSelect = $('#locationSelect');
        var $monthSelect = $('#monthSelect');
        app.populateSelect($cutomerSelect, document.customerList, 'CUSTOMER_ID', 'CUSTOMER_ENAME', 'Select An Customer', '');
        app.populateSelect($monthSelect, document.monthList, 'MONTH_ID', 'MONTH_EDESC', 'Select An Month', '');


        $cutomerSelect.on('change', function () {
            var customerId = $(this).val();

            console.log('sdf');
            app.serverRequest(document.pullCustomerLocation, {
                customerId: customerId
            }).then(function (response) {
                console.log(response);
                if (response.success == true) {
                    app.populateSelect($locationSelect, response.data, 'LOCATION_ID', 'LOCATION_NAME', 'All Location', '');
                }
            });

        });



        function GenerateColsForKendo(dayCount) {
            var cols = [];
            cols.push({
                field: 'EMPLOYEE_CODE',
                title: "Emp Code",
                locked: true,
                template: '<span>#:EMPLOYEE_CODE#</span>',
                width: 100
            });
            cols.push({
                field: 'FULL_NAME',
                title: "Name",
                locked: true,
                template: '<span>#:FULL_NAME#</span>',
                width: 100
            });
            cols.push({
                field: 'DESIGNATION_TITLE',
                title: "Designation",
                locked: true,
                template: '<span>#:DESIGNATION_TITLE#</span>',
                width: 100
            });
            cols.push({
                field: 'DUTY_TYPE_NAME',
                title: "Duty Type",
                locked: true,
                template: '<span>#:DUTY_TYPE_NAME#</span>',
                width: 100
            });
            cols.push({
                field: 'LOCATION_NAME',
                title: "Location",
                locked: true,
                template: '<span>#:LOCATION_NAME#</span>',
                width: 100
            });
            for (var i = 1; i <= dayCount; i++) {
                var temp = 'C' + i;
                var tempStatus = 'C' + i + '_STATUS';
                var tempNormalHour = 'C' + i + '_NORMAL_HOUR';
                var tempOtHour = 'C' + i + '_OT_HOUR';
                var tempSubEmp = 'C' + i + '_SUB_EMP_NAME';
                cols.push({
                    field: temp,
                    title: "" + i,
                    template: '<button data-field="' + i + '"  class="attdBtn #: setTemplate(' + tempStatus + ') #">#= (' + tempStatus + ' == null) ? "-" : getStausString(' + tempStatus + ',' + tempNormalHour + ',' + tempOtHour + ',' + tempSubEmp + ') #</button>',
                    width: 100,
                });
            }
            cols.push({
                field: 'PAID_HOLIDAY',
                title: "Present",
                template: '<span>#='+dayCount+'-ABSENT_DAYS-LEAVE#</span>',
                width: 100
            });
            cols.push({
                field: 'ABSENT_DAYS',
                title: "Absent",
                template: '<span>#:ABSENT_DAYS#</span>',
                width: 100
            });
            cols.push({
                field: 'LEAVE',
                title: "Leave",
                template: '<span>#:LEAVE#</span>',
                width: 100
            });
            cols.push({
                field: 'DAY_OFF',
                title: "DayOff",
                template: '<span>#:DAY_OFF#</span>',
                width: 100
            });
            cols.push({
                field: 'PAID_HOLIDAY',
                title: "Public Holiday",
                template: '<span>#:PAID_HOLIDAY#</span>',
                width: 100
            });
            return cols;
        }



        $('#viewBtn').on('click', function () {
            var selectedCustomerVal = $cutomerSelect.val();
            var selectedLocationVal = $locationSelect.val();
            var selectedMonthVal = $monthSelect.val();
            if (selectedCustomerVal == '' || selectedMonthVal == '') {
                app.errorMessage('Customer or Month is not selected ', 'error');
                return;
            }

            $("#grid").empty();
            
            app.serverRequest(document.pullCustomerMonthlyAttendanceUrl, {
                customerId: selectedCustomerVal,
                locationId: selectedLocationVal,
                monthId: selectedMonthVal

            }
            ).then(function (response) {
                console.log(response.data);
                var cols = [];
                cols = GenerateColsForKendo(response.data.monthDetails.DAYSCOUNT);
                var dataSource = new kendo.data.DataSource({
                    data: response.data.attendanceResult,
                    pageSize: 500,
                });
                $("#grid").kendoGrid({
                    dataSource: dataSource,
                    height: 450,
                    scrollable: true,
                    columns: cols,
                    editable: "inline"
                });
            });
        });




  app.searchTable($("#grid"), ['FULL_NAME','EMPLOYEE_CODE']);



    });
}
)(window.jQuery);