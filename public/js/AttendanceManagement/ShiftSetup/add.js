(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();

        var $startTime = $('#startTime');
        var $endTime = $('#endTime');
        var $halfDayEndTime = $('#halfDayEndTime');
        var $halfTime = $('#halfTime');
        var $graceStartTime = $('#graceStartTime');
        var $graceEndTime = $('#graceEndTime');
        var $halfDayInTime = $('#halfDayInTime');
        var $halfDayOutTime = $('#halfDayOutTime');
        var $lateIn = $('#lateIn');
        var $earlyOut = $('#earlyOut');
        var $actualWorkingHr = $('#actualWorkingHr');
        var $totalWorkingHr = $('#totalWorkingHr');

        app.addComboTimePicker($startTime, $endTime, $halfDayEndTime, $halfTime, $graceStartTime, $graceEndTime, $lateIn, $earlyOut, $totalWorkingHr, $actualWorkingHr, $halfDayInTime, $halfDayOutTime);


        var $form = $('#shiftSetup-form');

        var format = function (input) {
            var str = "" + input;
            var pad = "00";
            return pad.substring(0, pad.length - str.length) + str;
        };

        var onChangeTime = function () {
            try {
                var startValue = $startTime.combodate('getValue', 'YYYY-MM-DDTHH:mm:ss.sssZ');
                var endValue = $endTime.combodate('getValue', 'YYYY-MM-DDTHH:mm:ss.sssZ');

                if (startValue == '' || endValue == '') {
                    throw {message: 'Start Time or End Time not set.', type: 'internal'};
                }
                var startDate = new Date(startValue);
                var endDate = new Date(endValue);

                var timeDiff = endDate.getTime() - startDate.getTime();
                if (timeDiff <= 0) {
                    throw {message: 'End Time should be greater than Start Time.', type: 'external'};
                }

                var diffHr = Math.floor(timeDiff / (1000 * 3600));
                var diffMin = Math.floor((timeDiff % (1000 * 3600)) / (1000 * 60));
                $totalWorkingHr.combodate('setValue', format(diffHr) + ":" + format(diffMin));

            } catch (e) {
                console.log('exceptions', e);
                if (e.type == 'external') {
                    app.errorMessage(e.message);
                }
            }

        };

        $startTime.on('change', function () {
            onChangeTime();
        });

        $endTime.on('change', function () {
            onChangeTime();
        });

//        app.startEndDatePicker('startDate', 'endDate');
        app.startEndDatePickerWithNepali('nepaliStartDate1', 'startDate', 'nepaliEndDate1', 'endDate');
        /* prevent past event post || commented for now as needs discussion*/
//        $('#startDate').datepicker("setStartDate", new Date());
//        $('#endDate').datepicker("setStartDate", new Date());
        /* end of  prevent past event post */

        var inputFieldId = "shiftEname";
        var formId = "shiftSetup-form";
        var tableName = "HRIS_SHIFTS";
        var columnName = "SHIFT_ENAME";
        var checkColumnName = "SHIFT_ID";
        var selfId = $("#shiftId").val();
        if (typeof selfId === "undefined") {
            selfId = 0;
        }
        window.app.checkUniqueConstraints(inputFieldId, formId, tableName, columnName, checkColumnName, selfId, function () {
            try {
                var error = [];
                if ($startTime.val() === "") {
                    error.push({message: 'This field cannot be empty.', object: $startTime});
                }
                if ($endTime.val() === "") {
                    error.push({message: 'This field cannot be empty.', object: $endTime});
                }
                if ($totalWorkingHr.val() === "") {
                    error.push({message: 'This field cannot be empty.', object: $totalWorkingHr})
                }
                if ($actualWorkingHr.val() === "") {
                    error.push({message: 'This field cannot be empty.', object: $actualWorkingHr});
                }
                if (error.length > 0) {
                    throw error;
                }
                App.blockUI({target: "#hris-page-content"});
                return true;
            } catch (e) {
                $.each(e, function (index, item) {
                    var $errorElement = $('<span class="required" aria-required="true"></span>');
                    $errorElement.append(item.message);
                    var $parent = item.object.parent();
                    if (!($parent.find('span.required').length > 0)) {
                        $parent.append($errorElement);
                    }
                });
                return false;
            }
        });
        window.app.checkUniqueConstraints("shiftLname", formId, tableName, "SHIFT_LNAME", checkColumnName, selfId);
        window.app.checkUniqueConstraints("shiftCode", formId, tableName, "SHIFT_CODE", checkColumnName, selfId);

        $form.on('submit', function () {

        });
    });
})(window.jQuery, window.app);
