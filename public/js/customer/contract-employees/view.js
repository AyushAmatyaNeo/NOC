(function ($) {
    'use strict';
    $(document).ready(function () {

        $('select').select2();
        var $totalWorkingHr = $('.totalWorkingHr');
        var $employeeStartTime = $('.employeeStartTime');
        var $employeeEndTime = $('.employeeEndTime');

        app.addComboTimePicker($totalWorkingHr, $employeeStartTime, $employeeEndTime);






        console.log(document.locationList);

        app.populateSelect($('.employees'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '');
        app.populateSelect($('.location'), document.locationList, 'LOCATION_ID', 'LOCATION_NAME', 'Select An Location', '');

//        app.addDatePicker($('.contractEmpStartDate'));
//        app.addDatePicker($('.contractEmpEndDate'));



        $('#addContractEmp').on('click', function () {

            var appendValues = "<tr>"
                    + "<td><select required='required' name='employee[]' class='employees'></select></td>"
                    + "<td><select required='required' name='location[]' class='location'></select></td>"
                    + "<td><input name='employeeStartTime[]' type='text' class='employeeStartTime' data-format='h:mm a' data-template='hh : mm A'></td>"
                    + "<td><input name='employeeEndTime[]' type='text' class='employeeEndTime' data-format='h:mm a' data-template='hh : mm A'></td>"
                    + "<td>"
                    + "<div class='th-inner '>"
                    + "<label class='mt-checkbox mt-checkbox-single mt-checkbox-outline'>"
                    + "<input class='chkBoxContractEmp' type='checkbox'/>"
                    + "<span></span>"
                    + "</label>"
                    + "</div>"
                    + "</td>"
                    + "</tr>";

            $('#tblContractEmp tbody').append(appendValues);




            app.populateSelect($('#tblContractEmp tbody').find('.employees:last'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '');
            app.populateSelect($('#tblContractEmp tbody').find('.location:last'), document.locationList, 'LOCATION_ID', 'LOCATION_NAME', 'Select An Location', '');
            $('#tblContractEmp tbody').find('.employees:last').select2();
            $('#tblContractEmp tbody').find('.location:last').select2();

            app.addComboTimePicker(
                    $('#tblContractEmp tbody').find('.employeeStartTime:last'),
                    $('#tblContractEmp tbody').find('.employeeEndTime:last')
                    );


        });

        function populateEmployees(employeeData) {
            $("#tblContractEmp tbody").find("tr:gt(0)").remove();

            $.each(employeeData, function (index, value) {
                console.log(value);

                var appendValues = "<tr>"
                        + "<td><select name='employee[]' class='employees'></select></td>"
                        + "<td><input name='totalWorkingHr[]' type='text' class='totalWorkingHr' data-format='h:mm' data-template='hh : mm' ></td>"
                        + "<td><input name='employeeStartTime[]' type='text' class='employeeStartTime' data-format='h:mm a' data-template='hh : mm A'></td>"
                        + "<td><input name='employeeEndTime[]' type='text' class='employeeEndTime' data-format='h:mm a' data-template='hh : mm A'></td>"
                        + "<td>"
                        + "<div class='th-inner '>"
                        + "<label class='mt-checkbox mt-checkbox-single mt-checkbox-outline'>"
                        + "<input class='chkBoxContractEmp' type='checkbox'/>"
                        + "<span></span>"
                        + "</label>"
                        + "</div>"
                        + "</td>"
                        + "</tr>";

                $('#tblContractEmp tbody').append(appendValues);



//                $('#tblContractEmp tbody').find('.contractEmpStartDate:last').datepicker("update", value.START_DATE);
//                $('#tblContractEmp tbody').find('.contractEmpEndDate:last').datepicker("update", value.END_DATE);

                app.populateSelect($('#tblContractEmp tbody').find('.employees:last'), document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', 'Select An Employee', '');
                $('#tblContractEmp tbody').find('.employees:last').val(value.EMPLOYEE_ID);
                $('#tblContractEmp tbody').find('.employees:last').select2();



                $('#tblContractEmp tbody').find('.totalWorkingHr:last').combodate({
                    minuteStep: 1,
                    value: value.WORKING_HOUR
                });

                $('#tblContractEmp tbody').find('.employeeStartTime:last').combodate({
                    minuteStep: 1,
                    value: value.START_TIME
                });

                $('#tblContractEmp tbody').find('.employeeEndTime:last').combodate({
                    minuteStep: 1,
                    value: value.END_TIME
                });



            });

        }


        $('#delContractEmp').on('click', function () {
            $('#tblContractEmp .chkBoxContractEmp:checked').each(function () {
                $(this).parents("tr").remove()
            });
        });


//        populateEmployees();



//        console.log(document.monthDetails);
//        app.populateSelect($('#monthSelect'), document.monthDetails, 'MONTH_ID', 'MONTH_TITLE', 'Select Month', '');
//
//
//        $('#monthSelect').on('change', function () {
//            var selectedVal = $(this).val();
//            app.pullDataById(document.pullEmployeeAssignBy, {monthId: selectedVal}).then(function (response) {
////                console.log(response.data.length==o);
//                if (response.data.length == 0) {
//                    app.successMessage(['No Employee Assigned For This Month'])
//                }
//                populateEmployees(response.data);
//
//            }, function (error) {
//                console.log(error);
//            });
//        });


        var displayErrorMsg = function (object) {
            var selectedVal = object.val()
            var $parent = object.parent();
            if (selectedVal == "") {
                var $errorElement = $('</br><span class="errorMsg" aria-required="true">Field is Required</span>');
                if (!($parent.find('span.errorMsg').length > 0)) {
                    $parent.append($errorElement);
                }
                return 'error';
            } else {
                if ($parent.find('span.errorMsg').length > 0) {
                    $parent.find('span.errorMsg').remove();
                    $parent.find('br').remove();
                }
                return 'no error';
            }
        }


        $('#employeeAssign').submit(function () {

            var error = [];

            $('.totalWorkingHr').each(function (index) {
                console.log(this);
                var errorResult = displayErrorMsg($(this));
                if (errorResult == 'error') {
                    error.push('error');
                }
            });

            $('.employeeStartTime').each(function (index) {
                var errorResult = displayErrorMsg($(this));
                if (errorResult == 'error') {
                    error.push('error');
                }
            });

            $('.employeeEndTime').each(function (index) {
                var errorResult = displayErrorMsg($(this));
                if (errorResult == 'error') {
                    error.push('error');
                }
            });

            console.log(error);
            if (error.length > 0) {
                return false;
            } else {
                return true;
            }

        });












    });
})(window.jQuery);