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
    console.log(ot);
    
        var otClass='';
    if(ot!='00:00'){
         otClass='class="attendance-color-ot-red"';
    }
    var returnvalue = status + '</br><span>Normal:' + normal + '</span></br><span '+otClass+'>Ot:' + ot + '</span>';

    if (subEmp != null) {
        returnvalue += ' (' + subEmp + ')';
    }
    return returnvalue;
}


(function ($) {
    'use strict';
    $(document).ready(function () {

        var selectedUId;
        var selectedColumnName;

        var attdAttendanceDate;
        var attdCustomerId;
        var attdContractId;
        var attdEmployeeId;
        var attdLocationId;
        var attdDutyTypeId;
        var attdDesignationId;
        var attdEmpAssignId;
        var attdEmpAssignId;
        var attdDutyNormalHour;
        var attdDutyOtHour;
        $('#normalHour').combodate({
            minuteStep: 1,
        });
        $('#otHour').combodate({
            minuteStep: 1,
        });

        var $cutomerSelect = $('#customerSelect');
        var $locationSelect = $('#locationSelect');
        var $monthSelect = $('#monthSelect');
        app.populateSelect($cutomerSelect, document.customerList, 'CUSTOMER_ID', 'CUSTOMER_ENAME', 'Select An Customer', '');
        app.populateSelect($monthSelect, document.monthList, 'MONTH_ID', 'MONTH_EDESC', 'Select An Month', '');
        function CustomSelectElement(container, options) {

            var guid = kendo.guid();
            $('<select id="' + guid + '" name="' + options.field + '"><option>PR<option>AB</option><option>HO</option></select>').appendTo(container);
        }


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
//                    template: '<button data-field="' + i + '"  class="attdBtn #: setTemplate(' + tempStatus + ') #">PR</br>hour:02:00</br>ot:02:00</button>',
                    template: '<button data-field="' + i + '"  class="attdBtn #: setTemplate(' + tempStatus + ') #">#= (' + tempStatus + ' == null) ? "-" : getStausString(' + tempStatus + ',' + tempNormalHour + ',' + tempOtHour + ',' + tempSubEmp + ') #</button>',
//                    template: '<span class="#: setTemplate(' + temp + ') #">#: (' + temp + ' == null) ? "-" : ' + temp + ' #</span>',
                    width: 100,
//                    editor: CustomSelectElement
                });
            }


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
//                console.log(response.data.monthDetails.DAYSCOUNT);
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
        $('#grid').on("click", ".attdBtn", function () {

            var row = $(this).closest("tr"),
                    grid = $('#grid').data("kendoGrid"),
                    dataItem = grid.dataItem(row);
            selectedColumnName = $(this).attr('data-field');
            var selectedColumnVal = dataItem['C' + selectedColumnName + '_STATUS'];
            selectedUId = dataItem['uid'];

            console.log(dataItem);
            console.log(selectedUId);
            console.log(selectedColumnName);
            console.log(selectedColumnVal);
            if (selectedColumnVal != null) {
                app.serverRequest(document.pullAttendanceAbsentData, {
                    monthStartDate: dataItem['FROM_DATE'],
                    column: selectedColumnName,
                    customerId: dataItem['CUSTOMER_ID'],
                    contractId: dataItem['CONTRACT_ID'],
                    employeeId: dataItem['EMPLOYEE_ID'],
                    locationId: dataItem['LOCATION_ID'],
                    dutyTypeId: dataItem['DUTY_TYPE_ID'],
                    designationId: dataItem['DESIGNATION_ID'],
                    empAssignId: dataItem['EMP_ASSIGN_ID'],
                    startTime: dataItem['START_TIME'],
                    endTime: dataItem['END_TIME']
                }).then(function (response) {
                    console.log(response);
                    var attendanceData = response.data;
                    if (attendanceData) {
                        attdAttendanceDate = attendanceData.ATTENDNACE_DATE;
                        attdCustomerId = attendanceData.CUSTOMER_ID;
                        attdContractId = attendanceData.CONTRACT_ID;
                        attdEmployeeId = attendanceData.EMPLOYEE_ID;
                        attdLocationId = attendanceData.LOCATION_ID;
                        attdDutyTypeId = attendanceData.DUTY_TYPE_ID;
                        attdDesignationId = attendanceData.DESIGNATION_ID;
                        attdEmpAssignId = attendanceData.EMP_ASSIGN_ID;
                        attdDutyNormalHour = attendanceData.DUTY_NORMAL_HOUR;
                        attdDutyOtHour = attendanceData.DUTY_OT_HOUR;
                        var attdStatus = attendanceData.STATUS;
                        var attdNormalHour = attendanceData.NORMAL_HOUR;
                        var attdOtHour = attendanceData.OT_HOUR;
                        var attdSubEmp = attendanceData.SUB_EMPLOYEE_ID;
                        var attdpostingType = attendanceData.POSTING_TYPE;
                        var attdRate = attendanceData.RATE;
                        var attdOtRate = attendanceData.OT_RATE;
                        var attdOtType = attendanceData.OT_TYPE;
                        $('#attdStatus').val(attdStatus);
                        $('#normalHour').combodate('setValue', attdNormalHour);
                        $('#otHour').combodate('setValue', attdOtHour);
                        $('#postingType').val(attdpostingType);
                        $('#rate').val(attdRate);
                        $('#otRate').val(attdOtRate);
                        $('#otType').val(attdOtType);
                        app.populateSelect($('#subEmployee'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'No Substitute', '', attdSubEmp);
                        lockSubEmployee();
                        setDefaultDuty();
                        if (attdSubEmp) {
                            $('#subRateDiv').show();
                        } else {
                            $('#subRateDiv').hide();
                        }
                        $('#subEmpModal').modal('show');

                    }

                });
            }
        });


        $('#attdStatus').on('change', function () {
            console.log($('#attdStatus').val());
            lockSubEmployee();
            setDefaultDuty();
            if ($('#subEmployee').val()) {
                $('#subRateDiv').show();
            } else {
                $('#subRateDiv').hide();
            }
        });

        function setDefaultDuty() {
            if ($('#attdStatus').val() == 'DO' || $('#attdStatus').val() == 'PH') {
                $('#normalHour').combodate('setValue', attdDutyNormalHour);
                $('#otHour').combodate('setValue', attdDutyOtHour);
                $('#normalHour').parent().find('select').prop('disabled', true);
                $('#otHour').parent().find('select').prop('disabled', true);
            } else if ($('#attdStatus').val() == 'PR') {
                $('#normalHour').combodate('setValue', attdDutyNormalHour);
                $('#otHour').combodate('setValue', attdDutyOtHour);
                $('#normalHour').parent().find('select').prop('disabled', false);
                $('#otHour').parent().find('select').prop('disabled', false);
            } else if ($('#attdStatus').val() == 'AB') {
//                $('#normalHour').combodate('setValue', '00:00');
//                $('#otHour').combodate('setValue', '00:00');
                $('#normalHour').parent().find('select').prop('disabled', false);
                $('#otHour').parent().find('select').prop('disabled', false);
            }
        }

        function lockSubEmployee() {
            if ($('#attdStatus').val() == 'AB') {
                $('#subEmployee').prop("disabled", false);
                $('#postingType').prop("disabled", false);
            } else {
                $('#subEmployee').val('');
                $('#postingType').val('SU');
                $('#subEmployee').prop("disabled", true);
                $('#postingType').prop("disabled", true);
            }
        }


        $('#subEmployee').on('change', function () {
            if ($(this).val()) {
                $('#subRateDiv').show();
            } else {
                $('#subRateDiv').hide();
            }
        });




        $('#updateAttendanceBtn').on('click', function () {

            var attdStatus = $('#attdStatus').val();
            var attdNormalHour = $('#normalHour').val();
            var attdOtHour = $('#otHour').val();
            var attdSubEmployeeId = $('#subEmployee').val();
            var attdpostingType = $('#postingType').val();
            var attdrate = $('#rate').val();
            var attdOtRate = $('#otRate').val();
            var attdOtType = $('#otType').val();

//            if (attdStatus == 'AB' && attdSubEmployeeId == '') {
//                app.showMessage('SubEmployee is Required', 'error', 'Requierd');
//                return;
//            }

            console.log('sdf');
            app.serverRequest(document.updateAttendanceData, {
                attendanceDate: attdAttendanceDate,
                customerId: attdCustomerId,
                contractId: attdContractId,
                employeeId: attdEmployeeId,
                locationId: attdLocationId,
                dutyTypeId: attdDutyTypeId,
                designationId: attdDesignationId,
                empAssignId: attdEmpAssignId,
//                startTime: attdStartTime,
//                endTime: attdEndTime,
                stauts: attdStatus,
                normalHour: attdNormalHour,
                otHour: attdOtHour,
//                inTime: attdInTime,
//                outTime: attdOutTime,
                subEmployeeId: attdSubEmployeeId,
                postingType: attdpostingType,
                rate: attdrate,
                otRate: attdOtRate,
                otType: attdOtType
            }).then(function (response) {
                console.log(response);


                var grid = $('#grid').data('kendoGrid');
                var dataItem = grid.dataSource.getByUid(selectedUId);
                console.log(dataItem);
//                console.log(dataItem['C' + selectedColumnName + '_STATUS']);

                if (response.success == true) {

                    dataItem['C' + selectedColumnName + '_STATUS'] = response.data.STATUS;
                    dataItem['C' + selectedColumnName + '_NORMAL_HOUR'] = response.data.NORMAL_HOUR;
                    dataItem['C' + selectedColumnName + '_OT_HOUR'] = response.data.OT_HOUR;
                    dataItem['C' + selectedColumnName + '_SUB_EMP_NAME'] = response.data.SUB_EMPLOYEE;
                    grid.refresh();

                    $('#subEmpModal').modal('hide');
                }


            }, function (error) {
                console.log(error);
            });

        });
        
        app.searchTable($("#grid"), ['FULL_NAME','EMPLOYEE_CODE']);
        
        
    });
}
)(window.jQuery);