(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#academicUniversityTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:ACADEMIC_UNIVERSITY_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:ACADEMIC_UNIVERSITY_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "ACADEMIC_UNIVERSITY_NAME", title: "Academic University", width: 400},
            {field: "ACADEMIC_UNIVERSITY_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'AcademicUniversityList');

        app.searchTable('academicUniversityTable', ['ACADEMIC_UNIVERSITY_NAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'ACADEMIC_UNIVERSITY_NAME': 'Academic University Name',
            }, 'AcademicUniversityList');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'ACADEMIC_UNIVERSITY_NAME': 'Academic University Name',
            }, 'AcademicUniversityList');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);