(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        // app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, true);
        $("#reset").on("click", function () {
            if (typeof document.ids !== "undefined") {
                $.each(document.ids, function (key, value) {
                    $("#" + key).val(value).change();
                });
            }
        });
        var $table = $('#VacancyoptionsTable');
        var action = `
            <div class="clearfix">
                <a class="btn btn-icon-only green" href="${document.viewLink}/#:VACANCY_OPTION_ID#" style="height:17px;" title="View Detail">
                    <i class="fa fa-search"></i>
                </a>
                
                <a class="btn btn-icon-only yellow" href="${document.editLink}/#:VACANCY_OPTION_ID#" style="height:17px;" title="Edit">
                    <i class="fa fa-edit"></i>
                </a>
                
                
                <a  class="btn btn-icon-only red confirmation" href="${document.deleteLink}/#:VACANCY_OPTION_ID#" style="height:17px;" title="Delete">
                    <i class="fa fa-times"></i>
                </a>
                
            </div>
        `;

        app.initializeKendoGrid($table, [
            {title: "Vacancy Description",
                columns: [{
                        field: "VACANCY_ID",
                        title: "Vacancy Ad Number",
                    },
                    {field: "OPTION_ID",
                        title: "Options Name",
                    }]},
            {field: "QUOTA", title: "Quota"},
            {field: "OPEN_INTERNAL", title: "Open / Internal"},
            {field: "NORMAL_AMT", title: "Normal Amount"},
            {field: "LATE_AMT", title: "Late Amount"},
            {field: "REMARKS", title: "Remark"},
            {field: "VACANCY_OPTION_ID", title: "Action", template: action}


        ],null, null, null, 'VacancyOption List');


        $('#search').on('click', function () {

            var AdNo  = $('#AdNo').val();

            app.pullDataById('', {
                'AdNo' : AdNo
            }).then(function (response) {
                if (response.success) {
                    app.renderKendoGrid($table, response.data);
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });

        });


        app.searchTable($table ['VACANCY_ID']);
        var exportMap = {                
                'OPENING_ID': 'OpeningId',
                'VACANCY_ID' : 'VacancyId',
                'QUOTA': 'Quota',
                'OPEN_INTERNAL': 'OpenInternal',
                'NORMAL_AMT': 'NormalAmt',
                'LATE_AMT': 'LateAmt',
                'REMARKS' : 'Remarks',
                'STATUS' : 'Status'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Vacancy_Options.xlsx');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Vacancy_Options.pdf');
        });

    });
})(window.jQuery, window.app);
