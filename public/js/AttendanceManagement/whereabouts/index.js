(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();

        var $assignTable = $('#employeeTable');

        function searchAction() {

            var data = document.searchManager.getSearchValues();

            $assignTable.find("tr:gt(0)").remove();

            app.serverRequest("", {'data': data}).then(function (response) {
                console.log(response);

                $.each(response.data, function (index, value) {
                    value.BRANCH_NAME=(value.BRANCH_NAME!==null)?value.BRANCH_NAME:' ';
                    value.DEPARTMENT_NAME=(value.DEPARTMENT_NAME!==null)?value.DEPARTMENT_NAME:' ';
                    value.POSITION_NAME=(value.POSITION_NAME!==null)?value.POSITION_NAME:' ';
                    value.ORDER_BY = (value.ORDER_BY!=null)?value.ORDER_BY: '';
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
                            <td class="bs-textbox" style="width: 36px; " data-field="state" tabindex="0">
                                <div class="th-inner ">
                                    <label>
                                    <input value=" ` +  value.ORDER_BY + `" class="textBox"  type="text"/>
                                        <span></span>
                                    </label>
                                </div>
                                <div class="fht-cell" style="width: 46px;"></div>
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
                var $allTextbox = $row.find('input[class*="textBox"]');

                $allCheckBox.each(function (key, value) {
                    var employeeId = $(this).attr("dataEmp");
                    var isEmpChecked = $(this).is(':checked');
                    postValues[key] = {'employeeId': employeeId,
                        'isChecked': isEmpChecked,
                        };
                });

                $allTextbox.each(function(key, value){
                    var orderBy = $(this).val();
                    postValues[key]['orderBy'] = orderBy;
                });

                console.log(postValues);

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

    });
})(window.jQuery, window.app);
