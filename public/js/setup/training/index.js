(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#trainingTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:TRAINING_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:TRAINING_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
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
        ], null, null, null, 'TrainingList');

        app.searchTable('trainingTable', ['TRAINING_NAME', 'COMPANY_NAME', 'START_DATE_AD', 'END_DATE_AD', 'START_DATE_BS', 'END_DATE_BS', 'DURATION', 'INSTITUTE_NAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'TRAINING_NAME': 'Training',
                'COMPANY_NAME': 'Company',
                'START_DATE_AD': 'Start Date(AD)',
                'START_DATE_BS': 'Start Date(BS)',
                'END_DATE_AD': 'End Date(AD)',
                'END_DATE_BS': 'End Date(BS)',
                'DURATION': 'Duration',
                'INSTITUTE_NAME': 'Institute'
            }, 'TrainingList');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'TRAINING_NAME': 'Training',
                'COMPANY_NAME': 'Company',
                'START_DATE_AD': 'Start Date(AD)',
                'START_DATE_BS': 'Start Date(BS)',
                'END_DATE_AD': 'End Date(AD)',
                'END_DATE_BS': 'End Date(BS)',
                'DURATION': 'Duration',
                'INSTITUTE_NAME': 'Institute'
            }, 'TrainingList');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);