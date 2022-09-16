(function ($) {
    'use strict';
    $(document).ready(function () {
        var uniqueId = 1;
//        $('select').select2();
        $("#submitBtn").hide();

        var locationList;
        var empAssignId;

        var $assignTable = $('#assignTable');
        var $table = $('#table');

        $('#addModalBtn').hide();




        var $cutomerSelect = $('#CustomerSelect');
        var $contractSelect = $('#ContractSelect');
        var customer;
        var contract;
        var contractStartDate;
        var contractEndDate;




        //  console.log(document.customerList);

        app.populateSelect($('#CustomerSelect'), document.customerList, 'CUSTOMER_ID', 'CUSTOMER_ENAME', 'Select An Customer', '');



        $cutomerSelect.on('change', function () {
            var selectedCutomer = $(this).val();
            app.pullDataById(document.pullContractList, {
                customerId: selectedCutomer
            }).then(function (response) {
                console.log(response);
                app.populateSelect($contractSelect, response.data, 'CONTRACT_ID', 'CONTRACT_NAME', 'Select An Contract', '');
            }, function (error) {
                console.log(error);
            });
        });



        function showEmpAssignList() {
            app.serverRequest(document.pullContractWiseEmployeeAssign, {
                customerId: customer,
                contractId: contract

            }).then(function (response) {
                locationList = response.data.locationList;
                var contractDetails = response.data.contractDetails;
                contractStartDate = contractDetails['START_DATE'];
                contractEndDate = contractDetails['END_DATE'];

                var empAssignData = response.data.empDetails;
                console.log(empAssignData);
//                console.log(empAssignData.DESIGNATION_TITLE);

                app.populateSelect($('#addLocation'), locationList, 'LOCATION_ID', 'LOCATION_NAME', 'Select An Location', '');
                $("#assignTable").empty();

                var headerAppendData = `
                <thead>
                    <tr>
                    <th style="width:150px;">Designation</th>
                    <th style="width:80px;">DutyType</th>
                    <th>Employee</th>
                    <th>Location</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Rate</th>
                    <th>Action</th>
                    </tr></thead>`;
                $assignTable.append(headerAppendData);
                $.each(empAssignData, function (index, value) {



                    var appendData = `<tr>
                        <td><input type='hidden' name='assignId[]' class='assignId' value=''>
                            <input class='designation' name='designation[]' type='hidden' value='` + value.DESIGNATION_ID + `'>` + value.DESIGNATION_TITLE + `</td>
                        <td><input class='dutyTypeId' name='dutyTypeId[]' type='hidden' value='` + value.DUTY_TYPE_ID + `'>` + value.DUTY_TYPE_NAME + `</td>
                        <td>
                        <select required='required' name='employees[]' class='employees'></select>
                        <span class='employeeRate'></span>
                        </td>
                        <td><select required='required' name='location[]' class='location'></select></td>
                        <td>
<input style='width: 88px;' name='employeeStartDate[]' type='text' class='employeeStartDate' >
<div><input style='width: 88px;' id="employeeStartDateNepali` + uniqueId + `" name='employeeStartDateNepali[]' type='text' class='employeeStartDateNepali' >
                        </div>
</td>
                        <td>
<input style='width: 88px;' name='employeeEndDate[]' type='text' class='employeeEndDate' >
<div><input style='width: 88px;' id="employeeEndDateNepali` + uniqueId + `" name='employeeEndDateNepali[]' type='text' class='employeeEndDateNepali' >
                        </div>
</td>
                        <td><input style='width: 88px;' name='monthlyRate[]' type='number' step="0.01" class='monthlyRate' data-rate='' ></td>
                        <td>
                        <input type='button' class='btn assignEditBtn  button-sm' value='Edit'>
                        <button class='btn assignCancelBtn button-sm'><i class="fa fa-close"></i></button>
                        <input type='button' class=' btn assignDeleteBtn button-sm' value='Delete'>
                        </td>
                    
                        </tr>`;
                    $assignTable.append(appendData);
                    uniqueId++;

                    var $selectedstartDate = $('#assignTable tbody').find('.employeeStartDate:last');
                    var $selectedstartDateNepali = $('#assignTable tbody').find('.employeeStartDateNepali:last');
                    var $selectedEndDate = $('#assignTable tbody').find('.employeeEndDate:last');
                    var $selectedEndDateNepali = $('#assignTable tbody').find('.employeeEndDateNepali:last');

                    app.startEndDatePickerWithNepali($selectedstartDateNepali, $selectedstartDate, $selectedEndDateNepali, $selectedEndDate);


                    $('#assignTable tbody').find('.assignId:last').val(value.EMP_ASSIGN_ID);
                    app.populateSelect($('#assignTable tbody').find('.employees:last'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '', value.EMPLOYEE_ID);
                    app.populateSelect($('#assignTable tbody').find('.location:last'), locationList, 'LOCATION_ID', 'LOCATION_NAME', 'Select An Location', '', value.LOCATION_ID);


                    $selectedstartDate.datepicker("update", getDateFormat(value.START_DATE_AD));
                    $selectedstartDateNepali.val(nepaliDatePickerExt.fromEnglishToNepali(getDateFormat(value.START_DATE_AD)));
                    $selectedEndDate.datepicker("update", getDateFormat(value.END_DATE_AD));
                    $selectedEndDateNepali.val(nepaliDatePickerExt.fromEnglishToNepali(getDateFormat(value.END_DATE_AD)));

                    $('#assignTable tbody').find('.monthlyRate:last').val(value.MONTHLY_RATE);


                    $('#assignTable tbody').find('.employees:last').select2();
                    $('#assignTable tbody').find('.location:last').select2();

                });

                $('.assignCancelBtn').hide();
                $('.employees').prop("disabled", true);
                $('.location').prop("disabled", true);
                $('.employeeStartDate').prop("disabled", true);
                $('.employeeEndDate').prop("disabled", true);
                $('.employeeStartDateNepali').prop("disabled", true);
                $('.employeeEndDateNepali').prop("disabled", true);
                $('.monthlyRate').prop("disabled", true);






            });


        }


        $('#viewBtn').on('click', function () {
            customer = $cutomerSelect.val();
            contract = $contractSelect.val();
            if (contract == null || contract == '') {
                app.errorMessage('Contract not Selected', 'Not Selected');
                return;
            }
            showEmpAssignList();
            $('#addModalBtn').show();
        });


        function getDateFormat(date) {
            var m_names = new Array("Jan", "Feb", "Mar",
                    "Apr", "May", "Jun", "Jul", "Aug", "Sep",
                    "Oct", "Nov", "Dec");


            var d = new Date(date);
            var curr_date = d.getDate();
            var curr_month = d.getMonth();
            var curr_year = d.getFullYear();
            return curr_date + "-" + m_names[curr_month]
                    + "-" + curr_year;

        }



        app.startEndDatePickerWithNepali('nepaliStartDate', 'startDate', 'nepaliEndDate', 'endDate');
        $('#addModalBtn').on('click', function () {
            app.populateSelect($('#addDesignation'), document.designationList, 'DESIGNATION_ID', 'DESIGNATION_TITLE', 'Select An Designation', '');
            app.populateSelect($('#addDutyType'), document.dutyTypeList, 'DUTY_TYPE_ID', 'DUTY_TYPE_NAME', 'Select An Duty Type', '');

            $('#addQauntity').val('');

            $('#addModal').modal('show');
        });


        $('#addBtn').on('click', function () {
            var designation = $('#addDesignation').val();
            var dutyType = $('#addDutyType').val();
            var quantity = $('#addQauntity').val();
            var rate = $('#addRate').val();

            var designatioName = $('#addDesignation').find("option:selected").text();
            var dutyTypeName = $('#addDutyType').find("option:selected").text();

            if (designation == null || designation == '') {
                app.showMessage('Desingation is Required', 'info', 'Required');
                return;
            }

            if (dutyType == null || dutyType == '') {
                app.showMessage('Duty type is Required', 'info', 'Required');
                return;
            }

            if (rate == null || rate == '') {
                app.showMessage('Rate is Required', 'info', 'Required');
                return;
            }
            if (quantity == null || quantity == '') {
                app.showMessage('Quantity is Required', 'info', 'Required');
                return;
            }


            app.serverRequest(document.addContractEmpAssign, {
                'customer': customer,
                'contract': contract,
                'designation': designation,
                'dutyType': dutyType,
                'rate': rate
            }).then(function (response) {

                if (response.success = true) {



                    for (var i = 0; i < quantity; i++) {
                        var appendData = `<tr>
                        <td><input type='hidden' name='assignId[]' class='assignId' value=''>
                            <input class='designation' name='designation[]' type='hidden' value='` + designation + `'>` + designatioName + `</td>
                        <td><input class='dutyTypeId' name='dutyTypeId[]' type='hidden' value='` + dutyType + `'>` + dutyTypeName + `</td>
                        <td>
                        <select required='required' name='employees[]' class='employees'></select>
                        <span class='employeeRate'></span>
                        </td>
                        <td><select required='required' name='location[]' class='location'></select></td>
                        <td>
<input style='width: 88px;' name='employeeStartDate[]' type='text' class='employeeStartDate' >
<div><input style='width: 88px;' id="employeeStartDateNepali` + uniqueId + `" name='employeeStartDateNepali[]' type='text' class='employeeStartDateNepali' >
                        </div>
</td>
                        <td>
<input style='width: 88px;' name='employeeEndDate[]' type='text' class='employeeEndDate' >
<div><input style='width: 88px;' id="employeeEndDateNepali` + uniqueId + `" name='employeeEndDateNepali[]' type='text' class='employeeEndDateNepali' >
                        </div>
</td>
                        <td><input style='width: 88px;' name='monthlyRate[]' type='number' step="0.01" class='monthlyRate' data-rate='` + rate + `' ></td>
                        <td>
                        <input type='button' class='btn assignEditBtn  button-sm' value='Edit'>
                        <button class='btn assignCancelBtn button-sm'><i class="fa fa-close"></i></button>
                        <input type='button' class=' btn assignDeleteBtn button-sm' value='Delete'>
                        </td>
                    
                        </tr>`;

                        if ($("#assignTable tbody").length > 0) {
                            $("#assignTable tbody").prepend(appendData);
                        } else {
                            $assignTable.append(appendData);
                        }


                        var $selectedstartDate = $('#assignTable tbody').find('.employeeStartDate:first');
                        var $selectedstartDateNepali = $('#assignTable tbody').find('.employeeStartDateNepali:first');
                        var $selectedEndDate = $('#assignTable tbody').find('.employeeEndDate:first');
                        var $selectedEndDateNepali = $('#assignTable tbody').find('.employeeEndDateNepali:first');
//
                        app.startEndDatePickerWithNepali($selectedstartDateNepali, $selectedstartDate, $selectedEndDateNepali, $selectedEndDate);
//
                        app.populateSelect($('#assignTable tbody').find('.employees:first'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '', );
                        app.populateSelect($('#assignTable tbody').find('.location:first'), locationList, 'LOCATION_ID', 'LOCATION_NAME', 'Select An Location', '', );

                        $('#assignTable tbody').find('.employees:first').select2();
                        $('#assignTable tbody').find('.location:first').select2();

                        $('#assignTable tbody').find('.assignCancelBtn:first').hide();
                        $('#assignTable tbody').find('.employees:first').prop("disabled", true);
                        $('#assignTable tbody').find('.location:first').prop("disabled", true);
                        $('#assignTable tbody').find('.employeeStartDate:first').prop("disabled", true);
                        $('#assignTable tbody').find('.employeeEndDate:first').prop("disabled", true);
                        $('#assignTable tbody').find('.employeeStartDateNepali:first').prop("disabled", true);
                        $('#assignTable tbody').find('.employeeEndDateNepali:first').prop("disabled", true);
                        $('#assignTable tbody').find('.monthlyRate:first').prop("disabled", true);

                        uniqueId++;
                    }
                    $('#addModal').modal('hide');
                }
            });


        });




        $('#assignTable').on('click', '.assignDeleteBtn', function () {
            console.log('delete');
            $(this).confirmation({
                onConfirm: function () {
                    console.log('confirm');
                    var selectedtr = $(this).parent().parent();
                    var selectedAssignId = $(selectedtr.find('.assignId'));
                    var selectedDesignation = $(selectedtr.find('.designation'));
                    var selectedDutyTypeId = $(selectedtr.find('.dutyTypeId'));
                    console.log(selectedAssignId.val());

                    if (selectedAssignId.val() > 0) {
                        app.serverRequest(document.deleteLink, {
                            id: selectedAssignId.val(),
                            contract: contract,
                            designation: selectedDesignation.val(),
                            dutyType: selectedDutyTypeId.val()
                        }).then(function (response) {
                            console.log(response);
                            if (response.success = true) {
                                selectedtr.remove();
                            }
                        });
                    } else {
                        selectedtr.remove();
                    }
                }
                ,
                onCancel: function () {
                    console.log('cancel');
                }, });
//
//            



        });






        $('#assignTable').on('click', '.assignEditBtn', function () {
            var btnValue = $(this).val();

            console.log(btnValue);

            var selectedtr = $(this).parent().parent();
            var selectedEmployee = $(selectedtr.find('.employees'));
            var selectedLocation = $(selectedtr.find('.location'));
            var selectedStartDate = $(selectedtr.find('.employeeStartDate'));
            var selectedStartDateNepali = $(selectedtr.find('.employeeStartDateNepali'));
            var selectedEndDate = $(selectedtr.find('.employeeEndDate'));
            var selectedEndDateNepali = $(selectedtr.find('.employeeEndDateNepali'));
            var selectedAssignId = $(selectedtr.find('.assignId'));
            var selectedDutyTypeId = $(selectedtr.find('.dutyTypeId'));
            var selectedDesignation = $(selectedtr.find('.designation'));
            var selectedComboDate = $(selectedtr.find('.combodate'));
            var selectedDeleteBtn = $(selectedtr.find('.assignDeleteBtn'));
            var selectedCancelBtn = $(selectedtr.find('.assignCancelBtn'));
            var selectedMonthlyRate = $(selectedtr.find('.monthlyRate'));
            var $selectedEmployeeRate = $(selectedtr.find('.employeeRate'));

            if (btnValue == 'Edit') {
                $(this).val('Update')
                selectedDeleteBtn.hide();
                selectedCancelBtn.show();
                selectedtr.css("background-color", "rgb(222, 225, 228)");
                selectedEmployee.prop("disabled", false);
                selectedLocation.prop("disabled", false);
                selectedStartDate.prop("disabled", false);
                selectedStartDateNepali.prop("disabled", false);
                selectedEndDate.prop("disabled", false);
                selectedEndDateNepali.prop("disabled", false);
                selectedComboDate.css("opacity", "1");
                selectedMonthlyRate.prop("disabled", false);

                if (selectedStartDate.val() == '' && selectedEndDate.val() == '') {

                    selectedStartDate.datepicker("update", getDateFormat(contractStartDate));
                    selectedStartDateNepali.val(nepaliDatePickerExt.fromEnglishToNepali(getDateFormat(contractStartDate)));
                    selectedEndDate.datepicker("update", getDateFormat(contractEndDate));
                    selectedEndDateNepali.val(nepaliDatePickerExt.fromEnglishToNepali(getDateFormat(contractEndDate)));

//                    selectedStartDate.datepicker("update", new Date(contractStartDate));
//                    selectedEndDate.datepicker("update", new Date(contractEndDate));
                    selectedMonthlyRate.val(selectedMonthlyRate.attr("data-rate"));
                }

            } else if (btnValue == 'Update') {

                var employeeValue = selectedEmployee.val();
                var locationValue = selectedLocation.val();
                var startDateValue = selectedStartDate.val();
                var startEndValue = selectedEndDate.val();
                var assignIdValue = selectedAssignId.val();
                var dutyTypeIdValue = selectedDutyTypeId.val();
                var designationValue = selectedDesignation.val();
                var monthlyRateValue = selectedMonthlyRate.val();

                var requiredList = [
                    selectedEmployee,
                    selectedLocation,
                    selectedStartDate,
                    selectedEndDate
                ];
                var error = [];
                $.each(requiredList, function (index, value) {
                    var elementVal = value.val();
                    value.next(".red").remove();
                    if (elementVal.length == 0) {
                        error.push('error');
                        value.after('<div class="red" style="color:red;">Required</div>');
                    }
                });
                if (error.length == 0) {
                    app.serverRequest(document.updateLink, {
                        id: assignIdValue,
                        contractId: contract,
                        customerId: customer,
                        locationId: locationValue,
                        employeeId: employeeValue,
                        designationId: designationValue,
                        dutyTypeId: dutyTypeIdValue,
                        startDate: startDateValue,
                        endDate: startEndValue,
                        monthlyRate: monthlyRateValue

                    }).then(function (response) {
                        console.log(response);

                        if (response.success == true) {
                            var responseData = response.data;
                            var operation = responseData.operation;

                            if (operation == 'add') {
                                selectedAssignId.val(responseData.id);
                            }
                            $selectedEmployeeRate.html('');
                        }
                    });

                    selectedDeleteBtn.show();
                    selectedCancelBtn.hide();
                    $(this).val('Edit');
                    selectedtr.css("background-color", "");
                    selectedEmployee.prop("disabled", true);
                    selectedLocation.prop("disabled", true);
                    selectedStartDate.prop("disabled", true);
                    selectedStartDateNepali.prop("disabled", true);
                    selectedEndDate.prop("disabled", true);
                    selectedEndDateNepali.prop("disabled", true);
                    selectedComboDate.css("opacity", "0.6");
                    selectedMonthlyRate.prop("disabled", true);
                }
            }
        });


        $('#addDesignation').on('change', function () {
            getContractDetailRate();
        });

        $('#addDutyType').on('change', function () {
            getContractDetailRate();
            getEmployeeRate();
        });

        $('#addEmployee').on('change', function () {
            getEmployeeRate();
        });

        function getEmployeeRate() {
            var employeeId = $('#addEmployee').val();
            var dutyTypeId = $('#addDutyType').val();

            if (dutyTypeId && employeeId) {
                app.serverRequest(document.pullEmployeeRate, {
                    dutyTypeId: dutyTypeId,
                    employeeId: employeeId

                }).then(function (response) {
                    console.log(response.data);
                    $('#addEmployeeRate').html(response.data);
                });

            } else {
                $('#addEmployeeRate').html('');
            }
        }


        function getContractDetailRate() {
            var designation = $('#addDesignation').val();
            var dutyType = $('#addDutyType').val();


            if (designation != '' && dutyType != '') {
                app.serverRequest(document.pullCdContractDesignationDutyType, {
                    contract: contract,
                    designation: designation,
                    dutyType: dutyType
                }).then(function (response) {
                    if (response.success == true) {
                        console.log(response);
                        if (response.data != false) {
                            $('#addRate').val(response.data.RATE);
                            $('#addRate').prop('readonly', true);

                        } else {
                            $('#addRate').val('');
                            $('#addRate').prop('readonly', false);
                        }

                    }
                });

            } else {
                console.log('no vals');
            }


        }


        $('#assignTable').on('click', '.assignCancelBtn', function () {

            var selectedbtn = $(this);
            var selectedtr = $(this).parent().parent();
            var selectedEmployee = $(selectedtr.find('.employees'));
            var selectedLocation = $(selectedtr.find('.location'));
            var selectedStartDate = $(selectedtr.find('.employeeStartDate'));
            var selectedStartDateNepali = $(selectedtr.find('.employeeStartDateNepali'));
            var selectedEndDate = $(selectedtr.find('.employeeEndDate'));
            var selectedEndDateNepali = $(selectedtr.find('.employeeEndDateNepali'));
            var selectedAssignId = $(selectedtr.find('.assignId'));
            var selectedDeleteBtn = $(selectedtr.find('.assignDeleteBtn'));
            var selectedEditBtn = $(selectedtr.find('.assignEditBtn'));
            var selectedMonthlyRate = $(selectedtr.find('.monthlyRate'));

            var assignIdValue = selectedAssignId.val();
            if (assignIdValue > 0) {
                app.serverRequest(document.pullEmployeeAssignDataById, {
                    id: assignIdValue
                }).then(function (response) {
                    var responseData = response.data;
                    console.log(response);
                    if (responseData) {
                        selectedEmployee.val(responseData.EMPLOYEE_ID).change();
                        selectedLocation.val(responseData.LOCATION_ID).change();


                        selectedStartDate.datepicker("update", getDateFormat(responseData.START_DATE));
                        selectedStartDateNepali.val(nepaliDatePickerExt.fromEnglishToNepali(getDateFormat(responseData.START_DATE)));
                        selectedEndDate.datepicker("update", getDateFormat(responseData.END_DATE));
                        selectedEndDateNepali.val(nepaliDatePickerExt.fromEnglishToNepali(getDateFormat(responseData.END_DATE)));

                        selectedMonthlyRate.val(responseData.MONTHLY_RATE);
                    }
                });
            } else {
                selectedEmployee.val('').change();
                selectedLocation.val('').change();

                selectedStartDate.datepicker("update", '');
                selectedStartDateNepali.datepicker("update", '');
                selectedEndDate.datepicker("update", '');
                selectedEndDateNepali.datepicker("update", '');

                selectedMonthlyRate.val('');
            }

            selectedbtn.hide();
            selectedDeleteBtn.show();
            selectedEditBtn.val('Edit');
            selectedtr.css("background-color", "");
            selectedEmployee.prop("disabled", true);
            selectedLocation.prop("disabled", true);
            selectedStartDate.prop("disabled", true);
            selectedStartDateNepali.prop("disabled", true);
            selectedEndDate.prop("disabled", true);
            selectedEndDateNepali.prop("disabled", true);
            selectedMonthlyRate.prop("disabled", true);
        });



        $('#assignTable').on('change', '.employees', function () {
            var selectedEmployeeValue = $(this).val()
            var selectedtr = $(this).parent().parent();
            var $selectedDutyTypeId = $(selectedtr.find('.dutyTypeId'));
            var $selectedEmployeeRate = $(selectedtr.find('.employeeRate'));
            var selectedDutyTypeIdValue = $selectedDutyTypeId.val();
            var $selectedBtn = $(selectedtr.find('.assignEditBtn'));
            var btnValue = $selectedBtn.val()

            if (selectedEmployeeValue && selectedDutyTypeIdValue && btnValue == 'Update') {
                app.serverRequest(document.pullEmployeeRate, {
                    dutyTypeId: selectedDutyTypeIdValue,
                    employeeId: selectedEmployeeValue

                }).then(function (response) {
                    console.log(response.data);
                    $selectedEmployeeRate.html(response.data);
                });

            } else {
                $selectedEmployeeRate.html('');
            }


        });






    });
})(window.jQuery);