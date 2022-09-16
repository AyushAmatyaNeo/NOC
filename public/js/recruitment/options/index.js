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
        var $table = $('#OptionsTable');
        var action = `
            <div class="clearfix">
                                
                <a class="btn btn-icon-only yellow" href="${document.editLink}/#:OPTION_ID#" style="height:17px;" title="Edit">
                    <i class="fa fa-edit"></i>
                </a>
                
                
                <a  class="btn btn-icon-only red confirmation" href="${document.deleteLink}/#:OPTION_ID#" style="height:17px;" title="Delete">
                    <i class="fa fa-times"></i>
                </a>
                
            </div>
        `;

        app.initializeKendoGrid($table, [
            {title: "Options Details",
                columns: [{
                        field: "OPTION_EDESC",
                        title: "English",
                    },
                    {field: "OPTION_NDESC",
                        title: "Nepali",
                    }]},
            {field: "REMARKS", title: "Remark"},
            {field: "OPTION_ID", title: "Action", template: action}


        ],null, null, null, 'Vacancy List');


        $('#search').on('click', function () {
            var OptionEdesc          = $('#OptionEdesc').val();
           
            app.pullDataById('', {
                'OptionEdesc' : OptionEdesc,
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
                'OPTION_NDESC': 'OptionNdesc',
                'OPTION_EDESC': 'OptionEdesc',
                'REMARKS': 'Remarks',
                'STATUS' : 'Status'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, exportMap, 'Options.xlsx');
        });

        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, exportMap, 'Options.pdf');
        });

    });
})(window.jQuery, window.app);
