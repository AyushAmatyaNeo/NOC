(function ($, app) {
    'use strict';
    $(document).ready(function () {

        if (!jQuery().bootstrapWizard) {
            return;
        }

        var $wizard = $('#wizard');
        var $setupForm = $('#setup-form');
        var $compulsoryOtDesc = $('#compulsoryOtDesc');
        var $startDate = $('#startDate');
        var $endDate = $('#endDate');
        var $earlyOvertimeHour = $('#earlyOvertimeHour');
        var $lateOvertimeHour = $('#lateOvertimeHour');

        app.addComboTimePicker($earlyOvertimeHour, $lateOvertimeHour);
        app.startEndDatePickerWithNepali('nepaliStartDate', 'startDate', 'nepaliEndDate', 'endDate', null, true);

        var compulsorySetup = {compulsoryOvertimeId: null, compulsoryOtDesc: null, startDate: null, endDate: null, earlyOvertimeHour: null, lateOvertimeHour: null};

        var assignList = [];

        var $searchBtn = $('#searchBtn');
        var $table = $('#employeeTable');
        var $checkAll = $('#checkAll');

        var $compulsoryOtDescLabel = $('#compulsoryOtDescLabel');
        var $startDateLabel = $('#startDateLabel');
        var $endDateLabel = $('#endDateLabel');
        var $earlyOvertimeHourLabel = $('#earlyOvertimeHourLabel');
        var $lateOvertimeHourLabel = $('#lateOvertimeHourLabel');

        var $assignedEmployeeList = $('#assignedEmployeeList');




        var handleTitle = function (tab, navigation, index) {
            var total = navigation.find('li').length;
            var current = index + 1;
            // set wizard title
            $('.step-title', $wizard).text('Step ' + (index + 1) + ' of ' + total);
            // set done steps
            jQuery('li', $wizard).removeClass("done");
            var li_list = navigation.find('li');
            for (var i = 0; i < index; i++) {
                jQuery(li_list[i]).addClass("done");
            }

            if (current === 1) {
                $wizard.find('.button-previous').hide();
            } else {
                $wizard.find('.button-previous').show();
            }

            if (current >= total) {
                $wizard.find('.button-next').hide();
                $wizard.find('.button-submit').show();
            } else {
                $wizard.find('.button-next').show();
                $wizard.find('.button-submit').hide();
            }
        }


        // default form wizard
        $wizard.bootstrapWizard({
            'nextSelector': '.button-next',
            'previousSelector': '.button-previous',
            onTabClick: function (tab, navigation, index, clickedIndex) {
                return false;
            },
            onNext: function (tab, navigation, index) {
                var nextFlag = true;
                switch (index) {
                    case 1:
                        $setupForm.on('submit', function () {
                            var $this = $(this);
                            if (!$this.valid()) {
                                nextFlag = false;
                            }
                            if ($earlyOvertimeHour.val() === "" && $lateOvertimeHour.val() === "") {
                                $earlyOvertimeHour.parent().find('.combodate').after('<div><label id="startTime-error" class="error" for="startTime">This field is required.</label></div>');
                                $lateOvertimeHour.parent().find('.combodate').after('<div><label id="startTime-error" class="error" for="startTime">This field is required.</label></div>');
                                nextFlag = false;
                            }

                            compulsorySetup['compulsoryOtDesc'] = $compulsoryOtDesc.val();
                            compulsorySetup['startDate'] = $startDate.val();
                            compulsorySetup['endDate'] = $endDate.val();
                            compulsorySetup['earlyOvertimeHour'] = $earlyOvertimeHour.val();
                            compulsorySetup['lateOvertimeHour'] = $lateOvertimeHour.val();


                            return false;
                        });
                        $setupForm.submit();
                        break;
                    case 2:
                        if (assignList.length === 0) {
                            app.showMessage('Atleast one person should be assigned to continue.', 'info');
                            nextFlag = false;
                        }
                        break;
                    case 3:


                        break;
                }
                if (!nextFlag) {
                    return false;
                }

                handleTitle(tab, navigation, index);

            },
            onPrevious: function (tab, navigation, index) {
                handleTitle(tab, navigation, index);
            },
            onTabShow: function (tab, navigation, index) {
                var total = navigation.find('li').length;
                var current = index + 1;
                var $percent = (current / total) * 100;
                $wizard.find('.progress-bar').css({
                    width: $percent + '%'
                });
                if (index == 1) {
                    $('select').select2();
                }
                if (index === 2) {
                    $compulsoryOtDescLabel.html(compulsorySetup.compulsoryOtDesc);
                    $startDateLabel.html(compulsorySetup.startDate);
                    $endDateLabel.html(compulsorySetup.endDate);
                    $earlyOvertimeHourLabel.html(compulsorySetup.earlyOvertimeHour);
                    $lateOvertimeHourLabel.html(compulsorySetup.lateOvertimeHour);
                    $assignedEmployeeList.html('');
                    $.each(assignList, function (key, item) {
                        var employee = document.searchManager.getEmployeeById(item);
                        $assignedEmployeeList.append('<div class="col-sm-2"><div class="alert alert-info alert-dismissable">' +
                                '<button data-id="' + item + '" type="button" class="close remove-employee" data-dismiss="alert" aria-hidden="true"></button>' +
                                employee['FULL_NAME'] + '</div></div>');
                    });
                }
            }
        });

        $wizard.find('.button-previous').hide();
        $('#wizard .button-submit').click(function () {
            compulsorySetup['employeeList'] = assignList;
            app.pullDataById(document.handleWizardUrl, compulsorySetup).then(function (response) {
                if (response.success) {
                    document.location = document.overtimeAutomationPage;
                }
            }, function (error) {

            });
        }).hide();


        /*
         * 
         */



        if (document.editData != null) {
            var otSetup = document.editData['compulsoryOTSetup'];
            compulsorySetup = {compulsoryOvertimeId: otSetup['COMPULSORY_OVERTIME_ID'], compulsoryOtDesc: otSetup['COMPULSORY_OT_DESC'], earlyOvertimeHour: otSetup['EARLY_OVERTIME_HR'], lateOvertimeHour: otSetup['LATE_OVERTIME_HR'], startDate: otSetup['START_DATE'], endDate: otSetup['END_DATE']};
            assignList = document.editData['assignedEmployees'];
            $compulsoryOtDesc.val(compulsorySetup['compulsoryOtDesc']);
            $startDate.val(compulsorySetup['startDate']);
            $endDate.val(compulsorySetup['endDate']);
            $earlyOvertimeHour.combodate('setValue', compulsorySetup['earlyOvertimeHour']);
            $lateOvertimeHour.combodate('setValue', compulsorySetup['lateOvertimeHour']);

        }

        $searchBtn.on('click', function () {
            $table.find('tbody').empty();
            var filteredEmployeeList = document.searchManager.getSelectedEmployee();
            $.each(filteredEmployeeList, function (k, v) {
                var appendData = "<tr>"
                        + "<td>" + v.FULL_NAME + "</td>"
                        + "<td>"
                        + "<div class='th-inner'><label class='mt-checkbox mt-checkbox-single mt-checkbox-outline'>"
                        + "<input class='check' type='checkbox' name='checkapply[]' value='" + v.EMPLOYEE_ID + "'>"
                        + "<span></span></label></div></td>";
                +"<tr>";

                $("#employeeTable").find('tbody').append(appendData);
            });
            $('.check').each(function () {
                var $this = $(this);
                var value = $this.val();
                var filteredList = assignList.filter(function (item) {
                    return item == value;
                });
                if (filteredList.length > 0) {
                    $this.prop('checked', true);
                }
                $this.on('change', function () {
                    var checkedStatus = $(this).is(":checked")
                    var index = assignList.indexOf(value);
                    if (checkedStatus) {
                        if (index < 0) {
                            assignList.push(value);
                        }
                    } else {
                        if (index > -1) {
                            assignList.splice(index, 1);
                        }
                    }
                });

            });
        });

        $checkAll.on('click', function () {
            var checkedStatus = $(this).is(":checked");
            $('.check').prop('checked', checkedStatus);

            $('.check').each(function () {
                var $this = $(this);
                var checkedStatus = $this.is(":checked");
                var value = $this.val();
                var index = assignList.indexOf(value);
                if (checkedStatus) {
                    if (index < 0) {
                        assignList.push(value);
                    }
                } else {
                    if (index > -1) {
                        assignList.splice(index, 1);
                    }
                }
            });
        });

        $wizard.on('click', '.remove-employee', function () {
            var $this = $(this);
            var employeeId = $this.attr('data-id');
            var index = assignList.indexOf(employeeId);
            if (index > -1) {
                assignList.splice(index, 1);
            }
        });
    });


})(window.jQuery, window.app);

