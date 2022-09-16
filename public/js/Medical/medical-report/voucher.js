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
            {field: "ROWNUM", title: "SN", width: 80},
            {field: "ID_ACCOUNT_NO", title: "Account Number", width: 120},
            {field: "FULL_NAME", title: "a/c Name", width: 120},
            {field: "DR_AMT", title: "Dr.Amount", width: 100},
            {field: "CR_AMT", title: "Cr.Amount", width: 130},
            {field: "REMARKS", title: "Remarks", width: 90},
        ];
        
        var exportType = {"ID_ACCOUNT_NO":'STRING'};
        var map = {
            'ROWNUM': 'SN',
            'ID_ACCOUNT_NO': 'Account Number',
            'FULL_NAME': 'a/c Name',
            'DR_AMT': 'Dr.Amount',
            'CR_AMT': 'Cr.Amount',
            'REMARKS': 'Remarks'
        }
        app.initializeKendoGrid($table, columns, "MedicalVoucher.xlsx");

//        app.searchTable($table, ['EMPLOYEE_CODE', 'FULL_NAME', 'DEPARTMENT_NAME', 'FUNCTIONAL_TYPE_EDESC', 'SELF', 'DEPENDENT', 'OPERATION']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'MedicalVoucher.xlsx.xlsx',exportType);
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'MedicalVoucher.xlsx.pdf');
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
            let tempCnt = 1;
            $.each(data, function (index, value) {
                var appendData = `
                 <tr>
            <td>` + value.ROWNUM + `</td>
            <td style='mso-number-format:"\@"'>` + value.ID_ACCOUNT_NO + `</td>
            <td>` + value.FULL_NAME + `</td>
            <td>` + value.DR_AMT + `</td>
            <td>` + value.CR_AMT + `</td>
            <td>` + value.REMARKS + `</td>
                </tr>
            `;
                $('#printTable tbody').append(appendData);
                tempCnt++;
            });

            if (tempCnt !== 1) {
                var appendDebit = `
                 <tr>
            <td>` + tempCnt + `</td>
            <td style='mso-number-format:"\@"' >0101011126901</td>
            <td>SOALTEE CROWNE PLAZA</td>
            <td style='mso-number-format:"\@"' >` + total.TOTAL_AMT + `</td>
            <td></td>
            <td>Medical Reimbursement</td>
                </tr>
            `;
                $('#printTable tbody').append(appendDebit);


                var appendTotal = `
                 <tr>
            <td></td>
            <td>Total</td>
            <td></td>
            <td style='mso-number-format:"\@"' >` + total.TOTAL_AMT + `</td>
            <td style='mso-number-format:"\@"' >` + total.TOTAL_AMT + `</td>
            <td></td>
                </tr>
            `;
                $('#printTable tbody').append(appendTotal);
            }


            $('#curDate').html(total.CUR_DATE);




        }

//        $("#reset").on("click", function () {
//            $(".form-control").val("");
//        });

        $('#printExcel').on('click', function () {
            app.exportTableToExcel('printDiv', 'MedicalVoucher', 'MedicalVoucher');
        });



    });
})(window.jQuery);