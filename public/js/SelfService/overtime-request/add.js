/**
 * Created by punam on 9/28/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var $overtimeDate = $("#overtimeDate");
        var $employeeId = $('#employeeId');

        if (!($overtimeDate.is('[readonly]'))) {
            app.datePickerWithNepali("overtimeDate", "nepaliDate");
            // app.getServerDate().then(function (response) {
            //     $overtimeDate.datepicker('setEndDate', app.getSystemDate(response.data.serverDate));
            // }, function (error) {
            //     console.log("error=>getServerDate", error);
            // });
        } else {
            app.datePickerWithNepali("overtimeDate", "nepaliDate");
        }

        var $nepaliDate = $("#nepaliDate");
        var $englishDate = $("#overtimeDate");
        $("#nepaliDate").nepaliDatePicker({
            onChange: function(){
                var temp = nepaliDatePickerExt.fromNepaliToEnglish($nepaliDate.val());
                var englishStartDate = $englishDate.datepicker('getStartDate');
                var englishEndDate = $englishDate.datepicker('getEndDate');
                $englishDate.val(temp);
                let employeeId = $employeeId.val();
                let date = $overtimeDate.val();
                if(date != null || date != ''){
                    validateAttendance(employeeId, date);
                    validateOvertimeDate(employeeId, date);
                }
            }
        });

        function validateAttendance(employeeId, date){
            app.serverRequest(document.validateAttendanceLink, {
                employeeId: employeeId,
                date: date
            }).then(function(response){
                if(response.validation === 'F' || response.validation === null){
                    app.showMessage("Overtime not more than 2 hours", "error");
                    $("#submit").attr('disabled', 'disabled'); 
                }
                else{ $("#submit").removeAttr('disabled'); }
            });
        }

        function validateOvertimeDate(employeeId, date){
            app.serverRequest(document.validateOvertimeDateLink, {
                employeeId: employeeId,
                date: date
            }).then(function(response){
                if(response.validation === 'F' || response.validation === null){
                    app.showMessage("Overtime being requested is prior to today date", "error");
                    //$("#submit").attr('disabled', 'disabled'); 
                }
                else{ $("#submit").removeAttr('disabled'); }
            });
        }

        function validateEmployeeShift(employeeId, date, start_time, end_time){
            app.serverRequest(document.validateEmployeeShiftLink, {
                employeeId: employeeId,
                date: date,
                startTime: start_time,
                endTime: end_time
            }).then(function(response){
                if(response.validation === 'F' || response.validation === null){
                    app.showMessage("Employee not found, Please check the shift", "error");
                    //$("#submit").attr('disabled', 'disabled'); 
                } else if (response.validation === 'N'){
                    app.showMessage("Overtime should be before shift time starts or after the shift time ends", "error");
                } else if (response.validation === 'G'){
                    app.showMessage("End time should be greater than start time", "error");
                }
                else{ $("#submit").removeAttr('disabled'); }
            });
        }

        $('#submit, #plushIcon').on('click', function(){
            let employeeId = $employeeId.val();
            let date = $overtimeDate.val();

            let total_cnt_time = 0;
            let i=1;
            $('input[name="startTime[]"]').each(function() {
                total_cnt_time = i;
                i++;
            });
            if(total_cnt_time >= 1){
                let start_time = $('input[name="startTime[]"]').val();
                let end_time = $('input[name="endTime[]"]').val();
                validateEmployeeShift(employeeId, date, start_time, end_time);   
            }

            
        });

        $('#employeeId, #overtimeDate').on('change input select', function(){
            let employeeId = $employeeId.val();
            let date = $overtimeDate.val();
            if(date != null || date != ''){
                validateAttendance(employeeId, date);
                validateOvertimeDate(employeeId, date);
            }
        });

        app.floatingProfile.setDataFromRemote($employeeId.val());

        $employeeId.on("change", function (e) {
            app.floatingProfile.setDataFromRemote($(e.target).val());
        });
        app.setLoadingOnSubmit("overtimeRequest-form", function ($form) {
            var formData = new FormData($form[0]);
            if (formData.getAll('startTime[]').length == 0) {
                app.showMessage("Minimum One Start time and End time is required.", 'error');
                return false;
            } else {
                return true;
            }
        });
    });
})(window.jQuery, window.app);


