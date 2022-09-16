(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();

        var $assignTable = $('#employeeTable');

        var $wohFlag = $('#wohFlagId');
        var $wohReward = $('#wohRewardID');
        var $updateValue = $('#updateId');

        function searchAction() {

            var data = document.searchManager.getSearchValues();
            data.wohReward = $wohReward.val();

            $assignTable.find("tr:gt(0)").remove();

            app.serverRequest("", {'data': data}).then(function (response) {
                console.log(response);

                $.each(response.data, function (index, value) {
                    value.BRANCH_NAME=(value.BRANCH_NAME!==null)?value.BRANCH_NAME:' ';
                    value.DEPARTMENT_NAME=(value.DEPARTMENT_NAME!==null)?value.DEPARTMENT_NAME:' ';
                    value.POSITION_NAME=(value.POSITION_NAME!==null)?value.POSITION_NAME:' ';
                    var appendData = `<tr >
                            <td>` + value.EMPLOYEE_CODE + `</td>
                            <td>` + value.FULL_NAME + `</td>
                            <td>
                    ` + value.BRANCH_NAME + `
                            </td>
                            <td>
                    ` + value.DEPARTMENT_NAME+ `
                            </td>
                            <td>
                    ` + value.DESIGNATION_TITLE+ `
                            </td>
                            <td>
                    ` + value.POSITION_NAME+ `
                            </td>
                            <td>
                    ` + value.WOH_REWARD+ `
                            </td>
                            <td>
                    ` + value.ASSIGNED+ `
                            </td>
                            <td class="bs-checkbox " style="width: 36px; " data-field="state" tabindex="0">
                                <div class="th-inner ">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input dataEmp="` + value.EMPLOYEE_ID + `" class="insideChkBox"  type="checkbox"/>
                                        <span></span>
                                    </label>
                                </div>
                                <div class="fht-cell" style="width: 46px;"></div>
                            </td>
                        </tr>`;

                    $assignTable.append(appendData);
                    if (value.ASSIGNED == 'Y') {
                        var $lastAppendedChkbox = $('#employeeTable tbody').find('.insideChkBox:last');
                        $lastAppendedChkbox.prop("checked", true);
                    }
                });

            }, function (error) {

            });

        }
        ;

        $('#search').on('click', function () {
            searchAction();
        });


        $('#masterCheckElement').on('click', function () {
            var elementChecked = $(this).is(':checked');
            if (elementChecked) {
                $('.insideChkBox').prop("checked", true);
            } else {
                $('.insideChkBox').prop("checked", false);
            }

        });


        $('#assignBtn').on('click', function () {
            createcodes();
            searchAction();
        });


        function createcodes() {

            $assignTable.each(function (i, row) {
                var $row = $(row);
                var $allCheckBox = $row.find('input[class*="insideChkBox"]');
                var postValues = [];

                $allCheckBox.each(function (key, value) {
                    var employeeId = $(this).attr("dataEmp");
                    var isEmpChecked = $(this).is(':checked');
                    postValues[key] = {'employeeId': employeeId,
                                        'isChecked': isEmpChecked,
                                        'wohFlag': $wohFlag.val(),
                                        'overtimeEligible': $("input:radio[name=overtime]:checked").val(),
                                        'updateValue': $updateValue.val()};
                });

                app.serverRequest(document.assignSubMandatory, {
                    data: postValues}).then(function (response) {
                    if (response.success === true) {
                        app.showMessage("Sucessfully Assigned");
                    }
                }, function (failure) {
                    window.app.showMessage("failed");
                    throw failure;
                });

            });
        }

        $updateValue.change(function(){
            $(this).find("option:selected").each(function(){
                var optionValue = $(this).attr("value");
                if(optionValue){
                    $(".H").not("." + optionValue).hide();
                    $("." + optionValue).show();
                } else{
                    $(".H").hide();
                }
            });
        }).change();

    });
})(window.jQuery, window.app);
