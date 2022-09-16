(function ($, app) {
//    'use strict';
    $(document).ready(function () {
        
        console.log(document.leaveMaxEncash);
        
        $('select').select2();

        var $employee = $('#employeeId');
        var $leave = $('#leaveId');
        var $halfDay = $("#halfDay");
        var $availableDays = $('#availableDays');
        var $noOfDays = $('#noOfDays');
        var $request = $("#request");
        var $errorMsg = $("#errorMsg");
        var $startDate = $('#startDate'), $endDate = $('#endDate');
        var $leaveSubstitute = $('#leaveSubstitute');

        var dateDiff = ""; 
        
        $("#carryforward").on('input', function(){
            var ad = parseFloat($("#availableDays").val());
            var lfd = parseFloat($("#carryforward").val());
            var diff = ad - lfd;
            $("#encashment").attr('value', diff);
        });
        
        $('#request').click(function(){
                var carryforward = parseFloat($("#carryforward").val());
                var availableDays = parseFloat($("#availableDays").val());
                if (carryforward > availableDays ) {
                    app.showMessage("Carry Forward can't be more than available days. ",'info','error');
                    return false;
                } else if (carryforward > document.leaveMaxEncash) {
                    app.showMessage("Applied days can't be greater than "+document.leaveMaxEncash,'info','error');
                    return false;
                } else {
                    $errorMsg.html("");
                    return true;
                }
        })

        var substituteEmp = { 
            list: [],
            disable: function (employeeIds) { 
                if (this.list.length > 0) {
                    $.each(this.list, function (key, value) {
                        $leaveSubstitute.find('option[value="' + value + '"]').prop('disabled', false);
                    });
                    this.list = [];
                }
                $.each(employeeIds, function (key, value) {
                    $leaveSubstitute.find('option[value="' + value + '"]').prop('disabled', true);
                });
                this.list = employeeIds;
            }};

        app.floatingProfile.registerListener(function (data) {
            substituteEmp.disable([data.employeeId, data.recommenderId, data.approverId]);
        });
        app.floatingProfile.setDataFromRemote($employee.val());
        var leaveList = [];
        var availableDays = null;


        var calculateAvailableDays = function (startDateStr, endDateStr, halfDay, employeeId, leaveId) {
            if (startDateStr === null || startDateStr == '' || endDateStr === null || endDateStr == '' || employeeId === null || employeeId == '' || leaveId === null || leaveId == '') {
                return;
            }
            app.serverRequest(document.wsFetchAvailableDays, {
                startDate: startDateStr,
                endDate: endDateStr,
                employeeId: employeeId,
                halfDay: halfDay,
                leaveId: leaveId
            }).then(function (response) {
                if (!response.success) {
//                    app.showMessage(response.error, 'error');
//                    return;
                }

                var dateDiff = parseFloat(response.data['AVAILABLE_DAYS']);
                var availableDays = parseFloat($availableDays.val());

                $noOfDays.val(dateDiff);
                var balanceDiff = dateDiff / (halfDay === 'N' ? 1 : 2);

                if (balanceDiff > availableDays) {
                    $errorMsg.html("Applied days can't be more than available days.");
                    $request.prop("disabled", true);
                } else if (balanceDiff === 0) {
                    $errorMsg.html("* Applied days can't be 0 day.");
                    $request.prop("disabled", true);
                } else {
                    $errorMsg.html("");
                    $request.prop("disabled", false);
                }

            }, function (error) {
                app.showMessage(error, 'error');
            });
        };
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate', function (startDate, endDate, startDateStr, endDateStr) {
            var employeeId = $employee.val();
            var leaveId = $leave.val();
            if (typeof employeeId === 'undefined' || employeeId === null || employeeId === '' || employeeId === -1) {
                return;
            }
            leaveChange($leave[0]);
            var halfDayValue = $halfDay.is(':visible') ? $halfDay.val() : 'N';
            calculateAvailableDays(startDateStr, endDateStr, halfDayValue, employeeId, leaveId);
            checkForErrors(startDateStr, endDateStr, employeeId);
        });

        var $form = $('#leaveApply');
        var checkForErrors = function (startDateStr, endDateStr, employeeId) {
            app.pullDataById(document.wsValidateLeaveRequest, {startDate: startDateStr, endDate: endDateStr, employeeId: employeeId}).then(function (response) {
                if (response.data['ERROR'] === null) {
                    $form.prop('valid', 'true');
                    $form.prop('error-message', '');
                } else {
                    $form.prop('valid', 'false');
                    $form.prop('error-message', response.data['ERROR']);
                    app.showMessage(response.data['ERROR'], 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        }

        app.setLoadingOnSubmit("leaveApply", function ($form) {
            if ($form.prop('valid') === 'true') {
                return true;
            } else {
                app.showMessage($form.prop('error-message'), 'error');
                return true;
            }
        });

        var toggleSubstituteEmployeeReq = function ($flag) {
            if ($flag) {
                $('#substituteEmployeeCol').find('span[class="required"]').show();
                $('#leaveSubstitute').find('option[value=""]').prop('disabled', true);
                $('#leaveSubstitute').prop('required', true);
            } else {
                $('#substituteEmployeeCol').find('span[class="required"]').hide();
                $('#leaveSubstitute').find('option[value=""]').prop('disabled', false);
                $('#leaveSubstitute').prop('required', false);
            }
        };
        var toggleGracePeriod = function ($flag) {
            if ($flag) {
                $('#gracePeriodCol').show();
                $('#gracePeriod').prop('disabled', false);
            } else {
                $('#gracePeriodCol').hide();
                $('#gracePeriod').prop('disabled', true);
            }
        };
        var toggleHalfDay = function ($flag) {
            if ($flag) {
                $('#halfDayCol').show();
                $('#halfDay').prop('disabled', false);
            } else {
                $('#halfDayCol').hide();
                $('#halfDay').prop('disabled', true);
            }
        };
        var toggleSubstituteEmployee = function ($flag) {
            if ($flag) {
                $('#substituteEmployeeCol').show();
                $('#leaveSubstitute').prop('disabled', false);
            } else {
                $('#substituteEmployeeCol').hide();
                $('#leaveSubstitute').prop('disabled', true);
            }
        };

        toggleHalfDay(false);
        toggleGracePeriod(false);
        toggleSubstituteEmployee(false)
        toggleSubstituteEmployeeReq(false);



        var leaveChange = function (obj) {
            var $this = $(obj);
            
            if ($this.val() === null || $this.val() === '' || $this.val() === '-1') {
                return;
            }
            calculateAvailableDays($employee.val(), $leave.val());
            App.blockUI({target: "#hris-page-content", message: "Calculating Leave Days"});
           
            app.pullDataById(document.wsPullLeaveDetail, {
                'leaveId': $this.val(),
                'employeeId': $employee.val()
                
            }).then(function (success) {
                $("#carryforward").removeAttr("readonly");
                App.unblockUI("#hris-page-content");
                var leaveDetail = success.data;
                availableDays = (typeof leaveDetail.BALANCE=='undefined')?0:parseFloat(leaveDetail.BALANCE);
                $availableDays.val(availableDays);

                var noOfDays = parseFloat($noOfDays.val());

                if ((availableDays != "" && noOfDays != "") && noOfDays > availableDays) {
                    $("#errorMsg").html("* Applied days can't be more than available days");
                    $("#request").attr("disabled", "disabled");
                } else if ((availableDays != "" && noOfDays != "") && (noOfDays <= availableDays)) {
                    $("#errorMsg").html("");
                    $("#request").removeAttr("disabled");
                }

                toggleGracePeriod(leaveDetail.ALLOW_GRACE_LEAVE === "Y");
                toggleHalfDay(leaveDetail.ALLOW_HALFDAY === "Y");
                toggleSubstituteEmployee(leaveDetail.ENABLE_SUBSTITUTE === "Y");
                toggleSubstituteEmployeeReq(leaveDetail.IS_SUBSTITUTE_MANDATORY === 'Y');
            }, function (failure) {
                App.unblockUI("#hris-page-content");
                console.log(failure);
            });
        };

        $leave.on('change', function () {
            leaveChange(this);
        });



        var employeeChange = function (obj) {
            
            var $this = $(obj);

            app.floatingProfile.setDataFromRemote($this.val());
            App.blockUI({target: "#hris-page-content", message: "Fetching Employee Leaves"});
            app.pullDataById(document.wsPullLeaveDetailWidEmployeeId, {
                'employeeId': $this.val()
            }).then(function (success) {
                
                App.unblockUI("#hris-page-content");
                leaveList = success.data;
                app.populateSelect($leave, leaveList, 'id', 'name', 'Select a Leave', null, null, false);

                if ($startDate.val() != '' && $endDate.val() != '') {
                    var halfDayValue = $halfDay.is(':visible') ? $halfDay.val() : 'N';
                    calculateAvailableDays($startDate.val(), $endDate.val(), halfDayValue, $this.val(), $leave.val());
                }

            }, function (failure) {
                App.unblockUI("#hris-page-content");
                console.log(failure);
            });
        };

        $employee.on('change', function () {
            employeeChange(this);
        });
        $halfDay.on('change', function () {
            if ($startDate.val() !== '' && $endDate.val() !== '') {
                var halfDayValue = $halfDay.is(':visible') ? $halfDay.val() : 'N';
                calculateAvailableDays($startDate.val(), $endDate.val(), halfDayValue, $employee.val(), $leave.val());
            }
        });
 
  
 



    });
})(window.jQuery, window.app);


