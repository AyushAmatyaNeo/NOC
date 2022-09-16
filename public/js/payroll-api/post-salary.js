(function ($, app) {
    'use strict';
    $(document).ready(function () {

//        console.log(document.refreshPage);


        $('#refreshButton').on('click', function () {
            app.serverRequest(document.refreshTokenUrl, {}).then(function (response) {
                console.log(response);
                if (response.success == true) {
                    app.showMessage('Sucessfully Refreshed New Token', 'success', 'Token');
                    $('#refreshBtnDiv').hide();
                }else{
                    app.showMessage(response.error, 'warning', 'Token');
                    $('#refreshBtnDiv').show();
                }
            }, function (error) {
                console.log(error);
            });
//            
        });


        $("select").select2();
        var months = document.months;
        var fiscalYears = document.fiscalYears;

        var $monthId = $("#monthId");
        var $fiscalYearId = $("#fiscalYearId");



        app.populateSelect($fiscalYearId, fiscalYears, "FISCAL_YEAR_ID", "FISCAL_YEAR_NAME", "Select Fiscal Year");
        app.populateSelect($monthId, [], "MONTH_ID", "MONTH_EDESC", "Select Month");

        $fiscalYearId.on('change', function () {
            var value = $(this).val();
            var filteredMonths = [];
            if (value != -1) {
                var filteredMonths = months.filter(function (item) {
                    return item['FISCAL_YEAR_ID'] == value;
                });
            }
            app.populateSelect($monthId, filteredMonths, "MONTH_ID", "MONTH_EDESC", "Select Month");
        });



        $('#postBtn').on('click', function () {
//            var selectFiscalYear = $fiscalYearId.val();
            var selectMonthYear = $monthId.val();

            console.log(selectMonthYear);

            if (selectMonthYear == -1) {
                app.showMessage('Please select Month Before Posting', 'warning', 'Not Selected');
                return false;
            } else {
                $("#postSalaryForm").submit();
            }


        });





    });
})(window.jQuery, window.app);
