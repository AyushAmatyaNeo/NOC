(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();

        var $assignTable = $('#employeeTable');
        var $type = $('#type');
        var $leaveType = $('#leaveType');
        var $recommender = $('#recomender');
        var $approver = $('#approver');

        function searchAction() {

            var data = document.searchManager.getSearchValues();
            data['type'] = $type.val();
            data['typeId'] = $leaveType.val();

            $assignTable.find("tr:gt(0)").remove();

            app.serverRequest("", {'data': data}).then(function (response) {
                console.log(response);

                $.each(response.data, function (index, value) {
                    value.EMPLOYEE_CODE=(value.EMPLOYEE_CODE!==null)?value.EMPLOYEE_CODE:' ';
                    value.EMPLOYEE_NAME=(value.EMPLOYEE_NAME!==null)?value.EMPLOYEE_NAME:' ';
                    value.RECOMMENDER=(value.RECOMMENDER!==null)?value.RECOMMENDER:' ';
                    value.APPROVER = (value.APPROVER!=null)?value.APPROVER: '';
                    value.TYPE = (value.TYPE!=null)?value.TYPE: '';
                    value.TYPE_NAME = (value.TYPE_NAME!=null)?value.TYPE_NAME: '';
                    var appendData = `<tr >
                            <td>` + value.EMPLOYEE_CODE + `</td>
                            <td>` + value.EMPLOYEE_NAME + `</td>
                            <td>
                    ` + value.RECOMMENDER + `
                            </td>
                            <td>
                    ` + value.APPROVER+ `
                            </td>
                            <td>
                    ` + value.TYPE+ `
                            </td>
                            <td>
                    ` + value.TYPE_NAME+ `
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
                var recommender = $recommender.val();
                var approver = $approver.val();
                var type = $type.val();
                var leaveType = $leaveType.val();

                $allCheckBox.each(function (key, value) {
                    var employeeId = $(this).attr("dataEmp");
                    var isEmpChecked = $(this).is(':checked');
                    postValues[key] = {'employeeId': employeeId,
                        'isChecked': isEmpChecked,
                        'recommender': recommender,
                        'approver': approver,
                        'type': type,
                        'leaveType': leaveType,
                    };
                });

                app.serverRequest(document.assignReportingHierarchyOverride, {
                    data: postValues}).then(function (response) {
                    if (response.success === true) {
                        app.showMessage("Sucessfully Assigned");
                    }
                }, function (failure) {
                    window.app.showMessage("failed");
                    throw failure;
                });

            });
            searchAction();
        }

        $type.change(function(){
            if($type.val() == 'LV') {
                document.getElementById("leaveTypeDiv").style.display = "block"
            } else {
                document.getElementById("leaveTypeDiv").style.display = "none"
            }
        }).change();

    });
})(window.jQuery, window.app);
