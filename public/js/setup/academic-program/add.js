(function ($, app) {
    'use strict';
    $(document).ready(function () {

        var inputFieldId = "form-academicProgramName";
        var formId = "academicProgram-form";
        var tableName =  "HRIS_ACADEMIC_PROGRAMS";
        var columnName = "ACADEMIC_PROGRAM_NAME";
        var checkColumnName = "ACADEMIC_PROGRAM_ID";
        var selfId = $("#academicProgramId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId, function () {
            App.blockUI({target: "#hris-page-content"});
        });
        window.app.checkUniqueConstraints("form-academicProgramCode",formId,tableName,"ACADEMIC_PROGRAM_CODE",checkColumnName,selfId);
    });
})(window.jQuery, window.app);
