(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.datePickerWithNepali('transactionDt', 'nepaliTransactionDt');
        var $employeeId = $('#employeeId');
        var $dependentId = $('#eRId');
        var $remainingBalance = $('#remainingBalance');
        var $requestedAmt = $('#requestedAmt');
        var $billTable = $('#billTable');
        var $billAddBtn = $('.billAddBtn');
        app.addDatePicker($('#billDate'));
        var employeeBalance;
//        app.datePickerWithNepali($('#billDate'), $('#nepaliBillDate'));

        $('#dependentDiv').hide();
        app.populateSelect($employeeId, document.employeeList, 'EMPLOYEE_ID', 'FULL_NAME', '---', '');
        $employeeId.on('change', function () {
            $remainingBalance.val(0);
            employeeBalance = {
                DEPENDENT_TAKEN: "0",
                DEP_REAMINING: "0",
                DEP_REAMINING_WITH_OPER: "0",
                SELF_REAMINING: "0",
                SELF_TAKEN: "0"};

            var selectedEmployeeId = $(this).val();
            app.pullDataById(document.pullEmpMedicalDetailLink,
                    {employeeId: selectedEmployeeId}
            ).then(function (response) {
                if (response.success == true) {
                    employeeBalance = response.data;
                }
                allConfig(selectedEmployeeId);
            }, function (error) {

            });
        });
        // on self or dependend change
        $('input[type=radio][name=claimOf]').change(function () {
            let selectedEmployeeId = $employeeId.val()
            allConfig(selectedEmployeeId);
        });
        // to check if operation or not
        $('input[type=radio][name=operationFlag]').change(function () {
            let selectedEmployeeId = $employeeId.val()
            allConfig(selectedEmployeeId);
        });
        var allConfig = function (employeeId) {
            if (typeof employeeId !== 'undefined' && employeeId !== '') {
                let selectedDependent = $dependentId.val();
                let operationFlagVal = $("input[name='operationFlag']:checked").val();
                let claimOfVal = $("input[name='claimOf']:checked").val();
                app.pullDataById(document.pullEmployeeRelationLink, {employeeId: employeeId}).then(function (response) {
                    let realtionList = response.data;
                    if (selectedDependent && claimOfVal=='D') {
                        app.populateSelect($dependentId, realtionList, 'E_R_ID', 'PERSON_NAME', '---', '-1', selectedDependent);
                    } else {
                        app.populateSelect($dependentId, realtionList, 'E_R_ID', 'PERSON_NAME', '---', '-1');
                    }
                }, function (error) {

                });
//                console.log(operationFlagVal);
//                    console.log(claimOfVal);


                if (claimOfVal == 'D') {
                    if (operationFlagVal == 'Y') {
                        $remainingBalance.val(employeeBalance.DEP_REAMINING_WITH_OPER);
                        $requestedAmt.attr({"max": employeeBalance.DEP_REAMINING_WITH_OPER});
                    } else {
                        $remainingBalance.val(employeeBalance.DEP_REAMINING);
                        $requestedAmt.attr({"max": employeeBalance.DEP_REAMINING});
                    }
//                    $dependentId.prop('disabled', false);
                    $('#dependentDiv').show();
                } else if (claimOfVal == 'S') {
                    $remainingBalance.val(employeeBalance.SELF_REAMINING);
                    $requestedAmt.attr({"max": employeeBalance.SELF_REAMINING});
//                    $dependentId.prop('disabled', true);
                    $('input:radio[name=operationFlag]')[1].checked = true;
                    $('#dependentDiv').hide();
                }

            } else {
                $dependentId.prop('enable', true);
                $remainingBalance.val(0);
            }
        }



        $billAddBtn.on('click', function () {
            console.log('clciked');
            var appendData = `
            <tr>
                <td><input class="billno form-control" type="text" name="billNo[]" required></td>
                <td>
<input class="billDate form-control" type="text" name="billDate[]" required>

</td>
                <td><input class="billAmt form-control" type="number" name="billAmt[]" min="0" step="0.01" required></td>
            <td><input class="billDelBtn btn btn-danger" type="button" value="Del -"></td>
            </tr>
            
            `;
            $('#billTable tbody').append(appendData);
            var $selectedEnglishDate = $('#billTable tbody').find('.billDate:last');
            app.addDatePicker($selectedEnglishDate);
//            var $selectedNepaliDate = $('#billTable tbody').find('.nepaliBillDate:last');
//            app.datePickerWithNepali($selectedEnglishDate, $selectedNepaliDate);

        });
        $billTable.on('click', '.billDelBtn', function () {
            var selectedtr = $(this).parent().parent();
            selectedtr.remove();
            calculateRequestedAmt();
        });
        $billTable.on('input', '.billAmt', function () {
            calculateRequestedAmt();
        });
        var calculateRequestedAmt = function () {
            var sum = 0;
            $('.billAmt').each(function () {
                var tempVal = $(this).val()
                if (tempVal == '') {
                    tempVal = 0;
                }
                sum += parseFloat(Number(tempVal).toFixed(2));
            });
            console.log($('#requestedAmt').attr("max"));
            if ($remainingBalance.val() == 0 || sum > $remainingBalance.val()) {
                $('#submit').attr('disabled', true);
            } else {
                $('#submit').attr('disabled', false);
            }
            $('#requestedAmt').val(sum);
        }









    });
})(window.jQuery, window.app);
    