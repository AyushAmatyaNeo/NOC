(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#academicCourseTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:ACADEMIC_COURSE_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:ACADEMIC_COURSE_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "ACADEMIC_COURSE_NAME", title: "Academic Course", width: 200},
            {field: "ACADEMIC_PROGRAM_NAME", title: "Academic Program", width: 200},
            {field: "ACADEMIC_COURSE_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'AcademicCourseList');

        app.searchTable('academicCourseTable', ['ACADEMIC_COURSE_NAME', 'ACADEMIC_PROGRAM_NAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'ACADEMIC_COURSE_NAME': ' Course Name',
                'ACADEMIC_PROGRAM_NAME': 'AcademicProgram'
            }, 'AcademicCourseList');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'ACADEMIC_COURSE_NAME': ' Course Name',
                'ACADEMIC_PROGRAM_NAME': 'AcademicProgram'
            }, 'AcademicCourseList');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);