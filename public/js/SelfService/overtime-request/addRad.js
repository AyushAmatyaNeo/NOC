
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        var $overtimeDate = $("#overtimeDate");
        var $employeeId = $('#employeeId');

        if (!($overtimeDate.is('[readonly]'))) {
            app.datePickerWithNepali("overtimeDate", "nepaliDate");
            app.getServerDate().then(function (response) {
                $overtimeDate.datepicker('setEndDate', app.getSystemDate(response.data.serverDate));
            }, function (error) {
                console.log("error=>getServerDate", error);
            });
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
                }
            }
        });

        function validateAttendance(employeeId, date){
            app.serverRequest(document.showAttendanceDetail, {
                employeeId: employeeId,
                date: date
            }).then(function (response) {
                const div = document.getElementById('attdDetail');
                div.innerHTML = `
                        <div class="col-sm-3">
                        <div>
                        <label><strong>In Time:</strong></label>
                        <div>`+response.data[0]['IN_TIME']+`</div></div>
                        <div>
                        <label><strong>Out Time:</strong></label>
                        <div>`+response.data[0]['OUT_TIME']+`</div></div>
                        </div>
                        <div class="col-sm-3">
                        <div >
                        <label><strong>Working Minutes:</strong></label>
                        <div>`+response.data[0]['TOTAL_HOUR']+`</div></div>
                        <div>
                        <label><strong>OT Minutes:</strong></label>
                        <div>`+response.data[0]['OT_MINUTES']+`</div></div>
                        </div>` ;

                if(response.data[0]['OT_MINUTES'] > 0){
                    var num = response.data[0]['OT_MINUTES'];
                    var hours = (num / 60);
                    var rhours = Math.floor(hours);
                    var minutes = (hours - rhours) * 60;
                    var rminutes = Math.round(minutes);
                    var overtime = rhours + ':'+ rminutes
                    console.log(overtime);
                // document.getElementById('overtimeHour').val(overtime);
                $("#overtimeHour").val(overtime);
                // document.getElementById('sumAllTotal').setAttribute('value', overtime);
                // $("#sumAllTotal").val(overtime);
                } else {
                    $("#overtimeHour").val('');
                    $("#sumAllTotal").val('');
                }
            });

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

        $('#employeeId, #overtimeDate').on('change input select', function(){
            let employeeId = $employeeId.val();
            let date = $overtimeDate.val();
            if(date != null || date != ''){
                validateAttendance(employeeId, date);
            }
        });

        app.floatingProfile.setDataFromRemote($employeeId.val());

        $employeeId.on("change", function (e) {
            app.floatingProfile.setDataFromRemote($(e.target).val());
        });
        app.setLoadingOnSubmit("overtimeRequest-form", function ($form) {
            var formData = new FormData($form[0]);
            formData.set('startTime[]','13')
            if (formData.getAll('startTime[]').length == 0) {
                app.showMessage("Minimum One Start time and End time is required.", 'error');
                return false;
            } else {
                return true;
            }
        });
    });
})(window.jQuery, window.app);


