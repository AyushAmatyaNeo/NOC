(function ($) {
    'use strict';
    $(document).ready(function () {

        var $table = $('#trainingTable');
        var feedbackAction = '<span><a class="btn-edit" href="' + document.feedbackLink + '/#:EMPLOYEE_ID #/#:TRAINING_ID #" style="height:17px;" title="Send Feedback">'
                + '<i class="fa fa-comments"></i>'
                + '</a>'
                + '</span>';
        
        var feedbackAction = `#if(1==1){#<a class="btn-edit" title="Send Feedback" href="` + document.feedbackLink + `/#:EMPLOYEE_ID #/#:TRAINING_ID #" style="height:17px;"> <i class="fa fa-comments"></i></a>#}#`;
            
        
                
        var action = feedbackAction;
        app.initializeKendoGrid($table, [
            {field: "TRAINING_NAME", title: "Training"},
            {title: "Start Date",
                columns: [{
                        field: "START_DATE_AD",
                        title: "AD",
                        template: "<span>#: (START_DATE_AD == null) ? '-' : START_DATE_AD #</span>"},
                    {field: "START_DATE_BS",
                        title: "BS",
                        template: "<span>#: (START_DATE_BS == null) ? '-' : START_DATE_BS #</span>"}]},
            {title: "End Date",
                columns: [{
                        field: "END_DATE_AD",
                        title: "AD",
                        template: "<span>#: (END_DATE_AD == null) ? '-' : END_DATE_AD #</span>"},
                    {field: "END_DATE_BS",
                        title: "BS",
                        template: "<span>#: (END_DATE_BS == null) ? '-' : END_DATE_BS #</span>"}]},
            {field: "DURATION", title: "Duration(in hour)"},
            {field: "INSTITUTE_NAME", title: "Institute Name",
                template: "<span>#: (INSTITUTE_NAME == null) ? '-' : INSTITUTE_NAME #</span>"
            },
            {field: "LOCATION", title: "Location"},
            {field: ["EMPLOYEE_ID", "TRAINING_ID"], title: "Action", template: action}
        ], null, null, null, 'Training Request');


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });


        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'TRAINING_NAME': 'Training',
                'START_DATE_AD': 'Start Date AD',
                'START_DATE_BS': 'Start Date BS',
                'END_DATE_AD': 'End Date AD',
                'END_DATE_BS': 'End Date BS',
                'DURATION': 'Duration',
                'TRAINING_TYPE': 'Training',
                'INSTRUCTOR_NAME': 'Instructor',
                'INSTITUTE_NAME': 'Institute',
                'LOCATION': 'Location',
                'TELEPHONE': 'Telephone',
                'EMAIL': 'Email',
                'REMARKS': 'Remarks'
            }, 'Training List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'TRAINING_NAME': 'Training',
                'START_DATE_AD': 'Start Date AD',
                'START_DATE_BS': 'Start Date BS',
                'END_DATE_AD': 'End Date AD',
                'END_DATE_BS': 'End Date BS',
                'DURATION': 'Duration',
                'TRAINING_TYPE': 'Training',
                'INSTRUCTOR_NAME': 'Instructor',
                'INSTITUTE_NAME': 'Institute',
                'LOCATION': 'Location',
                'TELEPHONE': 'Telephone',
                'EMAIL': 'Email',
                'REMARKS': 'Remarks'
            }, 'Training List');
        });


        app.searchTable('trainingTable', ['TRAINING_NAME', 'START_DATE_AD', 'START_DATE_BS', 'END_DATE_AD', 'END_DATE_BS', 'DURATION', 'INSTITUTE_NAME', 'LOCATION']);

    });
})(window.jQuery, window.app);
