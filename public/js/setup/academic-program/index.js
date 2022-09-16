(function ($) {
    'use strict';
    $(document).ready(function () {
        var $table = $('#academicProgramTable');
        var editAction = document.acl.ALLOW_UPDATE == 'Y' ? '<a class="btn-edit" title="Edit" href="' + document.editLink + '/#:ACADEMIC_PROGRAM_ID#" style="height:17px;"> <i class="fa fa-edit"></i></a>' : '';
        var deleteAction = document.acl.ALLOW_DELETE == 'Y' ? '<a class="confirmation btn-delete" title="Delete" href="' + document.deleteLink + '/#:ACADEMIC_PROGRAM_ID#" style="height:17px;"><i class="fa fa-trash-o"></i></a>' : '';
        var action = editAction + deleteAction;
        app.initializeKendoGrid($table, [
            {field: "ACADEMIC_PROGRAM_NAME", title: "Academic Program", width: 400},
            {field: "ACADEMIC_PROGRAM_ID", title: "Action", width: 120, template: action}
        ], null, null, null, 'AcademicProgramList');

        app.searchTable('academicProgramTable', ['ACADEMIC_PROGRAM_NAME']);

        $('#excelExport').on('click', function () {
            app.excelExport($table, {
                'ACADEMIC_PROGRAM_NAME': 'Academic Program',
            }, 'AcademicProgramList');
        });
        $('#pdfExport').on('click', function () {
            app.exportToPDF($table, {
                'ACADEMIC_PROGRAM_NAME': 'Academic Program',
            }, 'AcademicProgramList');
        });


        app.pullDataById("", {}).then(function (response) {
            app.renderKendoGrid($table, response.data);
        }, function (error) {

        });
    });
})(window.jQuery);
