(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var actiontemplateConfig = {
            update: {
                'ALLOW_UPDATE': document.acl.ALLOW_UPDATE,
                'params': ["COMPANY_ID", "FISCAL_YEAR_ID", "FISCAL_YEAR_MONTH_NO"],
                'url': document.editLink
            },
            delete: {
                'ALLOW_DELETE': document.acl.ALLOW_DELETE,
                'params': ["COMPANY_ID", "FISCAL_YEAR_ID", "FISCAL_YEAR_MONTH_NO"],
                'url': document.deleteLink,
                'confirmation': false
            }
        };

        app.initializeKendoGrid($table, [
            {field: "COMPANY_NAME", title: "Company Name"},
            {field: "NO_OF_DAYS", title: "No of Days"},
            {field: ["COMPANY_ID", "FISCAL_YEAR_ID", "FISCAL_YEAR_MONTH_NO"], title: "Action", template: app.genKendoActionTemplate(actiontemplateConfig)}
        ]);
        app.searchTable($table, ['COMPANY_NAME']);
        var exportMap = {
            'COMPANY_NAME': 'Leave',
            'NO_OF_DAYS': 'Total Days',
            'MONTH_EDESC': 'Month'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Penalty List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Penalty List');
        });
        var months = null;
        var $year = $('#fiscalYear');
        var $month = $('#fiscalMonth');
        var $noOfDeductionDays = $('#noOfDeductionDays');
        $noOfDeductionDays.val(document.noOfDeductionDays);
        app.setFiscalMonth($year, $month, function (yearList, monthList, currentMonth) {
            months = monthList;
        });

        var monthChange = function ($this) {
            var value = $this.val();
            if (value == null) {
                return;
            }
            var selectedMonthList = months.filter(function (item) {
                return item['MONTH_ID'] === value;
            });
            if (selectedMonthList.length <= 0) {
                return;
            }
            app.serverRequest("", {fiscalYearId: selectedMonthList[0]['FISCAL_YEAR_ID'], fiscalYearMonthNo: selectedMonthList[0]['FISCAL_YEAR_MONTH_NO']}).then(function (response) {
                app.renderKendoGrid($table, response.data);
            }, function (error) {

            });
        };
        $month.on('change', function () {
            monthChange($(this));
        });

        var deductionProcess = function (link, config) {
            app.serverRequest(link, config).then(function (response) {
                if (response.success) {
                    monthChange($month);
                }
            }, function (error) {

            });
        };

        $('body').on('click', '.btn-edit', function () {
            var $this = $(this);
            var link = $this.attr('href');
            deductionProcess(link, {action: 'E', noOfDeductionDays: $noOfDeductionDays.val()});
            return false;
        });

        $('body').on('click', '.btn-delete', function () {
            var $this = $(this);
            var link = $this.attr('href');
            deductionProcess(link, {action: 'D', noOfDeductionDays: $noOfDeductionDays.val()});
            return false;
        });




    });
})(window.jQuery, window.app);
