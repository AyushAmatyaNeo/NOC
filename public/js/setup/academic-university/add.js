(function ($, app) {
    'use strict';
    $(document).ready(function () {

        var inputFieldId = "form-academicUniversityName";
        var formId = "academicUniversity-form";
        var tableName =  "HRIS_ACADEMIC_UNIVERSITY";
        var columnName = "ACADEMIC_UNIVERSITY_NAME";
        var checkColumnName = "ACADEMIC_UNIVERSITY_ID";
        var selfId = $("#academicUniversityId").val();
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId, function () {
            App.blockUI({target: "#hris-page-content"});
        });
        window.app.checkUniqueConstraints("form-academicUniversityCode",formId,tableName,"ACADEMIC_UNIVERSITY_CODE",checkColumnName,selfId);
    });
})(window.jQuery, window.app);
