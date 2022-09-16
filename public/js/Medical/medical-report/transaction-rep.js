(function ($) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
        var $search = $('#search');
        var $fromDate = $('#fromDate');
        var $toDate = $('#toDate');
        var $table = $('#table');
        var columns = [
            {field: "EMPLOYEE_CODE", title: "Code", width: 80},
            {field: "FULL_NAME", title: "Employee", width: 120},
            {field: "DEPARTMENT_NAME", title: "Department", width: 100},
            {field: "FUNCTIONAL_TYPE_EDESC", title: "Functional Type", width: 130},
            {field: "CLAIM_OF_NAME", title: "Claim Of", width: 90},
            {field: "ID_ACCOUNT_NO", title: "Account No", width: 120},
            {field: "APPROVED_AMT", title: "Amt", width: 80}
        ];
        var exportType = {"ID_ACCOUNT_NO":'STRING'};
        var map = {
            'EMPLOYEE_CODE': 'Code',
            'FULL_NAME': 'Employee',
            'DEPARTMENT_NAME': 'Department',
            'FUNCTIONAL_TYPE_EDESC': 'Functional Type',
            'CLAIM_OF_NAME': 'Claim Of',
            'ID_ACCOUNT_NO': 'Account No',
            'APPROVED_AMT': 'Amt'
        }
        app.initializeKendoGrid($table, columns, "Advance List.xlsx");

        app.searchTable($table, ['EMPLOYEE_CODE', 'FULL_NAME', 'DEPARTMENT_NAME', 'FUNCTIONAL_TYPE_EDESC', 'SELF', 'DEPENDENT', 'OPERATION']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'MedicalBalance.xlsx',exportType);
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'MedicalBalance.pdf');
        });


        $search.on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['fromDate'] = $fromDate.val();
            q['toDate'] = $toDate.val();
            app.serverRequest("", q).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($table, response.data);
                    populateTableForPrint(response.data, response.total)


                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

        $('#printPdf').on('click', function () {
            console.log('sdf');
            app.exportDomToPdf2('printDiv');
        });



        var populateTableForPrint = function (data, total) {
            $("#printTable tbody").empty();
            $.each(data, function (index, value) {
                var appendData = `
                 <tr>
            <td>` + value.EMPLOYEE_CODE + `</td>
            <td>` + value.FULL_NAME + `</td>
            <td>` + value.DEPARTMENT_NAME + `</td>
            <td>` + value.FUNCTIONAL_TYPE_EDESC + `</td>
            <td>` + value.CLAIM_OF_NAME + `</td>
            <td style='mso-number-format:"\@"' >` + value.ID_ACCOUNT_NO + `</td>
            <td>` + value.APPROVED_AMT + `</td>
                </tr>
            `;
                $('#printTable tbody').append(appendData);
            });
            var totalData = `
                 <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><b>Total</b></td>
            <td style='mso-number-format:"\@"' ><b>` + total.TOTAL_AMT + `</b></td>
                </tr>
            `;
            $('#printTable tbody').append(totalData);


            var totalInWords = `
                 <tr>
            <td><b>IN Words</b></td>
            <td colspan="6"><b> Rs. ` + total.TOTAL_AMT_IN_WORDS + `</b></td>
                </tr>
            `;

            $('#printTable tbody').append(totalInWords);

            let reportType = $('#reportType').val();
            if (reportType == 'TF') {

                var footerData = `
                 <tr>
            <td colspan="7"></td>
                </tr>
                 <tr>
            <td colspan="3" style="text-align:center !important;">Approved By</td>
            <td colspan="4" style="text-align:center !important;">Approved By</td>
                </tr>
                 <tr height="113">
            <td colspan="3" style="vertical-align:bottom !important;text-align:center !important;" >DIRECTOR FINANCE & BS</td>
            <td colspan="4" style="vertical-align:bottom !important;text-align:center !important;" >GENERAL MANAGER</td>
                </tr>
            `;

                $('#printTable tbody').append(footerData);
            }

        }


//        $("#reset").on("click", function () {
//            $(".form-control").val("");
//        });

        $('#printExcel').on('click', function () {
            app.exportTableToExcel('printDiv', 'MedicalTransaction', 'MedicalTransaction');
        });



    });
})(window.jQuery);