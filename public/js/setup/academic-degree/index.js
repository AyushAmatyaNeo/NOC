
(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#academicDegreeTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:ACADEMIC_DEGREE_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:ACADEMIC_DEGREE_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "ACADEMIC_DEGREE_NAME", title: "Academic Degree", width: 300},
            {field: "WEIGHT", title: "Weight", width: 100},
            {field: "ACADEMIC_DEGREE_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'Academic degree List');

        app.searchTable('academicDegreeTable', ['ACADEMIC_DEGREE_NAME', 'WEIGHT']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'ACADEMIC_DEGREE_NAME': 'Academic Degree Name',
                'WEIGHT': 'Weight',
            }, 'Academic Degree List');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'ACADEMIC_DEGREE_NAME': 'Academic Degree Name',
                'WEIGHT': 'Weight',
            }, 'Academic Degree List');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);