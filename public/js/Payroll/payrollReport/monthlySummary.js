(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('Select').select2();

        var monthList = null;
        var $fiscalYear = $('#fiscalYearId');
        var $month = $('#monthId');
        var $table = $('#table');
        var $salaryTypeId = $('#salaryTypeId');
        var map = {};
        var exportType = {
            "ACCOUNT_NO": "STRING",
        };
        
        app.populateSelect($salaryTypeId, document.salaryType, 'SALARY_TYPE_ID', 'SALARY_TYPE_NAME', 'All',-1,-1);






        app.setFiscalMonth($fiscalYear, $month, function (years, months, currentMonth) {
            monthList = months;
        });





        $('#searchEmployeesBtn').on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['fiscalId'] = $fiscalYear.val();
            q['monthId'] = $month.val();
            q['salaryTypeId'] = $salaryTypeId.val();
//            console.log(q);

            app.serverRequest(document.pullMonthlySummaryLink, q).then(function (response) {
                if (response.success) {
                    console.log(response);
                    renderView(response.data.additionDetail,response.data.deductionDetail)
//                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });


        });


        var renderView = function (addition, deduction) {
            $("table tbody").empty();
            let loopNo = 0;
            let additionLength = addition.length;
            let deductionLength = deduction.length;
            loopNo = (additionLength > deductionLength) ? additionLength : deductionLength;
            let totalAddition=0;
            let totalDeduction=0;

            let appendData = '';
            for (var n = 0; n < loopNo; ++n) {
                totalAddition+=parseFloat((n < additionLength) ? addition[n].TOTAL : 0);          
                totalDeduction+=parseFloat((n < deductionLength) ? deduction[n].TOTAL : 0);                
//                let sn = (n + 1);
                let additionSn = (n < additionLength) ? n+1 : ' ';
                let deductionSn = (n < deductionLength) ? n+1 : ' ';
                let additionName = (n < additionLength) ? addition[n].PAY_EDESC : ' ';
                let additionValue = (n < additionLength) ? parseFloat(addition[n].TOTAL).toFixed(2) : ' ';
                let deductionName = (n < deductionLength) ? deduction[n].PAY_EDESC : ' ';
                let deductionValue = (n < deductionLength) ? parseFloat(deduction[n].TOTAL).toFixed(2) : ' ';
                appendData += `<tr>
                        <td>` + additionSn + `</td>
                        <td>` + additionName + `</td>
                        <td>` + additionValue + `</td>
                        <td>` + deductionSn + `</td>
                        <td>` + deductionName + `</td>
                        <td>` + deductionValue+ `</td>
                        <tr>`;
            }
            appendData += `<tr>
                        <td> </td>
                        <td><b>Total Earnings</b></td>
                        <td><b>` + totalAddition.toFixed(2) + `</b></td>
                        <td></td>
                        <td><b>Total Deducation</b></td>
                        <td><b>` + totalDeduction.toFixed(2) + `</b></td>
                        <tr>`;
            $("table tbody").append(appendData);




        }

//
//
        $('#excelExport').on('click', function () {
             $("#tableSummary").table2excel({
                exclude: ".noExl",
                name: "Monthly Summary",
                filename: "Monthly Summary Report" 
            });
        });
        
        $('#pdfExport').on('click', function () {
            app.exportDomToPdf2("summaryDiv");
        });






    });
})(window.jQuery, window.app);


