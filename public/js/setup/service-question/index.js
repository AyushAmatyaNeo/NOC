(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#serviceQuestionTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:QA_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:QA_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "QUESTION_EDESC", title: "Question Name", width: 200},
            {field: "PARENT_QUESTION_EDESC", title: "Parent Question", width: 200},
            {field: "SERVICE_EVENT_TYPE_NAME", title: "Service Event Type", width: 100},
            {field: "QA_INDEX", title: "Question Index", width: 90},
            {field: "QA_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'ServiceQuestionList');

        app.searchTable('serviceQuestionTable', ['QUESTION_EDESC', 'PARENT_QUESTION_EDESC', 'SERVICE_EVENT_TYPE_NAME', 'QA_INDEX']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'QUESTION_EDESC': 'Question',
                'PARENT_QUESTION_EDESC': 'Parent Question',
                'SERVICE_EVENT_TYPE_NAME': 'serive Event Type',
                'QA_INDEX': 'Index',
                'REMARKS': 'Remarks'
            }, 'ServiceQuestionList');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'QUESTION_EDESC': 'Question',
                'PARENT_QUESTION_EDESC': 'Parent Question',
                'SERVICE_EVENT_TYPE_NAME': 'serive Event Type',
                'QA_INDEX': 'Index',
                'REMARKS': 'Remarks'
            }, 'ServiceQuestionList');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);