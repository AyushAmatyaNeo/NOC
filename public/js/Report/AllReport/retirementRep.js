(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        app.searchTable('ageReport', ['EMPLOYEE_CODE', 'FULL_NAME'], false);

        var $greaterThan = $('#greaterThan');
        var $lessThan = $('#lessThan');

        var $greaterThanM = $('#greaterThanM');
        var $lessThanM = $('#lessThanM');

        var $greaterThanY = $('#greaterThanY');
        var $lessThanY = $('#lessThanY');

        var $search = $('#search');
        var $ageReport = $('#ageReport');

        var map = {
            'EMPLOYEE_CODE': 'Code',
            'FULL_NAME': 'Name',
            'JOIN_DATE': 'Date of Join',
            'EST_RETIREMENT_DATE': 'Estimated Retirement Date',
            'EST_RETIREMENT_REMAINING_DAYS': 'Estimated days remaining',
            'RYEARS': 'Years',
            'RMONTHS': 'Months',
            'RDAYS': 'Days',
            
        };

        app.initializeKendoGrid($ageReport, [
            {field: "EMPLOYEE_CODE", title: "Code", width: 100, locked: true},
            {field: "FULL_NAME", title: "Name", width: 250, locked: true},
            {field: "JOIN_DATE", title: "Date of Join", width: 175},
            {field: "EST_RETIREMENT_DATE", title: "Estimated Retirement Date", width: 150},
            {field: "EST_RETIREMENT_REMAINING_DAYS", title: "Estimated days remaining", width: 180},
            {field: "RYEARS", title: "Years", width: 150},
            {field: "RMONTHS", title: "Months", width: 150},
            {field: "RDAYS", title: "Days", width: 150}

        ], null, null, null, 'Retirement Report.xlsx');

        $search.on('click', function () {
            var data = document.searchManager.getSearchValues();
            data['lessThan'] = $lessThan.val();
            data['greaterThan'] = $greaterThan.val();

            data['lessThanM'] = $lessThanM.val();
            data['greaterThanM'] = $greaterThanM.val();

            data['lessThanY'] = $lessThanY.val();
            data['greaterThanY'] = $greaterThanY.val();

            app.serverRequest(document.ageWs, data).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($ageReport, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });

        $('#export').on('click', function () {
            app.excelExport($ageReport, map, 'Age Report.xlsx');
        });

    });
})(window.jQuery, window.app);