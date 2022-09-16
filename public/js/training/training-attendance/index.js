(function ($, app) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#table');
        var viewAction = '<span><a class="btn-edit" href="' + document.viewLink + '/#:TRAINING_ID#" style="height:17px;" title="view detail">'
                + '<i class="fa fa-search-plus"></i>'
                + '</a>';
        var action = viewAction;
        app.initializeKendoGrid($table, [
            {field: "TRAINING_NAME", title: "Training"},
            {field: "COMPANY_NAME", title: "Company"},
            {title: "Start Date",
                columns: [
                    {field: "START_DATE_AD",
                        title: "AD",
                        template: "<span>#: (START_DATE_AD == null) ? '-' : START_DATE_AD #</span>"},
                    {field: "START_DATE_BS",
                        title: "BS",
                        template: "<span>#: (START_DATE_BS == null) ? '-' : START_DATE_BS #</span>"}]},
            {title: "End Date",
                columns: [
                    {field: "END_DATE_AD",
                        title: "AD",
                        template: "<span>#: (END_DATE_AD == null) ? '-' : END_DATE_AD #</span>"},
                    {field: "END_DATE_BS",
                        title: "BS",
                        template: "<span>#: (END_DATE_BS == null) ? '-' : END_DATE_BS #</span>"}]},
            {field: "DURATION", title: "Duration(in hour)"},
            {field: "INSTITUTE_NAME", title: "Institute"},
            {field: "TRAINING_ID", title: "Action", width: 120, template: action}
        ]);

        app.searchTable($table, ['TRAINING_NAME', 'COMPANY_NAME', 'START_DATE_AD', 'END_DATE_AD', 'START_DATE_BS', 'END_DATE_BS', 'DURATION', 'INSTITUTE_NAME']);
        var map = {
            'TRAINING_NAME': 'Training',
            'COMPANY_NAME': 'Company',
            'START_DATE_AD': 'Start Date(AD)',
            'START_DATE_BS': 'Start Date(BS)',
            'END_DATE_AD': 'End Date(AD)',
            'END_DATE_BS': 'End Date(BS)',
            'DURATION': 'Duration',
            'INSTITUTE_NAME': 'Institute'
        };
        $('#excelExport').on('click', function () {
            app.excelExport($table, map, 'Training attendance');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, map, 'Training attendance');
        });
        app.serverRequest("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery, window.app);